<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $table = 'deduction';
    protected $primaryKey = 'deduction_id';

    protected $fillable = [

        'deduction_name',
    ];
}
