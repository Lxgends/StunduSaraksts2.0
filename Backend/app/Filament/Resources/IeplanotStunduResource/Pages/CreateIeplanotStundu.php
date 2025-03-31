<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\IeplanotStundu;
use Filament\Notifications\Notification;

class CreateIeplanotStundu extends CreateRecord
{
    protected static string $resource = IeplanotStunduResource::class;
    
    protected function handleRecordCreation(array $data): Model
    {
        $kurssID = $data['kurssID'];
        $datumsID = $data['datumsID'];

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
                            'skaitlis' => $lesson['skaitlis'],
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
            return new IeplanotStundu();
        }

        foreach ($newLessons as $lesson) {
            IeplanotStundu::create($lesson);
        }

        return !empty($newLessons) 
            ? IeplanotStundu::where('kurssID', $kurssID)->where('datumsID', $datumsID)->first() 
            : new IeplanotStundu();
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

            $duplicateTimeSlot = IeplanotStundu::where('kurssID', $lesson['kurssID'])
                ->where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->exists();
                
            if ($duplicateTimeSlot) {
                $conflicts[] = "Šajā laikā jau eksistē stunda šim kursam un nedēļai (diena: {$lesson['skaitlis']})";
            }
            
            $duplicateRoomInDb = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('kabinetaID', $lesson['kabinetaID'])
                ->exists();
                
            if ($duplicateRoomInDb) {
                $conflicts[] = "Šajā laikā izvēlētais kabinets jau ir aizņemts (diena: {$lesson['skaitlis']})";
            }
            
            $teacherBusyInDb = IeplanotStundu::where('datumsID', $lesson['datumsID'])
                ->where('skaitlis', $lesson['skaitlis'])
                ->where('laiksID', $lesson['laiksID'])
                ->where('pasniedzejsID', $lesson['pasniedzejsID'])
                ->exists();
                
            if ($teacherBusyInDb) {
                $conflicts[] = "Izvēlētais pasniedzējs šajā laikā jau ir aizņemts (diena: {$lesson['skaitlis']})";
            }

            $timeSlots[$timeKey] = true;
            $teacherSlots[$teacherKey] = true;
            $roomSlots[$roomKey] = true;
        }
        
        return $conflicts;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}