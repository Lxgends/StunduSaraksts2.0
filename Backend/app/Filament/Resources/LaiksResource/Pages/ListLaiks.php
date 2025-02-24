<?php

namespace App\Filament\Resources\LaiksResource\Pages;

use App\Filament\Resources\LaiksResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaiks extends ListRecords
{
    protected static string $resource = LaiksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
