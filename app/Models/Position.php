<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'position';
    protected $primaryKey = 'position_id';

    protected $fillable = [
        'department_id',
        'position_name',
        'position_code',
        'description',
        'basic_salary',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id', 'position_id');
    }
}
