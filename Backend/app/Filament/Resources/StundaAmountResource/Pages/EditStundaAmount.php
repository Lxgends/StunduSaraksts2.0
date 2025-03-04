<?php

namespace App\Filament\Resources\StundaAmountResource\Pages;

use App\Filament\Resources\StundaAmountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStundaAmount extends EditRecord
{
    protected static string $resource = StundaAmountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
