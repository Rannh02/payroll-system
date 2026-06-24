<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll_Deduction extends Model
{
    protected $table = 'payroll_deduction';
    protected $primaryKey = 'payroll_deduction_id';

    protected $fillable = [
        'payroll_id',
        'deduction_id',
        'deduction_amount',

    ];
    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id', 'payroll_id');
    }
    public function deduction()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id', 'deduction_id');
    }
}
