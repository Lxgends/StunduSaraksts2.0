<?php

namespace App\Filament\Resources\StundaResource\Pages;

use App\Filament\Resources\StundaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStundas extends ListRecords
{
    protected static string $resource = StundaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
