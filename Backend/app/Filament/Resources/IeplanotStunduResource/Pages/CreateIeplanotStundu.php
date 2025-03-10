<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateIeplanotStundu extends CreateRecord
{
    protected static string $resource = IeplanotStunduResource::class;
    
    protected function handleRecordCreation(array $data): Model
    {
        $commonData = Arr::only($data, ['datumsID', 'kurssID']);
        $createdRecords = [];

        for ($day = 1; $day <= 5; $day++) {
            $dayKey = "day_{$day}_lessons";

            if (!isset($data[$dayKey]) || empty($data[$dayKey])) {
                continue;
            }

            foreach ($data[$dayKey] as $lesson) {
                $lessonData = array_merge($commonData, $lesson, [
                    'skaitlis' => $day,
                ]);
                
                $record = new (\App\Models\IeplanotStundu::class)();
                $record->fill($lessonData);
                $record->save();
                
                $createdRecords[] = $record;
            }
        }

        return $createdRecords[0] ?? new (\App\Models\IeplanotStundu::class)();
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}