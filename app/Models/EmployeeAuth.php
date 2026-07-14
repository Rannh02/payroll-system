<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class EmployeeAuth extends Authenticatable
{
    use Notifiable;

    protected $table = 'employee_auth';
    protected $primaryKey = 'id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = is_string($value) && preg_match('/^\$2[ayb]\$/', $value) ? $value : Hash::make($value);
    }

    public function getRoleAttribute(): string
    {
        return $this->attributes['role'] ?? 'employee';
    }

    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }
}
