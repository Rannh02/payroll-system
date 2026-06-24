<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Philhealth extends Model
{
    protected $table = 'philhealth';
    protected $primaryKey = 'philhealth_id';
    public $incrementing = true;
    protected $fillable = [
        'salary_from',
        'salary_to',
        'contribution_rate'
    ];

    protected $casts = [
        'salary_from' => 'decimal:2',
        'salary_to' => 'decimal:2',
        'contribution_rate' => 'decimal:2'
    ];
}
