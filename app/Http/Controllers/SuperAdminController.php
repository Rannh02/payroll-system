<?php

namespace App\Http\Controllers;

use App\Models\Superadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────────────
    public function dashboard()
    {
        return view('Superadmin.dashboard');
    }

    // ── Logout ─────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->session()->forget(['superadmin_id', 'superadmin_username']);
        $request->session()->regenerate();

        return redirect()->route('login');
    }
}