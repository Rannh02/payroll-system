<?php

use App\Models\Admin;
use App\Models\EmployeeAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

it('authenticates admins and employees from their own tables', function () {
    Schema::create('admin', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role')->default('admin');
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('employee_auth', function ($table) {
        $table->id();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role')->default('employee');
        $table->rememberToken();
        $table->timestamps();
    });

    Admin::create([
        'name' => 'Admin User',
        'email' => 'admin-separate@example.com',
        'password' => bcrypt('Password@123'),
    ]);

    EmployeeAuth::create([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'employee-separate@example.com',
        'password' => bcrypt('Password@123'),
    ]);

    expect(Auth::guard('admin')->attempt([
        'email' => 'admin-separate@example.com',
        'password' => 'Password@123',
    ]))->toBeTrue();

    expect(Auth::guard('employee')->attempt([
        'email' => 'employee-separate@example.com',
        'password' => 'Password@123',
    ]))->toBeTrue();
});
