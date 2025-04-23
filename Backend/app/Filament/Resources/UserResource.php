<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;


use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{

    public static function getModelLabel(): string{
        return 'administratora profils';
    }
    
public static function getPluralModelLabel(): string{
        return 'Administratori';
    }

    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Administrācija';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Administratori';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Vārds')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Ēpasts')
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', fn ($record) => $record)
                    ->rules([
                        'unique:users,email,' . ($record->id ?? 'NULL'),
                    ])
                    ->afterStateUpdated(fn ($state, $set) => $set('email', $state))
                    ->validationMessages([
                        'unique' => 'Šis ēpasts jau tiek izmantots. Lūdzu izvēlieties citu.',
                    ]),

                TextInput::make('password')
                    ->password()
                    ->label('Parole')
                    ->required()
                    ->minLength(8)
                    ->maxLength(64),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Vārds')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Ēpasts')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
