<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;

class ListIeplanotStundus extends ListRecords
{
    protected static string $resource = IeplanotStunduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('viewTimetable')
                ->label('Skatīties pārstundu grafikus')
                ->url(static::getResource()::getUrl('view-timetable')),
        ];
    }
}
