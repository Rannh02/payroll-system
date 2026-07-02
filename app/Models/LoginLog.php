<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'browser',
        'status',
        'locked_until',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id')->withDefault([
            'name' => $this->email,
            'role' => 'unknown',
        ]);
    }

    /**
     * Parse browser name from user agent string.
     */
    public static function parseBrowser(?string $userAgent): string
    {
        if (!$userAgent) return 'Unknown';

        if (str_contains($userAgent, 'Edg/') || str_contains($userAgent, 'Edge/')) return 'Edge';
        if (str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera/')) return 'Opera';
        if (str_contains($userAgent, 'YaBrowser/')) return 'Yandex';
        if (str_contains($userAgent, 'SamsungBrowser/')) return 'Samsung Browser';
        if (str_contains($userAgent, 'Firefox/')) return 'Firefox';
        if (str_contains($userAgent, 'Chrome/')) return 'Chrome';
        if (str_contains($userAgent, 'Safari/') && !str_contains($userAgent, 'Chrome/')) return 'Safari';
        if (str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident/')) return 'Internet Explorer';

        return 'Unknown';
    }
}
