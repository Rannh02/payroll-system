<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperadminGuest
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('superadmin_id')) {
            return redirect()->route('superadmin.dashboard');
        }

        return $next($request);
    }
}
