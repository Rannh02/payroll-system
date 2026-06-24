<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::paginate(10);
        return view('admin.Government Contribution.tax.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.Government Contribution.tax.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'salary_from' => 'required|numeric',
            'salary_to'   => 'required|numeric',
            'base_tax'    => 'required|numeric',
            'tax_rate'    => 'required|numeric',
        ]);

        Tax::create($request->all());

        return redirect()->route('tax.index')
            ->with('success', 'Tax bracket added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax)
    {
        return view('admin.Government Contribution.tax.index', compact('tax'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        return view('admin.Government Contribution.tax.index', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'salary_from' => 'required|numeric',
            'salary_to'   => 'required|numeric',
            'base_tax'    => 'required|numeric',
            'tax_rate'    => 'required|numeric',
        ]);

        $tax->update($request->all());

        return redirect()->route('tax.index')
            ->with('success', 'Tax bracket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('tax.index')
            ->with('success', 'Tax bracket deleted successfully.');
    }
}
