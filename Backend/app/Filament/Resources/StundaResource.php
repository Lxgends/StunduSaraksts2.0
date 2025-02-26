<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StundaResource\Pages;
use App\Filament\Resources\StundaResource\RelationManagers;
use App\Models\Stunda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class StundaResource extends Resource
{

    public static function getModelLabel(): string{
        return 'Pievienot mācību priekšmetu';	
    }
    
    public static function getPluralModelLabel(): string{
        return 'Pievienot mācību priekšmetus';
    }

    protected static ?string $model = Stunda::class;

    protected static ?string $navigationGroup = 'Stundas un Laiki';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Macību priekšmets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Nosaukums')
                    ->label('Stundas nosaukums')
                    ->required()
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Nosaukums')
                    ->label('Stundas nosaukums')
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
            'index' => Pages\ListStundas::route('/'),
            'create' => Pages\CreateStunda::route('/create'),
            'edit' => Pages\EditStunda::route('/{record}/edit'),
        ];
    }
}
