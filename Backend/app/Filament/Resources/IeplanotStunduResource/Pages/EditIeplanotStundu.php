<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIeplanotStundu extends EditRecord
{
    protected static string $resource = IeplanotStunduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
<<<<<<< Updated upstream
}
=======
    
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
            ->with(['stunda', 'pasniedzejs', 'laiks'])
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
                    'laiksSakums' => $lesson->laiks ? $lesson->laiks->sakumalaiks : null,
                    'laiksBeigas' => $lesson->laiks ? $lesson->laiks->beigulaiks : null,
                ];
            }
        }
    
        return $formData;
    }
    
    
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $kurssID = $data['kurssID'];
        $datumsID = $data['datumsID'];
    
        IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $datumsID)
            ->delete();
    
        for ($day = 1; $day <= 5; $day++) {
            $dayKey = "day_{$day}_lessons";
            
            if (isset($data[$dayKey]) && is_array($data[$dayKey])) {
                foreach ($data[$dayKey] as $lesson) {
                    if (isset($lesson['laiksID']) && isset($lesson['stundaID']) && 
                        isset($lesson['pasniedzejsID']) && isset($lesson['kabinetaID'])) {
                        
                        IeplanotStundu::create([
                            'kurssID' => $kurssID,
                            'datumsID' => $datumsID,
                            'skaitlis' => $day,
                            'laiksID' => $lesson['laiksID'],
                            'stundaID' => $lesson['stundaID'],
                            'pasniedzejsID' => $lesson['pasniedzejsID'],
                            'kabinetaID' => $lesson['kabinetaID'],
                        ]);
                    }
                }
            }
        }
    
        return $record;
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
>>>>>>> Stashed changes
