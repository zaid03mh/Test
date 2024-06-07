<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\LeaveRequestProcessed;
use Carbon\Carbon;
use HanifHefaz\Dcter\Dcter;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'description', 'status', 
        'processed', 'days_requested', 'note', 'processed_count', 'original_end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifyUser()
    {
        $this->user->notify(new LeaveRequestProcessed($this));
    }

    public function calculateDaysRequested()
    {
        $start_date = Carbon::parse($this->start_date);
        $end_date = Carbon::parse($this->end_date);
        $this->days_requested = $start_date->diffInDays($end_date);
        $this->save();
    }

    public function markAsDeducted()
    {
        $this->calculateHolidaysAndSundays();

        $userEmail = $this->user->email;
        $employee = Employee::where('email', $userEmail)->first();

        if ($employee) {
            $employee->deductLeave($this->days_requested);
        } else {
            throw new \Exception("Employee not found for the user with email: {$this->user->email}");
        }
    }

    public function calculateHolidaysAndSundays()
    {
        $start_date = Carbon::parse($this->start_date);
        $end_date = Carbon::parse($this->end_date);

        if (!$this->original_end_date) {
            $this->original_end_date = $end_date->copy();
        }

        $holidays = $this->getHolidaysBetween($start_date, $end_date);
        $sundays = $this->getSundaysBetween($start_date, $end_date);
        $islamicHolidays = $this->getIslamicHolidaysBetween($start_date, $end_date);

        $originalEndDate = $end_date->copy();
        $totalExtraDays = count($holidays) + count($sundays) + $this->getIslamicHolidayDays($islamicHolidays['dates']);

        $newEndDate = $end_date->addDays($totalExtraDays);

        $totalIslamicHolidays = $islamicHolidays['count'];
        $islamicHolidayNames = implode(', ', $islamicHolidays['names']);
        $this->note = "La date de fin a été mise à jour jusqu'à {$newEndDate->format('Y-m-d')} pour avoir " 
                    . count($sundays) . " dimanche(s), " 
                    . count($holidays) . " jour(s) férié(s) et " 
                    . " $islamicHolidayNames avec un total de " 
                    . "{$totalExtraDays} jours supplémentaires.";
        $this->end_date = $newEndDate;
        $this->save();
    }

    private function getHolidaysBetween($start_date, $end_date)
    {
        $holidays = [
            ['day' => 1, 'month' => 1, 'days' => 1],
            ['day' => 11, 'month' => 1, 'days' => 1],
            ['day' => 1, 'month' => 5, 'days' => 1],
            ['day' => 30, 'month' => 7, 'days' => 1],
            ['day' => 14, 'month' => 8, 'days' => 1],
            ['day' => 20, 'month' => 8, 'days' => 1],
            ['day' => 21, 'month' => 8, 'days' => 1],
            ['day' => 6, 'month' => 11, 'days' => 1],
            ['day' => 18, 'month' => 11, 'days' => 1]
        ];

        $year = $start_date->year;
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $holidayDate = Carbon::createFromDate($year, $holiday['month'], $holiday['day']);
            if ($holidayDate->between($start_date, $end_date)) {
                for ($i = 0; $i < $holiday['days']; $i++) {
                    $holidayDates[] = $holidayDate->copy()->addDays($i);
                }
            }
        }

        return $holidayDates;
    }

    private function getSundaysBetween($start_date, $end_date)
    {
        $sundays = [];
        $current = $start_date->copy();

        while ($current->lte($end_date)) {
            if ($current->isSunday()) {
                $sundays[] = $current->copy();
            }
            $current->addDay();
        }

        return $sundays;
    }

    private function getIslamicHolidaysBetween($start_date, $end_date)
    {
        $islamicHolidays = [
            ['day' => 1, 'month' => 1, 'days' => 1, 'name' => 'Islamic New Year'],
            ['day' => 12, 'month' => 3, 'days' => 1, 'name' => 'Mawlid al-Nabi'],
            ['day' => 27, 'month' => 7, 'days' => 1, 'name' => 'Isra and Miraj'],
            ['day' => 1, 'month' => 10, 'days' => 2, 'name' => 'Eid al-Fitr'],
            ['day' => 10, 'month' => 12, 'days' => 2, 'name' => 'Eid al-Adha']
        ];

        $startHijri = Dcter::GregorianToHijri($start_date->format('Y-m-d'));
        $endHijri = Dcter::GregorianToHijri($end_date->format('Y-m-d'));

        $holidayDates = [];
        $holidayNames = [];
        $totalIslamicHolidays = 0;
        foreach ($islamicHolidays as $holiday) {
            $hijriDate = sprintf('%s-%02d-%02d', substr($startHijri, 0, 4), $holiday['month'], $holiday['day']);
            $holidayDateGregorian = Dcter::HijriToGregorian($hijriDate);
            if ($holidayDateGregorian) {
                $holidayDateGregorian = Carbon::createFromFormat('Y-m-d', $holidayDateGregorian);
                if ($holidayDateGregorian->between($start_date, $end_date)) {
                    for ($i = 0; $i < $holiday['days']; $i++) {
                        $holidayDates[] = $holidayDateGregorian->copy()->addDays($i);
                    }
                    $holidayNames[] = $holiday['name'];
                    $totalIslamicHolidays++;
                }
            }
        }

        return ['dates' => $holidayDates, 'count' => $totalIslamicHolidays, 'names' => $holidayNames];
    }

    private function getIslamicHolidayDays($islamicHolidays)
    {
        $days = 0;
        foreach ($islamicHolidays as $holiday) {
            $days++;
        }
        return $days;
    }

    // New methods for statistics
    public static function getTotalRequests()
    {
        return self::count();
    }

    public static function getProcessedRequests()
    {
        return self::where('processed', true)->count();
    }

   
}
