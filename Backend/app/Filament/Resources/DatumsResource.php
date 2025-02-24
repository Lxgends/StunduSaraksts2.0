<?php

namespace App\Filament\Resources;

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

class DatumsResource extends Resource
{
    protected static ?string $model = Datums::class;

    protected static ?string $navigationGroup = 'Stundas un Laiki';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Datumi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('PirmaisDatums')
                    ->label('Nedēļas sākuma datums')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('PedejaisDatums')
                    ->label('Nedēļas beigu datums')
                    ->required()
                    ->maxLength(255)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PirmaisDatums')
                    ->label('Nedēļas sākuma datums')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('PedejaisDatums')
                    ->label('Nedēļas beigu datums')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListDatums::route('/'),
            'create' => Pages\CreateDatums::route('/create'),
            'edit' => Pages\EditDatums::route('/{record}/edit'),
        ];
    }
}
