<?php

namespace App\Filament\Resources\AbsencesResource\Pages;

use App\Filament\Resources\AbsencesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsences extends EditRecord
{
    protected static string $resource = AbsencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
