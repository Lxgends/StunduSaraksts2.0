<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms\Get;
use App\Filament\Resources\DatumsResource\Pages;
use App\Filament\Resources\DatumsResource\RelationManagers;
use App\Models\Datums;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;

class DatumsResource extends Resource
{
    public static function getModelLabel(): string
    {
        return 'Ieplānot nedēļas datumu';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Ieplānot nedēļas datumus';
    }

    protected static ?string $model = Datums::class;

    protected static ?string $navigationGroup = 'Datumi un pārstundu laiki';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Nedēļas Datumi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('PirmaisDatums')
                    ->label('Nedēļas sākuma datums (pirmdiena)')
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $monday = Carbon::parse($state);
                            if ($monday->isMonday()) {
                                $friday = $monday->copy()->next(Carbon::FRIDAY);
                                $set('PedejaisDatums', $friday->format('Y-m-d'));
                            }
                        }
                    }),
    
                Forms\Components\DatePicker::make('PedejaisDatums')
                    ->label('Nedēļas beigu datums (piektdiena)')
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PirmaisDatums')
                    ->label('Nedēļas sākuma datums')
                    ->date('d.m.Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('PedejaisDatums')
                    ->label('Nedēļas beigu datums')
                    ->date('d.m.Y')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([

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
            'index' => Pages\ListDatums::route('/'),
            'create' => Pages\CreateDatums::route('/create'),
            'edit' => Pages\EditDatums::route('/{record}/edit'),
        ];
    }
}