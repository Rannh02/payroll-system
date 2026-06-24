<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';

    protected $fillable = [

        'date',
        'time_in',
        'time_out',
        'overtime_hours',
        'total_hours',
        'status',
        'late_minutes',
        'undertime_minutes',
        'employee_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
