<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperadminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has('superadmin_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
