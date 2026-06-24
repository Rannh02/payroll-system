<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Sss;
use App\Models\Philhealth;
use App\Models\Pagibig;
use App\Models\Tax;

class PayrollService
{
    public function compute(Employee $employee, $monthlySalary)
    {
        // 1. SSS Calculation
        $sssRecord = Sss::where('sss_range_from', '<=', $monthlySalary)
            ->where('sss_range_to', '>=', $monthlySalary)
            ->first();
        
        if (!$sssRecord) {
            $sssRecord = Sss::orderBy('sss_range_from', 'desc')->first();
        }

        $sssEmployeeShare = $sssRecord ? (float)$sssRecord->employee_share : 0.0;
        $sssEmployerShare = $sssRecord ? (float)$sssRecord->employer_share : 0.0;

        // 2. PhilHealth Calculation
        $philhealthRecord = Philhealth::where('salary_from', '<=', $monthlySalary)
            ->where('salary_to', '>=', $monthlySalary)
            ->first();

        if (!$philhealthRecord) {
            $philhealthRecord = Philhealth::orderBy('salary_from', 'desc')->first();
        }

        $philhealthRate = $philhealthRecord ? (float)$philhealthRecord->contribution_rate : 0.0;
        $totalPhilhealth = $monthlySalary * ($philhealthRate / 100);
        $philhealthEmployeeShare = $totalPhilhealth / 2;
        $philhealthEmployerShare = $totalPhilhealth / 2;

        // 3. Pag-IBIG (HDMF) Calculation
        $pagibigRecord = Pagibig::where('salary_from', '<=', $monthlySalary)
            ->where('salary_to', '>=', $monthlySalary)
            ->first();

        if (!$pagibigRecord) {
            $pagibigRecord = Pagibig::orderBy('salary_from', 'desc')->first();
        }

        $pagibigEmpRate = $pagibigRecord ? (float)$pagibigRecord->employee_rate : 0.0;
        $pagibigEmployerRate = $pagibigRecord ? (float)$pagibigRecord->employer_rate : 0.0;
        $maxContribution = $pagibigRecord ? (float)$pagibigRecord->maximum_contribution : 100.0;

        $pagibigEmployeeShare = min($monthlySalary * ($pagibigEmpRate / 100), $maxContribution);
        $pagibigEmployerShare = min($monthlySalary * ($pagibigEmployerRate / 100), $maxContribution);

        // 4. Tax Calculation (Withholding Tax)
        $taxRecord = Tax::where('salary_from', '<=', $monthlySalary)
            ->where('salary_to', '>=', $monthlySalary)
            ->first();

        if (!$taxRecord) {
            $taxRecord = Tax::orderBy('salary_from', 'desc')->first();
        }

        $baseTax = $taxRecord ? (float)$taxRecord->base_tax : 0.0;
        $taxRate = $taxRecord ? (float)$taxRecord->tax_rate : 0.0;
        $excessOf = $taxRecord ? (float)$taxRecord->salary_from : 0.0;

        $withholdingTax = $baseTax + (($monthlySalary - $excessOf) * ($taxRate / 100));
        if ($withholdingTax < 0) {
            $withholdingTax = 0.0;
        }

        return [
            'sss' => [
                'employee_share' => $sssEmployeeShare,
                'employer_share' => $sssEmployerShare,
                'total' => $sssEmployeeShare + $sssEmployerShare,
            ],
            'philhealth' => [
                'employee_share' => $philhealthEmployeeShare,
                'employer_share' => $philhealthEmployerShare,
                'total' => $philhealthEmployeeShare + $philhealthEmployerShare,
            ],
            'pagibig' => [
                'employee_share' => $pagibigEmployeeShare,
                'employer_share' => $pagibigEmployerShare,
                'total' => $pagibigEmployeeShare + $pagibigEmployerShare,
            ],
            'tax' => [
                'employee_share' => $withholdingTax,
                'employer_share' => 0.0,
                'total' => $withholdingTax,
            ]
        ];
    }
}
