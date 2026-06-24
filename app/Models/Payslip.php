<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $table = 'payslip';
    protected $primaryKey = 'payslip_id';

    protected $fillable = [
        'payroll_id',
        'issue_date',
        'pay_period_start',
        'pay_period_end',
    ];
    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id', 'payroll_id');
    }
}
