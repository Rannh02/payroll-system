<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions   = Position::with('department')->orderBy('created_at', 'desc')->get();
        $departments = Department::where('status', 'Active')->orderBy('department_name')->get();
        return view('admin.position.position', compact('positions', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'nullable|exists:department,department_id',
            'position_name' => 'required|string|max:100',
            'position_code' => 'required|string|max:20|unique:position,position_code',
            'basic_salary'  => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'status'        => 'required|in:Active,Inactive',
        ]);

        Position::create([
            'department_id' => $request->department_id,
            'position_name' => $request->position_name,
            'position_code' => strtoupper($request->position_code),
            'basic_salary'  => $request->basic_salary,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);

        return redirect()->route('position.index')->with('success', 'Position added successfully.');
    }

    public function destroy(Position $position)
    {
        if ($position->employees()->count() > 0) {
            return redirect()->route('position.index')->with('error', 'Cannot delete position because it has linked employees.');
        }
        $position->delete();
        
        return redirect()->route('position.index')
        ->with('success', 'Position deleted successfully.');
    }
}
