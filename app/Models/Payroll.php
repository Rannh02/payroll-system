<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payroll';
    protected $primaryKey = 'payroll_id';

    protected $fillable = [
        'employee_id',
        'payroll_period_start',
        'payroll_period_end',
        'payroll_date',

        // Earnings
        'basic_salary',
        'overtime_pay',
        'gross_pay',

        // Government contributions
        'sss',
        'philhealth',
        'hdmf',
        'tax',

        // Summary
        'total_deductions',
        'net_pay',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function deductions()
    {
        return $this->hasMany(Payroll_Deduction::class, 'payroll_id', 'payroll_id');
    }

    public function getSssAttribute()
    {
        if ($this->relationLoaded('deductions')) {
            return $this->deductions->where('deduction_id', 1)->first()?->deduction_amount ?? 0;
        }
        return $this->deductions()->where('deduction_id', 1)->value('deduction_amount') ?? 0;
    }

    public function getPhilhealthAttribute()
    {
        if ($this->relationLoaded('deductions')) {
            return $this->deductions->where('deduction_id', 2)->first()?->deduction_amount ?? 0;
        }
        return $this->deductions()->where('deduction_id', 2)->value('deduction_amount') ?? 0;
    }

    public function getHdmfAttribute()
    {
        if ($this->relationLoaded('deductions')) {
            return $this->deductions->where('deduction_id', 3)->first()?->deduction_amount ?? 0;
        }
        return $this->deductions()->where('deduction_id', 3)->value('deduction_amount') ?? 0;
    }

    public function getTaxAttribute()
    {
        if ($this->relationLoaded('deductions')) {
            return $this->deductions->where('deduction_id', 4)->first()?->deduction_amount ?? 0;
        }
        return $this->deductions()->where('deduction_id', 4)->value('deduction_amount') ?? 0;
    }

    public function getAbsentDeductionAttribute()
    {
        $employee = $this->employee;
        if (!$employee) return 0;

        $absentDays = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->where('status', 'Absent')
            ->count();

        return $employee->daily_rate * $absentDays;
    }

    public function getDaysWorkedAttribute()
    {
        return Attendance::where('employee_id', $this->employee_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->where('status', 'Present')
            ->count();
    }

    public function getAbsentDaysAttribute()
    {
        return Attendance::where('employee_id', $this->employee_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->where('status', 'Absent')
            ->count();
    }

    public function getLateMinutesAttribute()
    {
        return Attendance::where('employee_id', $this->employee_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->sum('late_minutes');
    }

    public function getUndertimeMinutesAttribute()
    {
        return Attendance::where('employee_id', $this->employee_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->sum('undertime_minutes');
    }

    public function getGrossSalaryAttribute()
    {
        return $this->gross_pay;
    }

    public function getFromDateAttribute()
    {
        return $this->payroll_period_start;
    }

    public function getToDateAttribute()
    {
        return $this->payroll_period_end;
    }
}
