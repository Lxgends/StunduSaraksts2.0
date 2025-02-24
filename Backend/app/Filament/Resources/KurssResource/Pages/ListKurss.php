<?php

namespace App\Filament\Resources\KurssResource\Pages;

use App\Filament\Resources\KurssResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKurss extends ListRecords
{
    protected static string $resource = KurssResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
