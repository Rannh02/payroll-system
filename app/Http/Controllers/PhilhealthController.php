<?php

namespace App\Http\Controllers;

use App\Models\Philhealth;
use Illuminate\Http\Request;

class PhilhealthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $philhealth = Philhealth::paginate(10);
        return view('admin.Government Contribution.philhealth.index', compact('philhealth'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.Government Contribution.philhealth.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'salary_from'       => 'required|numeric',
            'salary_to'         => 'required|numeric',
            'contribution_rate' => 'required|numeric',
        ]);

        Philhealth::create($request->all());

        return redirect()->route('philhealth.index')
            ->with('success', 'PhilHealth record added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Philhealth $philhealth)
    {
        return view('admin.Government Contribution.philhealth.index', compact('philhealth'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Philhealth $philhealth)
    {
        return view('admin.Government Contribution.philhealth.index', compact('philhealth'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Philhealth $philhealth)
    {
        $request->validate([
            'salary_from'       => 'required|numeric',
            'salary_to'         => 'required|numeric',
            'contribution_rate' => 'required|numeric',
        ]);

        $philhealth->update($request->all());

        return redirect()->route('philhealth.index')
            ->with('success', 'PhilHealth record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Philhealth $philhealth)
    {
        $philhealth->delete();

        return redirect()->route('philhealth.index')
            ->with('success', 'PhilHealth record deleted successfully.');
    }
}
