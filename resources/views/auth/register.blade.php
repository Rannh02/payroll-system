<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register | VIA Architects Associates Payroll System</title>
    
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
                <p class="brand-subtitle">Create an Account</p>
            </div>

            <form action="{{ route('register.store') }}" method="POST">
                @csrf
                
                <div class="form-row" style="display: flex; gap: 16px; margin-bottom: 24px;">
                    <div class="form-group" style="margin-bottom: 0; flex: 1;">
                        <label class="form-label" for="first_name">First Name</label>
                        <div class="input-wrapper">
                            <input type="text" id="first_name" name="first_name" class="form-input @error('first_name') is-invalid @enderror" placeholder="John" value="{{ old('first_name') }}" style="padding-left: 16px;" required autofocus>
                        </div>
                        @error('first_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0; flex: 1;">
                        <label class="form-label" for="middle_name">Middle Name</label>
                        <div class="input-wrapper">
                            <input type="text" id="middle_name" name="middle_name" class="form-input @error('middle_name') is-invalid @enderror" placeholder="A. (Optional)" value="{{ old('middle_name') }}" style="padding-left: 16px;">
                        </div>
                        @error('middle_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row" style="display: flex; gap: 16px; margin-bottom: 24px;">
                    <div class="form-group" style="margin-bottom: 0; flex: 2;">
                        <label class="form-label" for="last_name">Last Name</label>
                        <div class="input-wrapper">
                            <input type="text" id="last_name" name="last_name" class="form-input @error('last_name') is-invalid @enderror" placeholder="Doe" value="{{ old('last_name') }}" style="padding-left: 16px;" required>
                        </div>
                        @error('last_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0; flex: 1;">
                        <label class="form-label" for="suffix">Suffix</label>
                        <div class="input-wrapper">
                            <input type="text" id="suffix" name="suffix" class="form-input @error('suffix') is-invalid @enderror" placeholder="Jr. (Optional)" value="{{ old('suffix') }}" style="padding-left: 16px;">
                        </div>
                        @error('suffix')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="email" id="email" name="email" class="form-input @error('email') is-invalid @enderror" placeholder="email@via-architect.com" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0110 0v4"></path>
                        </svg>
                        <input type="password" id="password" name="password" class="form-input @error('password') is-invalid @enderror" placeholder="••••••••" required>
                        <button type="button" id="togglePassword" class="toggle-password-btn">
                            <svg id="eyeIcon" class="eye-icon" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 32px;">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0110 0v4"></path>
                        </svg>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    Create Account
                    <svg class="btn-icon" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 00-4-4H5c-1.1 0-2 .9-2 2v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                </button>
            </form>

            <div class="footer-text">
                Already have an account? <a href="{{ route('login') }}" class="footer-link">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');

            if (togglePassword && password && eyeIcon) {
                togglePassword.addEventListener('click', function (e) {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    if (type === 'text') {
                        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    } else {
                        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                    }
                });
            }
        });
    </script>
</body>
</html>
