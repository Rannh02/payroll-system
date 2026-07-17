<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Payroll_Deduction;
use App\Models\Attendance;
use App\Models\Leave_Request;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Sss;
use App\Models\Philhealth;
use App\Models\Pagibig;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    /* -----------------------------------------------------------------------
     *  Finance Admin – Deductions & Contributions Section
     * ---------------------------------------------------------------------- */

    /**
     * Government Contributions: SSS / PhilHealth / Pag-IBIG / BIR
     * Shows per-employee breakdown from actual payroll records.
     */
    public function governmentContributions(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo   = $request->query('date_to',   Carbon::now()->endOfMonth()->toDateString());

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end   = Carbon::parse($dateTo)->endOfDay();

        // Pull every payroll record with its deductions & employee
        $payrolls = Payroll::with(['employee', 'deductions'])
            ->whereBetween('payroll_date', [$start, $end])
            ->get();

        // Build per-row data
        $rows = $payrolls->map(function ($p) {
            $sss        = (float) ($p->deductions->where('deduction_id', 1)->first()->deduction_amount ?? 0);
            $philhealth = (float) ($p->deductions->where('deduction_id', 2)->first()->deduction_amount ?? 0);
            $hdmf       = (float) ($p->deductions->where('deduction_id', 3)->first()->deduction_amount ?? 0);
            $tax        = (float) ($p->deductions->where('deduction_id', 4)->first()->deduction_amount ?? 0);
            return [
                'employee'        => $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                'department'      => $p->employee->department->department_name ?? '—',
                'period_start'    => $p->payroll_period_start,
                'period_end'      => $p->payroll_period_end,
                'gross_pay'       => (float) $p->gross_pay,
                'sss'             => $sss,
                'philhealth'      => $philhealth,
                'hdmf'            => $hdmf,
                'tax'             => $tax,
                'total_gov'       => $sss + $philhealth + $hdmf + $tax,
            ];
        });

        // Reference rate tables
        $sssRates        = Sss::orderBy('sss_range_from')->get();
        $philhealthRates = Philhealth::orderBy('salary_from')->get();
        $pagibigRates    = Pagibig::orderBy('salary_from')->get();
        $taxRates        = Tax::orderBy('salary_from')->get();

        // Totals
        $totals = [
            'sss'        => $rows->sum('sss'),
            'philhealth' => $rows->sum('philhealth'),
            'hdmf'       => $rows->sum('hdmf'),
            'tax'        => $rows->sum('tax'),
            'total_gov'  => $rows->sum('total_gov'),
        ];

        return view('finance_admin.deductions.government', compact(
            'rows', 'totals', 'dateFrom', 'dateTo',
            'sssRates', 'philhealthRates', 'pagibigRates', 'taxRates'
        ));
    }

    /**
     * Loan Deductions – deduction_id = 5 (loans / cash advances).
     */
    public function loanDeductions(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo   = $request->query('date_to',   Carbon::now()->endOfMonth()->toDateString());

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end   = Carbon::parse($dateTo)->endOfDay();

        // deduction_id = 5  →  loan / cash advance
        $deductions = Payroll_Deduction::with(['payroll.employee.department'])
            ->where('deduction_id', 5)
            ->whereHas('payroll', fn($q) => $q->whereBetween('payroll_date', [$start, $end]))
            ->get();

        $rows = $deductions->map(function ($d) {
            $emp = $d->payroll->employee ?? null;
            return [
                'employee'     => $emp ? trim("{$emp->first_name} {$emp->last_name}") : 'Unknown',
                'department'   => $emp->department->department_name ?? '—',
                'period_start' => $d->payroll->payroll_period_start,
                'period_end'   => $d->payroll->payroll_period_end,
                'pay_date'     => $d->payroll->payroll_date,
                'amount'       => (float) $d->deduction_amount,
            ];
        })->sortBy('employee')->values();

        $totalAmount = $rows->sum('amount');

        return view('finance_admin.deductions.loans', compact(
            'rows', 'totalAmount', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Other Deductions – anything with deduction_id >= 6.
     */
    public function otherDeductions(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo   = $request->query('date_to',   Carbon::now()->endOfMonth()->toDateString());

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end   = Carbon::parse($dateTo)->endOfDay();

        $deductions = Payroll_Deduction::with(['payroll.employee.department', 'deduction'])
            ->where('deduction_id', '>=', 6)
            ->whereHas('payroll', fn($q) => $q->whereBetween('payroll_date', [$start, $end]))
            ->get();

        $rows = $deductions->map(function ($d) {
            $emp = $d->payroll->employee ?? null;
            return [
                'employee'       => $emp ? trim("{$emp->first_name} {$emp->last_name}") : 'Unknown',
                'department'     => $emp->department->department_name ?? '—',
                'deduction_name' => $d->deduction->deduction_name ?? "Deduction #{$d->deduction_id}",
                'period_start'   => $d->payroll->payroll_period_start,
                'period_end'     => $d->payroll->payroll_period_end,
                'pay_date'       => $d->payroll->payroll_date,
                'amount'         => (float) $d->deduction_amount,
            ];
        })->sortBy('employee')->values();

        $totalAmount = $rows->sum('amount');

        return view('finance_admin.deductions.other', compact(
            'rows', 'totalAmount', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Pending Claims (Leave claims still awaiting processing).
     */
    public function pendingClaims(Request $request)
    {
        $claims = Leave_Request::with('employee')
            ->where('status', 'Pending')
            ->latest()
            ->get();

        return view('finance_admin.allowances.pending', compact('claims'));
    }

    /**
     * Approved Claims.
     */
    public function approvedClaims(Request $request)
    {
        $claims = Leave_Request::with('employee')
            ->where('status', 'Approved')
            ->latest()
            ->get();

        return view('finance_admin.allowances.approved', compact('claims'));
    }

    /* -----------------------------------------------------------------------
     *  Finance Admin – Reports & Analytics Section
     * ---------------------------------------------------------------------- */

    /**
     * Payroll Summary Report
     */
    public function payrollSummary(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo   = $request->query('date_to',   Carbon::now()->endOfMonth()->toDateString());
        $deptId   = $request->query('department_id');
        $search   = $request->query('search');

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end   = Carbon::parse($dateTo)->endOfDay();

        $query = Payroll::with(['employee.department', 'employee.position'])
            ->whereBetween('payroll_date', [$start, $end]);

        if ($deptId) {
            $query->whereHas('employee', function($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        // Handle CSV Export
        if ($request->query('export') === 'csv') {
            $filename = "payroll_summary_report_{$dateFrom}_to_{$dateTo}.csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            
            $callback = function () use ($query) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Employee #', 'Employee Name', 'Department', 'Position', 'Pay Date', 
                    'Period Start', 'Period End', 'Basic Salary (PHP)', 'Overtime Pay (PHP)', 
                    'Gross Pay (PHP)', 'SSS (PHP)', 'PhilHealth (PHP)', 'Pag-IBIG (PHP)', 
                    'BIR Tax (PHP)', 'Total Deductions (PHP)', 'Net Pay (PHP)', 'Status'
                ]);

                $records = $query->get();
                foreach ($records as $p) {
                    fputcsv($file, [
                        $p->employee->employee_number ?? '—',
                        $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                        $p->employee->department->department_name ?? '—',
                        $p->employee->position->position_name ?? '—',
                        $p->payroll_date,
                        $p->payroll_period_start,
                        $p->payroll_period_end,
                        $p->basic_salary,
                        $p->overtime_pay,
                        $p->gross_pay,
                        $p->sss,
                        $p->philhealth,
                        $p->hdmf,
                        $p->tax,
                        $p->total_deductions,
                        $p->net_pay,
                        $p->status ?? 'pending'
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // Stats (unpaginated total)
        $statsQuery = clone $query;
        $totalGross = $statsQuery->sum('gross_pay');
        $totalNet = $statsQuery->sum('net_pay');
        $totalDeductions = $statsQuery->sum('total_deductions');
        $totalBasic = $statsQuery->sum('basic_salary');
        $totalOvertime = $statsQuery->sum('overtime_pay');

        $payrolls = $query->latest('payroll_date')->paginate(15)->withQueryString();
        $departments = Department::orderBy('department_name')->get();

        return view('finance_admin.reports.payroll_summary', compact(
            'payrolls', 'departments', 'dateFrom', 'dateTo', 'deptId', 'search',
            'totalGross', 'totalNet', 'totalDeductions', 'totalBasic', 'totalOvertime'
        ));
    }

    /**
     * Government Remittance Report
     */
    public function governmentRemittance(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo   = $request->query('date_to',   Carbon::now()->endOfMonth()->toDateString());
        $type     = $request->query('contribution_type', 'all'); // all, sss, philhealth, hdmf
        $search   = $request->query('search');

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end   = Carbon::parse($dateTo)->endOfDay();

        $query = Payroll::with(['employee.department'])
            ->whereBetween('payroll_date', [$start, $end]);

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        $payrolls = $query->get();

        $rows = $payrolls->map(function ($p) {
            $shares = $this->getGovtShares($p->basic_salary, $p->sss, $p->philhealth, $p->hdmf);
            return [
                'employee_number' => $p->employee->employee_number ?? '—',
                'name'            => $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                'department'      => $p->employee->department->department_name ?? '—',
                'pay_date'        => $p->payroll_date,
                'sss_ee'          => $shares['sss_ee'],
                'sss_er'          => $shares['sss_er'],
                'sss_total'       => $shares['sss_ee'] + $shares['sss_er'],
                'ph_ee'           => $shares['ph_ee'],
                'ph_er'           => $shares['ph_er'],
                'ph_total'        => $shares['ph_ee'] + $shares['ph_er'],
                'hdmf_ee'         => $shares['hdmf_ee'],
                'hdmf_er'         => $shares['hdmf_er'],
                'hdmf_total'      => $shares['hdmf_ee'] + $shares['hdmf_er'],
            ];
        });

        // Handle CSV Export
        if ($request->query('export') === 'csv') {
            $filename = "government_remittance_{$type}_report_{$dateFrom}_to_{$dateTo}.csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            
            $callback = function () use ($rows, $type) {
                $file = fopen('php://output', 'w');
                if ($type === 'sss') {
                    fputcsv($file, ['Employee #', 'Employee Name', 'Department', 'Pay Date', 'SSS Employee Share (PHP)', 'SSS Employer Share (PHP)', 'SSS Total Contribution (PHP)']);
                } elseif ($type === 'philhealth') {
                    fputcsv($file, ['Employee #', 'Employee Name', 'Department', 'Pay Date', 'PhilHealth Employee Share (PHP)', 'PhilHealth Employer Share (PHP)', 'PhilHealth Total Contribution (PHP)']);
                } elseif ($type === 'hdmf') {
                    fputcsv($file, ['Employee #', 'Employee Name', 'Department', 'Pay Date', 'Pag-IBIG Employee Share (PHP)', 'Pag-IBIG Employer Share (PHP)', 'Pag-IBIG Total Contribution (PHP)']);
                } else {
                    fputcsv($file, [
                        'Employee #', 'Employee Name', 'Department', 'Pay Date', 
                        'SSS EE', 'SSS ER', 'SSS Total', 
                        'PhilHealth EE', 'PhilHealth ER', 'PhilHealth Total', 
                        'Pag-IBIG EE', 'Pag-IBIG ER', 'Pag-IBIG Total', 
                        'Grand Total EE', 'Grand Total ER', 'Combined Grand Total'
                    ]);
                }

                foreach ($rows as $r) {
                    if ($type === 'sss') {
                        fputcsv($file, [$r['employee_number'], $r['name'], $r['department'], $r['pay_date'], $r['sss_ee'], $r['sss_er'], $r['sss_total']]);
                    } elseif ($type === 'philhealth') {
                        fputcsv($file, [$r['employee_number'], $r['name'], $r['department'], $r['pay_date'], $r['ph_ee'], $r['ph_er'], $r['ph_total']]);
                    } elseif ($type === 'hdmf') {
                        fputcsv($file, [$r['employee_number'], $r['name'], $r['department'], $r['pay_date'], $r['hdmf_ee'], $r['hdmf_er'], $r['hdmf_total']]);
                    } else {
                        fputcsv($file, [
                            $r['employee_number'], $r['name'], $r['department'], $r['pay_date'],
                            $r['sss_ee'], $r['sss_er'], $r['sss_total'],
                            $r['ph_ee'], $r['ph_er'], $r['ph_total'],
                            $r['hdmf_ee'], $r['hdmf_er'], $r['hdmf_total'],
                            $r['sss_ee'] + $r['ph_ee'] + $r['hdmf_ee'],
                            $r['sss_er'] + $r['ph_er'] + $r['hdmf_er'],
                            $r['sss_total'] + $r['ph_total'] + $r['hdmf_total']
                        ]);
                    }
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // Totals
        $totals = [
            'sss_ee'     => $rows->sum('sss_ee'),
            'sss_er'     => $rows->sum('sss_er'),
            'sss_total'  => $rows->sum('sss_total'),
            'ph_ee'      => $rows->sum('ph_ee'),
            'ph_er'      => $rows->sum('ph_er'),
            'ph_total'   => $rows->sum('ph_total'),
            'hdmf_ee'    => $rows->sum('hdmf_ee'),
            'hdmf_er'    => $rows->sum('hdmf_er'),
            'hdmf_total' => $rows->sum('hdmf_total'),
        ];

        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 15;
        $paginatedRows = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => request()->query()]
        );

        return view('finance_admin.reports.government_remittance', compact(
            'paginatedRows', 'totals', 'dateFrom', 'dateTo', 'type', 'search'
        ));
    }

    /**
     * Tax (BIR) Report
     */
    public function taxBIR(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo   = $request->query('date_to',   Carbon::now()->endOfMonth()->toDateString());
        $search   = $request->query('search');

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end   = Carbon::parse($dateTo)->endOfDay();

        $query = Payroll::with(['employee.department'])
            ->whereBetween('payroll_date', [$start, $end]);

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        // Handle CSV Export
        if ($request->query('export') === 'csv') {
            $filename = "tax_bir_report_{$dateFrom}_to_{$dateTo}.csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            
            $callback = function () use ($query) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Employee #', 'Employee Name', 'Department', 'Pay Date', 
                    'Gross Compensation (PHP)', 'Non-Taxable Statutory Deductions (PHP)', 
                    'Taxable Compensation (PHP)', 'BIR Withholding Tax (PHP)'
                ]);

                $records = $query->get();
                foreach ($records as $p) {
                    $statutory = (float)$p->sss + (float)$p->philhealth + (float)$p->hdmf;
                    $taxable = max(0.0, (float)$p->gross_pay - $statutory);
                    fputcsv($file, [
                        $p->employee->employee_number ?? '—',
                        $p->employee ? trim("{$p->employee->first_name} {$p->employee->last_name}") : 'Unknown',
                        $p->employee->department->department_name ?? '—',
                        $p->payroll_date,
                        $p->gross_pay,
                        $statutory,
                        $taxable,
                        $p->tax
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // Stats
        $statsQuery = clone $query;
        $payrollsForStats = $statsQuery->get();
        $totalGross = $payrollsForStats->sum('gross_pay');
        $totalStatutory = $payrollsForStats->sum(function($p) {
            return (float)$p->sss + (float)$p->philhealth + (float)$p->hdmf;
        });
        $totalTaxable = max(0.0, $totalGross - $totalStatutory);
        $totalTax = $payrollsForStats->sum('tax');

        $payrolls = $query->latest('payroll_date')->paginate(15)->withQueryString();

        return view('finance_admin.reports.tax_bir', compact(
            'payrolls', 'dateFrom', 'dateTo', 'search',
            'totalGross', 'totalStatutory', 'totalTaxable', 'totalTax'
        ));
    }

    /**
     * Payroll Cost Trends
     */
    public function payrollCostTrends(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);

        // Fetch payroll for the entire year
        $payrolls = Payroll::with(['employee.department'])
            ->whereYear('payroll_date', $year)
            ->get();

        // Calculate stats per month (1 to 12)
        $monthlyData = collect();
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        foreach ($months as $num => $name) {
            $monthPayrolls = $payrolls->filter(function($p) use ($num) {
                return Carbon::parse($p->payroll_date)->month === $num;
            });

            $monthlyData->put($num, [
                'month_name' => $name,
                'employee_count' => $monthPayrolls->pluck('employee_id')->unique()->count(),
                'gross_pay' => (float)$monthPayrolls->sum('gross_pay'),
                'deductions' => (float)$monthPayrolls->sum('total_deductions'),
                'net_pay' => (float)$monthPayrolls->sum('net_pay'),
                'basic_salary' => (float)$monthPayrolls->sum('basic_salary'),
                'overtime_pay' => (float)$monthPayrolls->sum('overtime_pay'),
            ]);
        }

        // Calculate department distribution for the year
        $deptDistribution = collect();
        $departments = Department::all();
        foreach ($departments as $dept) {
            $deptPayrolls = $payrolls->filter(function($p) use ($dept) {
                return $p->employee && $p->employee->department_id === $dept->department_id;
            });

            if ($deptPayrolls->count() > 0) {
                $deptDistribution->push([
                    'department_name' => $dept->department_name,
                    'total_gross' => (float)$deptPayrolls->sum('gross_pay'),
                    'employee_count' => $deptPayrolls->pluck('employee_id')->unique()->count(),
                ]);
            }
        }

        // YoY (Year over Year) change
        $prevYearPayrolls = Payroll::whereYear('payroll_date', $year - 1)->get();
        $prevYearTotal = (float)$prevYearPayrolls->sum('gross_pay');
        $currYearTotal = (float)$payrolls->sum('gross_pay');

        $yoyChangePercent = 0.0;
        if ($prevYearTotal > 0) {
            $yoyChangePercent = (($currYearTotal - $prevYearTotal) / $prevYearTotal) * 100;
        }

        // Handle CSV Export
        if ($request->query('export') === 'csv') {
            $filename = "payroll_cost_trends_{$year}.csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            
            $callback = function () use ($monthlyData) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Month', 'Employee Count', 'Total Basic Salary (PHP)', 
                    'Total Overtime Pay (PHP)', 'Total Gross Pay (PHP)', 
                    'Total Deductions (PHP)', 'Total Net Pay (PHP)'
                ]);

                foreach ($monthlyData as $row) {
                    fputcsv($file, [
                        $row['month_name'],
                        $row['employee_count'],
                        $row['basic_salary'],
                        $row['overtime_pay'],
                        $row['gross_pay'],
                        $row['deductions'],
                        $row['net_pay']
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // General stats
        $totalGrossExpense = $currYearTotal;
        $totalNetExpense = (float)$payrolls->sum('net_pay');
        $totalDeductionsExpense = (float)$payrolls->sum('total_deductions');
        $avgMonthlyCost = $payrolls->count() > 0 ? $currYearTotal / 12 : 0;

        return view('finance_admin.reports.cost_trends', compact(
            'monthlyData', 'deptDistribution', 'year', 'yoyChangePercent',
            'totalGrossExpense', 'totalNetExpense', 'totalDeductionsExpense', 'avgMonthlyCost'
        ));
    }

    /**
     * Helper to compute government ER and EE shares dynamically based on salary.
     */
    private function getGovtShares($basicSalary, $sssEe, $phEe, $hdmfEe)
    {
        // 1. SSS ER share
        $sssRecord = Sss::where('sss_range_from', '<=', $basicSalary)
            ->where('sss_range_to', '>=', $basicSalary)
            ->first();
        if (!$sssRecord) {
            $sssRecord = Sss::orderBy('sss_range_from', 'desc')->first();
        }
        $sssEr = $sssRecord ? (float)$sssRecord->employer_share : 0.0;
        if ($sssEe <= 0) {
            $sssEr = 0.0;
        }

        // 2. PhilHealth ER share (Equal to EE share)
        $phEr = $phEe;

        // 3. Pag-IBIG ER share
        $pagibigRecord = Pagibig::where('salary_from', '<=', $basicSalary)
            ->where('salary_to', '>=', $basicSalary)
            ->first();
        if (!$pagibigRecord) {
            $pagibigRecord = Pagibig::orderBy('salary_from', 'desc')->first();
        }
        $hdmfEr = 0.0;
        if ($pagibigRecord && $hdmfEe > 0) {
            $pagibigEmployerRate = (float)$pagibigRecord->employer_rate;
            $maxContribution = (float)$pagibigRecord->maximum_contribution;
            $hdmfEr = min($basicSalary * ($pagibigEmployerRate / 100), $maxContribution);
        }

        return [
            'sss_ee'  => (float)$sssEe,
            'sss_er'  => $sssEr,
            'ph_ee'   => (float)$phEe,
            'ph_er'   => (float)$phEr,
            'hdmf_ee' => (float)$hdmfEe,
            'hdmf_er' => $hdmfEr
        ];
    }
}

