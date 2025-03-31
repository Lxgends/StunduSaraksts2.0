<?php
namespace App\Filament\Resources\KabinetsResource\Pages;

use App\Filament\Resources\KabinetsResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Kabinets;
use Filament\Notifications\Notification;
use Filament\Actions;

class EditKabinets extends EditRecord
{
    protected static string $resource = KabinetsResource::class;

    protected function beforeSave(): void
    {
        $data = $this->form->getState();
        $skaitlis = trim($data['skaitlis']);

        if (Kabinets::where('vieta', $data['vieta'])
            ->where('skaitlis', $skaitlis)
            ->where('id', '!=', $this->record->id)
            ->exists()) {
            
            Notification::make()
                ->title('Kabinetu dublikāts')
                ->body('Šāds kabinets jau eksistē norādītajā skolā!')
                ->danger()
                ->send();
                
            $this->halt();
        }

        $this->form->fill(['skaitlis' => $skaitlis]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}