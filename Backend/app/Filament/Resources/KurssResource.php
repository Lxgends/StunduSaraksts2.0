<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KurssResource\Pages;
use App\Filament\Resources\KurssResource\RelationManagers;
use App\Models\Kurss;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KurssResource extends Resource
{
    public static function getModelLabel(): string{
        return 'Pievienot Kursu';
    }
    
    public static function getPluralModelLabel(): string{
        return 'Pievienot Kursus';
    }

    protected static ?string $model = Kurss::class;

    protected static ?string $navigationGroup = 'Izglītības pārvaldība';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Skolēnu kursi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Nosaukums')
                    ->label('Kursa nosaukums')
                    ->required()
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Nosaukums')
                    ->label('Kursa nosaukums')
                    ->sortable()
                    ->searchable()
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKurss::route('/'),
            'create' => Pages\CreateKurss::route('/create'),
            'edit' => Pages\EditKurss::route('/{record}/edit'),
        ];
    }
}

