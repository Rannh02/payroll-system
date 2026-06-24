<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ─────────────────────────────────────────────
    //  ADMIN: List all attendance records
    // ─────────────────────────────────────────────
    public function index()
    {
        $employees = Employee::all();

        $attendance = Attendance::with('employee')
            ->latest()
            ->get();

        return view('admin.attendance.attendance', compact(
            'employees',
            'attendance'
        ));
    }

    // ─────────────────────────────────────────────
    //  ADMIN: Store a new attendance record
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'       => 'required|exists:employee,employee_id',
            'date'              => 'required|date',
            'time_in'           => 'nullable',
            'time_out'          => 'nullable',
            'overtime_hours'    => 'nullable|numeric',
            'total_hours'       => 'nullable|numeric',
            'status'            => 'required',
            'late_minutes'      => 'nullable|numeric',
            'undertime_minutes' => 'nullable|numeric',
        ]);

        $totalHours = 0;
        if ($request->time_in && $request->time_out) {
            $timeIn     = Carbon::parse($request->time_in);
            $timeOut    = Carbon::parse($request->time_out);
            // Calculate difference and ensure it's positive
            $diffMinutes = abs($timeOut->diffInMinutes($timeIn));
            $totalHours = $diffMinutes / 60;
        }

        Attendance::create([
            'employee_id'       => $request->employee_id,
            'date'              => $request->date,
            'time_in'           => $request->time_in,
            'time_out'          => $request->time_out,
            'overtime_hours'    => $request->overtime_hours    ?? 0,
            'total_hours'       => $totalHours,
            'status'            => $request->status,
            'late_minutes'      => $request->late_minutes      ?? 0,
            'undertime_minutes' => $request->undertime_minutes ?? 0,
        ]);

        return redirect()
            ->route('attendance.index')
            ->with('success', 'Attendance recorded successfully.');
    }

    // ─────────────────────────────────────────────
    //  USER: View own attendance list
    // ─────────────────────────────────────────────
    public function myAttendance()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return view('user.attendance.report', [
                'attendances' => collect(),
                'error' => 'No employee record found for your account. Please contact admin.'
            ]);
        }

        $employeeId = $employee->employee_id;

        $attendances = Attendance::where('employee_id', $employeeId)
            ->orderBy('date', 'desc')
            ->get();

        return view('user.attendance.report', compact('attendances'));
    }

    // ─────────────────────────────────────────────
    //  USER: Attendance Summary Report
    //  FIX 1 → returns 'user.attendance.report' (not 'index')
    //  FIX 2 → properly aggregates summary data
    //  FIX 3 → honours checkbox filters from the form
    // ─────────────────────────────────────────────
    public function report(Request $request)
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return view('user.attendance.report', [
                'error' => 'No employee record found for your account. Please contact admin.',
                'from' => $request->input('from_date', now()->startOfMonth()->toDateString()),
                'to' => $request->input('to_date', now()->toDateString()),
            ]);
        }

        $employeeId = $employee->employee_id;

        // ── Date range (with sensible fallback) ──────────────────────────
        $from = $request->input('from_date', now()->startOfMonth()->toDateString());
        $to   = $request->input('to_date',   now()->toDateString());

        // Ensure $from is never after $to
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        // ── Raw attendance records in range ──────────────────────────────
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();

        // ── Working days (Mon–Fri) in the selected range ─────────────────
        $workingDays = 0;
        $cursor      = Carbon::parse($from)->copy();
        $endDate     = Carbon::parse($to);

        while ($cursor->lte($endDate)) {
            if ($cursor->isWeekday()) {
                $workingDays++;
            }
            $cursor->addDay();
        }

        // ── Aggregated summary ───────────────────────────────────────────
        // Present  = records whose status is "Present" (case-insensitive)
        $present = $attendances->filter(function ($att) {
            return strtolower(trim($att->status)) === 'present';
        })->count();

        $absences        = $attendances->filter(function ($att) {
            return strtolower(trim($att->status)) === 'absent';
        })->count();
        $lateMinutes     = (int) $attendances->sum('late_minutes');
        $undertimeMinutes= (int) $attendances->sum('undertime_minutes');
        $totalLateUTM    = $lateMinutes + $undertimeMinutes;
        $totalHours      = round(abs($attendances->sum('total_hours')), 2);
        $overtimeHours   = round($attendances->sum('overtime_hours'), 2);

        // ── Checkbox filter flags (FIX 2: checkboxes now inside the form) ─
        $showAbsences   = $request->boolean('absences',    true);
        $showTardiness  = $request->boolean('tardiness',   true);
        $showUndertime  = $request->boolean('undertime',   true);
        $showUnpaidLeave= $request->boolean('unpaid_leave',true);

        // ── FIX 1: return the REPORT view, not the index view ────────────
        return view('user.attendance.report', compact(
            'attendances',
            'from',
            'to',
            'workingDays',
            'present',
            'absences',
            'lateMinutes',
            'undertimeMinutes',
            'totalLateUTM',
            'totalHours',
            'overtimeHours',
            'showAbsences',
            'showTardiness',
            'showUndertime',
            'showUnpaidLeave'
        ));
    }

    // ─────────────────────────────────────────────
    //  USER: Clock In Action
    // ─────────────────────────────────────────────
    public function userClockIn(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'No employee record found.'], 404);
        }

        $today = $request->input('date') ? Carbon::parse($request->input('date'))->toDateString() : Carbon::now()->toDateString();

        // Check if already clocked in today
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->time_in) {
            return response()->json(['success' => false, 'message' => 'You have already clocked in for this date.'], 400);
        }

        // Fixed clock-in target time: 08:00 AM
        $now = Carbon::now();
        $currentTime = $now->toTimeString();
        $targetIn = Carbon::parse($today . ' 08:00:00');

        $lateMinutes = 0;
        if ($now->greaterThan($targetIn)) {
            $lateMinutes = $now->diffInMinutes($targetIn);
        }

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->employee_id = $employee->employee_id;
            $attendance->date = $today;
        }

        $attendance->time_in = $currentTime;
        $attendance->time_out = null;
        $attendance->late_minutes = $lateMinutes;
        $attendance->undertime_minutes = 0;
        $attendance->overtime_hours = 0;
        $attendance->total_hours = 0;
        $attendance->status = 'Present';
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Clock-In successful!',
            'time_in' => Carbon::parse($currentTime)->format('g:i A'),
            'late_minutes' => $lateMinutes
        ]);
    }

    // ─────────────────────────────────────────────
    //  USER: Clock Out Action
    // ─────────────────────────────────────────────
    public function userClockOut(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'No employee record found.'], 404);
        }

        $today = $request->input('date') ? Carbon::parse($request->input('date'))->toDateString() : Carbon::now()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->time_in) {
            return response()->json(['success' => false, 'message' => 'You must clock in first.'], 400);
        }

        if ($attendance->time_out) {
            return response()->json(['success' => false, 'message' => 'You have already clocked out for this date.'], 400);
        }

        $now = Carbon::now();
        $currentTime = $now->toTimeString();

        // Fixed clock-out target time: 04:00 PM (16:00:00)
        $targetOut = Carbon::parse($today . ' 16:00:00');

        $undertimeMinutes = 0;
        $overtimeHours = 0;

        if ($now->lessThan($targetOut)) {
            $undertimeMinutes = $now->diffInMinutes($targetOut);
        } else {
            $overtimeHours = round($now->diffInMinutes($targetOut) / 60, 2);
        }

        // Calculate total hours worked
        $timeIn = Carbon::parse($today . ' ' . $attendance->time_in);
        $totalHours = round($now->diffInMinutes($timeIn) / 60, 2);

        $attendance->time_out = $currentTime;
        $attendance->undertime_minutes = $undertimeMinutes;
        $attendance->overtime_hours = $overtimeHours;
        $attendance->total_hours = $totalHours;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Clock-Out successful!',
            'time_out' => Carbon::parse($currentTime)->format('g:i A'),
            'undertime_minutes' => $undertimeMinutes,
            'overtime_hours' => $overtimeHours,
            'total_hours' => $totalHours
        ]);
    }

    // ─────────────────────────────────────────────
    //  USER: Get day status details
    // ─────────────────────────────────────────────
    public function getDayStatus(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $employee = Auth::user()->employee;
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'No employee record found.'], 404);
        }

        $dateStr = Carbon::parse($request->date)->toDateString();

        // Query attendance
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->where('date', $dateStr)
            ->first();

        // Query approved leave
        $leave = \App\Models\Leave_Request::where('employee_id', $employee->employee_id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->first();

        $status = 'Not Filed';
        $timeIn = '—';
        $timeOut = '—';
        $late = 0;
        $undertime = 0;
        $overtime = 0;
        $totalHours = 0;
        $leaveType = null;
        $leaveReason = null;

        if ($leave) {
            $status = 'Leave';
            $leaveType = $leave->leave_type;
            $leaveReason = $leave->reason;
        } elseif ($attendance) {
            $status = $attendance->status;
            $timeIn = $attendance->time_in ? Carbon::parse($attendance->time_in)->format('g:i A') : '—';
            $timeOut = $attendance->time_out ? Carbon::parse($attendance->time_out)->format('g:i A') : '—';
            $late = $attendance->late_minutes;
            $undertime = $attendance->undertime_minutes;
            $overtime = $attendance->overtime_hours;
            $totalHours = $attendance->total_hours;
        } else {
            // If it's a weekday and no attendance/leave, they are marked as Absent
            $dayOfWeek = Carbon::parse($dateStr)->dayOfWeek;
            if ($dayOfWeek !== Carbon::SUNDAY && $dayOfWeek !== Carbon::SATURDAY) {
                // If the selected date is in the past, mark as Absent
                if (Carbon::parse($dateStr)->isPast() && !Carbon::parse($dateStr)->isToday()) {
                    $status = 'Absent';
                }
            }
        }

        return response()->json([
            'success' => true,
            'date' => Carbon::parse($dateStr)->format('F d, Y'),
            'status' => $status,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'late_minutes' => $late,
            'undertime_minutes' => $undertime,
            'overtime_hours' => $overtime,
            'total_hours' => $totalHours,
            'leave_type' => $leaveType,
            'leave_reason' => $leaveReason
        ]);
    }
}