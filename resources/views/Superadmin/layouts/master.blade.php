<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Superadmin Control Panel')</title>

    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/common/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin/layout.css') }}">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    @yield('styles')

    <!-- Theme Initialization Script -->
    <script>
        (function () {
            // Control panels are light/white by default
            document.documentElement.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        })();
    </script>
</head>

<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
    <!-- Architectural Background Overlay -->
    <div class="bg-architectural-overlay"></div>

    <div class="dashboard-layout">
        <!-- Top Navbar -->
        <header class="nav-header">
            <div class="brand-container">
                <div class="brand-logo-box">VIA</div>
                <h1 class="brand-title">Control Deck</h1>
            </div>

            <div class="nav-actions">
                <div class="profile-container">
                    <button id="profile-btn" class="profile-trigger">
                        <div class="profile-avatar-gradient">
                            <div class="profile-avatar-inner"
                                style="display:flex;align-items:center;justify-content:center;background:#ffffff;color:#0f172a;font-weight:bold;font-family:'JetBrains Mono', monospace;font-size: 11px;">
                                CORE
                            </div>
                        </div>
                        <div class="profile-info">
                            <p class="profile-name">{{ session('superadmin_username') }}</p>
                            <p class="profile-role"
                                style="color: var(--accent); font-weight: 700; font-family: 'JetBrains Mono'; font-size: 9px;">
                                SYSTEM ROOT</p>
                        </div>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-500 transition-colors"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profile-dropdown" class="dropdown-menu">
                        <div class="dropdown-header">
                            <p class="dropdown-label">Account</p>
                            <p class="dropdown-user-name">{{ session('superadmin_username') }}</p>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('superadmin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-logout">
                                <i data-lucide="log-out" class="h-4 w-4"></i>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="dashboard-wrapper">
            <!-- Sidebar Navigation -->
            @include('Superadmin.layouts.navitems.sidebar')

            <!-- Main Content Container -->
            <main class="main-content" style="padding: 2rem;">
                @yield('content')
            </main>
        </div>
    </div>


























    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lucide Icons
            lucide.createIcons();

            // Profile Dropdown Logic
            const profileBtn = document.getElementById('profile-btn');
            const profileDropdown = document.getElementById('profile-dropdown');

            if (profileBtn) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('show');
                });
            }

            window.addEventListener('click', () => {
                if (profileDropdown) profileDropdown.classList.remove('show');
            });

            if (profileDropdown) {
                profileDropdown.addEventListener('click', (e) => e.stopPropagation());
            }

            // Sidebar Collapse Logic
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');
            let isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            const updateSidebarUI = (collapsed) => {
                sidebar.classList.toggle('sidebar-collapsed', collapsed);
                sidebarTexts.forEach(text => text.style.display = collapsed ? 'none' : 'block');
            };

            if (sidebar && sidebarToggle) {
                updateSidebarUI(isSidebarCollapsed);
                sidebarToggle.addEventListener('click', () => {
                    isSidebarCollapsed = !isSidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', isSidebarCollapsed);
                    updateSidebarUI(isSidebarCollapsed);
                });
            }
        });
    </script>

    @yield('scripts')
    {{-- Re-run Lucide after all page-specific scripts --}}
    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>

</html>