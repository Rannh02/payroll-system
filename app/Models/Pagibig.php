<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagibig extends Model
{
    protected $table = 'pagibig';
    protected $primaryKey = 'pagibig_id';
    public $incrementing = true;
    protected $fillable = [
        'salary_from',
        'salary_to',
        'employee_rate',
        'employer_rate',
        'maximum_contribution',
    ];

    protected $casts = [
        'salary_from' => 'decimal:2',
        'salary_to' => 'decimal:2',
        'employee_rate' => 'decimal:2',
        'employer_rate' => 'decimal:2',
        'maximum_contribution' => 'decimal:2',
    ];
}
