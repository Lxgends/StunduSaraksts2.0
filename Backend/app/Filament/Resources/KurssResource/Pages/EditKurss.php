<?php

namespace App\Filament\Resources\KurssResource\Pages;

use App\Filament\Resources\KurssResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKurss extends EditRecord
{
    protected static string $resource = KurssResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
