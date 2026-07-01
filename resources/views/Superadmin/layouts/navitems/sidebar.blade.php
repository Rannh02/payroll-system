<aside id="sidebar" class="sidebar sidebar-expanded">
    <div class="sidebar-nav">
        <!-- Sidebar Toggle Button -->
        <button id="sidebar-toggle" class="sidebar-toggle-btn">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

        <!-- Nav Items -->
        <a href="{{ route('superadmin.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="{{ route('superadmin.analytics') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.analytics') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="line-chart" class="h-5 w-5"></i>
            <span class="sidebar-text">Analytics</span>
        </a>

        <a href="{{ route('superadmin.security_logs') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.security_logs') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="shield-alert" class="h-5 w-5"></i>
            <span class="sidebar-text">Security Logs</span>
        </a>

        <a href="{{ route('superadmin.AuditLogs') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.Audit-Logs') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="user-cog" class="h-5 w-5"></i>
            <span class="sidebar-text">Audit Logs</span>
        </a>

        <a href="{{ route('superadmin.Administrator') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.Administrator') ? 'sidebar-link-active' : '' }}">
            <i data-lucide="user-cog" class="h-5 w-5"></i>
            <span class="sidebar-text">Administrator</span>
        </a>
        
        
        

        {{-- Additional nav items can be included here easily in their own folders/files --}}
    </div>
</aside>