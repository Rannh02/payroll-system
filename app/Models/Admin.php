<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
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
        return $this->attributes['role'] ?? 'admin';
    }
}
