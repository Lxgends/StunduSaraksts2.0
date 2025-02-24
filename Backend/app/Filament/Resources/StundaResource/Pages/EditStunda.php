<?php

namespace App\Filament\Resources\StundaResource\Pages;

use App\Filament\Resources\StundaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStunda extends EditRecord
{
    protected static string $resource = StundaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
