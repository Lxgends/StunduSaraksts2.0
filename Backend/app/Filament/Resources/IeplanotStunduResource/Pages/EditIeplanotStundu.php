<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\IeplanotStundu;
use Illuminate\Database\Eloquent\Model;

class EditIeplanotStundu extends EditRecord
{
    protected static string $resource = IeplanotStunduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // This handles the default form data
        $record = $this->getRecord();
        
        // Check if we're coming from the timetable view
        $timetableData = session()->get('editing_timetable');
        
        if ($timetableData && 
            $timetableData['kurssID'] == $record->kurssID && 
            $timetableData['datumsID'] == $record->datumsID) {
                
            // We're editing from the timetable view, so get all lessons for this week and course
            $this->loadWeekLessons($record->kurssID, $record->datumsID);
        }
        
        return $data;
    }
    
    protected function loadWeekLessons($kurssID, $datumsID)
    {
        $weekLessons = IeplanotStundu::where('kurssID', $kurssID)
            ->where('datumsID', $datumsID)
            ->get();
            
        // Format data for each day
        $dayLessons = [
            'day_1_lessons' => [],
            'day_2_lessons' => [],
            'day_3_lessons' => [],
            'day_4_lessons' => [],
            'day_5_lessons' => [],
        ];
        
        foreach ($weekLessons as $lesson) {
            $day = $lesson->skaitlis;
            
            $dayLessons["day_{$day}_lessons"][] = [
                'laiksID' => $lesson->laiksID,
                'stundaID' => $lesson->stundaID,
                'pasniedzejsID' => $lesson->pasniedzejsID,
                'kabinetaID' => $lesson->kabinetaID,
                'skaitlis' => $day,
            ];
        }

        $this->form->fill(array_merge(
            [
                'datumsID' => $datumsID,
                'kurssID' => $kurssID,
            ],
            $dayLessons
        ));
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
        
        return $record;
    }
    
    public function afterSave(): void
    {
        if (session()->has('editing_timetable')) {
            $timetableData = session()->get('editing_timetable');

            session()->forget('editing_timetable');

            $this->redirect(route('filament.resources.ieplanot-stundu-resource.timetable', [
                'selectedKurssId' => $timetableData['kurssID'],
                'selectedDatumsId' => $timetableData['datumsID'],
            ]));
        } else {
            parent::afterSave();
        }
    }
}