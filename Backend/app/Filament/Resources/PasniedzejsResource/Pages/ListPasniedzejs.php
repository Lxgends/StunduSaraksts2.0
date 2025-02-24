<?php

namespace App\Filament\Resources\PasniedzejsResource\Pages;

use App\Filament\Resources\PasniedzejsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPasniedzejs extends ListRecords
{
    protected static string $resource = PasniedzejsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
