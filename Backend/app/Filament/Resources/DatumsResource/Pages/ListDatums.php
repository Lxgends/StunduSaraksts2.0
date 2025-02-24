<?php

namespace App\Filament\Resources\DatumsResource\Pages;

use App\Filament\Resources\DatumsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDatums extends ListRecords
{
    protected static string $resource = DatumsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
