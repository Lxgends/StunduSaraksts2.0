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
use Filament\Forms\Components\Select;

class KabinetsResource extends Resource
{

public static function getModelLabel(): string{
        return 'skolas kabineta ieraksts';
    }
    
public static function getPluralModelLabel(): string{
        return 'Skolu kabineti';
    }

    protected static ?string $model = Kabinets::class;

    protected static ?string $navigationGroup = 'Izglītības pārvaldība';

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Skolas Kabineti';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('vieta')
                    ->label('Skola kurā atrodas kabinets')
                    ->required()
                    ->options([
                        'Cēsis' => 'Cēsis',
                        'Priekuļi' => 'Priekuļi',
                    ]),

                Forms\Components\TextInput::make('skaitlis')
                    ->label('Kabineta numurs vai nosaukums')
                    ->required()
                    ->maxLength(20)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vieta')
                    ->label('Vieta')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Cēsis' => 'info',
                        'Priekuļi' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Cēsis' => 'Cēsis',
                        'Priekuļi' => 'Priekuļi',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('skaitlis')
                    ->label('Kabineta numurs vai nosaukums')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vieta')
                    ->label('Skolas atrašanās vieta')
                    ->options([
                        'Cēsis' => 'Cēsis',
                        'Priekuļi' => 'Priekuļi',
                    ])
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
