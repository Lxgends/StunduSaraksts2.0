<?php

namespace App\Filament\Resources\StundaAmountResource\Pages;

use App\Filament\Resources\StundaAmountResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\StundaAmount;
use Filament\Notifications\Notification;
use Filament\Actions;

class EditStundaAmount extends EditRecord
{
    protected static string $resource = StundaAmountResource::class;

    protected function beforeSave(): void
    {
        $data = $this->form->getState();
        
        $exists = StundaAmount::where('stundaID', $data['stundaID'])
            ->where('kurssID', $data['kurssID'])
            ->where('id', '!=', $this->record->id)
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}