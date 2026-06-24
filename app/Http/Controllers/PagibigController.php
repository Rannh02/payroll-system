<?php

namespace App\Http\Controllers;

use App\Models\Pagibig;
use Illuminate\Http\Request;

class PagibigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pagibig = Pagibig::paginate(10);
        return view('admin.Government Contribution.pagibig.index', compact('pagibig'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.Government Contribution.pagibig.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'salary_from' => 'required | numeric',
            'salary_to' => 'required | numeric',
            'employee_rate' => 'required | numeric',
            'employer_rate' => 'required | numeric',
            'maximum_contribution' => 'required | numeric',
        ]);
        
        Pagibig::create($request->all());
        return redirect()->route('pagibig.index')
            ->with('success', 'Pagibig record added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pagibig $pagibig)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pagibig $pagibig)
    {
        $pagibig = Pagibig::all();
        return view('pagibig.edit', compact('pagibig'));    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pagibig $pagibig)
    {
        $request->validate([
            'salary_from' => 'required | numeric',
            'salary_to' => 'required | numeric',
            'employee_rate' => 'required | numeric',
            'employer_rate' => 'required | numeric',
            'maximum_contribution' => 'required | numeric',
        ]);

        $pagibig->update($request->all());

        
        return redirect()->route('pagibig.index')
            ->with('success', 'Pag-IBIG bracket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pagibig $pagibig)
    {
        $pagibig = Pagibig::findOrFail($pagibig->pagibig_id);
        $pagibig->delete();
        return redirect()->route('pagibig.index')
            ->with('success', 'Pagibig rates deleted successfully');
    }
}
