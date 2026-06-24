<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.master', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                
                if ($user->role === 'admin') {
                    $pendingLeaves = \App\Models\Leave_Request::with('employee')
                        ->where('status', 'pending')
                        ->where('is_cleared_by_admin', false)
                        ->latest()
                        ->take(5)
                        ->get();
                    
                    $view->with([
                        'pendingLeaveCount' => \App\Models\Leave_Request::where('status', 'pending')->where('is_viewed_by_admin', false)->count(),
                        'recentPendingLeaves' => $pendingLeaves
                    ]);
                } else {
                    $employee = \App\Models\Employee::where('user_id', $user->id)->first();
                    if ($employee) {
                        $processedLeaves = \App\Models\Leave_Request::where('employee_id', $employee->employee_id)
                            ->whereIn('status', ['approved', 'rejected'])
                            ->where('is_cleared_by_employee', false)
                            ->latest()
                            ->take(5)
                            ->get();
                        
                        $view->with([
                            'pendingLeaveCount' => \App\Models\Leave_Request::where('employee_id', $employee->employee_id)
                                ->whereIn('status', ['approved', 'rejected'])
                                ->where('is_viewed_by_employee', false)
                                ->count(),
                            'recentPendingLeaves' => $processedLeaves
                        ]);
                    } else {
                        $view->with([
                            'pendingLeaveCount' => 0,
                            'recentPendingLeaves' => collect()
                        ]);
                    }
                }
            }
        });
    }
}
