<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KabinetsResource\Pages;
use App\Filament\Resources\KabinetsResource\RelationManagers;
use App\Models\Kabinets;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KabinetsResource extends Resource
{
    protected static ?string $model = Kabinets::class;

    protected static ?string $navigationGroup = 'Personāla';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Skolas Kabineti';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('vieta')
                    ->label('Skolas atrašanās vieta')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('skaitlis')
                    ->label('Kabineta skaitlis')
                    ->required()
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vieta')
                    ->label('Skolas atrašanās vieta')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('skaitlis')
                    ->label('Kabineta skaitlis')
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
            'index' => Pages\ListKabinets::route('/'),
            'create' => Pages\CreateKabinets::route('/create'),
            'edit' => Pages\EditKabinets::route('/{record}/edit'),
        ];
    }
}
