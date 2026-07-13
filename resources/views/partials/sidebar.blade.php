<aside id="sidebar" class="sidebar sidebar-expanded">
    <div class="sidebar-nav">
        <!-- Sidebar Toggle Button -->
        <button id="sidebar-toggle" class="sidebar-toggle-btn">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

        @if(Auth::check() && (Auth::user()->role === 'admin'))
            <!-- Admin Navigation Items -->
            <a href="{{ route('dashboard') }}"
                class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('analytics.index') }}"
                class="sidebar-link {{ request()->routeIs('analytics.index') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="line-chart" class="h-5 w-5"></i>
                <span class="sidebar-text">Analytics</span>
            </a>

            <a href="{{ route('employees.index') }}"
                class="sidebar-link {{ request()->routeIs('employees.*') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="users" class="h-5 w-5"></i>
                <span class="sidebar-text">Employees</span>
            </a>

            <a href="{{ route('department.index') }}"
                class="sidebar-link {{ request()->routeIs('department.*') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="building-2" class="h-5 w-5"></i>
                <span class="sidebar-text">Departments</span>
            </a>

            <a href="{{ route('position.index') }}"
                class="sidebar-link {{ request()->routeIs('position.*') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="briefcase" class="h-5 w-5"></i>
                <span class="sidebar-text">Positions</span>
            </a>

            <!-- Accordion for Government Contributions -->
            <div>
                <button type="button" class="sidebar-link" onclick="toggleGovtMenu()"
                    style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <span style="display: flex; align-items: center;">
                        <i data-lucide="file-text" class="h-5 w-5"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Govt Contributions</span>
                    </span>
                    <i data-lucide="chevron-down" id="govt-chevron" class="h-4 w-4 transition-transform duration-200"></i>
                </button>
                <div id="govt-submenu" style="display: none; padding-left: 1rem; margin-top: 0.25rem;">
                    <a href="{{ route('sss.index') }}"
                        class="sidebar-link {{ request()->routeIs('sss.*') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">SSS Contribution</span>
                    </a>
                    <a href="{{ route('philhealth.index') }}"
                        class="sidebar-link {{ request()->routeIs('philhealth.*') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="heart" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Philhealth</span>
                    </a>
                    <a href="{{ route('pagibig.index') }}"
                        class="sidebar-link {{ request()->routeIs('pagibig.*') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="home" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Pag-IBIG</span>
                    </a>
                    <a href="{{ route('tax.index') }}"
                        class="sidebar-link {{ request()->routeIs('tax.*') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="coins" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">BIR Tax</span>
                    </a>
                </div>
            </div>

            <a href="{{ route('payroll.index') }}"
                class="sidebar-link {{ request()->routeIs('payroll.*') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="credit-card" class="h-5 w-5"></i>
                <span class="sidebar-text">Payroll Run</span>
            </a>

            <a href="{{ route('attendance.index') }}"
                class="sidebar-link {{ request()->routeIs('attendance.*') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="calendar" class="h-5 w-5"></i>
                <span class="sidebar-text">Attendance</span>
            </a>

            <a href="{{ route('approval_workflow.index') }}"
                class="sidebar-link {{ request()->routeIs('approval_workflow.index') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="git-pull-request" class="h-5 w-5"></i>
                <span class="sidebar-text">Leave Requests</span>
            </a>

            <a href="{{ route('reports.index') }}"
                class="sidebar-link {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="printer" class="h-5 w-5"></i>
                <span class="sidebar-text">Reports</span>
            </a>

        @elseif (Auth::user()->role === 'it_admin')
            <a href="{{ route('it_admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('it_admin.dashboard') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('it_admin.analytics') }}"
                class="sidebar-link {{ request()->routeIs('it_admin.analytics') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="line-chart" class="h-5 w-5"></i>
                <span class="sidebar-text">Analytics</span>
            </a>

             <div>
                <button type="button" class="sidebar-link" onclick="toggleUserMenu()"
                    style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <span style="display: flex; align-items: center;">
                        <i data-lucide="users" class="h-5 w-5"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">User Management</span>
                    </span>
                    <i data-lucide="chevron-down" id="user-chevron" class="h-4 w-4 transition-transform duration-200"></i>
                </button>
                <div id="user-submenu" style="display: none; padding-left: 1rem; margin-top: 0.25rem;">
                    <a href="{{ route('it_admin.users') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.users') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="user" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">All Users</span>
                    </a>
                    
                    <a href="{{ route('it_admin.users.create_edit') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.users.create_edit') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="user-plus" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Create & Edit Accounts</span>
                    </a>
                    
                    <a href="{{ route('it_admin.roles') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.roles') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="shield" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Role and Permissions</span>
                    </a>
                </div>
            </div>
            
            <!-- Accordion for Security -->
            <div>
                <button type="button" class="sidebar-link" onclick="toggleSecurityMenu()"
                    style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <span style="display: flex; align-items: center;">
                        <i data-lucide="shield-alert" class="h-5 w-5"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Security</span>
                    </span>
                    <i data-lucide="chevron-down" id="security-chevron" class="h-4 w-4 transition-transform duration-200"></i>
                </button>
                <div id="security-submenu" style="display: none; padding-left: 1rem; margin-top: 0.25rem;">
                    <a href="{{ route('it_admin.audit_logs') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.audit_logs') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="scroll-text" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Audit Logs</span>
                    </a>
                    <a href="{{ route('it_admin.security_logs') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.security_logs') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="shield" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Security Logs</span>
                    </a>
                    <a href="{{ route('it_admin.session_management') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.session_management') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="monitor" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Session Management</span>
                    </a>
                </div>
            </div>
            <!-- Accordion for Reports -->
            <div>
                <button type="button" class="sidebar-link" onclick="toggleReportsMenu()"
                    style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                    <span style="display: flex; align-items: center;">
                        <i data-lucide="file-bar-chart" class="h-5 w-5"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Reports</span>
                    </span>
                    <i data-lucide="chevron-down" id="reports-chevron" class="h-4 w-4 transition-transform duration-200"></i>
                </button>
                <div id="reports-submenu" style="display: none; padding-left: 1rem; margin-top: 0.25rem;">
                    <a href="{{ route('it_admin.reports.user_activity') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.reports.user_activity') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">User Activity Report</span>
                    </a>
                    <a href="{{ route('it_admin.reports.security_incident') }}"
                        class="sidebar-link {{ request()->routeIs('it_admin.reports.security_incident') ? 'sidebar-link-active' : '' }}"
                        style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <i data-lucide="shield-alert" class="h-4 w-4"></i>
                        <span class="sidebar-text" style="margin-left: 0.875rem;">Security Incident</span>
                    </a>
                </div>
            </div>
            

        @else
            <!-- Employee Navigation Items -->
            <a href="{{ route('user.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('user.attendance') }}"
                class="sidebar-link {{ request()->routeIs('user.attendance') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="calendar" class="h-5 w-5"></i>
                <span class="sidebar-text">Attendance</span>
            </a>

            <a href="{{ route('user.payslip') }}"
                class="sidebar-link {{ request()->routeIs('user.payslip') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="credit-card" class="h-5 w-5"></i>
                <span class="sidebar-text">My Payslips</span>
            </a>

            <a href="{{ route('user.my_requests') }}"
                class="sidebar-link {{ request()->routeIs('user.my_requests') || request()->routeIs('user.leave_form') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="git-pull-request" class="h-5 w-5"></i>
                <span class="sidebar-text">My Requests</span>
            </a>
        @endif
    </div>
    <script>
        function toggleReportsMenu() {
            const submenu = document.getElementById('reports-submenu');
            const chevron = document.getElementById('reports-chevron');
            const isOpen = submenu.style.display !== 'none';

            submenu.style.display = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            localStorage.setItem('reportsMenuOpen', !isOpen);
        }

        function toggleSecurityMenu() {
            const submenu = document.getElementById('security-submenu');
            const chevron = document.getElementById('security-chevron');
            const isOpen = submenu.style.display !== 'none';

            submenu.style.display = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            localStorage.setItem('securityMenuOpen', !isOpen);
        }

        function toggleUserMenu() {
            const submenu = document.getElementById('user-submenu');
            const chevron = document.getElementById('user-chevron');
            const isOpen = submenu.style.display !== 'none';

            submenu.style.display = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            localStorage.setItem('userMenuOpen', !isOpen);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Reports submenu persistence
            const reportsSubmenu = document.getElementById('reports-submenu');
            const reportsChevron = document.getElementById('reports-chevron');
            if (reportsSubmenu && reportsChevron) {
                const reportsActive = {{ request()->routeIs('it_admin.reports.*') ? 'true' : 'false' }};
                const reportsSavedOpen = localStorage.getItem('reportsMenuOpen') === 'true';

                if (reportsActive || reportsSavedOpen) {
                    reportsSubmenu.style.display = 'block';
                    reportsChevron.style.transform = 'rotate(180deg)';
                }
            }

            // Security submenu persistence
            const securitySubmenu = document.getElementById('security-submenu');
            const securityChevron = document.getElementById('security-chevron');
            if (securitySubmenu && securityChevron) {
                const securityActive = {{ request()->routeIs('it_admin.security_logs') || request()->routeIs('it_admin.audit_logs') || request()->routeIs('it_admin.session_management') ? 'true' : 'false' }};
                const securitySavedOpen = localStorage.getItem('securityMenuOpen') === 'true';

                if (securityActive || securitySavedOpen) {
                    securitySubmenu.style.display = 'block';
                    securityChevron.style.transform = 'rotate(180deg)';
                }
            }

            // User management submenu persistence
            const submenu = document.getElementById('user-submenu');
            const chevron = document.getElementById('user-chevron');
            if (!submenu || !chevron) return;

            const userActive = {{ request()->routeIs('it_admin.users*') || request()->routeIs('it_admin.roles*') ? 'true' : 'false' }};
            const savedOpen = localStorage.getItem('userMenuOpen') === 'true';

            if (userActive || savedOpen) {
                submenu.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            }
        });
    </script>
</aside>