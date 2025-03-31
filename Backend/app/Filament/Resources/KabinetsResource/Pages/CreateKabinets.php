<?php
namespace App\Filament\Resources\KabinetsResource\Pages;

use App\Filament\Resources\KabinetsResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Kabinets;
use Filament\Notifications\Notification;

class CreateKabinets extends CreateRecord
{
    protected static string $resource = KabinetsResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $skaitlis = trim($data['skaitlis']);

        $exists = Kabinets::where('vieta', $data['vieta'])
            ->where('skaitlis', $skaitlis)
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Kabinetu dublikāts')
                ->body('Šāds kabinets jau eksistē norādītajā skolā!')
                ->danger()
                ->send();
                
            $this->halt();
            return new Kabinets();
        }

        return Kabinets::create([
            'vieta' => $data['vieta'],
            'skaitlis' => $skaitlis
        ]);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}