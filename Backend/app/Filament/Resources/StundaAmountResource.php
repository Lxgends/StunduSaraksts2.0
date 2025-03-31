<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StundaAmountResource\Pages;
use App\Models\StundaAmount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;

class StundaAmountResource extends Resource
{
    protected static ?string $model = StundaAmount::class;

    protected static ?string $navigationGroup = 'Izglītības pārvaldība';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Mācību priekšmeta daudzums';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('daudzums')
                    ->label('Daudzums')
                    ->numeric()
                    ->required(),

                Select::make('stundaID')
                    ->label('Stunda kuru pasniedz')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Stunda::pluck('Nosaukums', 'id')->toArray();
                    }),

                Select::make('pasniedzejsID')
                    ->label('Pasniedzējs, kurš pasniedz šo stundu')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Pasniedzejs::all()->mapWithKeys(function ($item) {
                            return [$item->id => $item->Vards . ' ' . $item->Uzvards];
                        })->toArray();
                    }),

                Select::make('kurssID')
                    ->label('Kurss, kuram pasniedz šo stundu')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Kurss::pluck('Nosaukums', 'id')->toArray();
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('daudzums')
                    ->label('Daudzums')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('stundaID')
                    ->label('Stunda kuru pasniedz')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->stundaID) {
                            return $record->stunda()->pluck('Nosaukums')->first();
                        }
                    }),

                TextColumn::make('pasniedzejsID')
                    ->label('Pasniedzējs, kurš pasniedz šo stundu')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->pasniedzejsID) {
                            return $record->pasniedzejs()->pluck('Vards', 'Uzvards')->first();
                        }
                    }),

                TextColumn::make('kurssID')
                    ->label('Kurss, kuram pasniedz šo stundu')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->kurssID) {
                            return $record->kurss()->pluck('Nosaukums')->first();
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStundaAmounts::route('/'),
            'create' => Pages\CreateStundaAmount::route('/create'),
            'edit' => Pages\EditStundaAmount::route('/{record}/edit'),
        ];
    }
}
