<?php
namespace App\Filament\Resources\KurssResource\Pages;

use App\Filament\Resources\KurssResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Kurss;
use Filament\Notifications\Notification;

class CreateKurss extends CreateRecord
{
    protected static string $resource = KurssResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $nosaukums = trim($data['Nosaukums']);

        if (Kurss::whereRaw('LOWER(Nosaukums) = ?', [strtolower($nosaukums)])
            ->exists()) {
            
            Notification::make()
                ->title('Kursa dublikāts')
                ->body('Kurss ar šādu nosaukumu jau eksistē!')
                ->danger()
                ->send();
                
            $this->halt();
            return new Kurss();
        }

        return Kurss::create([
            'Nosaukums' => $nosaukums
        ]);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}