<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password | VIA Architects Associates Payroll System</title>

    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">

    <!-- Theme Initialization Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
</head>
<body>
    <div class="bg-overlay"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-logo">
                    <span>VIA ARCHITECTS ASSOCIATES</span>
                </div>
                <p class="brand-subtitle">Set a new password for your account</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" style="background:#fee2e2; color:#991b1b; border-radius:0.5rem; padding:0.75rem 1rem; margin-bottom:1rem; font-size:0.875rem;">
                    <ul style="margin:0; padding-left:1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf

                <!-- Hidden fields -->
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="email" id="email" name="email"
                            class="form-input @error('email') is-invalid @enderror"
                            value="{{ old('email', request()->email) }}"
                            readonly
                            style="background: var(--surface, #f8fafc); color: var(--slate-500, #64748b); cursor: not-allowed;">
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0110 0v4"></path>
                        </svg>
                        <input type="password" id="password" name="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="Min 15 chars: A-z, 0-9, symbols" required>
                        <button type="button" id="togglePassword" class="toggle-password-btn">
                            <svg id="eyeIcon" class="eye-icon" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <div id="pw-strength-bar-wrap" style="margin-top: 8px; display: none;">
                        <div style="display: flex; gap: 5px; margin-bottom: 5px;">
                            <div id="pw-seg-1" style="flex:1; height:4px; border-radius:4px; background:#e2e8f0; transition:background 0.3s;"></div>
                            <div id="pw-seg-2" style="flex:1; height:4px; border-radius:4px; background:#e2e8f0; transition:background 0.3s;"></div>
                            <div id="pw-seg-3" style="flex:1; height:4px; border-radius:4px; background:#e2e8f0; transition:background 0.3s;"></div>
                        </div>
                        <p id="pw-strength-text" style="font-size:0.78rem; font-weight:600; margin:0;"></p>
                        <p id="pw-strength-hint" style="font-size:0.73rem; color:#64748b; margin:3px 0 0 0;"></p>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0110 0v4"></path>
                        </svg>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-input"
                            placeholder="Re-enter your new password" required>
                        <button type="button" id="toggleConfirm" class="toggle-password-btn">
                            <svg id="eyeIconConfirm" class="eye-icon" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    Reset Password
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <div class="footer-text">
                Remember your password? <a href="{{ route('login') }}" class="footer-link">Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function setupToggle(toggleId, inputId, iconId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (toggle && input && icon) {
                    toggle.addEventListener('click', function () {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);

                        if (type === 'text') {
                            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                        } else {
                            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                        }
                    });
                }
            }

            setupToggle('togglePassword', 'password', 'eyeIcon');
            setupToggle('toggleConfirm', 'password_confirmation', 'eyeIconConfirm');

            // --- Password Strength Checker ---
            const pwInput = document.getElementById('password');
            if (pwInput) {
                pwInput.addEventListener('input', function() { checkPasswordStrength(this.value); });
            }

            function checkPasswordStrength(val) {
                const wrap = document.getElementById('pw-strength-bar-wrap');
                const seg1 = document.getElementById('pw-seg-1');
                const seg2 = document.getElementById('pw-seg-2');
                const seg3 = document.getElementById('pw-seg-3');
                const text = document.getElementById('pw-strength-text');
                const hint = document.getElementById('pw-strength-hint');

                if (!val) { wrap.style.display = 'none'; return; }
                wrap.style.display = 'block';

                const hasUpper   = /[A-Z]/.test(val);
                const hasLower   = /[a-z]/.test(val);
                const hasNumber  = /[0-9]/.test(val);
                const hasSymbol  = /[^A-Za-z0-9]/.test(val);
                const longEnough = val.length >= 15;

                const score = [hasUpper, hasLower, hasNumber, hasSymbol, longEnough].filter(Boolean).length;

                const hints = [];
                if (!hasUpper)   hints.push('uppercase letter');
                if (!hasLower)   hints.push('lowercase letter');
                if (!hasNumber)  hints.push('number');
                if (!hasSymbol)  hints.push('symbol');
                if (!longEnough) hints.push('at least 15 characters');

                let level, color, segs;
                if (score <= 2)      { level = 'Weak';     color = '#ef4444'; segs = 1; }
                else if (score <= 3) { level = 'Moderate'; color = '#f59e0b'; segs = 2; }
                else                 { level = 'Strong';   color = '#22c55e'; segs = 3; }

                seg1.style.background = segs >= 1 ? color : '#e2e8f0';
                seg2.style.background = segs >= 2 ? color : '#e2e8f0';
                seg3.style.background = segs >= 3 ? color : '#e2e8f0';

                text.textContent = level;
                text.style.color = color;
                hint.textContent = hints.length ? 'Missing: ' + hints.join(', ') : '✓ All requirements met';
                hint.style.color = hints.length ? '#94a3b8' : '#22c55e';
            }
        });
    </script>
</body>
</html>
