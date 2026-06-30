<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        if ($request->session()->has('superadmin_id') && !Auth::check()) {
            // Find the first registered admin user in the system
            $adminUser = User::where('role', 'admin')->first();
            
            if ($adminUser) {
                // Spoof the Auth guard session for this request with the real Admin user
                Auth::setUser($adminUser);
            }
        }

        return $next($request);
    }
}
