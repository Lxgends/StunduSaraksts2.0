<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PasniedzejsResource\Pages;
use App\Filament\Resources\PasniedzejsResource\RelationManagers;
use App\Models\Pasniedzejs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class PasniedzejsResource extends Resource
{
    protected static ?string $model = Pasniedzejs::class;

    protected static ?string $navigationGroup = 'Personāla';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pasniedzēji';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Vards')
                    ->label('Pasniedzēja vārds')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('Uzvards')
                    ->label('Pasniedzēja uzvārds')
                    ->required()
                    ->maxLength(255),

                    Select::make('KabinetsID')
                        ->label('Kabinets')
                        ->options(function () {
                            return \App\Models\Kabinets::pluck('Skaitlis', 'id')->toArray();
                        })
                        ->placeholder('Nav Speciāls kabinets'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Vards')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('Uzvards')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('KabinetsID')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->KabinetsID) {
                            return $record->kabinets()->pluck('Skaitlis')->first() ?? 'Nav Speciāls kabinets';
                        }
                        return 'Nav Speciāls kabinets';
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
            'index' => Pages\ListPasniedzejs::route('/'),
            'create' => Pages\CreatePasniedzejs::route('/create'),
            'edit' => Pages\EditPasniedzejs::route('/{record}/edit'),
        ];
    }
}
