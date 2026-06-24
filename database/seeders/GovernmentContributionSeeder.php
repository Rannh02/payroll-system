<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sss;
use App\Models\Philhealth;
use App\Models\Pagibig;
use App\Models\Tax;

class GovernmentContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed SSS Brackets (Social Security System)
        // Employee rate is 4.5% of MSC, Employer rate is 9.5% of MSC.
        // Minimum range: below 4,250
        Sss::updateOrCreate(
            ['sss_range_from' => 0.00, 'sss_range_to' => 4249.99],
            [
                'monthly_salary_credit' => 4000.00,
                'employee_share' => 180.00,
                'employer_share' => 380.00,
                'total_contribution' => 560.00,
            ]
        );

        // Intermediate ranges: 4,250 to 29,750 in steps of 500
        for ($msc = 4500; $msc <= 29500; $msc += 500) {
            $from = $msc - 250;
            $to = $msc + 249.99;
            $ee = $msc * 0.045;
            $er = $msc * 0.095;
            Sss::updateOrCreate(
                ['sss_range_from' => $from, 'sss_range_to' => $to],
                [
                    'monthly_salary_credit' => $msc,
                    'employee_share' => $ee,
                    'employer_share' => $er,
                    'total_contribution' => $ee + $er,
                ]
            );
        }

        // Maximum range: 29,750 and above
        Sss::updateOrCreate(
            ['sss_range_from' => 29750.00, 'sss_range_to' => 9999999.99],
            [
                'monthly_salary_credit' => 30000.00,
                'employee_share' => 1350.00,
                'employer_share' => 2850.00,
                'total_contribution' => 4200.00,
            ]
        );

        // 2. Seed PhilHealth
        // 5% premium rate shared equally (2.5% EE, 2.5% ER)
        Philhealth::updateOrCreate(
            ['salary_from' => 0.00, 'salary_to' => 9999999.99],
            [
                'contribution_rate' => 5.00, // 5% total rate (shared 50/50 in calculation service)
            ]
        );

        // 3. Seed Pag-IBIG
        // Under 1,500: 1% EE, 2% ER. Above 1,500: 2% EE, 2% ER. Max contribution limit 100.
        Pagibig::updateOrCreate(
            ['salary_from' => 0.00, 'salary_to' => 1500.00],
            [
                'employee_rate' => 1.00,
                'employer_rate' => 2.00,
                'maximum_contribution' => 100.00,
            ]
        );

        Pagibig::updateOrCreate(
            ['salary_from' => 1500.01, 'salary_to' => 9999999.99],
            [
                'employee_rate' => 2.00,
                'employer_rate' => 2.00,
                'maximum_contribution' => 100.00,
            ]
        );

        // 4. Seed Withholding Tax (TRAIN Law Monthly Table)
        Tax::updateOrCreate(
            ['salary_from' => 0.00, 'salary_to' => 20833.33],
            [
                'base_tax' => 0.00,
                'tax_rate' => 0.00,
            ]
        );

        Tax::updateOrCreate(
            ['salary_from' => 20833.34, 'salary_to' => 33333.33],
            [
                'base_tax' => 0.00,
                'tax_rate' => 15.00,
            ]
        );

        Tax::updateOrCreate(
            ['salary_from' => 33333.34, 'salary_to' => 66666.67],
            [
                'base_tax' => 1875.00,
                'tax_rate' => 20.00,
            ]
        );

        Tax::updateOrCreate(
            ['salary_from' => 66666.68, 'salary_to' => 250000.00],
            [
                'base_tax' => 8541.67,
                'tax_rate' => 25.00,
            ]
        );

        Tax::updateOrCreate(
            ['salary_from' => 250000.01, 'salary_to' => 666666.67],
            [
                'base_tax' => 54375.00,
                'tax_rate' => 30.00,
            ]
        );

        Tax::updateOrCreate(
            ['salary_from' => 666666.68, 'salary_to' => 9999999.99],
            [
                'base_tax' => 179375.00,
                'tax_rate' => 35.00,
            ]
        );
    }
}
