<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIeplanotStundu extends EditRecord
{
    protected static string $resource = IeplanotStunduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
