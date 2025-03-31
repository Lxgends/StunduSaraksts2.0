<?php
namespace App\Filament\Resources\LaiksResource\Pages;

use App\Filament\Resources\LaiksResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Laiks;
use Filament\Notifications\Notification;

class CreateLaiks extends CreateRecord
{
    protected static string $resource = LaiksResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $start = date('H:i:s', strtotime($data['sakumalaiks']));
        $end = date('H:i:s', strtotime($data['beigulaiks']));

        if (Laiks::where('DienasTips', $data['DienasTips'])
            ->where('sakumalaiks', $start)
            ->where('beigulaiks', $end)
            ->exists()) {
            
            Notification::make()
                ->title('Laika logs jau eksistē')
                ->body('Identisks laika logs jau ir reģistrēts sistēmā.')
                ->danger()
                ->send();
                
            $this->halt();
            return new Laiks();
        }

        return Laiks::create([
            'DienasTips' => $data['DienasTips'],
            'sakumalaiks' => $start,
            'beigulaiks' => $end
        ]);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}