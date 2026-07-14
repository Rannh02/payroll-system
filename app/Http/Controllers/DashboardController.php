<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Leave_Request;
use App\Models\Department;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $payrollsProcessed = Payroll::count();
        $pendingApprovals = Leave_Request::where('status', 'Pending')->count();
        $totalDepartments = Department::count();

        // Get recent leave requests for the activity feed
        $recentActivities = Leave_Request::with('employee')
            ->latest('created_at')
            ->take(5)
            ->get();

        // Fetch dynamic payroll expense data (last 6 months rolling)
        $payrollLabels = [];
        $payrollData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $payrollLabels[] = $month->format('M');
            
            // Sum gross_pay for each month
            $monthlyExpense = Payroll::whereYear('payroll_date', $month->year)
                ->whereMonth('payroll_date', $month->month)
                ->sum('gross_pay');
                
            $payrollData[] = (float)$monthlyExpense;
        }

        // Fetch dynamic leave status counts
        $approvedLeaves = Leave_Request::where('status', 'approved')->count();
        $pendingLeaves = Leave_Request::where('status', 'pending')->count();
        $rejectedLeaves = Leave_Request::where('status', 'rejected')->count();
        $leaveChartData = [$approvedLeaves, $pendingLeaves, $rejectedLeaves];

        // New Security KPIs
        $securityAlerts = \App\Models\LoginLog::whereNotNull('locked_until')->whereDate('created_at', Carbon::today())->count();
        $failedLogins = \App\Models\LoginLog::where('status', 'FAILED')->whereDate('created_at', Carbon::today())->count();
        $activeSessions = \Illuminate\Support\Facades\DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', Carbon::now()->subMinutes(15)->getTimestamp())
            ->distinct()
            ->count('user_id');
        $passwordResets = \Illuminate\Support\Facades\DB::table('password_reset_tokens')->count();
        $securityScore = 94; // Example score based on rules

        return view('admin.dashboard.index', compact(
            'totalEmployees',
            'payrollsProcessed',
            'pendingApprovals',
            'totalDepartments',
            'recentActivities',
            'payrollLabels',
            'payrollData',
            'leaveChartData',
            'securityAlerts',
            'activeSessions',
            'failedLogins',
            'passwordResets',
            'securityScore'
        ));
    }

    public function itAdminDashboard()
    {
        $totalUsers = \App\Models\User::count();
        $activeUsers = \App\Models\LoginLog::where('status', 'SUCCESS')->whereDate('created_at', Carbon::today())->count();
        $failedLogins = \App\Models\LoginLog::where('status', 'FAILED')->count();
        $lockedAccounts = \App\Models\LoginLog::whereNotNull('locked_until')->count();
        $alerts = \App\Models\LoginLog::where('status', 'FAILED')->orWhereNotNull('locked_until')->count();
        $auditLogs = \App\Models\LoginLog::count();
        $backupCount = 0;

        $recentAlerts = \App\Models\LoginLog::where('status', 'FAILED')
            ->orWhereNotNull('locked_until')
            ->latest()
            ->take(5)
            ->get();

        $recentLogins = \App\Models\LoginLog::where('status', 'SUCCESS')
            ->latest()
            ->take(5)
            ->get();

        $userStats = [
            'total_users' => $totalUsers,
            'admins' => \App\Models\User::where('role', 'admin')->count(),
            'it_admins' => \App\Models\User::where('role', 'it_admin')->count(),
            'superadmins' => \App\Models\User::where('role', 'superadmin')->count(),
            'employees' => \App\Models\User::where('role', 'employee')->count(),
            'suspended' => \App\Models\User::where('is_suspended', true)->count(),
        ];

        return view('it_admin.dashboard.itdashboard', compact(
            'totalUsers',
            'activeUsers',
            'failedLogins',
            'lockedAccounts',
            'alerts',
            'auditLogs',
            'backupCount',
            'recentAlerts',
            'recentLogins',
            'userStats'
        ));
    }

    public function financeAdminDashboard()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // KPI Calculations
        $totalPayrollCost = Payroll::whereYear('payroll_date', $currentYear)->sum('gross_pay');
        $netPayDisbursed = Payroll::whereYear('payroll_date', $currentYear)->sum('net_pay');

        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->toDateString();
        $hasPayrollCount = Payroll::where(function ($query) use ($start, $end) {
                $query->whereBetween('payroll_period_start', [$start, $end])
                      ->orWhereBetween('payroll_period_end', [$start, $end]);
            })
            ->distinct('employee_id')
            ->count('employee_id');
        $activeEmployeesCount = Employee::count();
        $pendingRuns = max(0, $activeEmployeesCount - $hasPayrollCount);

        $awaitingApproval = Payroll::where('status', 'pending')->count();

        $totalDeductions = Payroll::whereYear('payroll_date', $currentYear)
            ->whereMonth('payroll_date', $currentMonth)
            ->sum('total_deductions');

        $govContributions = \App\Models\Payroll_Deduction::whereHas('payroll', function($q) use ($currentYear) {
            $q->whereYear('payroll_date', $currentYear);
        })->sum('deduction_amount');

        $pendingClaims = Leave_Request::where('status', 'Pending')->count();
        $flaggedDiscrepancies = Payroll::where('status', 'flagged')->count();

        $deadline = Carbon::now()->day(20);
        if (Carbon::now()->day > 20) {
            $deadline->addMonth();
        }
        $upcomingDeadlines = $deadline->format('M d, Y');

        $ytdPayrollExpense = $totalPayrollCost; // Year-to-date gross cost

        return view('finance admin.finance_dashboard', compact(
            'totalPayrollCost',
            'netPayDisbursed',
            'pendingRuns',
            'awaitingApproval',
            'totalDeductions',
            'govContributions',
            'pendingClaims',
            'flaggedDiscrepancies',
            'upcomingDeadlines',
            'ytdPayrollExpense'
        ));
    }

    public function userIndex()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $todayAttendance = null;
        if ($employee) {
            $todayAttendance = Attendance::where('employee_id', $employee->employee_id)
                ->where('date', Carbon::now()->toDateString())
                ->first();
        }

        // Default deduction values
        $deductionData = [
            'sss' => 0,
            'philhealth' => 0,
            'pagibig' => 0,
            'tax' => 0,
            'absences' => 0
        ];

        // Default stats
        $stats = [
            'attendance' => 0,
            'absences' => 0,
            'late' => 0,
            'overtime' => 0
        ];

        // Default attendance summary for chart
        $attendanceSummary = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'ot' => 0
        ];

        if ($employee) {
            // Latest Payroll for Deductions
            $latestPayroll = Payroll::where('employee_id', $employee->employee_id)
                ->latest('payroll_date')
                ->first();

            if ($latestPayroll) {
                $deductionData['sss'] = $latestPayroll->sss ?? 0;
                $deductionData['philhealth'] = $latestPayroll->philhealth ?? 0;
                $deductionData['pagibig'] = $latestPayroll->hdmf ?? 0;
                $deductionData['tax'] = $latestPayroll->tax ?? 0;
                $deductionData['absences'] = $latestPayroll->absent_deduction ?? 0;
            }

            // Attendance Stats for current month
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $attendances = Attendance::where('employee_id', $employee->employee_id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->get();

            $stats['attendance'] = $attendances->filter(function ($att) {
                return strtolower(trim($att->status)) !== 'absent';
            })->count();

            $stats['absences'] = $attendances->filter(function ($att) {
                return strtolower(trim($att->status)) === 'absent';
            })->count();

            $stats['late'] = $attendances->filter(function ($att) {
                return (int)$att->late_minutes > 0;
            })->count();

            $stats['overtime'] = $attendances->sum('overtime_hours');

            // For the chart
            $attendanceSummary['present'] = $stats['attendance'];
            $attendanceSummary['absent']  = $stats['absences'];
            $attendanceSummary['late']    = $stats['late'];
            $attendanceSummary['ot']      = $stats['overtime'];

            // Financial Summary (Monthly Payroll for the current year)
            $financialData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthStart = Carbon::now()->month($m)->startOfMonth();
                $monthEnd = Carbon::now()->month($m)->endOfMonth();
                
                $monthlyPayroll = Payroll::where('employee_id', $employee->employee_id)
                    ->whereBetween('payroll_date', [$monthStart, $monthEnd])
                    ->sum('net_pay');
                    
                $financialData[] = $monthlyPayroll;
            }
            
            $latestPayrollAmount = $latestPayroll ? $latestPayroll->net_pay : 0;
            $ytdPayroll = array_sum($financialData);

            $payrolls = Payroll::with('deductions')
                ->where('employee_id', $employee->employee_id)
                ->orderBy('payroll_period_end', 'desc')
                ->get();
        } else {
            $financialData = array_fill(0, 12, 0);
            $latestPayrollAmount = 0;
            $ytdPayroll = 0;
            $payrolls = collect();
        }

        return view('user.dashboard.index', compact(
            'user', 
            'employee', 
            'deductionData', 
            'stats', 
            'attendanceSummary', 
            'financialData', 
            'latestPayrollAmount', 
            'ytdPayroll',
            'payrolls',
            'todayAttendance'
        ));
    }

    public function analytics()
    {
        // 1. Login Activity Data (Weekly & Monthly)
        $loginWeeklyData = ['labels' => [], 'success' => [], 'failed' => []];
        for ($i = 3; $i >= 0; $i--) {
            $start = \Carbon\Carbon::now()->subWeeks($i)->startOfWeek();
            $end = clone $start;
            $end->endOfWeek();
            $loginWeeklyData['labels'][] = 'Wk ' . $start->format('W');
            $loginWeeklyData['success'][] = \App\Models\LoginLog::whereBetween('created_at', [$start, $end])->where('status', 'SUCCESS')->count();
            $loginWeeklyData['failed'][] = \App\Models\LoginLog::whereBetween('created_at', [$start, $end])->where('status', 'FAILED')->count();
        }

        $loginMonthlyData = ['labels' => [], 'success' => [], 'failed' => []];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i);
            $loginMonthlyData['labels'][] = $month->format('M');
            $loginMonthlyData['success'][] = \App\Models\LoginLog::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->where('status', 'SUCCESS')->count();
            $loginMonthlyData['failed'][] = \App\Models\LoginLog::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->where('status', 'FAILED')->count();
        }

        // 2. Security Threats (Brute Force = FAILED logins, Lockouts = locked_until not null)
        $threat6m = ['labels' => [], 'brute' => [], 'locks' => []];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i);
            $threat6m['labels'][] = $month->format('M');
            $threat6m['brute'][] = \App\Models\LoginLog::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->where('status', 'FAILED')->count();
            $threat6m['locks'][] = \App\Models\LoginLog::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->whereNotNull('locked_until')->count();
        }

        $threat3m = ['labels' => [], 'brute' => [], 'locks' => []];
        for ($i = 2; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i);
            $threat3m['labels'][] = $month->format('M');
            $threat3m['brute'][] = \App\Models\LoginLog::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->where('status', 'FAILED')->count();
            $threat3m['locks'][] = \App\Models\LoginLog::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->whereNotNull('locked_until')->count();
        }

        // 3. User Role Distribution
        $adminCount = \App\Models\User::where('role', 'admin')->count();
        $employeeCount = \App\Models\User::where('role', 'employee')->count();
        $roleChartData = [$adminCount, $employeeCount];

        // 4. Browser Analytics
        $browsers = \App\Models\LoginLog::select('browser', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('browser')
            ->orderByDesc('total')
            ->get();

        $browserLabels = [];
        $browserData = [];
        foreach ($browsers as $browser) {
            $label = $browser->browser ?: 'Unknown';
            $browserLabels[] = $label;
            $browserData[] = $browser->total;
        }
        
        if (empty($browserLabels)) {
            $browserLabels = ['No Data'];
            $browserData = [1];
        }

        if (Auth::user()->role === 'it_admin') {
            return view('it_admin.analytics.analytic', compact(
                'loginWeeklyData', 'loginMonthlyData',
                'threat6m', 'threat3m',
                'roleChartData', 'browserLabels', 'browserData'
            ));
        }

        // Payroll expense – last 6 months (same logic as dashboard)
        $payrollLabels = [];
        $payrollData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i);
            $payrollLabels[] = $month->format('M');
            $payrollData[]   = (float) Payroll::whereYear('payroll_date', $month->year)
                ->whereMonth('payroll_date', $month->month)
                ->sum('gross_pay');
        }

        // Leave status distribution
        $leaveChartData = [
            Leave_Request::where('status', 'approved')->count(),
            Leave_Request::where('status', 'pending')->count(),
            Leave_Request::where('status', 'rejected')->count(),
        ];

        return view('admin.analytics.index', compact(
            'payrollLabels', 'payrollData', 'leaveChartData',
            'loginWeeklyData', 'loginMonthlyData',
            'threat6m', 'threat3m',
            'roleChartData', 'browserLabels', 'browserData'
        ));
    }
}
