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

        return view('admin.dashboard.index', compact(
            'totalEmployees',
            'payrollsProcessed',
            'pendingApprovals',
            'totalDepartments',
            'recentActivities',
            'payrollLabels',
            'payrollData',
            'leaveChartData'
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
}
