<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::updateOrCreate(
            ['email' => 'admin@via-architect.com'],
            [
                'name' => 'Archi. Gabriel Bryan Licao',
                'password' => bcrypt('Password@123'),
                'role' => 'admin',
            ]
        );

        // Regular User Account
        User::updateOrCreate(
            ['email' => 'user@via-architect.com'],
            [
                'name' => 'User',
                'password' => bcrypt('User@123'),
                'role' => 'user',
            ]
        );

        // Seed default deductions
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 1], ['deduction_name' => 'SSS']);
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 2], ['deduction_name' => 'PhilHealth']);
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 3], ['deduction_name' => 'Pag-IBIG']);
        \App\Models\Deduction::updateOrCreate(['deduction_id' => 4], ['deduction_name' => 'Tax']);

        $this->call(GovernmentContributionSeeder::class);
    }
}
