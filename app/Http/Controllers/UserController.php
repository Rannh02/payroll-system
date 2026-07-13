<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\EmployeeAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Fetch users sorted by latest
        $users = $query->latest()->paginate(10)->withQueryString();

        return view('it_admin.user management.all users.index', compact('users'));
    }

    /**
     * Show the form for creating or editing the specified resource.
     */
    public function createEdit(User $user = null)
    {
        $recentUsers = User::latest()->take(5)->get();
        return view('it_admin.user management.create&edit users.index', compact('user', 'recentUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $isAdminRole = in_array($request->role, ['admin', 'it_admin', 'hr_admin', 'finance_admin']);

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'string', Rule::in(['admin', 'it_admin', 'hr_admin', 'finance_admin', 'employee'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // Also validate uniqueness against the auth table that will receive the record
        if ($isAdminRole) {
            $rules['email'][] = Rule::unique('admin', 'email');
        } else {
            $rules['email'][] = Rule::unique('employee_auth', 'email');
        }

        $request->validate($rules, [
            'email.unique' => 'This email address is already registered in the system.',
        ]);

        try {
            DB::transaction(function () use ($request, $isAdminRole) {
                // 1. Create in users table
                User::create([
                    'name'         => $request->name,
                    'email'        => $request->email,
                    'password'     => Hash::make($request->password),
                    'role'         => $request->role,
                    'is_suspended' => false,
                ]);

                // 2. Sync to respective auth table
                if ($isAdminRole) {
                    Admin::create([
                        'name'     => $request->name,
                        'email'    => $request->email,
                        'password' => $request->password, // Password mutator hashes it
                        'role'     => $request->role,
                    ]);
                } else {
                    $parts     = explode(' ', $request->name, 2);
                    $firstName = $parts[0] ?? 'User';
                    $lastName  = $parts[1] ?? 'Account';

                    EmployeeAuth::create([
                        'first_name' => $firstName,
                        'last_name'  => $lastName,
                        'email'      => $request->email,
                        'password'   => $request->password, // Password mutator hashes it
                        'role'       => 'employee',
                    ]);
                }
            });

            return redirect()->route('it_admin.users')->with('success', 'User account created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'it_admin', 'hr_admin', 'finance_admin', 'employee'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $oldRole = $user->role;
                $oldEmail = $user->email;

                // 1. Update user model
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role,
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);

                // 2. Synchronize across Admin and EmployeeAuth tables
                $isAdminRole = in_array($request->role, ['admin', 'it_admin', 'hr_admin', 'finance_admin']);
                $wasAdminRole = in_array($oldRole, ['admin', 'it_admin', 'hr_admin', 'finance_admin']);

                if ($wasAdminRole && !$isAdminRole) {
                    // Role changed from Admin to Employee
                    Admin::where('email', $oldEmail)->delete();

                    $parts = explode(' ', $request->name, 2);
                    $firstName = $parts[0] ?? 'User';
                    $lastName = $parts[1] ?? 'Account';

                    $empData = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $request->email,
                        'role' => 'employee',
                    ];
                    if ($request->filled('password')) {
                        $empData['password'] = $request->password;
                    } else {
                        // Inherit password from existing User record
                        $empData['password'] = $user->password;
                    }

                    EmployeeAuth::create($empData);
                } 
                elseif (!$wasAdminRole && $isAdminRole) {
                    // Role changed from Employee to Admin
                    EmployeeAuth::where('email', $oldEmail)->delete();

                    $adminData = [
                        'name' => $request->name,
                        'email' => $request->email,
                        'role' => $request->role,
                    ];
                    if ($request->filled('password')) {
                        $adminData['password'] = $request->password;
                    } else {
                        // Inherit password from existing User record
                        $adminData['password'] = $user->password;
                    }

                    Admin::create($adminData);
                } 
                else {
                    // Role type remained the same, update the existing record
                    if ($isAdminRole) {
                        $adminRecord = Admin::where('email', $oldEmail)->first();
                        if ($adminRecord) {
                            $adminUpdate = [
                                'name' => $request->name,
                                'email' => $request->email,
                                'role' => $request->role,
                            ];
                            if ($request->filled('password')) {
                                $adminUpdate['password'] = $request->password;
                            }
                            $adminRecord->update($adminUpdate);
                        }
                    } else {
                        $employeeRecord = EmployeeAuth::where('email', $oldEmail)->first();
                        if ($employeeRecord) {
                            $parts = explode(' ', $request->name, 2);
                            $firstName = $parts[0] ?? 'User';
                            $lastName = $parts[1] ?? 'Account';

                            $empUpdate = [
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'email' => $request->email,
                            ];
                            if ($request->filled('password')) {
                                $empUpdate['password'] = $request->password;
                            }
                            $employeeRecord->update($empUpdate);
                        }
                    }
                }
            });

            return redirect()->route('it_admin.users')->with('success', 'User account updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            DB::transaction(function () use ($user) {
                // Delete from sub-tables
                if (in_array($user->role, ['admin', 'it_admin', 'hr_admin', 'finance_admin'])) {
                    Admin::where('email', $user->email)->delete();
                } else {
                    EmployeeAuth::where('email', $user->email)->delete();
                }

                // Delete from users table
                $user->delete();
            });

            return redirect()->route('it_admin.users')->with('success', 'User account deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the suspension status of the specified user.
     */
    public function toggleSuspend(User $user)
    {
        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $status = $user->is_suspended ? 'suspended' : 'activated';
        return redirect()->route('it_admin.users')->with('success', "User account has been successfully {$status}.");
    }

    /**
     * Show the roles and permissions overview.
     */
    public function rolesIndex()
    {
        return view('it_admin.user management.Role & Permission.index');
    }
}
