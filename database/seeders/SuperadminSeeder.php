<?php

namespace Database\Seeders;

use App\Models\Superadmin;
use Illuminate\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    /**
     * Seed the Superadmin table.
     * The password is automatically hashed by the Superadmin model mutator.
     */
    public function run(): void
    {
        Superadmin::updateOrCreate(
            ['username' => 'Administrator@02'],
            ['password' => 'Administrator@02']
        );
    }
}
