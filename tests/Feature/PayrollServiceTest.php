<?php

use App\Models\Employee;
use App\Models\Sss;
use App\Models\Philhealth;
use App\Models\Pagibig;
use App\Models\Tax;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('computes payroll contributions correctly', function () {
    // 1. Arrange default brackets/rates
    Sss::create([
        'sss_range_from' => 10000.00,
        'sss_range_to' => 20000.00,
        'monthly_salary_credit' => 15000.00,
        'employee_share' => 675.00,
        'employer_share' => 1425.00,
        'total_contribution' => 2100.00,
    ]);

    Philhealth::create([
        'salary_from' => 10000.00,
        'salary_to' => 20000.00,
        'contribution_rate' => 4.00, // 4%
    ]);

    Pagibig::create([
        'salary_from' => 10000.00,
        'salary_to' => 20000.00,
        'employee_rate' => 2.00, // 2%
        'employer_rate' => 2.00, // 2%
        'maximum_contribution' => 100.00,
    ]);

    Tax::create([
        'salary_from' => 10000.00,
        'salary_to' => 20000.00,
        'base_tax' => 0.00,
        'tax_rate' => 15.00, // 15% on excess of 10000
    ]);

    $employee = new Employee();
    $employee->salary_rate = 15000.00;

    // 2. Act
    $service = new PayrollService();
    $result = $service->compute($employee, 15000.00);

    // 3. Assert
    // SSS
    expect($result['sss']['employee_share'])->toBe(675.00);
    expect($result['sss']['employer_share'])->toBe(1425.00);

    // PhilHealth: 15000 * 4% = 600 total -> 300 employee / 300 employer
    expect($result['philhealth']['employee_share'])->toBe(300.00);
    expect($result['philhealth']['employer_share'])->toBe(300.00);

    // Pag-IBIG: 15000 * 2% = 300, capped at max contribution of 100
    expect($result['pagibig']['employee_share'])->toBe(100.00);
    expect($result['pagibig']['employer_share'])->toBe(100.00);

    // Tax: base_tax (0) + (15000 - 10000) * 15% = 5000 * 0.15 = 750
    expect($result['tax']['employee_share'])->toBe(750.00);
});

test('payroll run creates payroll record and deductions in database', function () {
    // 1. Arrange default brackets/rates
    Sss::create([
        'sss_range_from' => 10000.00,
        'sss_range_to' => 20000.00,
        'monthly_salary_credit' => 15000.00,
        'employee_share' => 675.00,
        'employer_share' => 1425.00,
        'total_contribution' => 2100.00,
    ]);

    Philhealth::create([
        'salary_from' => 10000.00,
        'salary_to' => 20000.00,
        'contribution_rate' => 4.00,
    ]);

    Pagibig::create([
        'salary_from' => 10000.00,
        'salary_to' => 20000.00,
        'employee_rate' => 2.00,
        'employer_rate' => 2.00,
        'maximum_contribution' => 100.00,
    ]);

    Tax::create([
        'salary_from' => 10000.00,
        'salary_to' => 20000.00,
        'base_tax' => 0.00,
        'tax_rate' => 15.00,
    ]);

    // Create an employee
    $employee = Employee::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'salary_rate' => 15000.00,
        'sex' => 'Male',
        'employment_status' => 'Regular',
        'marital_status' => 'Single',
    ]);

    // We need to authenticate a user since the route is protected by 'auth' middleware
    $user = \App\Models\User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    // Act: Call the post route
    $response = $this->actingAs($user)
        ->post(route('payroll.run', [
            'employee' => $employee->employee_id,
            'from' => '2026-05-01',
            'to' => '2026-05-31',
        ]));

    // Assert
    $response->assertStatus(200);

    // Verify payroll record was created
    $this->assertDatabaseHas('payroll', [
        'employee_id' => $employee->employee_id,
        'basic_salary' => 0.00, // no attendance present status recorded yet
        'net_pay' => -1825.00, // basic salary (0) - total deductions (1825) = -1825.00
    ]);

    // Verify payroll deductions were saved
    $this->assertDatabaseHas('payroll_deduction', [
        'deduction_id' => 1, // SSS
        'deduction_amount' => 675.00,
    ]);
    $this->assertDatabaseHas('payroll_deduction', [
        'deduction_id' => 2, // PhilHealth
        'deduction_amount' => 300.00,
    ]);
    $this->assertDatabaseHas('payroll_deduction', [
        'deduction_id' => 3, // Pag-IBIG
        'deduction_amount' => 100.00,
    ]);
    $this->assertDatabaseHas('payroll_deduction', [
        'deduction_id' => 4, // Tax
        'deduction_amount' => 750.00,
    ]);
});
