<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'VIA Architects Associates')</title>

    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/common/dashboard.css') }}">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    @yield('styles')

    <!-- Theme Initialization Script -->
    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
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
                <h1 class="brand-title">Architects Association</h1>
            </div>

            <div class="nav-actions">
                <div class="profile-container">
                    <button id="notification-btn" class="icon-btn">
                        <i data-lucide="bell" class="h-5 w-5"></i>
                        @if($pendingLeaveCount > 0)
                            <span class="notification-dot"></span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown" class="dropdown-menu notification-dropdown">
                        <div class="notification-header">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <h3 class="dropdown-label" style="margin: 0;">Notifications</h3>
                                @if($pendingLeaveCount > 0)
                                    <span class="badge-teal" id="notif-badge">{{ $pendingLeaveCount }} New</span>
                                @endif
                            </div>
                            <button onclick="clearAllNotifications(event)" class="text-xs font-bold hover:underline btn-clear-all">Clear All</button>
                        </div>
                        
                        <div class="notification-list" id="notification-list">
                            @forelse($recentPendingLeaves as $leave)
                                @php
                                    $isAdmin = Auth::user()->role === 'admin';
                                    $targetRoute = $isAdmin ? route('approval_workflow.index') : route('user.my_requests');
                                    $title = $isAdmin ? 'Leave Request Pending' : 'Leave ' . ucfirst($leave->status);
                                    $icon = $leave->status === 'approved' ? 'check-circle' : ($leave->status === 'rejected' ? 'x-circle' : 'calendar');
                                    $iconColor = $leave->status === 'approved' ? '#10b981' : ($leave->status === 'rejected' ? '#ef4444' : 'var(--primary)');
                                @endphp
                                <a href="{{ $targetRoute }}" class="notification-item">
                                    <div class="notification-icon-box" style="background-color: {{ $iconColor }}15; color: {{ $iconColor }};">
                                        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-title">{{ $title }}</p>
                                        <p class="notification-desc">
                                            @if($isAdmin)
                                                <strong>{{ $leave->employee->name }}</strong> requested for {{ $leave->leave_type }}.
                                            @else
                                                Your <strong>{{ $leave->leave_type }}</strong> request has been {{ $leave->status }}.
                                            @endif
                                        </p>
                                        <span class="notification-time">{{ $leave->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="empty-notifications" id="empty-notif-msg">
                                    <i data-lucide="bell-off" class="h-8 w-8 mb-2"></i>
                                    <p>No new notifications</p>
                                </div>
                            @endforelse
                        </div>

                        <div id="notif-footer" class="notification-footer {{ $pendingLeaveCount > 0 || count($recentPendingLeaves) > 0 ? '' : 'hidden' }}">
                            @php
                                $viewAllRoute = Auth::user()->role === 'admin' ? route('approval_workflow.index') : route('user.my_requests');
                            @endphp
                            <a href="{{ $viewAllRoute }}" class="view-all-link">View All Requests</a>
                        </div>
                    </div>
                </div>

                <!-- Profile Section -->
                <div class="profile-container">
                    <button id="profile-btn" class="profile-trigger">
                        <div class="profile-avatar-gradient">
                            <div class="profile-avatar-inner">
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="User"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="profile-info">
                            <p class="profile-name">{{ Auth::user()->name }}</p>
                            <p class="profile-role">{{ Auth::user()->role === 'admin' ? 'Administrator' : 'Employee' }}
                            </p>
                        </div>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-500 transition-colors"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profile-dropdown" class="dropdown-menu">
                        <div class="dropdown-header">
                            <p class="dropdown-label">Account</p>
                            <p class="dropdown-user-name">{{ Auth::user()->name }}</p>
                        </div>

                        <a href="{{ route('profile.settings') }}" class="dropdown-item">
                            <i data-lucide="user" class="h-4 w-4"></i>
                            Profile Settings
                        </a>

                        <button id="theme-toggle" class="dropdown-item">
                            <i data-lucide="moon" class="h-4 w-4 theme-icon"></i>
                            <span class="theme-text">Dark Mode</span>
                        </button>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
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
            <aside id="sidebar" class="sidebar">
                <nav class="sidebar-nav">
                    <button id="sidebar-toggle" class="sidebar-toggle-btn">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('dashboard') }}"
                            class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                            <span class="sidebar-text">Dashboard</span>
                        </a>
                        <a href="{{ route('employees.create') }}"
                            class="sidebar-link {{ request()->routeIs('employees.create') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="users" class="h-5 w-5"></i>
                            <span class="sidebar-text">Add Employees</span>
                        </a>

                        <a href="{{ route('employees.index') }}"
                            class="sidebar-link {{ request()->routeIs('employees.index') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="user-check" class="h-5 w-5"></i>
                            <span class="sidebar-text">Manage Employees</span>
                        </a>

                        <a href="{{ route('attendance.index') }}"
                            class="sidebar-link {{ request()->routeIs('attendance.index') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="calendar" class="h-5 w-5"></i>
                            <span class="sidebar-text">Attendance</span>

                        <a href="{{ route('department.index')}}"
                            class="sidebar-link {{ request()->routeIs('department.index') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="briefcase" class="h-5 w-5"></i>
                            <span class="sidebar-text">Department</span>
                        </a>

                        <a href="{{ route('position.index')}}"
                            class="sidebar-link {{ request()->routeIs('position.index') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="briefcase" class="h-5 w-5"></i>
                            <span class="sidebar-text">Position</span>
                        </a>

                        <a href="{{ route('payroll.index') }}"
                            class="sidebar-link {{ request()->routeIs('payroll.index') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="banknote" class="h-5 w-5"></i>
                            <span class="sidebar-text">Payroll</span>
                        </a>

                        <a href="{{ route('approval_workflow.index')}}"
                            class="sidebar-link {{ request()-> routeIs ('approval_workflow.index') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="calendar" class="h-5 w-5"></i>
                            <span class="sidebar-text">Approval Workflow</span>
                        </a>

                        {{-- Government Contributions Accordion --}}
                        @php
                            $govtRoutes = ['sss.index', 'philhealth.index', 'pagibig.index', 'tax.index'];
                            $govtActive = request()->routeIs($govtRoutes);
                        @endphp

                        <div class="sidebar-group">
                            <button id="govt-toggle"
                                class="sidebar-link sidebar-group-btn w-full {{ $govtActive ? 'sidebar-link-active' : '' }}"
                                onclick="toggleGovtMenu()"
                                style="background:none; border:none; cursor:pointer; width:100%; text-align:left;">
                                <i data-lucide="landmark" class="h-5 w-5"></i>
                                <span class="sidebar-text">Government Contributions</span>
                                <i data-lucide="chevron-down" class="h-4 w-4 sidebar-text govt-chevron" id="govt-chevron"
                                    style="margin-left:auto; transition: transform 0.3s;"></i>
                            </button>

                            <div id="govt-submenu" class="sidebar-submenu" style="display:none; padding-left:1rem;">
                                <a href="{{ route('sss.index') }}"
                                    class="sidebar-link sidebar-sublink {{ request()->routeIs('sss.index') ? 'sidebar-link-active' : '' }}">
                                    <i data-lucide="shield" class="h-4 w-4"></i>
                                    <span class="sidebar-text">SSS</span>
                                </a>
                                <a href="{{ route('philhealth.index') }}"
                                    class="sidebar-link sidebar-sublink {{ request()->routeIs('philhealth.index') ? 'sidebar-link-active' : '' }}">
                                    <i data-lucide="heart-pulse" class="h-4 w-4"></i>
                                    <span class="sidebar-text">PhilHealth</span>
                                </a>
                                <a href="{{ route('pagibig.index') }}"
                                    class="sidebar-link sidebar-sublink {{ request()->routeIs('pagibig.index') ? 'sidebar-link-active' : '' }}">
                                    <i data-lucide="home" class="h-4 w-4"></i>
                                    <span class="sidebar-text">Pag-IBIG</span>
                                </a>
                                <a href="{{ route('tax.index') }}"
                                    class="sidebar-link sidebar-sublink {{ request()->routeIs('tax.index') ? 'sidebar-link-active' : '' }}">
                                    <i data-lucide="receipt" class="h-4 w-4"></i>
                                    <span class="sidebar-text">Tax</span>
                                </a>
                            </div>
                        </div>

                        {{-- Security Logs Accordion --}}
                        @php
                            $securityRoutes = ['security_logs.login'];
                            $securityActive = request()->routeIs($securityRoutes);
                        @endphp

                        <div class="sidebar-group">
                            <button id="security-toggle"
                                class="sidebar-link sidebar-group-btn w-full {{ $securityActive ? 'sidebar-link-active' : '' }}"
                                onclick="toggleSecurityMenu()"
                                style="background:none; border:none; cursor:pointer; width:100%; text-align:left;">
                                <i data-lucide="shield-alert" class="h-5 w-5"></i>
                                <span class="sidebar-text">Security Logs</span>
                                <i data-lucide="chevron-down" class="h-4 w-4 sidebar-text security-chevron" id="security-chevron"
                                    style="margin-left:auto; transition: transform 0.3s;"></i>
                            </button>

                            <div id="security-submenu" class="sidebar-submenu" style="display:none; padding-left:1rem;">
                                <a href="{{ route('security_logs.login') }}"
                                    class="sidebar-link sidebar-sublink {{ request()->routeIs('security_logs.login') ? 'sidebar-link-active' : '' }}">
                                    <i data-lucide="log-in" class="h-4 w-4"></i>
                                    <span class="sidebar-text">Login Logs</span>
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('reports.index') }}"
                            class="sidebar-link {{ request()->routeIs('reports.index') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="file-bar-chart" class="h-5 w-5"></i>
                            <span class="sidebar-text">Reports</span>
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}"
                            class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                            <span class="sidebar-text">Overview</span>
                        </a>
                        <a href="{{ route('user.attendance') }}"
                            class="sidebar-link {{ request()->routeIs('user.attendance') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="calendar" class="h-5 w-5"></i>
                            <span class="sidebar-text">Attendance</span>
                        </a>

                        <a href="{{ route('user.payslip') }}"
                            class="sidebar-link {{ request()->routeIs('user.payslip') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="file-text" class="h-5 w-5"></i>
                            <span class="sidebar-text">Payslip</span>
                        </a>

                        <a href="{{ route('user.leave_form') }}"
                            class="sidebar-link {{ request()->routeIs('user.leave_form') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="file-edit" class="h-5 w-5"></i>
                            <span class="sidebar-text">Apply for Leave</span>
                        </a>

                        <a href="{{ route('user.my_requests') }}"
                            class="sidebar-link {{ request()->routeIs('user.my_requests') ? 'sidebar-link-active' : '' }}">
                            <i data-lucide="git-pull-request" class="h-5 w-5"></i>
                            <span class="sidebar-text">My Requests</span>
                        </a>
                    @endif
                </nav>
            </aside>

            <!-- Main Content Container -->
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lucide Icons
            lucide.createIcons();

            // Profile & Notification Dropdown Logic
            const profileBtn = document.getElementById('profile-btn');
            const profileDropdown = document.getElementById('profile-dropdown');
            const notificationBtn = document.getElementById('notification-btn');
            const notificationDropdown = document.getElementById('notification-dropdown');

            // Profile Dropdown Logic
            if (profileBtn) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (notificationDropdown) notificationDropdown.classList.remove('show');
                    profileDropdown.classList.toggle('show');
                });
            }

            // Notification Logic
            if (notificationBtn) {
                notificationBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (profileDropdown) profileDropdown.classList.remove('show');
                    notificationDropdown.classList.toggle('show');
                    
                    // Mark as viewed when opening the dropdown
                    if (notificationDropdown.classList.contains('show')) {
                        markNotificationsAsViewed();
                    }
                });
            }

            window.markNotificationsAsViewed = () => {
                const dot = document.querySelector('.notification-dot');
                const badge = document.getElementById('notif-badge');
                
                if (dot || badge) {
                    fetch('{{ route("notifications.viewed") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => {
                        if (res.ok) {
                            if (dot) dot.classList.add('hidden');
                            if (badge) badge.classList.add('hidden');
                        }
                    });
                }
            };

            window.clearAllNotifications = (e) => {
                e.stopPropagation();
                if (!confirm('Clear all notifications?')) return;

                fetch('{{ route("notifications.clear") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(res => {
                    if (res.ok) {
                        const list = document.getElementById('notification-list');
                        const footer = document.getElementById('notif-footer');
                        const dot = document.querySelector('.notification-dot');
                        const badge = document.getElementById('notif-badge');
                        
                        list.innerHTML = `
                            <div class="empty-notifications" id="empty-notif-msg">
                                <i data-lucide="bell-off" class="h-8 w-8 mb-2"></i>
                                <p>No new notifications</p>
                            </div>
                        `;
                        if (footer) footer.classList.add('hidden');
                        if (dot) dot.classList.add('hidden');
                        if (badge) badge.classList.add('hidden');
                        if (window.lucide) window.lucide.createIcons();
                    }
                });
            };

            window.addEventListener('click', () => {
                if (profileDropdown) profileDropdown.classList.remove('show');
                if (notificationDropdown) notificationDropdown.classList.remove('show');
            });

            [profileDropdown, notificationDropdown].forEach(dropdown => {
                if (dropdown) dropdown.addEventListener('click', (e) => e.stopPropagation());
            });

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

            // Theme Toggle Logic
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.querySelector('.theme-icon');
            const themeText = document.querySelector('.theme-text');

            const updateThemeUI = (theme) => {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                    if (themeIcon) themeIcon.setAttribute('data-lucide', 'sun');
                    if (themeText) themeText.textContent = 'Light Mode';
                } else {
                    document.documentElement.classList.remove('dark-mode');
                    if (themeIcon) themeIcon.setAttribute('data-lucide', 'moon');
                    if (themeText) themeText.textContent = 'Dark Mode';
                }
                lucide.createIcons();
            };

            if (themeToggle) {
                themeToggle.addEventListener('click', (e) => {
                    e.stopPropagation(); // Keep dropdown open
                    const isDark = document.documentElement.classList.contains('dark-mode');
                    const newTheme = isDark ? 'light' : 'dark';
                    localStorage.setItem('theme', newTheme);
                    updateThemeUI(newTheme);
                });

                // Initialize theme UI
                updateThemeUI(localStorage.getItem('theme') || 'light');
            }
        });
    </script>

    <script>
        // Government Contributions accordion
        function toggleGovtMenu() {
            const submenu  = document.getElementById('govt-submenu');
            const chevron  = document.getElementById('govt-chevron');
            const isOpen   = submenu.style.display !== 'none';

            submenu.style.display = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            localStorage.setItem('govtMenuOpen', !isOpen);
        }

        // Restore accordion state on load
        document.addEventListener('DOMContentLoaded', function () {
            const submenu = document.getElementById('govt-submenu');
            const chevron = document.getElementById('govt-chevron');
            if (!submenu || !chevron) return;

            // Auto-open if currently on a govt route
            const govtActive = {{ isset($govtActive) && $govtActive ? 'true' : 'false' }};
            const savedOpen  = localStorage.getItem('govtMenuOpen') === 'true';

            if (govtActive || savedOpen) {
                submenu.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            }

            // Security Logs accordion
            const secSubmenu = document.getElementById('security-submenu');
            const secChevron = document.getElementById('security-chevron');
            if (secSubmenu && secChevron) {
                const securityActive = {{ isset($securityActive) && $securityActive ? 'true' : 'false' }};
                const secSavedOpen  = localStorage.getItem('securityMenuOpen') === 'true';

                if (securityActive || secSavedOpen) {
                    secSubmenu.style.display = 'block';
                    secChevron.style.transform = 'rotate(180deg)';
                }
            }
        });

        function toggleSecurityMenu() {
            const submenu  = document.getElementById('security-submenu');
            const chevron  = document.getElementById('security-chevron');
            const isOpen   = submenu.style.display !== 'none';

            submenu.style.display = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            localStorage.setItem('securityMenuOpen', !isOpen);
        }
    </script>

    @yield('scripts')
    {{-- Re-run Lucide after all page-specific scripts so every icon is rendered --}}
    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>

</html>