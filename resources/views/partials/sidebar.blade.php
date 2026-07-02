<aside id="sidebar" class="sidebar sidebar-expanded">
    <div class="sidebar-nav">
        <!-- Sidebar Toggle Button -->
        <button id="sidebar-toggle" class="sidebar-toggle-btn">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

        @if(Auth::check() && Auth::user()->role === 'admin')
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

            <a href="{{ route('admin.security_logs') }}"
                class="sidebar-link {{ request()->routeIs('admin.security_logs') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="shield" class="h-5 w-5"></i>
                <span class="sidebar-text">Security Logs</span>
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
</aside>