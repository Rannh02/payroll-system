<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SssController;
use App\Http\Controllers\PhilhealthController;
use App\Http\Controllers\PagibigController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SecurityLogController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;




// ── Superadmin routes ─────────────────────────────────────────────────────
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    // Authenticated superadmin only
    Route::middleware('superadmin.auth')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
        Route::get('/security', [SuperAdminController::class, 'security'])->name('security');
        Route::get('/security-logs', [SuperAdminController::class, 'securityLogs'])->name('security_logs');
        Route::get('/Administrator', [SuperAdminController::class, 'Administrator'])->name('Administrator');
        Route::post('/Administrator', [SuperAdminController::class, 'storeAdmin'])->name('Administrator.store');
        Route::get('/AuditLogs', [SuperAdminController::class, 'AuditLogs'])->name('AuditLogs');





        //post 
        Route::post('/security-logs/toggle-suspend', [SuperAdminController::class, 'toggleSuspend'])->name('security_logs.suspend');
    });

    Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');
});





Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/password-reset', [AuthController::class, 'showPasswordRequestForm'])->name('password.request');
Route::post('/password-email', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'middle_name' => ['nullable', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'suffix' => ['nullable', 'string', 'max:50'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $nameParts = array_filter([
        $validated['first_name'],
        $validated['middle_name'] ?? null,
        $validated['last_name'],
        $validated['suffix'] ?? null,
    ]);
    $fullName = implode(' ', $nameParts);

    $user = \App\Models\User::create([
        'name' => $fullName,
        'email' => $validated['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        'role' => 'admin',
    ]);

    // Do not auto-login after registration. Send the user to login first.
    return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
})->name('register.store');
Route::middleware(['auth:employee', 'employee'])->group(function () {
    // EMPLOYEE ROUTES (Strictly for Employees only)
    Route::get('/user-dashboard', [DashboardController::class, 'userIndex'])->name('user.dashboard');

    Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])->name('user.attendance');
    Route::get('/my-attendance/report', [AttendanceController::class, 'report'])->name('user.attendance.report');

    Route::get('/payslip', [PayrollController::class, 'myPayslip'])->name('user.payslip');

    Route::get('/leave_form', [LeaveController::class, 'showForm'])->name('user.leave_form');
    Route::post('/leave_form', [LeaveController::class, 'store'])->name('user.leave_form.store');
    Route::get('/my-requests', [LeaveRequestController::class, 'myRequests'])->name('user.my_requests');

    Route::post('/my-attendance/clock-in', [AttendanceController::class, 'userClockIn'])->name('user.clock_in');
    Route::post('/my-attendance/clock-out', [AttendanceController::class, 'userClockOut'])->name('user.clock_out');
    Route::get('/my-attendance/day-status', [AttendanceController::class, 'getDayStatus'])->name('user.day_status');

});

// SHARED ROUTES (Accessible by both Employees and Admins)
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/profile/settings', function () {
        if (Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            return view('admin.settings.index');
        }
        return view('user.settings.index');
    })->name('profile.settings');
});




Route::middleware(['auth:admin', 'admin'])->group(function () {
    // ADMIN ROUTES (Strictly for Admins only)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics.index');

    // Employees Management
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/archived', [EmployeeController::class, 'archived'])->name('employees.archived');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('/employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Departments
    Route::get('/department/index', [DepartmentController::class, 'index'])->name('department.index');
    Route::post('/department', [DepartmentController::class, 'store'])->name('department.store');
    Route::delete('/department/{department}', [DepartmentController::class, 'destroy'])->name('department.destroy');

    // Positions
    Route::get('/positions/index', [PositionController::class, 'index'])->name('position.index');
    Route::post('/positions', [PositionController::class, 'store'])->name('position.store');
    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('position.destroy');

    // Deductions & Contributions
    Route::get('/sss/index', [SssController::class, 'index'])->name('sss.index');
    Route::post('/sss', [SssController::class, 'store'])->name('sss.store');
    Route::put('/sss/{sss}', [SssController::class, 'update'])->name('sss.update');
    Route::delete('/sss/{sss}', [SssController::class, 'destroy'])->name('sss.destroy');

    Route::get('/philhealth/index', [PhilhealthController::class, 'index'])->name('philhealth.index');
    Route::post('/philhealth', [PhilhealthController::class, 'store'])->name('philhealth.store');
    Route::put('/philhealth/{philhealth}', [PhilhealthController::class, 'update'])->name('philhealth.update');
    Route::delete('/philhealth/{philhealth}', [PhilhealthController::class, 'destroy'])->name('philhealth.destroy');

    Route::get('/pagibig', [PagibigController::class, 'index'])->name('pagibig.index');
    Route::post('/pagibig', [PagibigController::class, 'store'])->name('pagibig.store');
    Route::put('/pagibig/{pagibig}', [PagibigController::class, 'update'])->name('pagibig.update');
    Route::delete('/pagibig/{pagibig}', [PagibigController::class, 'destroy'])->name('pagibig.destroy');

    Route::get('/tax/index', [TaxController::class, 'index'])->name('tax.index');
    Route::post('/tax', [TaxController::class, 'store'])->name('tax.store');
    Route::put('/tax/{tax}', [TaxController::class, 'update'])->name('tax.update');
    Route::delete('/tax/{tax}', [TaxController::class, 'destroy'])->name('tax.destroy');

    // Attendance Management
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Leave Approvals & Workflow
    Route::get('/approval_workflow', [LeaveRequestController::class, 'index'])->name('approval_workflow.index');
    Route::patch('/approval_workflow/{leaveRequest}/status', [LeaveRequestController::class, 'updateStatus'])->name('approval_workflow.status');
    Route::post('/notifications/clear', [LeaveRequestController::class, 'clearNotifications'])->name('notifications.clear');
    Route::post('/notifications/mark-as-viewed', [LeaveRequestController::class, 'markAsViewed'])->name('notifications.viewed');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/details/{type}', [ReportsController::class, 'details'])->name('reports.details');
    Route::get('/reports/export/{type}', [ReportsController::class, 'export'])->name('reports.export');

    Route::get('/security-logs/login', [SecurityLogController::class, 'loginLogs'])->name('admin.security_logs');
    Route::post('/security-logs/unlock', [App\Http\Controllers\SecurityLogController::class, 'unlock'])->name('security_logs.unlock');
    Route::post('/security-logs/toggle-suspend', [App\Http\Controllers\SecurityLogController::class, 'toggleSuspend'])->name('security_logs.suspend');

});

Route::middleware(['auth:admin', 'payroll_access'])->group(function () {
    Route::post('/payroll/run/{employee}/{from}/{to}', [PayrollController::class, 'runForEmployee'])->name('payroll.run');
    Route::get('/payroll/payslip-preview', [PayrollController::class, 'payslipPreview'])->name('payroll.payslip.preview');
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
});

Route::middleware(['auth:admin', 'it_admin'])->prefix('it_admin')->name('it_admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'itAdminDashboard'])->name('dashboard');
    
    Route::get('/security-logs/login', [SecurityLogController::class, 'loginLogsIT'])->name('security_logs');
    Route::post('/security-logs/unlock', [SecurityLogController::class, 'unlockIT'])->name('security_logs.unlock');
    Route::post('/security-logs/toggle-suspend', [SecurityLogController::class, 'toggleSuspendIT'])->name('security_logs.suspend');

    //analytics
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Audit Logs
    Route::get('/audit-logs', [SecurityLogController::class, 'auditLogs'])->name('audit_logs');

    // Session Management
    Route::get('/session-management', [SecurityLogController::class, 'sessionManagement'])->name('session_management');
    Route::delete('/session-management/{sessionId}', [SecurityLogController::class, 'revokeSession'])->name('session_management.revoke');
    Route::delete('/session-management', [SecurityLogController::class, 'revokeAllSessions'])->name('session_management.revoke_all');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/create-edit/{user?}', [UserController::class, 'createEdit'])->name('users.create_edit');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-suspend', [UserController::class, 'toggleSuspend'])->name('users.toggle-suspend');

    // Roles & Permissions
    Route::get('/roles', [UserController::class, 'rolesIndex'])->name('roles');

    // Reports
    Route::get('/reports/user-activity', [App\Http\Controllers\ITReportsController::class, 'userActivity'])->name('reports.user_activity');
    Route::get('/reports/user-activity/pdf', [App\Http\Controllers\ITReportsController::class, 'exportUserActivityPdf'])->name('reports.user_activity.pdf');
    Route::get('/reports/security-incident', [App\Http\Controllers\ITReportsController::class, 'securityIncident'])->name('reports.security_incident');
    Route::get('/reports/security-incident/pdf', [App\Http\Controllers\ITReportsController::class, 'exportSecurityIncidentPdf'])->name('reports.security_incident.pdf');
});

// Finance Admin Routes
Route::middleware(['auth:admin', 'finance_admin'])->prefix('finance_admin')->name('finance_admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'financeAdminDashboard'])->name('dashboard');
    
    // Payroll Processing Routes
    Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::get('/payroll/history', [PayrollController::class, 'history'])->name('payroll.history');
    Route::get('/payroll/pending-approvals', [PayrollController::class, 'pendingApprovals'])->name('payroll.pending_approvals');
    Route::get('/payroll/discrepancy-review', [PayrollController::class, 'discrepancyReview'])->name('payroll.discrepancy_review');
    
    Route::post('/payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
    Route::post('/payroll/{payroll}/flag', [PayrollController::class, 'flag'])->name('payroll.flag');
    
    // Deductions & Contributions Routes
    Route::get('/deductions/government-contributions', [ReportsController::class, 'governmentContributions'])->name('deductions.government');
    Route::get('/deductions/loans', [ReportsController::class, 'loanDeductions'])->name('deductions.loans');
    Route::get('/deductions/other', [ReportsController::class, 'otherDeductions'])->name('deductions.other');
    
    // Allowances & Reimbursements Routes
    Route::get('/allowances/pending-claims', [ReportsController::class, 'pendingClaims'])->name('allowances.pending');
    Route::get('/allowances/approved-claims', [ReportsController::class, 'approvedClaims'])->name('allowances.approved');
    
    // Reports Routes
    Route::get('/reports/payroll-summary', [ReportsController::class, 'payrollSummary'])->name('reports.payroll_summary');
    Route::get('/reports/government-remittance', [ReportsController::class, 'governmentRemittance'])->name('reports.government_remittance');
    Route::get('/reports/tax-bIR', [ReportsController::class, 'taxBIR'])->name('reports.tax_bir');
    Route::get('/reports/payroll-cost-trends', [ReportsController::class, 'payrollCostTrends'])->name('reports.cost_trends');
});

Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
