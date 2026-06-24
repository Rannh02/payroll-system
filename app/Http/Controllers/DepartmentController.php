<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        $departments = Department::orderBy('created_at', 'desc')->get();
        return view('admin.department.department', compact('departments'));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_code' => 'required|string|max:20|unique:department,department_code',
            'department_name' => 'required|string|max:100',
            'description'     => 'nullable|string|max:255',
            'status'          => 'required|in:Active,Inactive',
        ]);

        Department::create([
            'department_code' => strtoupper($request->department_code),
            'department_name' => $request->department_name,
            'description'     => $request->description,
            'status'          => $request->status,
        ]);

        return redirect()->route('department.index')->with('success', 'Department added successfully.');
    }

    /**
     * Remove the specified department from storage.
     */
   public function destroy(Department $department)
    {
    if ($department->positions()->count() > 0) {
        return redirect()->route('department.index')
            ->with('error', 'Cannot delete department because it has linked positions.');
    }

    $department->delete();

    return redirect()->route('department.index')
        ->with('success', 'Department deleted successfully.');
}
}
