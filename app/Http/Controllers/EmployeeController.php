<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\EmployeeAuth;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'position', 'user'])->get();
        $departments = Department::all();
        $positions = Position::all();
        return view('admin.employees.manage_employee', compact('employees', 'departments', 'positions'));
    }

    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        
        // Calculate next employee number
        $latestEmployee = Employee::orderBy('employee_id', 'desc')->first();
        $nextId = $latestEmployee ? $latestEmployee->employee_id + 1 : 1;
        $nextEmployeeId = 'VIA-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('admin.employees.create', compact('departments', 'positions', 'nextEmployeeId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:employee_auth,email',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'sex' => 'nullable|in:Male,Female',
            'current_street_address' => 'nullable|string|max:255',
            'current_barangay' => 'nullable|string|max:100',
            'current_city' => 'nullable|string|max:100',
            'current_province' => 'nullable|string|max:100',
            'current_zip_code' => 'nullable|string|max:10',
            'permanent_street_address' => 'nullable|string|max:255',
            'permanent_barangay' => 'nullable|string|max:100',
            'permanent_city' => 'nullable|string|max:100',
            'permanent_province' => 'nullable|string|max:100',
            'permanent_zip_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'employee_status' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'dependents' => 'nullable|integer|min:0',
            'sss_num' => 'nullable|string|max:50',
            'philhealth_num' => 'nullable|string|max:50',
            'pagibig_num' => 'nullable|string|max:50',
            'position' => 'nullable|exists:position,position_id',
            'department' => 'nullable|exists:department,department_id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

         /*
    |--------------------------------------------------------------------------
    | 2. Check for Duplicate Employee
    |--------------------------------------------------------------------------
    | Prevent creating an employee if ALL important personal details already
    | exist in the database.
    */
    $existingEmployee = Employee::where('first_name', $request->first_name)
        ->where('middle_name', $request->middle_name)
        ->where('last_name', $request->last_name)
        ->where('date_of_birth', $request->date_of_birth)
        ->where('sex', $request->sex)
        ->first();

    if ($existingEmployee) {
        return back()
            ->withInput()
            ->withErrors([
                'duplicate' =>
                    'This employee already exists in the database. Duplicate records are not allowed.'
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Additional Duplicate Checks for Government IDs
    |--------------------------------------------------------------------------
    */
    if ($request->filled('sss_num')) {
        $existingSSS = Employee::where('sss_num', $request->sss_num)->first();
        if ($existingSSS) {
            return back()
                ->withInput()
                ->withErrors([
                    'sss_num' => 'The SSS Number is already assigned to another employee.'
                ]);
        }
    }

    if ($request->filled('philhealth_num')) {
        $existingPhilHealth = Employee::where('philhealth_num', $request->philhealth_num)->first();
        if ($existingPhilHealth) {
            return back()
                ->withInput()
                ->withErrors([
                    'philhealth_num' => 'The PhilHealth Number is already assigned to another employee.'
                ]);
        }
    }

    if ($request->filled('pagibig_num')) {
        $existingPagibig = Employee::where('pagibig_num', $request->pagibig_num)->first();
        if ($existingPagibig) {
            return back()
                ->withInput()
                ->withErrors([
                    'pagibig_num' => 'The Pag-IBIG Number is already assigned to another employee.'
                ]);
        }
    }

    if ($request->filled('phone')) {
        $existingPhone = Employee::where('contact_info', $request->phone)->first();
        if ($existingPhone) {
            return back()
                ->withInput()
                ->withErrors([
                    'phone' => 'This contact number is already used by another employee.'
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Save Employee
    |--------------------------------------------------------------------------
    */
        try {
            DB::transaction(function () use ($request) {
                // Handle photo upload
                $photoPath = null;
                if ($request->hasFile('profile_photo')) {
                    $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
                }

                // Create the employee authentication record for login
                $employeeAuth = EmployeeAuth::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'role' => 'employee',
                ]);

                // Create the Employee record
                Employee::create([
                    'employee_number' => $request->employee_id, // This comes from the readonly input
                    'user_id' => $employeeAuth->id,
                    'created_by' => auth()->id(),
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'sex' => $request->sex,
                    
                    'current_street_address' => $request->current_street_address,
                    'current_barangay' => $request->current_barangay,
                    'current_city_municipality' => $request->current_city,
                    'current_province' => $request->current_province,
                    'current_zip_code' => $request->current_zip_code,
                    
                    'permanent_street_address' => $request->permanent_street_address,
                    'permanent_barangay' => $request->permanent_barangay,
                    'permanent_city_municipality' => $request->permanent_city,
                    'permanent_province' => $request->permanent_province,
                    'permanent_zip_code' => $request->permanent_zip_code,

                    'contact_info' => $request->phone,
                    'date_of_birth' => $request->date_of_birth,
                    
                    'salary_rate' => $request->salary,
                    'hire_date' => $request->join_date,
                    
                    'employment_status' => $request->employee_status,
                    'marital_status' => $request->marital_status,
                    'number_of_dependents' => $request->dependents ?: 0,
                    
                    'sss_num' => $request->sss_num,
                    'philhealth_num' => $request->philhealth_num,
                    'pagibig_num' => $request->pagibig_num,
                    
                    'position_id' => $request->position,
                    'department_id' => $request->department,
                    'profile_photo' => $photoPath,
                ]);

                \Log::info('Employee created successfully for email: ' . $employeeAuth->email);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create employee: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create employee: ' . $e->getMessage()]);
        }

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }



    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'email' => 'required|email|unique:employee_auth,email,' . $employee->user_id,
            'password' => 'nullable|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'sex' => 'nullable|in:Male,Female',
            'current_street_address' => 'nullable|string|max:255',
            'current_barangay' => 'nullable|string|max:100',
            'current_city' => 'nullable|string|max:100',
            'current_province' => 'nullable|string|max:100',
            'current_zip_code' => 'nullable|string|max:10',
            'permanent_street_address' => 'nullable|string|max:255',
            'permanent_barangay' => 'nullable|string|max:100',
            'permanent_city' => 'nullable|string|max:100',
            'permanent_province' => 'nullable|string|max:100',
            'permanent_zip_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'employee_status' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'dependents' => 'nullable|integer|min:0',
            'sss_num' => 'nullable|string|max:50',
            'philhealth_num' => 'nullable|string|max:50',
            'pagibig_num' => 'nullable|string|max:50',
            'position' => 'nullable|exists:position,position_id',
            'department' => 'nullable|exists:department,department_id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Check for Duplicate Employee
        |--------------------------------------------------------------------------
        | Prevent updating an employee to match another existing employee's details.
        */
        $existingEmployee = Employee::where('first_name', $request->first_name)
            ->where('middle_name', $request->middle_name)
            ->where('last_name', $request->last_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->where('sex', $request->sex)
            ->where('employee_id', '!=', $employee->employee_id)
            ->first();

        if ($existingEmployee) {
            return back()
                ->withInput()
                ->withErrors([
                    'duplicate' => 'This employee already exists in the database. Duplicate records are not allowed.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Additional Duplicate Checks for Government IDs and Phone
        |--------------------------------------------------------------------------
        */
        if ($request->filled('sss_num')) {
            $existingSSS = Employee::where('sss_num', $request->sss_num)
                ->where('employee_id', '!=', $employee->employee_id)
                ->first();
            if ($existingSSS) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'sss_num' => 'The SSS Number is already assigned to another employee.'
                    ]);
            }
        }

        if ($request->filled('philhealth_num')) {
            $existingPhilHealth = Employee::where('philhealth_num', $request->philhealth_num)
                ->where('employee_id', '!=', $employee->employee_id)
                ->first();
            if ($existingPhilHealth) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'philhealth_num' => 'The PhilHealth Number is already assigned to another employee.'
                    ]);
            }
        }

        if ($request->filled('pagibig_num')) {
            $existingPagibig = Employee::where('pagibig_num', $request->pagibig_num)
                ->where('employee_id', '!=', $employee->employee_id)
                ->first();
            if ($existingPagibig) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'pagibig_num' => 'The Pag-IBIG Number is already assigned to another employee.'
                    ]);
            }
        }

        if ($request->filled('phone')) {
            $existingPhone = Employee::where('contact_info', $request->phone)
                ->where('employee_id', '!=', $employee->employee_id)
                ->first();
            if ($existingPhone) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'phone' => 'This contact number is already used by another employee.'
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Save Updates
        |--------------------------------------------------------------------------
        */
        try {
            DB::transaction(function () use ($request, $employee) {
                // Handle photo upload
                $photoPath = $employee->profile_photo;
                if ($request->hasFile('profile_photo')) {
                    // Delete old photo if exists
                    if ($photoPath && \Storage::disk('public')->exists($photoPath)) {
                        \Storage::disk('public')->delete($photoPath);
                    }
                    $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
                }

                // Update the employee authentication record for login
                $employeeAuth = $employee->user;
                if ($employeeAuth) {
                    $employeeAuthData = [
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                    ];
                    if ($request->filled('password')) {
                        $employeeAuthData['password'] = $request->password;
                    }
                    $employeeAuth->update($employeeAuthData);
                }

                // Update the Employee record
                $employee->update([
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'sex' => $request->sex,
                    
                    'current_street_address' => $request->current_street_address,
                    'current_barangay' => $request->current_barangay,
                    'current_city_municipality' => $request->current_city,
                    'current_province' => $request->current_province,
                    'current_zip_code' => $request->current_zip_code,
                    
                    'permanent_street_address' => $request->permanent_street_address,
                    'permanent_barangay' => $request->permanent_barangay,
                    'permanent_city_municipality' => $request->permanent_city,
                    'permanent_province' => $request->permanent_province,
                    'permanent_zip_code' => $request->permanent_zip_code,

                    'contact_info' => $request->phone,
                    'date_of_birth' => $request->date_of_birth,
                    
                    'salary_rate' => $request->salary,
                    'hire_date' => $request->join_date,
                    
                    'employment_status' => $request->employee_status,
                    'marital_status' => $request->marital_status,
                    'number_of_dependents' => $request->dependents ?: 0,
                    
                    'sss_num' => $request->sss_num,
                    'philhealth_num' => $request->philhealth_num,
                    'pagibig_num' => $request->pagibig_num,
                    
                    'position_id' => $request->position,
                    'department_id' => $request->department,
                    'profile_photo' => $photoPath,
                ]);

                \Log::info('Employee updated successfully: ' . $employee->employee_id);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to update employee: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update employee: ' . $e->getMessage()]);
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee archived successfully.');
    }

    public function archived()
    {
        $employees = Employee::onlyTrashed()->with(['department', 'position', 'user'])->get();
        return view('admin.employees.archived_employees', compact('employees'));
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();
        return redirect()->route('employees.archived')->with('success', 'Employee restored successfully.');
    }
}   
