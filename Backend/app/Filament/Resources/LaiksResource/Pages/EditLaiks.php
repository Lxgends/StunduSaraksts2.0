<?php

namespace App\Filament\Resources\LaiksResource\Pages;

use App\Filament\Resources\LaiksResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaiks extends EditRecord
{
    protected static string $resource = LaiksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
