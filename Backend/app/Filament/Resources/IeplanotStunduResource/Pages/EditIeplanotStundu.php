<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\IeplanotStundu;
use App\Models\Datums;
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
                
            Actions\DeleteAction::make(),
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

        foreach ($sourceWeekLessons as $lesson) {
            IeplanotStundu::create([
                'kurssID' => $kurssID,
                'datumsID' => $targetDatumsID,
                'skaitlis' => $lesson->skaitlis,
                'laiksID' => $lesson->laiksID,
                'stundaID' => $lesson->stundaID,
                'pasniedzejsID' => $lesson->pasniedzejsID,
                'kabinetaID' => $lesson->kabinetaID,
            ]);
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
            ->with(['stunda', 'pasniedzejs'])
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
                    'stundaNosaukums' => $lesson->stunda ? $lesson->stunda->nosaukums : null,
                    'pasniedzejsID' => $lesson->pasniedzejsID,
                    'pasniedzejsVards' => $lesson->pasniedzejs ? $lesson->pasniedzejs->vards : null,
                    'kabinetaID' => $lesson->kabinetaID,
                    'skaitlis' => $day,
                ];
            }
        }
    
        return $formData;
    }
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $kurssID = $data['kurssID'];
        $datumsID = $data['datumsID'];

        $existingRecords = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $datumsID)
            ->get();

        $newLessons = [];
        
        for ($day = 1; $day <= 5; $day++) {
            $dayKey = "day_{$day}_lessons";
            
            if (isset($data[$dayKey]) && is_array($data[$dayKey])) {
                foreach ($data[$dayKey] as $lesson) {
                    if (isset($lesson['laiksID']) && isset($lesson['stundaID']) && 
                        isset($lesson['pasniedzejsID']) && isset($lesson['kabinetaID'])) {
                        
                        $newLessons[] = [
                            'kurssID' => $kurssID,
                            'datumsID' => $datumsID,
                            'skaitlis' => $day,
                            'laiksID' => $lesson['laiksID'],
                            'stundaID' => $lesson['stundaID'],
                            'pasniedzejsID' => $lesson['pasniedzejsID'],
                            'kabinetaID' => $lesson['kabinetaID'],
                        ];
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

        IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $datumsID)
            ->delete();
            
        foreach ($newLessons as $lesson) {
            IeplanotStundu::create($lesson);
        }

        return $record;
    }
    
    protected function checkForConflicts(array $lessons): array
    {
        $conflicts = [];
        $timeSlots = [];
        $teacherSlots = [];
        $roomSlots = [];
        
        foreach ($lessons as $index => $lesson) {
            $timeKey = "{$lesson['datumsID']}_{$lesson['skaitlis']}_{$lesson['laiksID']}";
            $teacherKey = "{$lesson['datumsID']}_{$lesson['skaitlis']}_{$lesson['laiksID']}_{$lesson['pasniedzejsID']}";
            $roomKey = "{$lesson['datumsID']}_{$lesson['skaitlis']}_{$lesson['laiksID']}_{$lesson['kabinetaID']}";
            
            if (isset($timeSlots[$timeKey])) {
                $conflicts[] = "Šajā laikā jau eksistē stunda šim kursam un nedēļai (diena: {$lesson['skaitlis']})";
            }
            
            if (isset($teacherSlots[$teacherKey])) {
                $conflicts[] = "Izvēlētais pasniedzējs šajā laikā jau ir aizņemts (diena: {$lesson['skaitlis']})";
            }
            
            if (isset($roomSlots[$roomKey])) {
                $conflicts[] = "Šajā laikā izvēlētais kabinets jau ir aizņemts (diena: {$lesson['skaitlis']})";
            }
            
            $duplicateRoomInDb = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('kabinetaID', $lesson['kabinetaID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->exists();
                
            if ($duplicateRoomInDb) {
                $conflicts[] = "Šajā laikā izvēlētais kabinets jau ir aizņemts cita kursa stundai (diena: {$lesson['skaitlis']})";
            }
            
            $teacherBusyInDb = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('pasniedzejsID', $lesson['pasniedzejsID'])
                ->where('kurssID', '!=', $lesson['kurssID'])
                ->exists();
                
            if ($teacherBusyInDb) {
                $conflicts[] = "Izvēlētais pasniedzējs šajā laikā jau ir aizņemts cita kursa stundai (diena: {$lesson['skaitlis']})";
            }

            $timeSlots[$timeKey] = true;
            $teacherSlots[$teacherKey] = true;
            $roomSlots[$roomKey] = true;
        }
        
        return $conflicts;
    }
    
    public function afterSave(): void
    {
        if (session()->has('editing_timetable')) {
            $timetableData = session()->get('editing_timetable');
            session()->forget('editing_timetable');

            $this->redirect(static::getResource()::getUrl('view-timetable', [
                'selectedKurssId' => $timetableData['kurssID'],
                'selectedDatumsId' => $timetableData['datumsID'],
            ]));
        } else {
            parent::afterSave();
        }
    }
}