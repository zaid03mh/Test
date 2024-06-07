<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;
    
    protected $fillable = ['first_name', 'last_name', 'birthdate', 'hire_date', 'email', 'phone_number'];
    protected $appends = ['annual_leave_balance', 'age', 'total_accumulated_leave', 'annual_leave_by_year'];

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function calculateAnnualLeave($year)
    {
        $hireDate = Carbon::parse($this->hire_date);
        $currentDate = Carbon::now();
        $yearStart = Carbon::createFromDate($year, 1, 1);
        $yearEnd = Carbon::createFromDate($year, 12, 31);

        if ($year < $hireDate->year) {
            return 0;
        }

        if ($year == $hireDate->year) {
            $monthsWorked = $hireDate->diffInMonths($yearEnd) ;
        } else if ($year == $currentDate->year) {
            $monthsWorked = $yearStart->diffInMonths($currentDate->endOfMonth());
        } else {
            $monthsWorked = 12;
        }

        $age = Carbon::parse($this->birthdate)->age;

        $annualLeave = 0;

        if ($age < 18) {
            $annualLeave += $monthsWorked * 2;
        } else {
            $annualLeave += $monthsWorked * 1.5;
        }

        $yearsWorked = $year - $hireDate->year;
        if ($yearsWorked >= 5) {
            $additionalLeave = floor($yearsWorked / 5) * 1.5;
            $annualLeave += $additionalLeave;
        }

        return $this->roundLeave($annualLeave, 30);
    }

    public function calculateLeavePerYear()
    {
        $hireDate = Carbon::parse($this->hire_date);
        $currentYear = Carbon::now()->year;
        $yearsWorked = $currentYear - $hireDate->year;
        
        $annualLeaveByYear = [];

        for ($i = 0; $i <= $yearsWorked; $i++) {
            $year = $hireDate->year + $i;
            $annualLeaveByYear[$year] = $this->calculateAnnualLeave($year);
        }

        return $annualLeaveByYear;
    }

    public function getTotalAccumulatedLeaveAttribute()
    {
        $annualLeaveByYear = $this->calculateLeavePerYear();
        $currentYear = Carbon::now()->year;

        $currentYearLeave = $annualLeaveByYear[$currentYear] ?? 0;
        $previousYearLeave = $annualLeaveByYear[$currentYear - 1] ?? 0;

        $userEmail = $this->email;

        $approvedLeaveDays = LeaveRequest::whereHas('user', function ($query) use ($userEmail) {
            $query->where('email', $userEmail);
        })->where('status', 'approuvé')->sum('days_requested');

        $totalLeave = ($currentYearLeave + $previousYearLeave) - $approvedLeaveDays;

        return $this->roundLeave($totalLeave);
    }

    public function getAnnualLeaveBalanceAttribute()
    {
        $currentYearLeave = $this->calculateAnnualLeave(Carbon::now()->year);

        $userEmail = $this->email;

        $approvedLeaveDays = LeaveRequest::whereHas('user', function ($query) use ($userEmail) {
            $query->where('email', $userEmail);
        })->where('status', 'approuvé')->whereYear('start_date', Carbon::now()->year)->sum('days_requested');

        $annualLeaveBalance = $currentYearLeave - $approvedLeaveDays;

        return $this->roundLeave($annualLeaveBalance);
    }

    public function getAnnualLeaveByYearAttribute()
    {
        $annualLeaveByYear = $this->calculateLeavePerYear();

        return array_map([$this, 'roundLeave'], $annualLeaveByYear);
    }

    private function roundLeave($leave, $max = PHP_INT_MAX)
    {
        $leave = min($leave, $max);
        return ceil($leave - 0.5);
    }

    public function deductLeave($days)
    {
        $currentLeaveBalance = $this->getTotalAccumulatedLeaveAttribute();
        $newLeaveBalance = max(0, $currentLeaveBalance - $days);
    }
}
