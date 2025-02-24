<?php

namespace App\Filament\Resources\KabinetsResource\Pages;

use App\Filament\Resources\KabinetsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKabinets extends EditRecord
{
    protected static string $resource = KabinetsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
