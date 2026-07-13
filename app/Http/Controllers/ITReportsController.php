<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ITReportsController extends Controller
{
    /**
     * Display the User Activity Report dashboard.
     */
    public function userActivity(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->endOfMonth()->toDateString());
        $search = $request->query('search');

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $query = LoginLog::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['SUCCESS', 'UNLOCKED']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('browser', 'like', "%{$search}%");
            });
        }

        // Summary Stats (Filtered by Date Range only for consistency)
        $statsQuery = LoginLog::whereBetween('created_at', [$start, $end]);
        
        $totalLogins = (clone $statsQuery)->where('status', 'SUCCESS')->count();
        $uniqueUsers = (clone $statsQuery)->whereIn('status', ['SUCCESS', 'UNLOCKED'])->distinct()->count('email');
        $totalUnlocks = (clone $statsQuery)->where('status', 'UNLOCKED')->count();

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('it_admin.reports.user_activity', compact(
            'logs', 'dateFrom', 'dateTo', 'search',
            'totalLogins', 'uniqueUsers', 'totalUnlocks'
        ));
    }

    /**
     * Export the User Activity Report to PDF.
     */
    public function exportUserActivityPdf(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->endOfMonth()->toDateString());
        $search = $request->query('search');

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $query = LoginLog::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['SUCCESS', 'UNLOCKED']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('browser', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->get();

        // Stats for the PDF header
        $statsQuery = LoginLog::whereBetween('created_at', [$start, $end]);
        $totalLogins = (clone $statsQuery)->where('status', 'SUCCESS')->count();
        $uniqueUsers = (clone $statsQuery)->whereIn('status', ['SUCCESS', 'UNLOCKED'])->distinct()->count('email');
        $totalUnlocks = (clone $statsQuery)->where('status', 'UNLOCKED')->count();

        $pdf = Pdf::loadView('it_admin.reports.pdf.user_activity', compact(
            'logs', 'dateFrom', 'dateTo', 'search',
            'totalLogins', 'uniqueUsers', 'totalUnlocks'
        ));

        return $pdf->download("user_activity_report_{$dateFrom}_to_{$dateTo}.pdf");
    }

    /**
     * Display the Security Incident Report dashboard.
     */
    public function securityIncident(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->endOfMonth()->toDateString());
        $search = $request->query('search');

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $query = LoginLog::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['FAILED', 'LOCKED']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('browser', 'like', "%{$search}%");
            });
        }

        // Summary Stats (Filtered by Date Range only for consistency)
        $statsQuery = LoginLog::whereBetween('created_at', [$start, $end]);
        
        $totalIncidents = (clone $statsQuery)->whereIn('status', ['FAILED', 'LOCKED'])->count();
        $bruteForceAttempts = (clone $statsQuery)->where('status', 'FAILED')->count();
        $accountLockouts = (clone $statsQuery)->where('status', 'LOCKED')->count();

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('it_admin.reports.security_incident', compact(
            'logs', 'dateFrom', 'dateTo', 'search',
            'totalIncidents', 'bruteForceAttempts', 'accountLockouts'
        ));
    }

    /**
     * Export the Security Incident Report to PDF.
     */
    public function exportSecurityIncidentPdf(Request $request)
    {
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->endOfMonth()->toDateString());
        $search = $request->query('search');

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $query = LoginLog::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['FAILED', 'LOCKED']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('browser', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->get();

        // Stats for the PDF header
        $statsQuery = LoginLog::whereBetween('created_at', [$start, $end]);
        $totalIncidents = (clone $statsQuery)->whereIn('status', ['FAILED', 'LOCKED'])->count();
        $bruteForceAttempts = (clone $statsQuery)->where('status', 'FAILED')->count();
        $accountLockouts = (clone $statsQuery)->where('status', 'LOCKED')->count();

        $pdf = Pdf::loadView('it_admin.reports.pdf.security_incident', compact(
            'logs', 'dateFrom', 'dateTo', 'search',
            'totalIncidents', 'bruteForceAttempts', 'accountLockouts'
        ));

        return $pdf->download("security_incident_report_{$dateFrom}_to_{$dateTo}.pdf");
    }
}
