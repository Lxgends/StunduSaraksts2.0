<x-filament::page>
    {{ $this->form }}

    @if($this->selectedKurssId && $this->selectedDatumsId)
        <div class="mt-6">
            <x-filament::card>
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                    {{ \App\Models\Kurss::find($this->selectedKurssId)->Nosaukums }} - 
                    {{ \App\Models\Datums::find($this->selectedDatumsId)->PirmaisDatums }} līdz
                    {{ \App\Models\Datums::find($this->selectedDatumsId)->PedejaisDatums }}
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <th class="border px-4 py-2">Laiks</th>
                                <th class="border px-4 py-2">Pirmdiena</th>
                                <th class="border px-4 py-2">Otrdiena</th>
                                <th class="border px-4 py-2">Trešdiena</th>
                                <th class="border px-4 py-2">Ceturtdiena</th>
                                <th class="border px-4 py-2">Piektdiena</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allTimes = collect();
                                foreach ($this->timetableData as $dayData) {
                                    foreach (array_keys($dayData) as $time) {
                                        $allTimes->push($time);
                                    }
                                }
                                $allTimes = $allTimes->unique()->sort()->values()->all();
                            @endphp

                            @foreach($allTimes as $time)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="border px-4 py-2 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $time }}
                                    </td>
                                    
                                    @for($day = 1; $day <= 5; $day++)
                                        <td class="border px-4 py-2 {{ isset($this->timetableData[$day][$time]) ? 'bg-blue-50 dark:bg-blue-900' : 'bg-white dark:bg-gray-900' }}">
                                            @if(isset($this->timetableData[$day][$time]))
                                                @foreach($this->timetableData[$day][$time] as $lesson)
                                                    <div class="p-2 mb-2 bg-white dark:bg-gray-800 rounded shadow-sm">
                                                        <div class="font-bold text-gray-900 dark:text-gray-100">
                                                            {{ $lesson['stunda'] }}
                                                        </div>
                                                        <div class="text-gray-700 dark:text-gray-300">
                                                            {{ $lesson['pasniedzejs'] }}
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ $lesson['kabinets'] }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::card>
        </div>
    @endif
</x-filament::page>
