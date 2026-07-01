@extends('Superadmin.layouts.master')

@section('title', 'Control Deck')

@section('content')
    <div style="max-width: 1600px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">


        <div class="content-header">
            <div>
                <h2 class="header-title">Overview</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Administrator! Here's your current happenings.
                </p>
            </div>

        </div>
        <!-- System Stats Metrics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Active Sessions</h3>
                <p class="stat-value">1</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Login Attempts (failed)</h3>
                <p class="stat-value">32</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Locked Accounts</h3>
                <p class="stat-value">12</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Security Alerts</h3>
                <p class="stat-value">5</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Audit Logs (today)</h3>
                <p class="stat-value">8</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Backup Status</h3>
                <p class="stat-value">Successful</p>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">System Health</h3>
                <p class="stat-value">99.9%</p>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-teal">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                </div>
                <h3 class="stat-label">Audit Logs (today)</h3>
                <p class="stat-value">1</p>
            </div>
        </div>

        <!-- Detailed Status Logs / Panel Console -->

    </div>
@endsection