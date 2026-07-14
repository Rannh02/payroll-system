<aside id="sidebar" class="sidebar sidebar-expanded">
    <div class="sidebar-nav">
        <!-- Sidebar Toggle Button -->
        <button id="sidebar-toggle" class="sidebar-toggle-btn">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

        <!-- Nav Items -->
        <a href="{{ route('it_admin.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.dashboard') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="{{ route('it_admin.users') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.users') && !request()->routeIs('it_admin.users.create_edit') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="users" class="h-5 w-5"></i>
            <span class="sidebar-text">User Management</span>
        </a>

        <a href="{{ route('it_admin.users.create_edit') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.users.create_edit') ? 'sidebar-link-active' : '' }}"
            style="padding-left: 2.25rem;">
            <i data-lucide="user-plus" class="h-4 w-4"></i>
            <span class="sidebar-text" style="font-size: 0.825rem;">Create / Edit</span>
        </a>

        <a href="{{ route('it_admin.roles') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.roles') ? 'sidebar-link-active' : '' }}"
            style="padding-left: 2.25rem;">
            <i data-lucide="key-round" class="h-4 w-4"></i>
            <span class="sidebar-text" style="font-size: 0.825rem;">Roles & Permissions</span>
        </a>

        <a href="{{ route('it_admin.analytics') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.analytics') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="line-chart" class="h-5 w-5"></i>
            <span class="sidebar-text">Analytics</span>
        </a>

        <a href="{{ route('it_admin.security_logs') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.security_logs') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="shield-alert" class="h-5 w-5"></i>
            <span class="sidebar-text">Security Logs</span>
        </a>

        <a href="{{ route('it_admin.audit_logs') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.audit_logs') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="clipboard-list" class="h-5 w-5"></i>
            <span class="sidebar-text">Audit Logs</span>
        </a>

        <a href="{{ route('it_admin.session_management') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.session_management') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="monitor-smartphone" class="h-5 w-5"></i>
            <span class="sidebar-text">Sessions</span>
        </a>

        <!-- Reports Group (Simplified for now) -->
        <a href="{{ route('it_admin.reports.user_activity') }}"
            class="sidebar-link {{ request()->routeIs('it_admin.reports.*') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="file-text" class="h-5 w-5"></i>
            <span class="sidebar-text">Reports</span>
        </a>
    </div>
</aside>
