<?php

namespace App\Filament\Resources\DatumsResource\Pages;

use App\Filament\Resources\DatumsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDatums extends EditRecord
{
    protected static string $resource = DatumsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
