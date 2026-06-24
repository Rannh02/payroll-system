<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'position_id',
        'department_id',
        'employee_number',
        'created_by',
        'user_id',
        'profile_photo',
        
        // Personal Information
        'first_name',
        'middle_name',
        'last_name',
        'sex',

        // Current Address
        'current_street_address',
        'current_barangay',
        'current_city_municipality',
        'current_province',
        'current_zip_code',

        // Permanent Address
        'permanent_street_address',
        'permanent_barangay',
        'permanent_city_municipality',
        'permanent_province',
        'permanent_zip_code',

        // Other Details
        'contact_info',
        'date_of_birth',
        'salary_rate',
        'hire_date',
        'employment_status',
        'marital_status',
        'number_of_dependents',
        'spouse',
        'sss_num',
        'philhealth_num',
        'pagibig_num'
    ];

    // Relationships
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->profile_photo && \Storage::disk('public')->exists($this->profile_photo)) {
            return asset('storage/' . $this->profile_photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=3b82f6&color=ffffff';
    }

    public function getDailyRateAttribute()
    {
        $monthlySalary = $this->salary_rate ?? ($this->position->basic_salary ?? 0);
        return $monthlySalary > 0 ? round($monthlySalary / 22, 2) : 0;
    }
}
