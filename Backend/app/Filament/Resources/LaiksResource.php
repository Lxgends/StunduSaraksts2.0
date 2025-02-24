<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaiksResource\Pages;
use App\Filament\Resources\LaiksResource\RelationManagers;
use App\Models\Laiks;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;

class LaiksResource extends Resource
{
    protected static ?string $model = Laiks::class;

    protected static ?string $navigationGroup = 'Stundas un Laiki';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Stundu Laiki';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TimePicker::make('sakumalaiks')
                    ->label('Stundu sākuma laiks')
                    ->format('H:i')
                    ->required(),


                TimePicker::make('beigulaiks')
                    ->label('Stundu beigu laiks')
                    ->format('H:i')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sakumalaiks')
                    ->label('Stundu sākuma laiks')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('H:i');
                }),
                TextColumn::make('beigulaiks')
                    ->label('Stundu beigu laiks')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('H:i');
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
            'index' => Pages\ListLaiks::route('/'),
            'create' => Pages\CreateLaiks::route('/create'),
            'edit' => Pages\EditLaiks::route('/{record}/edit'),
        ];
    }
}
