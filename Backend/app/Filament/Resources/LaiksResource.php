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
    public static function getModelLabel(): string{
        return 'Pievienot pārstundas laiku';
    }
    
    public static function getPluralModelLabel(): string{
        return 'Pievienot pārstundas laikus';
    }
    protected static ?string $model = Laiks::class;

    protected static ?string $navigationGroup = 'Stundas un Laiki';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Pārstundu Laiki';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('DienasTips')
                    ->label('Dienas garuma tips')
                    ->required()
                    ->options([
                        'normal' => 'Ikdienas stundas',
                        'short' => 'Īsās stundas',
                    ]),

                TimePicker::make('sakumalaiks')
                    ->label('Pārstundu sākuma laiks')
                    ->format('H:i')
                    ->seconds(false)
                    ->required(),
                
                TimePicker::make('beigulaiks')
                    ->label('Pārstundu beigu laiks')
                    ->format('H:i')
                    ->seconds(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DienasTips')
                    ->label('Dienas garuma tips')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => [
                        'normal' => 'Ikdienas stundas',
                        'short' => 'Īsās stundas',
                    ][$state] ?? 'Unknown'),

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
