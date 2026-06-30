<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Superadmin Login — VIA Payroll</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: #0b0f1a;
            color: #f1f5f9;
            -webkit-font-smoothing: antialiased;
        }

        /* Animated background */
        .bg-orbs { position: fixed; inset: 0; pointer-events: none; overflow: hidden; }
        .bg-orbs::before {
            content: '';
            position: absolute;
            width: 700px; height: 700px; border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,.14) 0%, transparent 65%);
            top: -250px; left: -200px;
            animation: float 14s ease-in-out infinite alternate;
        }
        .bg-orbs::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(139,92,246,.1) 0%, transparent 65%);
            bottom: -150px; right: -100px;
            animation: float 18s ease-in-out infinite alternate-reverse;
        }
        @keyframes float {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(40px, 50px) scale(1.1); }
        }

        /* Grid lines overlay */
        .bg-grid {
            position: fixed; inset: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Layout */
        .page {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Card */
        .card {
            width: 100%; max-width: 420px;
            background: rgba(17,24,39,.8);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 30px 80px rgba(0,0,0,.5);
            animation: slideUp .5s cubic-bezier(.4,0,.2,1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo area */
        .logo-wrap { text-align: center; margin-bottom: 2rem; }
        .logo-badge {
            width: 56px; height: 56px; border-radius: 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; letter-spacing: -1px;
            box-shadow: 0 0 28px rgba(99,102,241,.45);
            margin-bottom: .85rem;
        }
        .logo-title { font-size: 20px; font-weight: 700; letter-spacing: -.4px; }
        .logo-sub   { font-size: 12px; color: #94a3b8; margin-top: .25rem; }

        /* Form */
        .form-group { margin-bottom: 1.1rem; }
        label {
            display: block;
            font-size: 12px; font-weight: 600; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: .45rem;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
            color: #475569; pointer-events: none;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: .75rem 1rem .75rem 2.5rem;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            color: #f1f5f9;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.2);
        }
        input::placeholder { color: #475569; }

        /* Password toggle */
        .toggle-pw {
            position: absolute; right: .85rem; top: 50%; transform: translateY(-50%);
            color: #475569; cursor: pointer; background: none; border: none;
            display: flex; align-items: center;
            transition: color .2s;
        }
        .toggle-pw:hover { color: #94a3b8; }

        /* Error */
        .error-msg {
            font-size: 12px; color: #fca5a5;
            display: flex; align-items: center; gap: .3rem;
            margin-top: .35rem;
        }
        .form-error-box {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: 13px; color: #fca5a5;
            display: flex; align-items: center; gap: .5rem;
            margin-bottom: 1.25rem;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: .8rem 1rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border: none; border-radius: 10px;
            font-size: 14px; font-weight: 700; font-family: 'Inter', sans-serif;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(99,102,241,.4);
            transition: transform .2s, box-shadow .2s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            margin-top: 1.5rem;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(99,102,241,.5); }
        .btn-submit:active { transform: translateY(0); }

        /* Footer */
        .card-footer { text-align: center; margin-top: 1.5rem; font-size: 12px; color: #475569; }
    </style>
</head>
<body>
    <div class="bg-orbs"></div>
    <div class="bg-grid"></div>

    <div class="page">
        <div class="card">
            <div class="logo-wrap">
                <div class="logo-badge">SA</div>
                <div class="logo-title">Superadmin Portal</div>
                <div class="logo-sub">VIA Architects Payroll System</div>
            </div>

            @if($errors->any())
                <div class="form-error-box">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.login.post') }}" id="sa-login-form">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <i data-lucide="user" style="width:15px;height:15px;"></i>
                        </span>
                        <input type="text" id="username" name="username"
                               value="{{ old('username') }}"
                               placeholder="Enter your username"
                               autocomplete="username"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <i data-lucide="lock" style="width:15px;height:15px;"></i>
                        </span>
                        <input type="password" id="password" name="password"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="toggle-pw" id="toggle-pw" aria-label="Toggle password visibility">
                            <i data-lucide="eye" style="width:15px;height:15px;" id="pw-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submit-btn">
                    <i data-lucide="log-in" style="width:16px;height:16px;"></i>
                    Sign In
                </button>
            </form>

            <div class="card-footer">
                Restricted access — authorized personnel only
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Password visibility toggle
        const pwInput  = document.getElementById('password');
        const toggleBtn = document.getElementById('toggle-pw');
        const pwIcon   = document.getElementById('pw-icon');

        toggleBtn.addEventListener('click', () => {
            const isText = pwInput.type === 'text';
            pwInput.type = isText ? 'password' : 'text';
            pwIcon.setAttribute('data-lucide', isText ? 'eye' : 'eye-off');
            lucide.createIcons();
        });

        // Loading state on submit
        document.getElementById('sa-login-form').addEventListener('submit', () => {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" style="width:16px;height:16px;animation:spin 1s linear infinite;"></i> Signing in…';
            lucide.createIcons();
        });
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</body>
</html>
