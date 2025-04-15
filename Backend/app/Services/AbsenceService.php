<?php

namespace App\Services;

use App\Models\Absences;
use App\Models\Datums;
use Carbon\Carbon;

class AbsenceService
{
    /**
     * Check if a teacher is absent on a specific date
     *
     * @param int $pasniedzejsId
     * @param string|Carbon $date
     * @return bool
     */
    public function isTeacherAbsent(int $pasniedzejsId, $date): bool
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        return Absences::where('absence_type', 'pasniedzejs')
            ->where('pasniedzejsID', $pasniedzejsId)
            ->where('sakuma_datums', '<=', $date)
            ->where('beigu_datums', '>=', $date)
            ->exists();
    }
    
    /**
     * Check if a course is absent on a specific date
     *
     * @param int $kurssId
     * @param string|Carbon $date
     * @return bool
     */
    public function isCourseAbsent(int $kurssId, $date): bool
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        return Absences::where('absence_type', 'kurss')
            ->where('kurssID', $kurssId)
            ->where('sakuma_datums', '<=', $date)
            ->where('beigu_datums', '>=', $date)
            ->exists();
    }
    
    /**
     * Get concrete dates from a Datums record and skaitlis (day number)
     *
     * @param Datums $weekDates
     * @param int $skaitlis
     * @return Carbon|null
     */
    public function getDateFromWeekAndSkaitlis(Datums $weekDates, int $skaitlis): ?Carbon
    {
        if ($skaitlis < 1 || $skaitlis > 5) {
            return null;
        }
        
        $startDate = Carbon::parse($weekDates->PirmaisDatums);
        
        // Skaitlis 1 is Monday, 2 is Tuesday, etc.
        return $startDate->copy()->addDays($skaitlis - 1);
    }
    
    /**
     * Get all teacher absences between two dates
     *
     * @param int $pasniedzejsId
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeacherAbsencesBetween(int $pasniedzejsId, $startDate, $endDate)
    {
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate);
        }
        
        if (!$endDate instanceof Carbon) {
            $endDate = Carbon::parse($endDate);
        }
        
        return Absences::where('absence_type', 'pasniedzejs')
            ->where('pasniedzejsID', $pasniedzejsId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sakuma_datums', [$startDate, $endDate])
                    ->orWhereBetween('beigu_datums', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('sakuma_datums', '<=', $startDate)
                          ->where('beigu_datums', '>=', $endDate);
                    });
            })
            ->get();
    }
}