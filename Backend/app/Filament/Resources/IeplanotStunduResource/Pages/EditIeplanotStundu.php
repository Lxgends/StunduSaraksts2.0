<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\IeplanotStundu;
use App\Models\Datums;
use App\Models\StundaAmount;
use App\Models\Stunda;
use App\Models\Absences;
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

                    $lessonsToDelete = IeplanotStundu::where('kurssID', $kurssID)
                        ->where('datumsID', $datumsID)
                        ->get();
                    
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

    // Funkcija kopē nedēļas stundu sarakstu uz citu datumu (nedēļu)
    protected function copyWeekSchedule($targetDatumsID): void
    {
        // Iegūstam avota ierakstu un nepieciešamos ID
        $sourceRecord = $this->record;
        $kurssID = $sourceRecord->kurssID;
        $sourceDatumsID = $sourceRecord->datumsID;

        // Pārbauda, vai mērķa nedēļai jau ir stundu dati
        $existingTargetLessons = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $targetDatumsID)
            ->count();

        // Ja dati jau eksistē, attēlo kļūdas paziņojumu un pārtrauc izpildi
        if ($existingTargetLessons > 0) {
            $targetWeek = Datums::find($targetDatumsID);

            Notification::make()
                ->title('Nevar kopēt nedēļu')
                ->body("Nedēļai {$targetWeek->PirmaisDatums} - {$targetWeek->PedejaisDatums} jau eksistē dati šim kursam.")
                ->danger()
                ->send();

            return;
        }

        // Iegūst visas stundas no avota nedēļas
        $sourceWeekLessons = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $sourceDatumsID)
            ->get();

        // Sagatavo jauno stundu masīvu kopēšanai
        $newLessons = [];
        foreach ($sourceWeekLessons as $lesson) {
            $newLessons[] = [
                'kurssID' => $kurssID,
                'datumsID' => $targetDatumsID,
                'skaitlis' => $lesson->skaitlis,
                'laiksID' => $lesson->laiksID,
                'stundaID' => $lesson->stundaID,
                'pasniedzejsID' => $lesson->pasniedzejsID,
                'kabinetaID' => $lesson->kabinetaID,
            ];
        }

        // Saskaita, cik reizes katra stunda/pasniedzējs tiek kopēts
        $lessonCount = [];
        foreach ($newLessons as $lesson) {
            $key = $lesson['kurssID'] . '_' . $lesson['stundaID'] . '_' . $lesson['pasniedzejsID'];
            $lessonCount[$key] = ($lessonCount[$key] ?? 0) + 1;
        }

        // Pārbauda, vai ir pietiekams stundu daudzums (resurss)
        $insufficientAmounts = [];
        foreach ($lessonCount as $key => $countToInsert) {
            [$kurssID, $stundaID, $pasniedzejsID] = explode('_', $key);
            $stundaAmount = StundaAmount::where('kurssID', $kurssID)
                ->where('stundaID', $stundaID)
                ->where('pasniedzejsID', $pasniedzejsID)
                ->first();
            
            $availableAmount = $stundaAmount ? $stundaAmount->daudzums : 0;

            // Ja nepietiek stundu daudzuma, saglabā kļūdas info
            if ($countToInsert > $availableAmount) {
                $stundaName = Stunda::find($stundaID)?->Nosaukums ?? 'Unknown';
                $pasniedzejsName = \App\Models\Pasniedzejs::find($pasniedzejsID)?->vards ?? 'Unknown';
                $insufficientAmounts[] = "'{$stundaName}' (pasniedzējs: {$pasniedzejsName}) - pieejams: {$availableAmount}, nepieciešams: {$countToInsert}";
            }
        }

        // Ja kādai stundai nepietiek daudzuma, attēlo brīdinājumu un apstājas
        if (!empty($insufficientAmounts)) {
            Notification::make()
                ->title('Nepietiekams stundu skaits')
                ->body("Nav pietiekams stundu skaits šādiem priekšmetiem: " . implode('; ', array_unique($insufficientAmounts)))
                ->danger()
                ->send();
            return;
        }

        // Pārbauda, vai nav konfliktu jaunajās stundās
        $conflicts = $this->checkForAllConflicts($newLessons);
        if (!empty($conflicts)) {
            Notification::make()
                ->title('Validācijas kļūda')
                ->body($conflicts[0])
                ->danger()
                ->send();
            return;
        }

        // Ja viss kārtībā, izveido jaunos ierakstus un samazina pieejamo stundu daudzumu
        foreach ($sourceWeekLessons as $lesson) {
            $stundaAmount = StundaAmount::where('kurssID', $lesson->kurssID)
                ->where('stundaID', $lesson->stundaID)
                ->where('pasniedzejsID', $lesson->pasniedzejsID)
                ->first();
            
            if ($stundaAmount && $stundaAmount->daudzums > 0) {
                $newLesson = IeplanotStundu::create([
                    'kurssID' => $kurssID,
                    'datumsID' => $targetDatumsID,
                    'skaitlis' => $lesson->skaitlis,
                    'laiksID' => $lesson->laiksID,
                    'stundaID' => $lesson->stundaID,
                    'pasniedzejsID' => $lesson->pasniedzejsID,
                    'kabinetaID' => $lesson->kabinetaID,
                ]);

                $stundaAmount->daudzums -= 1;
                $stundaAmount->save();
            }
        }

        // Sagatavo un parāda paziņojumu par veiksmīgu kopēšanu
        $sourceWeek = Datums::find($sourceDatumsID);
        $targetWeek = Datums::find($targetDatumsID);

        Notification::make()
            ->title('Nedēļa veiksmīgi nokopēta')
            ->body("Stundas no nedēļas {$sourceWeek->PirmaisDatums} - {$sourceWeek->PedejaisDatums} ir nokopētas uz nedēļu {$targetWeek->PirmaisDatums} - {$targetWeek->PedejaisDatums}")
            ->success()
            ->send();

        // Pāradresē lietotāju uz rediģēšanas vai saraksta lapu
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
        $oldDatumsID = $record->datumsID;
        
        $isDateChangeOnly = ($oldDatumsID != $datumsID && $record->kurssID == $kurssID);
        
        if ($isDateChangeOnly) {
            $existingLessons = IeplanotStundu::where('kurssID', $kurssID)
                ->where('datumsID', $oldDatumsID)
                ->get();
                
            $newDateLessons = [];
            foreach ($existingLessons as $lesson) {
                $newDateLessons[] = [
                    'kurssID' => $lesson->kurssID,
                    'datumsID' => $datumsID,
                    'skaitlis' => $lesson->skaitlis,
                    'laiksID' => $lesson->laiksID,
                    'stundaID' => $lesson->stundaID,
                    'pasniedzejsID' => $lesson->pasniedzejsID,
                    'kabinetaID' => $lesson->kabinetaID,
                ];
            }
            
            $conflicts = $this->checkForAllConflicts($newDateLessons);
            if (!empty($conflicts)) {
                Notification::make()
                    ->title('Validācijas kļūda')
                    ->body($conflicts[0])
                    ->danger()
                    ->send();
                
                $this->halt();
                return $record;
            }
            
            IeplanotStundu::where('kurssID', $kurssID)
                ->where('datumsID', $oldDatumsID)
                ->update(['datumsID' => $datumsID]);
                
            $record->datumsID = $datumsID;
            $record->save();
            
            Notification::make()
                ->title('Nedēļa atjaunināta')
                ->body('Visas stundas pārvietotas uz jauno nedēļu')
                ->success()
                ->send();
                
            return $record;
        }
        
        $existingLessons = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $datumsID)
            ->get();
    
        $existingLessonsMap = $existingLessons->keyBy(function($item) {
            return $item->skaitlis . '_' . $item->laiksID . '_' . $item->stundaID . '_' . $item->pasniedzejsID . '_' . $item->kabinetaID;
        });
    
        $existingStundaCount = [];
        foreach ($existingLessons as $lesson) {
            $stundaKey = $lesson->stundaID . '_' . $lesson->pasniedzejsID;
            $existingStundaCount[$stundaKey] = isset($existingStundaCount[$stundaKey]) ? 
                $existingStundaCount[$stundaKey] + 1 : 1;
        }
    
        $newLessons = [];
        $newLessonsMap = [];
        $newStundaCount = [];
    
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
                        
                        $stundaKey = $lesson['stundaID'] . '_' . $lesson['pasniedzejsID'];
                        $newStundaCount[$stundaKey] = isset($newStundaCount[$stundaKey]) ? 
                            $newStundaCount[$stundaKey] + 1 : 1;
                    }
                }
            }
        }
    
        $stundaChanges = [];
        $allStundaKeys = array_unique(array_merge(array_keys($existingStundaCount), array_keys($newStundaCount)));
        
        foreach ($allStundaKeys as $stundaKey) {
            $oldCount = $existingStundaCount[$stundaKey] ?? 0;
            $newCount = $newStundaCount[$stundaKey] ?? 0;
            $stundaChanges[$stundaKey] = $newCount - $oldCount;
        }
    
        foreach ($stundaChanges as $stundaKey => $change) {
            if ($change > 0) {
                list($stundaID, $pasniedzejsID) = explode('_', $stundaKey);
                
                $stundaAmount = StundaAmount::where('kurssID', $kurssID)
                    ->where('stundaID', $stundaID)
                    ->where('pasniedzejsID', $pasniedzejsID)
                    ->first();
                
                if (!$stundaAmount || $stundaAmount->daudzums < $change) {
                    Notification::make()
                        ->title('Nepietiekams stundu skaits')
                        ->body('Šim mācību priekšmetam nav pietiekams stundu skaits.')
                        ->danger()
                        ->send();
                    
                    $this->halt();
                    return $record;
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
                $existingLesson->delete();
            }
        }
    
        foreach ($newLessonsMap as $key => $newLesson) {
            if (!isset($existingLessonsMap[$key])) {
                IeplanotStundu::create($newLesson);
            }
        }
    
        foreach ($stundaChanges as $stundaKey => $change) {
            if ($change != 0) {
                list($stundaID, $pasniedzejsID) = explode('_', $stundaKey);
                
                $stundaAmount = StundaAmount::where('kurssID', $kurssID)
                    ->where('stundaID', $stundaID)
                    ->where('pasniedzejsID', $pasniedzejsID)
                    ->first();
                
                if ($stundaAmount) {
                    $stundaAmount->daudzums -= $change;
                    $stundaAmount->save();
                }
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
                $conflicts[] = "Vairākas stundas ieplānotas vienā laikā dienā {$day}, laikā (LaiksID: {$time}).";
            }
        }
        
        foreach ($newLessons as $lesson) {
            $datums = Datums::find($lesson['datumsID']);
            if (!$datums) {
                $conflicts[] = "Nederīgs datuma ieraksts. LaiksID: {$lesson['laiksID']}";
                continue;
            }
    
            $start = $datums->PirmaisDatums;
            $lessonDate = $this->getDateForDayOfWeek($lesson['skaitlis'], $start);
            $formattedDate = ucfirst($lessonDate->locale('lv')->isoFormat('dddd, D.MM.YYYY'));
            $laiksInfo = "LaiksID: {$lesson['laiksID']}";
    
            $isPasniedzejsAbsent = Absences::where('absence_type', 'pasniedzejs')
                ->where('pasniedzejsID', $lesson['pasniedzejsID'])
                ->where(function ($q) use ($lessonDate) {
                    $q->whereDate('sakuma_datums', '<=', $lessonDate)
                      ->whereDate('beigu_datums', '>=', $lessonDate);
                })
                ->exists();
    
            if ($isPasniedzejsAbsent) {
                $conflicts[] = "Pasniedzējs ir prombūtnē šajā dienā ({$formattedDate}). {$laiksInfo}";
            }
    
            $isKurssAbsent = Absences::where('absence_type', 'kurss')
                ->where('kurssID', $lesson['kurssID'])
                ->where(function ($q) use ($lessonDate) {
                    $q->whereDate('sakuma_datums', '<=', $lessonDate)
                      ->whereDate('beigu_datums', '>=', $lessonDate);
                })
                ->exists();
    
            if ($isKurssAbsent) {
                $conflicts[] = "Kurss ir prombūtnē šajā dienā ({$formattedDate}). {$laiksInfo}";
            }
            
            $teacherConflicts = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('pasniedzejsID', $lesson['pasniedzejsID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->first();
                
            if ($teacherConflicts) {
                $conflicts[] = "Pasniedzējs jau ir ieplānots citam kursam šajā laikā. {$laiksInfo}";
            }
            
            $roomConflicts = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('kabinetaID', $lesson['kabinetaID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->first();
                
            if ($roomConflicts) {
                $conflicts[] = "Kabinets jau ir ieplānots citam kursam šajā laikā. {$laiksInfo}";
            }
            
            $existingCourseLesson = IeplanotStundu::where('kurssID', $lesson['kurssID'])
                ->where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where(function($query) use ($lesson) {
                    if (isset($lesson['id'])) {
                        $query->where('id', '!=', $lesson['id']);
                    }
                })
                ->exists();
                
            if ($existingCourseLesson) {
                $conflicts[] = "Šim kursam jau eksistē stunda šajā laikā (diena: {$formattedDate}). {$laiksInfo}";
            }
        }
        
        return $conflicts;
    }
    
    protected function checkForAllConflicts(array $lessons): array
    {
        $conflicts = [];
        $timeSlots = [];
        $teacherSlots = [];
        $roomSlots = [];
    
        foreach ($lessons as $lesson) {
            $datums = Datums::find($lesson['datumsID']);
            if (!$datums) {
                $conflicts[] = "Nederīgs datuma ieraksts. LaiksID: {$lesson['laiksID']}";
                continue;
            }
    
            $start = $datums->PirmaisDatums;
            $end = $datums->PedejaisDatums;
            $lessonDate = $this->getDateForDayOfWeek($lesson['skaitlis'], $start);
    
            $formattedDate = ucfirst($lessonDate->locale('lv')->isoFormat('dddd, D.MM.YYYY'));
            $laiksInfo = "LaiksID: {$lesson['laiksID']}";
    
            $isPasniedzejsAbsent = Absences::where('absence_type', 'pasniedzejs')
                ->where('pasniedzejsID', $lesson['pasniedzejsID'])
                ->where(function ($q) use ($lessonDate) {
                    $q->whereDate('sakuma_datums', '<=', $lessonDate)
                      ->whereDate('beigu_datums', '>=', $lessonDate);
                })
                ->exists();
    
            if ($isPasniedzejsAbsent) {
                $conflicts[] = "Pasniedzējs ir prombūtnē šajā dienā ({$formattedDate}). {$laiksInfo}";
            }
    
            $isKurssAbsent = Absences::where('absence_type', 'kurss')
                ->where('kurssID', $lesson['kurssID'])
                ->where(function ($q) use ($lessonDate) {
                    $q->whereDate('sakuma_datums', '<=', $lessonDate)
                      ->whereDate('beigu_datums', '>=', $lessonDate);
                })
                ->exists();
    
            if ($isKurssAbsent) {
                $conflicts[] = "Kurss ir prombūtnē šajā dienā ({$formattedDate}). {$laiksInfo}";
            }
    
            $timeKey = "{$lesson['datumsID']}_{$lesson['skaitlis']}_{$lesson['laiksID']}";
            if (isset($timeSlots[$timeKey])) {
                $conflicts[] = "Šajā laikā jau eksistē stunda šim kursam un nedēļai (diena: {$formattedDate}). {$laiksInfo}";
            }
    
            $teacherKey = "{$timeKey}_{$lesson['pasniedzejsID']}";
            if (isset($teacherSlots[$teacherKey])) {
                $conflicts[] = "Izvēlētais pasniedzējs šajā laikā jau ir aizņemts (diena: {$formattedDate}). {$laiksInfo}";
            }
    
            $roomKey = "{$timeKey}_{$lesson['kabinetaID']}";
            if (isset($roomSlots[$roomKey])) {
                $conflicts[] = "Šajā laikā izvēlētais kabinets jau ir aizņemts (diena: {$formattedDate}). {$laiksInfo}";
            }

            $existingCourseLesson = IeplanotStundu::where('kurssID', $lesson['kurssID'])
                ->where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->exists();
                
            if ($existingCourseLesson) {
                $conflicts[] = "Šim kursam jau eksistē stunda šajā laikā (diena: {$formattedDate}). {$laiksInfo}";
            }
    
            $existingTeacherConflict = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('pasniedzejsID', $lesson['pasniedzejsID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->exists();
                
            if ($existingTeacherConflict) {
                $conflicts[] = "Pasniedzējs jau ir ieplānots citam kursam šajā laikā (diena: {$formattedDate}). {$laiksInfo}";
            }
            
            $existingRoomConflict = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('kabinetaID', $lesson['kabinetaID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->exists();
                
            if ($existingRoomConflict) {
                $conflicts[] = "Kabinets jau ir ieplānots citam kursam šajā laikā (diena: {$formattedDate}). {$laiksInfo}";
            }
    
            $timeSlots[$timeKey] = true;
            $teacherSlots[$teacherKey] = true;
            $roomSlots[$roomKey] = true;
        }
    
        return $conflicts;
    }

    protected function getDateForDayOfWeek(int $dayNumber, string $startDate)
    {
        $date = \Carbon\Carbon::parse($startDate);
        return $date->addDays($dayNumber - 1);
    }
}