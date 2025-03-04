<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IeplanotStunduResource\Pages;
use App\Filament\Resources\IeplanotStunduResource\RelationManagers;
use App\Models\IeplanotStundu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class IeplanotStunduResource extends Resource
{
    public static function getModelLabel(): string{
        return 'Ieplānot Stundu';
    }

    public static function getPluralModelLabel(): string{
        return 'Ieplānot Pārstundas';
    }

    protected static ?string $model = IeplanotStundu::class;

    protected static ?string $navigationGroup = 'Nedēļas Grafiks';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Ieplānot pārstundas';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Select::make('skaitlis')
                ->label('Diena kurai paredzēta pārstunda')
                ->required()
                ->options([
                    '1' => 'Primdiena',
                    '2' => 'Otrdiena',
                    '3' => 'Trešdiena',
                    '4' => 'Ceturtdiena',
                    '5' => 'Piektdiena',
                ]),

            Select::make('kurssID')
                ->label('Kursa nosaukums')
                ->required()
                ->searchable()
                ->options(function () {
                    return \App\Models\Kurss::pluck('Nosaukums', 'id')->toArray();
                })
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('stundaID', null);
                    $set('pasniedzejsID', null);
                }),

            Select::make('laiksID')
                ->label('Pārstundas laiks')
                ->required()
                ->searchable()
                ->options(function () {
                    return \App\Models\Laiks::all()->mapWithKeys(function ($item) {
                        return [$item->id => $item->DienasTips . ' ' . $item->sakumalaiks . ' - ' . $item->beigulaiks];
                    })->toArray();
                }),

            Select::make('datumsID')
                ->label('Nedēļas datums')
                ->required()
                ->searchable()
                ->options(function () {
                    return \App\Models\Datums::all()->mapWithKeys(function ($item) {
                        return [$item->id => $item->PirmaisDatums . ' - ' . $item->PedejaisDatums];
                    })->toArray();
                }),

            Select::make('stundaID')
                ->label('Stundas nosaukums')
                ->required()
                ->searchable()
                ->options(function (callable $get) {
                    $kurssID = $get('kurssID');
                    
                    if ($kurssID) {
                        $stundaIDs = \App\Models\StundaAmount::where('kurssID', $kurssID)
                            ->where('daudzums', '>', 0)
                            ->distinct('stundaID')
                            ->pluck('stundaID')
                            ->toArray();
                            
                        return \App\Models\Stunda::whereIn('id', $stundaIDs)
                            ->pluck('Nosaukums', 'id')
                            ->toArray();
                    }
                    
                    return \App\Models\Stunda::pluck('Nosaukums', 'id')->toArray();
                })
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('pasniedzejsID', null);
                }),

            Select::make('pasniedzejsID')
                ->label('Stundas Pasniedzējs')
                ->required()
                ->searchable()
                ->options(function (callable $get) {
                    $kurssID = $get('kurssID');
                    $stundaID = $get('stundaID');
                    
                    if ($kurssID && $stundaID) {
                        $pasniedzejsIDs = \App\Models\StundaAmount::where('kurssID', $kurssID)
                            ->where('stundaID', $stundaID)
                            ->where('daudzums', '>', 0)
                            ->pluck('pasniedzejsID')
                            ->toArray();
                            
                        return \App\Models\Pasniedzejs::whereIn('id', $pasniedzejsIDs)
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->Vards . ' ' . $item->Uzvards];
                            })
                            ->toArray();
                    } elseif ($kurssID) {
                        $pasniedzejsIDs = \App\Models\StundaAmount::where('kurssID', $kurssID)
                            ->where('daudzums', '>', 0)
                            ->distinct('pasniedzejsID')
                            ->pluck('pasniedzejsID')
                            ->toArray();
                            
                        return \App\Models\Pasniedzejs::whereIn('id', $pasniedzejsIDs)
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->Vards . ' ' . $item->Uzvards];
                            })
                            ->toArray();
                    }
                    
                    return \App\Models\Pasniedzejs::all()->mapWithKeys(function ($item) {
                        return [$item->id => $item->Vards . ' ' . $item->Uzvards];
                    })->toArray();
                }),

            Select::make('kabinetaID')
                ->label('Kabinets kurā notiek stunda')
                ->required()
                ->searchable()
                ->options(function () {
                    return \App\Models\Kabinets::all()->mapWithKeys(function ($item) {
                        return [$item->id => $item->vieta . ' ' . $item->skaitlis];
                    })->toArray();
                }),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('skaitlis')
                    ->label('Nedēļas diena')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => [
                        '1' => 'Primdiena',
                        '2' => 'Otrdiena',
                        '3' => 'Trešdiena',
                        '4' => 'Ceturtdiena',
                        '5' => 'Piektdiena',
                        '6' => 'Sestdiena',
                        '7' => 'Svētdiena',
                    ][$state] ?? 'Unknown'),

                TextColumn::make('kurssID')
                    ->label('Kursa nosaukums')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->kurssID) {
                            return $record->Kurss()->pluck('Nosaukums')->first();
                        }
                    }),

                TextColumn::make('datumsID')
                    ->label('Nedēļas sākuma datums')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->datumsID) {
                            return $record->Datums()->pluck('PirmaisDatums')->first();
                        }
                    }),

                TextColumn::make('laiksID')
                    ->label('Pārstundas sākuma laiks')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->laiksID) {
                            return $record->laiks()->pluck('sakumalaiks', 'beigulaiks')->first();
                        }
                    }),

                TextColumn::make('stundaID')
                    ->label('Stundas nosaukums')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->stundaID) {
                            return $record->stunda()->pluck('Nosaukums')->first();
                        }
                    }),

                TextColumn::make('pasniedzejsID')
                    ->label('Stundas Pasniedzējs')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->pasniedzejsID) {
                            return $record->pasniedzejs()->pluck('Vards', 'Uzvards')->first();
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIeplanotStundus::route('/'),
            'create' => Pages\CreateIeplanotStundu::route('/create'),
            'edit' => Pages\EditIeplanotStundu::route('/{record}/edit'),
        ];
    }
}
