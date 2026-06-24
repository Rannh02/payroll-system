<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Leave_Request;

class LeaveController extends Controller
{
    /**
     * Show the leave request form with the authenticated user's employee details.
     */
    public function showForm()
    {
        $employee = Employee::with(['department', 'position'])
            ->where('user_id', Auth::id())
            ->first();

        return view('user.leave_form.leave_form', compact('employee'));
    }

    /**
     * Store a new leave request submitted by the employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
        ]);

        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        Leave_Request::create([
            'employee_id' => $employee->employee_id,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason ?? '',
            'status'      => 'pending',
            'date_filed'  => now()->toDateString(),
        ]);

        return redirect()->route('user.leave_form')
            ->with('success', 'Leave request submitted successfully. Please wait for approval.');
    }
}
