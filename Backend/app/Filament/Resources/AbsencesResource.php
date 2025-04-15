<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsencesResource\Pages;
use App\Models\Absences;
use App\Models\Kurss;
use App\Models\Pasniedzejs;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsencesResource extends Resource
{
    protected static ?string $model = Absences::class;

    protected static ?string $navigationGroup = 'Datumi un pārstundu laiki';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Prombūtnes';

    public static function getModelLabel(): string
    {
        return 'Prombūtne';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Prombūtnes';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('absence_type')
                    ->label('Prombūtnes tips')
                    ->options([
                        'pasniedzejs' => 'Pasniedzējs',
                        'kurss' => 'Kurss',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\Select::make('pasniedzejsID')
                    ->label('Pasniedzējs')
                    ->options(
                        Pasniedzejs::all()
                            ->mapWithKeys(fn ($p) => [
                                $p->id => "{$p->Vards} {$p->Uzvards}"
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->visible(fn (Forms\Get $get) => $get('absence_type') === 'pasniedzejs')
                    ->required(fn (Forms\Get $get) => $get('absence_type') === 'pasniedzejs'),

                Forms\Components\Select::make('kurssID')
                    ->label('Kurss')
                    ->options(
                        Kurss::all()
                            ->mapWithKeys(fn ($k) => [
                                $k->id => $k->Nosaukums ?? 'Nezināms kurss'
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->visible(fn (Forms\Get $get) => $get('absence_type') === 'kurss')
                    ->required(fn (Forms\Get $get) => $get('absence_type') === 'kurss'),

                Forms\Components\DatePicker::make('sakuma_datums')
                    ->label('Prombūtnes sākuma datums')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('beigu_datums')
                    ->label('Prombūtnes beigu datums')
                    ->required()
                    ->native(false)
                    ->afterOrEqual('sakuma_datums'),

                Forms\Components\Textarea::make('piezimes')
                    ->label('Piezīmes')
                    ->maxLength(255),

                Forms\Components\Section::make('Ietekmētās dienas')
                    ->description('Šeit tiks parādītas visas dienas, kurās būs prombūtne')
                    ->schema([
                        Forms\Components\Placeholder::make('affected_days')
                            ->label('Ietekmētās dienas')
                            ->content(function (Forms\Get $get) {
                                $startDate = $get('sakuma_datums');
                                $endDate = $get('beigu_datums');

                                if (!$startDate || !$endDate) {
                                    return 'Lūdzu, izvēlieties sākuma un beigu datumus.';
                                }

                                $period = CarbonPeriod::create($startDate, $endDate);
                                $affectedDays = [];

                                foreach ($period as $date) {
                                    if ($date->isWeekend()) {
                                        continue;
                                    }

                                    $affectedDays[] = $date->format('d.m.Y') . ' (' .
                                        [
                                            'Monday' => 'Pirmdiena (1)',
                                            'Tuesday' => 'Otrdiena (2)',
                                            'Wednesday' => 'Trešdiena (3)',
                                            'Thursday' => 'Ceturtdiena (4)',
                                            'Friday' => 'Piektdiena (5)',
                                        ][$date->format('l')] . ')';
                                }

                                return implode(', ', $affectedDays);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('absence_type')
                    ->label('Tips')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pasniedzejs' => 'info',
                        'kurss' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pasniedzejs' => 'Pasniedzējs',
                        'kurss' => 'Kurss',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('pasniedzejs.Vards')
                    ->label('Pasniedzējs')
                    ->formatStateUsing(fn ($state, $record) => $record?->pasniedzejs?->Vards . ' ' . $record?->pasniedzejs?->Uzvards)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->visible(fn ($record) => $record && $record->absence_type === 'pasniedzejs'),

                Tables\Columns\TextColumn::make('kurss.Nosaukums')
                    ->label('Kurss')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->visible(fn ($record) => $record && $record->absence_type === 'kurss'),

                Tables\Columns\TextColumn::make('sakuma_datums')
                    ->label('Sākuma datums')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('beigu_datums')
                    ->label('Beigu datums')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('affected_days_count')
                    ->label('Dienu skaits')
                    ->getStateUsing(function ($record) {
                        if (!$record) return 0;

                        $startDate = Carbon::parse($record->sakuma_datums);
                        $endDate = Carbon::parse($record->beigu_datums);
                        $period = CarbonPeriod::create($startDate, $endDate);

                        return collect($period)->reject(fn ($date) => $date->isWeekend())->count();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('absence_type')
                    ->label('Tips')
                    ->options([
                        'pasniedzejs' => 'Pasniedzējs',
                        'kurss' => 'Kurss',
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('No datuma')
                            ->native(false),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Līdz datumam')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from_date'], fn (Builder $query, $date) => $query->whereDate('sakuma_datums', '>=', $date))
                            ->when($data['to_date'], fn (Builder $query, $date) => $query->whereDate('beigu_datums', '<=', $date));
                    }),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsences::route('/'),
            'create' => Pages\CreateAbsences::route('/create'),
            'edit' => Pages\EditAbsences::route('/{record}/edit'),
        ];
    }
}
