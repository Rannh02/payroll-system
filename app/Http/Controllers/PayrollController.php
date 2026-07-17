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

        $employees = Employee::all();

        return view('admin.employees.payroll_run', compact('employees', 'from', 'to'));
    }

    // ─────────────────────────────────────────────
    //  FINANCE ADMIN: Create/Run Payroll
    // ─────────────────────────────────────────────
    public function create(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->endOfMonth()->toDateString());

        $employees = Employee::with(['department', 'position'])->get();

        $payrolls = Payroll::with(['employee', 'deductions'])
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('payroll_period_start', [$from, $to])
                      ->orWhereBetween('payroll_period_end', [$from, $to]);
            })
            ->latest()
            ->get();

        return view('finance admin.payroll.create', compact('employees', 'payrolls', 'from', 'to'));
    }

    // Fixed: added PayrollService injection in the method signature
    /**
     * Compute and save semi-monthly payroll for a single employee based on 15/30 cutoff period.
     * This replaces the daily-rate-based gross pay with a semi-monthly salary basis.
     * Attendance metrics (days worked, late, undertime, overtime) are restricted to the cutoff period.
     * 
     * RATIONALE FOR CHANGES:
     * 1. semiMonthlySalary = Monthly Salary ÷ 2: Standard semi-monthly base earnings for a 15/30 structure.
     * 2. Derived daily, hourly, and per-minute rates are computed using configured working days per month (22 days)
     *    and used strictly to calculate attendance deductions (absences, lates, undertime) and overtime pay.
     * 3. Overtime pay is computed as Overtime Hours × Derived Hourly Rate × 1.25.
     * 4. Gross Pay = Semi-Monthly Salary - Attendance Deductions + Overtime Pay.
     * 5. Total Deductions = Government contributions (SSS + PhilHealth + Pag-IBIG) + Tax.
     * 6. Net Pay = Gross Pay - Total Deductions.
     * 7. Saved basic_salary in database represents the Semi-Monthly base salary.
     */
    public function runForEmployee(Employee $employee, $from, $to, PayrollService $payrollService)
    {
        // 1. Fetch attendances within the selected cutoff period
        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$from, $to])
            ->get();

        // 2. Calculate attendance metrics for deductions and overtime
        $daysWorked = $attendances->filter(function ($att) {
            return strtolower(trim($att->status)) === 'present';
        })->count();

        $absentDays = $attendances->filter(function ($att) {
            return strtolower(trim($att->status)) === 'absent';
        })->count();

        $totalLateMinutes      = $attendances->sum('late_minutes');
        $totalUndertimeMinutes = $attendances->sum('undertime_minutes');
        $totalOvertimeHours    = $attendances->sum('overtime_hours');

        // 3. Compute rates from full monthly salary rate based on configured 22 working days
        $monthlySalary = $employee->salary_rate ?? ($employee->position->basic_salary ?? 0);
        $workingDays   = 22; // Configured average working days per month
        $dailyRate     = $monthlySalary > 0 ? ($monthlySalary / $workingDays) : 0.0;
        $hourlyRate    = $dailyRate / 8;
        $minuteRate    = $hourlyRate / 60;

        // 4. Compute attendance-based deductions
        $absenceDeduction   = $dailyRate * $absentDays;
        $lateDeduction      = $minuteRate * $totalLateMinutes;
        $undertimeDeduction = $minuteRate * $totalUndertimeMinutes;
        $attendanceDeductions = $absenceDeduction + $lateDeduction + $undertimeDeduction;

        // 5. Compute overtime pay (125% regular overtime rate)
        $overtimePay = $totalOvertimeHours * $hourlyRate * 1.25;

        // 6. Compute government contributions dynamically from database brackets using full monthly salary
        $breakdown     = $payrollService->compute($employee, $monthlySalary);

        $sss       = $breakdown['sss']['employee_share'] ?? 0;
        $philhealth = $breakdown['philhealth']['employee_share'] ?? 0;
        $pagibig   = $breakdown['pagibig']['employee_share'] ?? 0;
        $tax       = $breakdown['tax']['employee_share'] ?? 0;

        // 7. Calculate semi-monthly salaries, gross pay, total deductions, and net pay
        $semiMonthlySalary = $monthlySalary / 2; // Semi-monthly salary basis (Monthly Salary ÷ 2)
        
        $grossPay = $semiMonthlySalary - $attendanceDeductions + $overtimePay;
        $totalDeductions = $sss + $philhealth + $pagibig + $tax;
        $netPay   = $grossPay - $totalDeductions;

        $status = 'pending';
        // Auto-flag discrepancies (if net pay is negative, or if they didn't work at all)
        if ($netPay <= 0 || $daysWorked == 0) {
            $status = 'flagged';
        }

        // 8. Save payroll record with computed values
        $payroll = Payroll::create([
            'employee_id'          => $employee->employee_id,
            'payroll_period_start' => $from,
            'payroll_period_end'   => $to,
            'payroll_date'         => now(),
            'basic_salary'         => $semiMonthlySalary, // Gross Salary = Monthly Salary ÷ 2
            'overtime_pay'         => $overtimePay,
            'gross_pay'            => $grossPay,
            'total_deductions'     => $totalDeductions,
            'net_pay'              => $netPay,
            'status'               => $status,
        ]);

        // 9. Ensure deduction types exist, then save each deduction line item
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

        PayrollDeduction::create([
            'payroll_id'       => $payroll->payroll_id,
            'deduction_id'     => 4,
            'deduction_amount' => $tax,
        ]);

        // 10. Return payslip view
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

        // Get specific or latest payroll — employees only see APPROVED payslips
        $payrollId = $request->query('payroll_id');
        if ($payrollId) {
            $payroll = Payroll::where('employee_id', $employee->employee_id)
                ->where('payroll_id', $payrollId)
                ->where('status', 'approved')   // must be approved
                ->first();
        } else {
            $payroll = Payroll::where('employee_id', $employee->employee_id)
                ->where('status', 'approved')   // only show latest approved
                ->latest('payroll_period_end')
                ->first();
        }

        // Check if there is any payroll at all (approved or not) to give a better message
        if (!$payroll) {
            $hasPending = Payroll::where('employee_id', $employee->employee_id)
                ->whereIn('status', ['pending', 'flagged'])
                ->exists();

            $errorMsg = $hasPending
                ? 'Your payslip is currently being reviewed and has not been approved yet. Please check back later.'
                : 'No payslip found for your account. Please wait for the admin to process your payroll.';

            return view('user.payslip.payslip', [
                'error'     => $errorMsg,
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

    // ─────────────────────────────────────────────
    //  FINANCE ADMIN: Payroll History
    // ─────────────────────────────────────────────
    public function history(Request $request)
    {
        $from = $request->input('from', now()->subMonths(3)->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->endOfMonth()->toDateString());

        $payrolls = Payroll::with(['employee', 'deductions'])
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('payroll_period_start', [$from, $to])
                      ->orWhereBetween('payroll_period_end', [$from, $to]);
            })
            ->latest()
            ->paginate(20);

        return view('finance admin.payroll.history', compact('payrolls', 'from', 'to'));
    }

    // ─────────────────────────────────────────────
    //  FINANCE ADMIN: Pending Approvals
    // ─────────────────────────────────────────────
    public function pendingApprovals(Request $request)
    {
        $payrolls = Payroll::with(['employee', 'deductions'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('finance admin.payroll.pending-approvals', compact('payrolls'));
    }

    // ─────────────────────────────────────────────
    //  FINANCE ADMIN: Discrepancy Review
    // ─────────────────────────────────────────────
    public function discrepancyReview(Request $request)
    {
        $payrolls = Payroll::with(['employee', 'deductions'])
            ->where('status', 'flagged')
            ->latest()
            ->paginate(20);

        return view('finance admin.payroll.discrepancy-review', compact('payrolls'));
    }

    public function approve(Payroll $payroll)
    {
        $payroll->update(['status' => 'approved']);
        return back()->with('success', 'Payroll run approved successfully.');
    }

    public function flag(Payroll $payroll)
    {
        $payroll->update(['status' => 'flagged']);
        return back()->with('success', 'Payroll run flagged for discrepancy.');
    }

    // ─────────────────────────────────────────────
    //  PDF DOWNLOAD: Generate and stream payslip PDF
    // ─────────────────────────────────────────────
    public function downloadPayslipPdf(Payroll $payroll, PayrollService $payrollService)
    {
        // Load all required relationships
        $payroll->load(['employee.department', 'employee.position', 'deductions']);
        $employee = $payroll->employee;

        if (!$employee) {
            abort(404, 'Employee record not found for this payroll.');
        }

        // Ownership + approval check for employees
        if (Auth::guard('employee')->check()) {
            $authEmployee = Auth::guard('employee')->user()->employee;
            if (!$authEmployee || $authEmployee->employee_id !== $employee->employee_id) {
                abort(403, 'You are not authorized to download this payslip.');
            }
            // Employees can only download approved payslips
            if ($payroll->status !== 'approved') {
                abort(403, 'This payslip has not been approved yet. Please wait for finance admin approval before downloading.');
            }
        }

        // Compute government contributions breakdown (does not alter payroll data)
        $monthlySalary = $employee->salary_rate ?? ($employee->position->basic_salary ?? 0);
        $breakdown     = $payrollService->compute($employee, $monthlySalary);

        // Attendance deduction sub-totals (derived from stored payroll fields)
        $dailyRate          = $monthlySalary > 0 ? ($monthlySalary / 22) : 0;
        $hourlyRate         = $dailyRate / 8;
        $minuteRate         = $hourlyRate / 60;
        $absenceDeduction   = $dailyRate  * $payroll->absent_days;
        $lateDeduction      = $minuteRate * $payroll->late_minutes;
        $undertimeDeduction = $minuteRate * $payroll->undertime_minutes;
        $attendanceTotal    = $absenceDeduction + $lateDeduction + $undertimeDeduction;

        // Build file name
        $empName       = str_replace(' ', '_', $employee->name);
        $periodStart   = \Carbon\Carbon::parse($payroll->payroll_period_start)->format('Y-m-d');
        $periodEnd     = \Carbon\Carbon::parse($payroll->payroll_period_end)->format('Y-m-d');
        $filename      = "Payslip_{$empName}_{$periodStart}_to_{$periodEnd}.pdf";

        // Render PDF blade and download
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.payslip.pdf', [
            'employee'           => $employee,
            'payroll'            => $payroll,
            'breakdown'          => $breakdown,
            'monthlySalary'      => $monthlySalary,
            'absenceDeduction'   => $absenceDeduction,
            'lateDeduction'      => $lateDeduction,
            'undertimeDeduction' => $undertimeDeduction,
            'attendanceTotal'    => $attendanceTotal,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}

