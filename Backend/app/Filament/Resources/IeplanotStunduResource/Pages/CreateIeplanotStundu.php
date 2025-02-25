<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIeplanotStundu extends CreateRecord
{
    protected static string $resource = IeplanotStunduResource::class;

    public function getTitle(): string
    {
        return 'Ieplānot Stundas';
    }
}
