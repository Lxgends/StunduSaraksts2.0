<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IeplanotStunduResource\Pages;
use App\Filament\Resources\IeplanotStunduResource\RelationManagers;
use App\Models\IeplanotStundu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;
use App\Filament\Resources\IeplanotStunduResource\Pages\ViewWeeklyTimetable;
use App\Filament\Resources\IeplanotStunduResource\Widgets\TimetableWidget;

class IeplanotStunduResource extends Resource
{
    public static function getModelLabel(): string
    {
        return 'Ieplānot Pārstundu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Ieplānot Pārstundas';
    }

    protected static ?string $model = IeplanotStundu::class;

    protected static ?string $navigationGroup = 'Nedēļas Grafiks';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Stundu Plānotājs';

    public static function form(Form $form): Form
    {
        $weekDays = [
            '1' => 'Pirmdiena',
            '2' => 'Otrdiena', 
            '3' => 'Trešdiena',
            '4' => 'Ceturtdiena',
            '5' => 'Piektdiena',
        ];

        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('datumsID')
                            ->label('Nedēļas datums')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return \App\Models\Datums::all()->mapWithKeys(function ($item) {
                                    return [$item->id => $item->PirmaisDatums . ' - ' . $item->PedejaisDatums];
                                })->toArray();
                            }),

                        Select::make('kurssID')
                            ->label('Kursa nosaukums')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return \App\Models\Kurss::pluck('Nosaukums', 'id')->toArray();
                            })
                    ]),

                Tabs::make('WeeklySchedule')
                    ->tabs([
                        Tab::make('Pirmdiena')
                            ->schema(self::getDayScheduleFields(1)),
                        Tab::make('Otrdiena')
                            ->schema(self::getDayScheduleFields(2)),
                        Tab::make('Trešdiena')
                            ->schema(self::getDayScheduleFields(3)),
                        Tab::make('Ceturtdiena')
                            ->schema(self::getDayScheduleFields(4)),
                        Tab::make('Piektdiena')
                            ->schema(self::getDayScheduleFields(5)),
                    ])
            ]);
    }

private static function getDayScheduleFields(int $dayNumber): array 
{
    return [
        Repeater::make("day_{$dayNumber}_lessons")
            ->label('Dienas Pārstundas')
            ->reactive()
            ->maxItems($dayNumber == 5 ? 5 : 5)
            ->schema([
                Forms\Components\Hidden::make('skaitlis')
                    ->default($dayNumber),
                
                    Select::make('laiksID')
                    ->label('Stundas laiks')
                    ->required()
                    ->searchable()
                    ->options(function (callable $get) use ($dayNumber) {
                        $kurssID = $get('kurssID'); // Get selected course
                        $datumsID = $get('datumsID'); // Get selected week/date
                        
                        // Fetch already selected laiksID values for the given kurssID and datumsID
                        $existingLessons = IeplanotStundu::where('kurssID', $kurssID)
                            ->where('datumsID', $datumsID)
                            ->where('skaitlis', $dayNumber)
                            ->pluck('laiksID')
                            ->toArray();
                        
                        // Fetch available laiks based on day type (normal or short) and exclude selected ones
                        return \App\Models\Laiks::where('DienasTips', $dayNumber == 5 ? 'short' : 'normal')
                            ->whereNotIn('id', $existingLessons) // Exclude already selected times
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->sakumalaiks . ' - ' . $item->beigulaiks];
                            })
                            ->toArray();
                    })
                    ->default(fn (callable $get) => $get('laiksLabel')),
               

                Select::make('stundaID')
                    ->label('Stundas nosaukums')
                    ->required()
                    ->searchable()
                    ->options(function (callable $get) {
                        $kurssID = $get('../../kurssID');

                        if ($kurssID) {
                            $stundaIDs = \App\Models\StundaAmount::where('kurssID', $kurssID)
                                ->where('daudzums', '>', 0)
                                ->distinct('stundaID')
                                ->pluck('stundaID')
                                ->toArray();
                                
                            return \App\Models\Stunda::whereIn('id', $stundaIDs)
                                ->pluck('Nosaukums', 'id')
                                ->toArray();
                        }

                        return \App\Models\Stunda::pluck('Nosaukums', 'id')->toArray();
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('pasniedzejsID', null);
                    }),

                Select::make('pasniedzejsID')
                    ->label('Stundas Pasniedzējs')
                    ->required()
                    ->searchable()
                    ->options(function (callable $get) {
                        $kurssID = $get('../../kurssID');
                        $stundaID = $get('stundaID');

                        if ($kurssID && $stundaID) {
                            $pasniedzejsIDs = \App\Models\StundaAmount::where('kurssID', $kurssID)
                                ->where('stundaID', $stundaID)
                                ->where('daudzums', '>', 0)
                                ->pluck('pasniedzejsID')
                                ->toArray();
                                
                            return \App\Models\Pasniedzejs::whereIn('id', $pasniedzejsIDs)
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    return [$item->id => $item->Vards . ' ' . $item->Uzvards];
                                })
                                ->toArray();
                        } elseif ($kurssID) {
                            $pasniedzejsIDs = \App\Models\StundaAmount::where('kurssID', $kurssID)
                                ->where('daudzums', '>', 0)
                                ->distinct('pasniedzejsID')
                                ->pluck('pasniedzejsID')
                                ->toArray();
                                
                            return \App\Models\Pasniedzejs::whereIn('id', $pasniedzejsIDs)
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    return [$item->id => $item->Vards . ' ' . $item->Uzvards];
                                })
                                ->toArray();
                        }

                        return \App\Models\Pasniedzejs::all()->mapWithKeys(function ($item) {
                            return [$item->id => $item->Vards . ' ' . $item->Uzvards];
                        })->toArray();
                    }),

                Select::make('kabinetaID')
                    ->label('Kabinets')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Kabinets::all()->mapWithKeys(function ($item) {
                            return [$item->id => $item->vieta . ' ' . $item->skaitlis];
                        })->toArray();
                    }),
            ])
            ->createItemButtonLabel('Pievienot jaunu pārstundu')
            ->columns(4),
    ];
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('skaitlis')
                    ->label('Nedēļas diena')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => [
                        '1' => 'Pirmdiena',
                        '2' => 'Otrdiena',
                        '3' => 'Trešdiena',
                        '4' => 'Ceturtdiena',
                        '5' => 'Piektdiena',
                        '6' => 'Sestdiena',
                        '7' => 'Svētdiena',
                    ][$state] ?? 'Nezināms'),

                TextColumn::make('kurss.Nosaukums')
                    ->label('Kursa nosaukums')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('datums.PirmaisDatums')
                    ->label('Nedēļas sākuma datums')
                    ->date('d.m.Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('laiks.sakumalaiks')
                    ->label('Sākuma laiks')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('stunda.Nosaukums')
                    ->label('Stundas nosaukums')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pasniedzejs.Vards')
                    ->label('Pasniedzējs')
                    ->formatStateUsing(fn (string $state, $record) => 
                        $state . ' ' . ($record->pasniedzejs->Uzvards ?? ''))
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('kabinets.vieta')
                    ->label('Kabinets')
                    ->formatStateUsing(fn (string $state, $record) => 
                        $state . ' ' . ($record->kabinets->skaitlis ?? ''))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('kurssID')
                    ->label('Kurss')
                    ->relationship('kurss', 'Nosaukums'),
                    
                SelectFilter::make('datumsID')
                    ->label('Nedēļa')
                    ->relationship('datums', 'PirmaisDatums'),
                    
                SelectFilter::make('skaitlis')
                    ->label('Diena')
                    ->options([
                        '1' => 'Pirmdiena',
                        '2' => 'Otrdiena',
                        '3' => 'Trešdiena',
                        '4' => 'Ceturtdiena',
                        '5' => 'Piektdiena',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListIeplanotStundus::route('/'),
            'create' => Pages\CreateIeplanotStundu::route('/create'),
            'edit' => Pages\EditIeplanotStundu::route('/{record}/edit'),
            'view-timetable' => Pages\ViewWeeklyTimetable::route('/timetable'),
        ];
    }
}