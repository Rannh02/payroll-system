<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tax';
    protected $primaryKey = 'tax_id';
    public $incrementing = true;
    protected $fillable = [
        'salary_from',
        'salary_to',
        'base_tax',
        'tax_rate'
    ];
    protected $casts = [
        'salary_from',
        'salary_to',
        'base_tax',
        'tax_rate'
    ];
}
