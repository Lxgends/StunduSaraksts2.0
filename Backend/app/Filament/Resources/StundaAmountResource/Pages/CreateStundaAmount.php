<?php

namespace App\Filament\Resources\StundaAmountResource\Pages;

use App\Filament\Resources\StundaAmountResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\StundaAmount;
use Filament\Notifications\Notification;

class CreateStundaAmount extends CreateRecord
{
    protected static string $resource = StundaAmountResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        
        $exists = StundaAmount::where('stundaID', $data['stundaID'])
            ->where('kurssID', $data['kurssID'])
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Validācijas kļūda')
                ->body('Šis priekšmets ir jau piešķirts kādam pasniedzējam priekš šī kursa!')
                ->danger()
                ->send();
                
            $this->halt();
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}