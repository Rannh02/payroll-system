<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\EmployeeAuth;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Account
        Admin::updateOrCreate(
            ['email' => 'admin@via-architect.com'],
            [
                'name' => 'Archi. Gabriel Bryan Licao',
                'password' => 'Password@123',
                'role' => 'admin',
            ]
        );

        // Employee Account
        EmployeeAuth::updateOrCreate(
            ['email' => 'user@via-architect.com'],
            [
                'first_name' => 'User',
                'last_name' => 'Account',
                'password' => 'User@123',
                'role' => 'employee',
            ]
        );

        // Seed default deductions
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 1], ['deduction_name' => 'SSS']);
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 2], ['deduction_name' => 'PhilHealth']);
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 3], ['deduction_name' => 'Pag-IBIG']);
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 4], ['deduction_name' => 'Tax']);

        $this->call(GovernmentContributionSeeder::class);
        $this->call(SuperadminSeeder::class);
    }
}
