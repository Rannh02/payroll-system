<?php

namespace App\Http\Controllers;

use App\Models\Sss;
use Illuminate\Http\Request;

class SssController extends Controller
{
    public function index()
    {
        $sss = Sss::paginate(10);
        return view('admin.Government Contribution.sss.index', compact('sss'));
    }

    public function create()
    {
        return view('admin.Government Contribution.sss.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sss_range_from'        => 'required|numeric',
            'sss_range_to'          => 'required|numeric',
            'monthly_salary_credit' => 'required|numeric',
            'employee_share'        => 'required|numeric',
            'employer_share'        => 'required|numeric',
        ]);

        Sss::create($request->all());

        return redirect()->route('sss.index')
            ->with('success', 'SSS contribution added successfully.');
    }

    public function show(Sss $sss)
    {
        return view('admin.Government Contribution.sss.index', compact('sss'));
    }

    public function edit(Sss $sss)
    {
        return view('admin.Government Contribution.sss.index', compact('sss'));
    }

    public function update(Request $request, Sss $sss)
    {
        $request->validate([
            'sss_range_from'        => 'required|numeric',
            'sss_range_to'          => 'required|numeric',
            'monthly_salary_credit' => 'required|numeric',
            'employee_share'        => 'required|numeric',
            'employer_share'        => 'required|numeric',
        ]);

        $sss->update($request->all());

        return redirect()->route('sss.index')
            ->with('success', 'SSS contribution updated successfully.');
    }

    public function destroy(Sss $sss)
    {
        $sss->delete();

        return redirect()->route('sss.index')
            ->with('success', 'SSS contribution deleted successfully.');
    }
}

