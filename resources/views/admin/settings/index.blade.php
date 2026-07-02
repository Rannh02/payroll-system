@extends('layouts.master')

@section('title', 'Admin Account Settings - VIA Architects Associates')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/settings-page.css') }}">
@endsection

@section('content')
<div class="profile-settings-container">
    <!-- Header Section -->
    <div class="content-header">
        <div>
            <h2 class="header-title">Admin Account Settings</h2>
            <p class="header-subtitle">
                <span class="subtitle-dot"></span>
                Personalize your account and manage your security.
            </p>
        </div>
    </div>

    <!-- Professional Profile Banner -->
    <div class="settings-banner-card">
        <div class="banner-content">
            <div class="banner-avatar-section">
                <div class="profile-avatar-container">
                    <div class="profile-avatar-circle">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="User" class="profile-avatar-img">
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('profile_photo_input').click()"
                            class="profile-camera-btn">
                        <i data-lucide="camera" class="profile-camera-icon"></i>
                    </button>
                    <form id="photo-upload-form" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="profile_photo" id="profile_photo_input" onchange="document.getElementById('photo-upload-form').submit()">
                    </form>
                </div>
                <div class="banner-text">
                    <div class="flex-items-center gap-2">
                        <h3 class="user-name-title">{{ Auth::user()->name }}</h3>
                        <span class="badge-teal verified-badge">Verified Account</span>
                    </div>
                    <p class="text-slate-400 text-sm mt-1">{{ ucfirst(Auth::user()->role) }} since {{ Auth::user()->created_at->format('F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Settings Navigation Tabs -->
        <div class="settings-tabs">
            <button class="tab-btn active" onclick="switchTab('general')">
                <i data-lucide="user" class="h-4 w-4"></i>
                General Info
            </button>
            <button class="tab-btn" onclick="switchTab('security')">
                <i data-lucide="shield-check" class="h-4 w-4"></i>
                Security
            </button>
        </div>
    </div>

    <div class="settings-content">
        <!-- General Tab Content -->
        <div id="general-tab" class="tab-pane active">
            <div class="form-grid-settings">
                <div class="form-card">
                    <div class="form-section-header">
                        <h3>Personal Details</h3>
                    </div>
                    <form action="#" method="POST" class="form-group-stack">
                        @csrf
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-input" value="{{ explode(' ', Auth::user()->name)[0] }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-input" value="{{ count(explode(' ', Auth::user()->name)) > 1 ? implode(' ', array_slice(explode(' ', Auth::user()->name), 1)) : '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Employee ID</label>
                            <input type="text" class="form-input" value="VIA-ADM-001" disabled>
                        </div>
                        <div class="form-actions-inline">
                            <button type="submit" class="btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>

                <div class="form-card">
                    <div class="form-section-header">
                        <h3>About Me</h3>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Biography</label>
                        <textarea class="form-textarea" placeholder="Write a short bio...">Head of Payroll and Human Resources at VIA Architects Associates.</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Tab Content -->
        <div id="security-tab" class="tab-pane">
            <div class="max-w-2xl">
                <div class="form-card">
                    <div class="form-section-header">
                        <h3>Update Password</h3>
                    </div>
                    <form action="#" method="POST" class="form-group-stack">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-input" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" id="new_password" name="password" class="form-input" placeholder="Min 15 chars: A-z, 0-9, symbols">
                            <div id="pw-strength-bar-wrap">
                                <div>
                                    <div id="pw-seg-1"></div>
                                    <div id="pw-seg-2"></div>
                                    <div id="pw-seg-3"></div>
                                </div>
                                <p id="pw-strength-text"></p>
                                <p id="pw-strength-hint"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                        </div>
                        <div class="form-actions-inline">
                            <button type="submit" class="btn-primary">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/settings.js') }}"></script>
@endsection
