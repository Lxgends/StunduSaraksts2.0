<?php

namespace App\Filament\Resources\KabinetsResource\Pages;

use App\Filament\Resources\KabinetsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKabinets extends ListRecords
{
    protected static string $resource = KabinetsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
