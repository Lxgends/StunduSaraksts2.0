<?php

namespace App\Filament\Resources\IeplanotStunduResource\Pages;

use App\Filament\Resources\IeplanotStunduResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Models\IeplanotStundu;
use App\Models\Absences;
use App\Models\StundaAmount;
use App\Models\Kurss;
use App\Models\Stunda;
use App\Models\Datums;

class CreateIeplanotStundu extends CreateRecord
{
    protected static string $resource = IeplanotStunduResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $kurssID = $data['kurssID'];
        $datumsID = $data['datumsID'];
        $newLessons = [];

        for ($day = 1; $day <= 5; $day++) {
            $dayKey = "day_{$day}_lessons";
            if (!empty($data[$dayKey]) && is_array($data[$dayKey])) {
                foreach ($data[$dayKey] as $lesson) {
                    if (isset($lesson['laiksID'], $lesson['stundaID'], $lesson['pasniedzejsID'], $lesson['kabinetaID'])) {
                        $newLessons[] = [
                            'kurssID' => $kurssID,
                            'datumsID' => $datumsID,
                            'skaitlis' => $lesson['skaitlis'],
                            'laiksID' => $lesson['laiksID'],
                            'stundaID' => $lesson['stundaID'],
                            'pasniedzejsID' => $lesson['pasniedzejsID'],
                            'kabinetaID' => $lesson['kabinetaID'],
                        ];
                    }
                }
            }
        }

        $lessonCount = [];
        foreach ($newLessons as $lesson) {
            $key = $lesson['kurssID'] . '_' . $lesson['stundaID'];
            $lessonCount[$key] = ($lessonCount[$key] ?? 0) + 1;
        }

        foreach ($lessonCount as $key => $countToInsert) {
            [$kurssID, $stundaID] = explode('_', $key);
            $availableAmount = StundaAmount::where('kurssID', $kurssID)
                ->where('stundaID', $stundaID)
                ->value('daudzums') ?? 0;

            if ($countToInsert > $availableAmount) {
                $kurssName = Kurss::find($kurssID)?->Nosaukums ?? 'Nezināms kurss';
                $stundaName = Stunda::find($stundaID)?->Nosaukums ?? 'Nezināma stunda';

                Notification::make()
                    ->title('Pārsniegts stundu limits')
                    ->body("{$kurssName} priekšmets — {$stundaName}: var ieplānot tikai {$availableAmount} stundu(-as), jūs cenšaties ievietot {$countToInsert}.")
                    ->danger()
                    ->send();

                $this->halt();
                return new IeplanotStundu();
            }
        }

        $conflicts = $this->checkForConflicts($newLessons);
        if (!empty($conflicts)) {
            Notification::make()
                ->title('Validācijas kļūda')
                ->body($conflicts[0])
                ->danger()
                ->send();

            $this->halt();
            return new IeplanotStundu();
        }

        foreach ($newLessons as $lesson) {
            IeplanotStundu::create($lesson);
        }

        return !empty($newLessons)
            ? IeplanotStundu::where('kurssID', $kurssID)->where('datumsID', $datumsID)->first()
            : new IeplanotStundu();
    }

    protected function checkForConflicts(array $lessons): array
{
    $conflicts = [];
    $timeSlots = [];
    $teacherSlots = [];
    $roomSlots = [];

    foreach ($lessons as $lesson) {
        $datums = Datums::find($lesson['datumsID']);
        if (!$datums) {
            $conflicts[] = "Nederīgs datuma ieraksts.";
            continue;
        }

        $start = $datums->PirmaisDatums;
        $end = $datums->PedejaisDatums;
        $lessonDate = $this->getDateForDayOfWeek($lesson['skaitlis'], $start);

        $formattedDate = ucfirst($lessonDate->locale('lv')->isoFormat('dddd, D.MM.YYYY'));
        
        $laiks = \App\Models\Laiks::find($lesson['laiksID']);
        $laiksInfo = $laiks ? "Laiks: {$laiks->sakumalaiks} - {$laiks->beigulaiks}" : "LaiksID: {$lesson['laiksID']}";

        $isPasniedzejsAbsent = Absences::where('absence_type', 'pasniedzejs')
            ->where('pasniedzejsID', $lesson['pasniedzejsID'])
            ->where(function ($q) use ($lessonDate) {
                $q->whereDate('sakuma_datums', '<=', $lessonDate)
                ->whereDate('beigu_datums', '>=', $lessonDate);
            })
            ->exists();

        if ($isPasniedzejsAbsent) {
            $conflicts[] = "Pasniedzējs ir prombūtnē šajā dienā ({$formattedDate}). {$laiksInfo}";
        }

        $isKurssAbsent = Absences::where('absence_type', 'kurss')
            ->where('kurssID', $lesson['kurssID'])
            ->where(function ($q) use ($lessonDate) {
                $q->whereDate('sakuma_datums', '<=', $lessonDate)
                ->whereDate('beigu_datums', '>=', $lessonDate);
            })
            ->exists();

        if ($isKurssAbsent) {
            $conflicts[] = "Kurss ir prombūtnē šajā dienā ({$formattedDate}). {$laiksInfo}";
        }

        $timeKey = "{$lesson['datumsID']}_{$lesson['skaitlis']}_{$lesson['laiksID']}";
        if (isset($timeSlots[$timeKey])) {
            $conflicts[] = "Šajā laikā jau eksistē stunda šim kursam un nedēļai (diena: {$formattedDate}). {$laiksInfo}";
        }

        $teacherKey = "{$timeKey}_{$lesson['pasniedzejsID']}";
        if (isset($teacherSlots[$teacherKey])) {
            $conflicts[] = "Izvēlētais pasniedzējs šajā laikā jau ir aizņemts (diena: {$formattedDate}). {$laiksInfo}";
        }

        $roomKey = "{$timeKey}_{$lesson['kabinetaID']}";
        if (isset($roomSlots[$roomKey])) {
            $conflicts[] = "Šajā laikā izvēlētais kabinets jau ir aizņemts (diena: {$formattedDate}). {$laiksInfo}";
        }

        $existingCourseLesson = IeplanotStundu::where('kurssID', $lesson['kurssID'])
            ->where('datumsID', $lesson['datumsID'])
            ->where('skaitlis', $lesson['skaitlis'])
            ->where('laiksID', $lesson['laiksID'])
            ->exists();
            
        if ($existingCourseLesson) {
            $conflicts[] = "Šim kursam jau eksistē stunda šajā laikā (diena: {$formattedDate}). {$laiksInfo}";
        }
        
        $existingTeacherLesson = IeplanotStundu::where('datumsID', $lesson['datumsID'])
            ->where('skaitlis', $lesson['skaitlis'])
            ->where('laiksID', $lesson['laiksID'])
            ->where('pasniedzejsID', $lesson['pasniedzejsID'])
            ->exists();
            
        if ($existingTeacherLesson) {
            $conflicts[] = "Izvēlētais pasniedzējs jau ir aizņemts ar citu kursu šajā laikā (diena: {$formattedDate}). {$laiksInfo}";
        }
        
        $existingRoomLesson = IeplanotStundu::where('datumsID', $lesson['datumsID'])
            ->where('skaitlis', $lesson['skaitlis'])
            ->where('laiksID', $lesson['laiksID'])
            ->where('kabinetaID', $lesson['kabinetaID'])
            ->exists();
            
        if ($existingRoomLesson) {
            $conflicts[] = "Izvēlētais kabinets jau ir aizņemts ar citu kursu šajā laikā (diena: {$formattedDate}). {$laiksInfo}";
        }

        $timeSlots[$timeKey] = true;
        $teacherSlots[$teacherKey] = true;
        $roomSlots[$roomKey] = true;
    }

    return $conflicts;
}
    
    
    protected function getDateForDayOfWeek(int $dayNumber, string $startDate)
    {
        $date = \Carbon\Carbon::parse($startDate);
        return $date->addDays($dayNumber - 1);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
