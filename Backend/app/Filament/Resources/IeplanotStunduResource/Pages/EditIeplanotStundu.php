<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\IeplanotStundu;
use App\Models\Datums;
use App\Models\StundaAmount;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class EditIeplanotStundu extends EditRecord
{
    protected static string $resource = IeplanotStunduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('copyWeek')
                ->label('Kopēt nedēļu')
                ->color('success')
                ->form([
                    Select::make('targetDatumsID')
                        ->label('Izvēlēties mērķa nedēļu')
                        ->required()
                        ->searchable()
                        ->options(function () {
                            $currentDatumsID = $this->record->datumsID;
                            $kurssID = $this->record->kurssID;
                            $allWeeks = Datums::where('id', '!=', $currentDatumsID)
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    return [$item->id => $item->PirmaisDatums . ' - ' . $item->PedejaisDatums];
                                })
                                ->toArray();
                            $weeksWithData = IeplanotStundu::where('kurssID', $kurssID)
                                ->where('datumsID', '!=', $currentDatumsID)
                                ->distinct('datumsID')
                                ->pluck('datumsID')
                                ->toArray();
                            $availableWeeks = [];
                            foreach ($allWeeks as $weekId => $weekLabel) {
                                if (!in_array($weekId, $weeksWithData)) {
                                    $availableWeeks[$weekId] = $weekLabel;
                                }
                            }

                            return $availableWeeks;
                        })
                        ->helperText('Tiek rādītas tikai tās nedēļas, kurām vēl nav pievienoti dati šim kursam.'),
                ])
                ->action(function (array $data): void {
                    $this->copyWeekSchedule($data['targetDatumsID']);
                }),

            Actions\DeleteAction::make()
                ->action(function () {
                    $kurssID = $this->record->kurssID;
                    $datumsID = $this->record->datumsID;

                    // Get all lessons before deleting them to restore stundu amounts
                    $lessonsToDelete = IeplanotStundu::where('kurssID', $kurssID)
                        ->where('datumsID', $datumsID)
                        ->get();
                    
                    // Increment back stundaAmount for each deleted lesson
                    foreach ($lessonsToDelete as $lesson) {
                        $stundaAmount = StundaAmount::where('kurssID', $lesson->kurssID)
                            ->where('stundaID', $lesson->stundaID)
                            ->where('pasniedzejsID', $lesson->pasniedzejsID)
                            ->first();
                        
                        if ($stundaAmount) {
                            $stundaAmount->daudzums += 1;
                            $stundaAmount->save();
                        }
                    }

                    // Now delete the lessons
                    IeplanotStundu::where('kurssID', $kurssID)
                        ->where('datumsID', $datumsID)
                        ->delete();

                    Notification::make()
                        ->title('Stundas izdzēstas')
                        ->body('Visas šīs nedēļas stundas ir veiksmīgi izdzēstas.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }

    protected function copyWeekSchedule($targetDatumsID): void
    {
        $sourceRecord = $this->record;
        $kurssID = $sourceRecord->kurssID;
        $sourceDatumsID = $sourceRecord->datumsID;

        $existingTargetLessons = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $targetDatumsID)
            ->count();

        if ($existingTargetLessons > 0) {
            $targetWeek = Datums::find($targetDatumsID);

            Notification::make()
                ->title('Nevar kopēt nedēļu')
                ->body("Nedēļai {$targetWeek->PirmaisDatums} - {$targetWeek->PedejaisDatums} jau eksistē dati šim kursam.")
                ->danger()
                ->send();

            return;
        }

        $sourceWeekLessons = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $sourceDatumsID)
            ->get();

        $insufficientAmounts = [];
        
        foreach ($sourceWeekLessons as $lesson) {
            $stundaAmount = StundaAmount::where('kurssID', $lesson->kurssID)
                ->where('stundaID', $lesson->stundaID)
                ->where('pasniedzejsID', $lesson->pasniedzejsID)
                ->first();
            
            if (!$stundaAmount || $stundaAmount->daudzums <= 0) {
                $insufficientAmounts[] = $lesson->stunda ? $lesson->stunda->Nosaukums : 'Unknown';
            }
        }
        
        if (!empty($insufficientAmounts)) {
            Notification::make()
                ->title('Nepietiekams stundu skaits')
                ->body("Nav pietiekams stundu skaits šādiem priekšmetiem: " . implode(', ', array_unique($insufficientAmounts)) . ", bet tika ievietots pieejamais stundu skaits.")
                ->danger()
                ->send();
            return;
        }

        foreach ($sourceWeekLessons as $lesson) {
            $newLesson = IeplanotStundu::create([
                'kurssID' => $kurssID,
                'datumsID' => $targetDatumsID,
                'skaitlis' => $lesson->skaitlis,
                'laiksID' => $lesson->laiksID,
                'stundaID' => $lesson->stundaID,
                'pasniedzejsID' => $lesson->pasniedzejsID,
                'kabinetaID' => $lesson->kabinetaID,
            ]);
            $stundaAmount = StundaAmount::where('kurssID', $lesson->kurssID)
                ->where('stundaID', $lesson->stundaID)
                ->where('pasniedzejsID', $lesson->pasniedzejsID)
                ->first();
            
            if ($stundaAmount) {
                $stundaAmount->daudzums -= 1;
                $stundaAmount->save();
            }
        }

        $sourceWeek = Datums::find($sourceDatumsID);
        $targetWeek = Datums::find($targetDatumsID);

        Notification::make()
            ->title('Nedēļa veiksmīgi nokopēta')
            ->body("Stundas no nedēļas {$sourceWeek->PirmaisDatums} - {$sourceWeek->PedejaisDatums} ir nokopētas uz nedēļu {$targetWeek->PirmaisDatums} - {$targetWeek->PedejaisDatums}")
            ->success()
            ->send();

        $newRecord = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $targetDatumsID)
            ->first();

        if ($newRecord) {
            $this->redirect(static::getResource()::getUrl('edit', ['record' => $newRecord]));
        } else {
            $this->redirect(static::getResource()::getUrl('index'));
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $lessonData = $this->loadWeekLessons($record->kurssID, $record->datumsID);

        return array_merge($data, $lessonData);
    }

    protected function loadWeekLessons($kurssID, $datumsID): array
    {
        $weekLessons = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $datumsID)
            ->with(['stunda', 'pasniedzejs', 'kabinets', 'laiks'])
            ->get();

        $formData = [
            'datumsID' => $datumsID,
            'kurssID' => $kurssID,
            'day_1_lessons' => [],
            'day_2_lessons' => [],
            'day_3_lessons' => [],
            'day_4_lessons' => [],
            'day_5_lessons' => [],
        ];

        foreach ($weekLessons as $lesson) {
            $day = $lesson->skaitlis;

            if ($day >= 1 && $day <= 5) {
                $formData["day_{$day}_lessons"][] = [
                    'laiksID' => $lesson->laiksID,
                    'stundaID' => $lesson->stundaID,
                    'pasniedzejsID' => $lesson->pasniedzejsID,
                    'kabinetaID' => $lesson->kabinetaID,
                    'skaitlis' => $day,
                    'stunda' => [
                        'label' => $lesson->stunda ? $lesson->stunda->nosaukums : null,
                        'value' => $lesson->stundaID,
                    ],
                    'pasniedzejs' => [
                        'label' => $lesson->pasniedzejs ? $lesson->pasniedzejs->vards : null,
                        'value' => $lesson->pasniedzejsID,
                    ],
                    'kabinets' => [
                        'label' => $lesson->kabineta ? $lesson->kabineta->numurs : null,
                        'value' => $lesson->kabinetaID,
                    ],
                    'laiks' => [
                        'label' => $lesson->laiks ? $lesson->laiks->laiks : null, 
                        'value' => $lesson->laiksID,
                    ],
                ];
            }
        }

        return $formData;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
{
    $kurssID = $data['kurssID'];
    $datumsID = $data['datumsID'];

    $existingLessons = IeplanotStundu::where('kurssID', $kurssID)
        ->where('datumsID', $datumsID)
        ->get();

    $existingLessonsMap = $existingLessons->keyBy(function($item) {
        return $item->skaitlis . '_' . $item->laiksID . '_' . $item->stundaID . '_' . $item->pasniedzejsID . '_' . $item->kabinetaID;
    });

    $newLessons = [];
    $newLessonsMap = [];

    for ($day = 1; $day <= 5; $day++) {
        $dayKey = "day_{$day}_lessons";

        if (isset($data[$dayKey]) && is_array($data[$dayKey])) {
            foreach ($data[$dayKey] as $lesson) {
                if (isset($lesson['laiksID']) && isset($lesson['stundaID']) && 
                    isset($lesson['pasniedzejsID']) && isset($lesson['kabinetaID'])) {

                    $lessonData = [
                        'kurssID' => $kurssID,
                        'datumsID' => $datumsID,
                        'skaitlis' => $day,
                        'laiksID' => $lesson['laiksID'],
                        'stundaID' => $lesson['stundaID'],
                        'pasniedzejsID' => $lesson['pasniedzejsID'],
                        'kabinetaID' => $lesson['kabinetaID'],
                    ];

                    $newLessons[] = $lessonData;
                    $key = $day . '_' . $lesson['laiksID'] . '_' . $lesson['stundaID'] . '_' . $lesson['pasniedzejsID'] . '_' . $lesson['kabinetaID'];
                    $newLessonsMap[$key] = $lessonData;
                }
            }
        }
    }

    $conflicts = $this->checkForConflicts($newLessons);
    if (!empty($conflicts)) {
        Notification::make()
            ->title('Validācijas kļūda')
            ->body($conflicts[0])
            ->danger()
            ->send();

        $this->halt();
        return $record;
    }

    $originalDispatches = IeplanotStundu::getEventDispatcher();
    IeplanotStundu::unsetEventDispatcher();

    foreach ($existingLessonsMap as $key => $existingLesson) {
        if (!isset($newLessonsMap[$key])) {
            $stundaAmount = StundaAmount::where('kurssID', $existingLesson->kurssID)
                ->where('stundaID', $existingLesson->stundaID)
                ->where('pasniedzejsID', $existingLesson->pasniedzejsID)
                ->first();
            
            if ($stundaAmount) {
                $stundaAmount->daudzums += 1;
                $stundaAmount->save();
            }
            
            $existingLesson->delete();
        }
    }

    foreach ($newLessonsMap as $key => $newLesson) {
        if (!isset($existingLessonsMap[$key])) {
            $stundaAmount = StundaAmount::where('kurssID', $newLesson['kurssID'])
                ->where('stundaID', $newLesson['stundaID'])
                ->where('pasniedzejsID', $newLesson['pasniedzejsID'])
                ->first();
            
            if ($stundaAmount) {
                if ($stundaAmount->daudzums <= 0) {
                    IeplanotStundu::setEventDispatcher($originalDispatches);
                    
                    Notification::make()
                        ->title('Nepietiekams stundu skaits')
                        ->body('Šim mācību priekšmetam nav pietiekams stundu skaits.')
                        ->danger()
                        ->send();
                    
                    $this->halt();
                    return $record;
                }
                
                $stundaAmount->daudzums -= 1;
                $stundaAmount->save();
            }
            
            IeplanotStundu::create($newLesson);
        }
    }

    IeplanotStundu::setEventDispatcher($originalDispatches);

    return $record;
}

    protected function checkForConflicts(array $newLessons): array
    {
        $conflicts = [];
        
        $dayTimeGroups = [];
        foreach ($newLessons as $lesson) {
            $key = $lesson['skaitlis'] . '-' . $lesson['laiksID'];
            if (!isset($dayTimeGroups[$key])) {
                $dayTimeGroups[$key] = [];
            }
            $dayTimeGroups[$key][] = $lesson;
        }
        
        foreach ($dayTimeGroups as $key => $lessons) {
            if (count($lessons) > 1) {
                $parts = explode('-', $key);
                $day = $parts[0];
                $time = $parts[1];
                $conflicts[] = "Vairākas stundas ieplānotas vienā laikā dienā {$day}, laikā {$time}.";
            }
        }
        
        foreach ($newLessons as $lesson) {
            $teacherConflicts = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('pasniedzejsID', $lesson['pasniedzejsID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->first();
                
            if ($teacherConflicts) {
                $conflicts[] = "Pasniedzējs jau ir ieplānots citam kursam šajā laikā.";
            }
            
            $roomConflicts = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('kabinetaID', $lesson['kabinetaID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->first();
                
            if ($roomConflicts) {
                $conflicts[] = "Kabinets jau ir ieplānots citam kursam šajā laikā.";
            }
        }
        
        return $conflicts;
    }
}