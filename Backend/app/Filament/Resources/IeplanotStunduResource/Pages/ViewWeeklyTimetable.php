<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Card;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\IeplanotStundu;

class ViewWeeklyTimetable extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = IeplanotStunduResource::class;

    protected static string $view = 'filament.resources.ieplanot-stundu-resource.pages.view-weekly-timetable';
    
    public ?int $selectedKurssId = null;
    public ?int $selectedDatumsId = null;
    public $timetableData = [];

    public function mount()
    {
        $this->form->fill();
    }
    
    protected function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Select::make('selectedKurssId')
                        ->label('Kurss')
                        ->searchable()
                        ->options(function () {
                            return \App\Models\Kurss::pluck('Nosaukums', 'id')->toArray();
                        })
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            $this->loadTimetableData();
                        }),

                    Select::make('selectedDatumsId')
                        ->label('Nedēļa')
                        ->searchable()
                        ->options(function () {
                            return \App\Models\Datums::all()->mapWithKeys(function ($item) {
                                return [$item->id => $item->PirmaisDatums . ' - ' . $item->PedejaisDatums];
                            })->toArray();
                        })
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            $this->loadTimetableData();
                        }),
                ])
                ->columns(2)
        ];
    }
    
    public function loadTimetableData()
    {
        if (!$this->selectedKurssId || !$this->selectedDatumsId) {
            $this->timetableData = [];
            return;
        }
        
        $lessons = \App\Models\IeplanotStundu::with(['stunda', 'pasniedzejs', 'laiks', 'kabinets'])
            ->where('kurssID', $this->selectedKurssId)
            ->where('datumsID', $this->selectedDatumsId)
            ->get();
            
        $timetableData = [
            '1' => [],
            '2' => [],
            '3' => [],
            '4' => [],
            '5' => [],
        ];
        
        foreach ($lessons as $lesson) {
            $day = $lesson->skaitlis;
            $time = $lesson->laiks->sakumalaiks . ' - ' . $lesson->laiks->beigulaiks;
            
            if (!isset($timetableData[$day][$time])) {
                $timetableData[$day][$time] = [];
            }
            
            $timetableData[$day][$time][] = [
                'stunda' => $lesson->stunda->Nosaukums,
                'pasniedzejs' => $lesson->pasniedzejs->Vards . ' ' . $lesson->pasniedzejs->Uzvards,
                'kabinets' => $lesson->kabinets->vieta . ' ' . $lesson->kabinets->skaitlis,
                'id' => $lesson->id,
            ];
        }
        
        $this->timetableData = $timetableData;
    }
    
    public function redirectToEdit(int $lessonId)
    {
        $lesson = IeplanotStundu::find($lessonId);
        
        if ($lesson) {
            $weekLessons = IeplanotStundu::where('kurssID', $lesson->kurssID)
                ->where('datumsID', $lesson->datumsID)
                ->get();
            session()->put('editing_timetable', [
                'kurssID' => $lesson->kurssID,
                'datumsID' => $lesson->datumsID,
                'sourceId' => $lessonId
            ]);
            return redirect()->route('filament.resources.ieplanot-stundu-resource.edit', ['record' => $lessonId]);
        }
    }
}