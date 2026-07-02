<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class SuperadminAuthSpoof
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('superadmin_id') && !Auth::guard('admin')->check()) {
            $adminUser = Admin::latest()->first();

            if ($adminUser) {
                Auth::guard('admin')->setUser($adminUser);
            }
        }

        return $next($request);
    }
}
