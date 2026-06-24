<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Payroll_Deduction;
use App\Models\Attendance;
use App\Models\Leave_Request;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->endOfMonth()->toDateString());

        // Parse date objects
        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        // 1. Total Payroll Cost (Gross Pay in period)
        $totalPayrollCost = Payroll::whereBetween('payroll_date', [$start, $end])->sum('gross_pay');

        // 2. Taxes Withheld (deduction_id = 4 in period)
        $taxesWithheld = Payroll_Deduction::whereHas('payroll', function ($query) use ($start, $end) {
            $query->whereBetween('payroll_date', [$start, $end]);
        })->where('deduction_id', 4)->sum('deduction_amount');

        // 3. Total Overtime Hours
        $totalOvertimeHours = Attendance::whereBetween('date', [$start, $end])->sum('overtime_hours');

        // 4. Pending Leaves (Current active total, doesn't depend strictly on range)
        $pendingLeaves = Leave_Request::where('status', 'Pending')->count();

        // Previous Month Comparison for Stats
        $prevStart = Carbon::parse($dateFrom)->subMonth()->startOfMonth();
        $prevEnd = Carbon::parse($dateFrom)->subMonth()->endOfMonth();

        $prevPayrollCost = Payroll::whereBetween('payroll_date', [$prevStart, $prevEnd])->sum('gross_pay');
        $prevTaxes = Payroll_Deduction::whereHas('payroll', function ($query) use ($prevStart, $prevEnd) {
            $query->whereBetween('payroll_date', [$prevStart, $prevEnd]);
        })->where('deduction_id', 4)->sum('deduction_amount');
        $prevOvertime = Attendance::whereBetween('date', [$prevStart, $prevEnd])->sum('overtime_hours');

        // Percentages
        $costChange = $prevPayrollCost > 0 ? (($totalPayrollCost - $prevPayrollCost) / $prevPayrollCost) * 100 : 0;
        $taxChange = $prevTaxes > 0 ? (($taxesWithheld - $prevTaxes) / $prevTaxes) * 100 : 0;
        $overtimeChange = $prevOvertime > 0 ? (($totalOvertimeHours - $prevOvertime) / $prevOvertime) * 100 : 0;

        return view('admin.reports.reports', compact(
            'dateFrom',
            'dateTo',
            'totalPayrollCost',
            'taxesWithheld',
            'totalOvertimeHours',
            'pendingLeaves',
            'costChange',
            'taxChange',
            'overtimeChange'
        ));
    }

    public function details(Request $request, $type)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->endOfMonth()->toDateString());

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        if ($type === 'payroll') {
            $payrolls = Payroll::with('employee')
                ->whereBetween('payroll_date', [$start, $end])
                ->get();

            $data = $payrolls->map(function ($p) {
                return [
                    'employee' => $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                    'start_date' => $p->payroll_period_start,
                    'end_date' => $p->payroll_period_end,
                    'pay_date' => $p->payroll_date,
                    'basic_salary' => (float)$p->basic_salary,
                    'overtime_pay' => (float)$p->overtime_pay,
                    'gross_pay' => (float)$p->gross_pay,
                    'total_deductions' => (float)$p->total_deductions,
                    'net_pay' => (float)$p->net_pay,
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        }

        if ($type === 'tax') {
            $payrolls = Payroll::with(['employee', 'deductions'])
                ->whereBetween('payroll_date', [$start, $end])
                ->get();

            $data = $payrolls->map(function ($p) {
                return [
                    'employee' => $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                    'pay_date' => $p->payroll_date,
                    'sss' => (float)$p->sss,
                    'philhealth' => (float)$p->philhealth,
                    'hdmf' => (float)$p->hdmf,
                    'tax' => (float)$p->tax,
                    'total_deductions' => (float)$p->total_deductions,
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        }

        if ($type === 'departmental') {
            $payrolls = Payroll::with('employee.department')
                ->whereBetween('payroll_date', [$start, $end])
                ->get();

            $deptData = [];
            foreach ($payrolls->groupBy('employee.department_id') as $deptId => $deptPayrolls) {
                $dept = $deptPayrolls->first()->employee->department ?? null;
                $deptName = $dept ? $dept->department_name : 'No Department';
                $deptData[] = [
                    'department_name' => $deptName,
                    'employee_count' => $deptPayrolls->pluck('employee_id')->unique()->count(),
                    'total_basic' => (float)$deptPayrolls->sum('basic_salary'),
                    'total_overtime' => (float)$deptPayrolls->sum('overtime_pay'),
                    'total_gross' => (float)$deptPayrolls->sum('gross_pay'),
                    'total_deductions' => (float)$deptPayrolls->sum('total_deductions'),
                    'total_net' => (float)$deptPayrolls->sum('net_pay'),
                ];
            }

            return response()->json(['success' => true, 'data' => $deptData]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid report type specified.']);
    }

    public function export(Request $request, $type)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->endOfMonth()->toDateString());

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $filename = "{$type}_report_{$dateFrom}_to_{$dateTo}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($type, $start, $end) {
            $file = fopen('php://output', 'w');

            if ($type === 'payroll') {
                fputcsv($file, ['Employee', 'Period Start', 'Period End', 'Pay Date', 'Basic Salary (PHP)', 'Overtime Pay (PHP)', 'Gross Pay (PHP)', 'Total Deductions (PHP)', 'Net Pay (PHP)']);
                
                $payrolls = Payroll::with('employee')
                    ->whereBetween('payroll_date', [$start, $end])
                    ->get();

                foreach ($payrolls as $p) {
                    fputcsv($file, [
                        $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                        $p->payroll_period_start,
                        $p->payroll_period_end,
                        $p->payroll_date,
                        $p->basic_salary,
                        $p->overtime_pay,
                        $p->gross_pay,
                        $p->total_deductions,
                        $p->net_pay
                    ]);
                }
            } elseif ($type === 'tax') {
                fputcsv($file, ['Employee', 'Pay Date', 'SSS (PHP)', 'PhilHealth (PHP)', 'Pag-IBIG (PHP)', 'Tax (PHP)', 'Total Deductions (PHP)']);

                $payrolls = Payroll::with(['employee', 'deductions'])
                    ->whereBetween('payroll_date', [$start, $end])
                    ->get();

                foreach ($payrolls as $p) {
                    fputcsv($file, [
                        $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                        $p->payroll_date,
                        $p->sss,
                        $p->philhealth,
                        $p->hdmf,
                        $p->tax,
                        $p->total_deductions
                    ]);
                }
            } elseif ($type === 'departmental') {
                fputcsv($file, ['Department Name', 'Employee Count', 'Total Basic Salary (PHP)', 'Total Overtime Pay (PHP)', 'Total Gross Pay (PHP)', 'Total Deductions (PHP)', 'Total Net Pay (PHP)']);

                $payrolls = Payroll::with('employee.department')
                    ->whereBetween('payroll_date', [$start, $end])
                    ->get();

                foreach ($payrolls->groupBy('employee.department_id') as $deptId => $deptPayrolls) {
                    $dept = $deptPayrolls->first()->employee->department ?? null;
                    fputcsv($file, [
                        $dept ? $dept->department_name : 'No Department',
                        $deptPayrolls->pluck('employee_id')->unique()->count(),
                        $deptPayrolls->sum('basic_salary'),
                        $deptPayrolls->sum('overtime_pay'),
                        $deptPayrolls->sum('gross_pay'),
                        $deptPayrolls->sum('total_deductions'),
                        $deptPayrolls->sum('net_pay')
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
