<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_code',
        'department_name',
        'description',
        'status',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class, 'department_id');
    }
}
