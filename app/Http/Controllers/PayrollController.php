<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Payroll_Deduction as PayrollDeduction;
use App\Services\PayrollService;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->endOfMonth()->toDateString());

        $employees = Employee::with(['department', 'position'])->get();

        // Fixed: removed the overwriting second query so date-range filtering works correctly
        $payrolls = Payroll::with(['employee', 'deductions'])
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('payroll_period_start', [$from, $to])
                      ->orWhereBetween('payroll_period_end', [$from, $to]);
            })
            ->latest()
            ->get();

        return view('admin.employees.payroll_run', compact('employees', 'payrolls'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
        ]);

        $from = $request->from;
        $to   = $request->to;

        $employees = Employee::where('status', 'active')->get();

        return view('admin.employees.payroll_run', compact('employees', 'from', 'to'));
    }

    // Fixed: added PayrollService injection in the method signature
    public function runForEmployee(Employee $employee, $from, $to, PayrollService $payrollService)
    {
        // 1. Fetch attendances within the pay period
        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$from, $to])
            ->get();

        // 2. Calculate attendance metrics using case-insensitive, trimmed comparison
        $daysWorked = $attendances->filter(function ($att) {
            return strtolower(trim($att->status)) === 'present';
        })->count();

        $absentDays = $attendances->filter(function ($att) {
            return strtolower(trim($att->status)) === 'absent';
        })->count();

        $totalLateMinutes      = $attendances->sum('late_minutes');
        $totalUndertimeMinutes = $attendances->sum('undertime_minutes');

        // 3. Compute earnings
        $dailyRate   = $employee->daily_rate;
        $basicSalary = $dailyRate * $daysWorked;
        $hourlyRate  = $dailyRate / 8;
        $minuteRate  = $hourlyRate / 60;

        // 4. Compute attendance-based deductions
        $absenceDeduction   = $dailyRate * $absentDays;
        $lateDeduction      = $minuteRate * $totalLateMinutes;
        $undertimeDeduction = $minuteRate * $totalUndertimeMinutes;

        // 5. Compute government contributions dynamically from database brackets
        $monthlySalary = $employee->salary_rate ?? ($employee->position->basic_salary ?? 0);
        $breakdown     = $payrollService->compute($employee, $monthlySalary);

        $sss       = $breakdown['sss']['employee_share'] ?? 0;
        $philhealth = $breakdown['philhealth']['employee_share'] ?? 0;
        $pagibig   = $breakdown['pagibig']['employee_share'] ?? 0;
        $tax       = $breakdown['tax']['employee_share'] ?? 0;

        // 6. Total deductions & net pay
        $totalDeductions =
            $absenceDeduction +
            $lateDeduction +
            $undertimeDeduction +
            $sss +
            $philhealth +
            $pagibig +
            $tax;

        $grossPay = $basicSalary;
        $netPay   = $grossPay - $totalDeductions;

        // 7. Save payroll record — corrected: single employee_id using the correct PK, no phantom columns
        $payroll = Payroll::create([
            'employee_id'          => $employee->employee_id,
            'payroll_period_start' => $from,
            'payroll_period_end'   => $to,
            'payroll_date'         => now(),
            'basic_salary'         => $basicSalary,
            'overtime_pay'         => 0,
            'gross_pay'            => $grossPay,
            'total_deductions'     => $totalDeductions,
            'net_pay'              => $netPay,
        ]);

        // 8. Ensure deduction types exist, then save each deduction line item
        \App\Models\Deduction::firstOrCreate(['deduction_id' => 1], ['deduction_name' => 'SSS']);
        \App\Models\Deduction::firstOrCreate(['deduction_id' => 2], ['deduction_name' => 'PhilHealth']);
        \App\Models\Deduction::firstOrCreate(['deduction_id' => 3], ['deduction_name' => 'Pag-IBIG']);
        \App\Models\Deduction::firstOrCreate(['deduction_id' => 4], ['deduction_name' => 'Tax']);

        PayrollDeduction::create([
            'payroll_id'       => $payroll->payroll_id,
            'deduction_id'     => 1,
            'deduction_amount' => $sss,
        ]);

        PayrollDeduction::create([
            'payroll_id'       => $payroll->payroll_id,
            'deduction_id'     => 2,
            'deduction_amount' => $philhealth,
        ]);

        PayrollDeduction::create([
            'payroll_id'       => $payroll->payroll_id,
            'deduction_id'     => 3,
            'deduction_amount' => $pagibig,
        ]);

        // Fixed: tax deduction was never saved before — now it is
        PayrollDeduction::create([
            'payroll_id'       => $payroll->payroll_id,
            'deduction_id'     => 4,
            'deduction_amount' => $tax,
        ]);

        // 9. Return payslip view — single return, removed the unreachable second return
        return view('user.payslip.payslip', [
            'employee'  => $employee,
            'payroll'   => $payroll,
            'breakdown' => $breakdown,
        ]);
    }

    // ─────────────────────────────────────────────
    //  USER: View own payslip
    // ─────────────────────────────────────────────
    public function myPayslip(Request $request, PayrollService $payrollService)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return view('user.payslip.payslip', [
                'error'     => 'No employee record found for your account. Please contact admin.',
                'employee'  => null,
                'payroll'   => null,
                'breakdown' => null,
            ]);
        }

        // Get specific or latest payroll
        $payrollId = $request->query('payroll_id');
        if ($payrollId) {
            $payroll = Payroll::where('employee_id', $employee->employee_id)
                ->where('payroll_id', $payrollId)
                ->first();
        } else {
            $payroll = Payroll::where('employee_id', $employee->employee_id)
                ->latest('payroll_period_end')
                ->first();
        }

        if (!$payroll) {
            return view('user.payslip.payslip', [
                'error'     => 'No payslip found for your account. Please wait for the admin to process your payroll.',
                'employee'  => $employee,
                'payroll'   => null,
                'breakdown' => null,
            ]);
        }

        $monthlySalary = $employee->salary_rate ?? ($employee->position->basic_salary ?? 0);
        $breakdown     = $payrollService->compute($employee, $monthlySalary);

        return view('user.payslip.payslip', compact('employee', 'payroll', 'breakdown'));
    }

    // ─────────────────────────────────────────────
    //  ADMIN: Preview a payslip
    // ─────────────────────────────────────────────
    public function payslipPreview(Request $request, PayrollService $payrollService)
    {
        $payrollId = $request->query('payroll_id');
        $payroll   = Payroll::with('employee')->findOrFail($payrollId);
        $employee  = $payroll->employee;

        $monthlySalary = $employee->salary_rate ?? ($employee->position->basic_salary ?? 0);
        $breakdown     = $payrollService->compute($employee, $monthlySalary);

        return view('user.payslip.payslip', compact('employee', 'payroll', 'breakdown'));
    }
}
