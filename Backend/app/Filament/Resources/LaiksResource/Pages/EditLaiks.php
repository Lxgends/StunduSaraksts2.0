<?php
namespace App\Filament\Resources\LaiksResource\Pages;

use App\Filament\Resources\LaiksResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\Laiks;
use Filament\Actions;

class EditLaiks extends EditRecord
{
    protected static string $resource = LaiksResource::class;

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        $duplicateExists = Laiks::where([
            'DienasTips' => $data['DienasTips'],
            'sakumalaiks' => $data['sakumalaiks'],
            'beigulaiks' => $data['beigulaiks']
        ])->where('id', '!=', $this->record->id)->exists();

        if ($duplicateExists) {
            Notification::make()
                ->title('Dublikāts')
                ->body('Identisks laika logs jau eksistē!')
                ->danger()
                ->send();
                
            $this->halt();
        }
    }

}