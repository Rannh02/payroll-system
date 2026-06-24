<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave_Request extends Model
{
    protected $table = 'leave_request';
    protected $primaryKey = 'leave_request_id';

    protected $fillable = [
        'start_date',
        'end_date',
        'leave_type',
        'reason',
        'status',
        'date_filed',
        'employee_id',
        'approved_by',
        'is_viewed_by_employee',
        'is_viewed_by_admin',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
