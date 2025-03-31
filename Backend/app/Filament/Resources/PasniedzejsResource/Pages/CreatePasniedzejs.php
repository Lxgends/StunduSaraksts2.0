<?php

namespace App\Filament\Resources\PasniedzejsResource\Pages;

use App\Filament\Resources\PasniedzejsResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Pasniedzejs;
use Filament\Notifications\Notification;

class CreatePasniedzejs extends CreateRecord
{
    protected static string $resource = PasniedzejsResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (Pasniedzejs::where('Vards', $data['Vards'])
            ->where('Uzvards', $data['Uzvards'])
            ->exists()) {
            
            Notification::make()
                ->title('Validācijas kļūda')
                ->body('Pasniedzējs ar šo vārdu un uzvārdu jau eksistē.')
                ->danger()
                ->send();
                
            $this->halt();
            
            return new Pasniedzejs();
        }

        return parent::handleRecordCreation($data);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}