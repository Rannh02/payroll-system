<aside id="sidebar" class="sidebar sidebar-expanded">
    <div class="sidebar-nav">
        <!-- Sidebar Toggle Button -->
        <button id="sidebar-toggle" class="sidebar-toggle-btn">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

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

        <!-- Accordion for User Management -->
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
                    class="sidebar-link {{ request()->routeIs('it_admin.users') && !request()->routeIs('it_admin.users.create_edit') ? 'sidebar-link-active' : '' }}"
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
            const userSubmenu = document.getElementById('user-submenu');
            const userChevron = document.getElementById('user-chevron');
            if (userSubmenu && userChevron) {
                const userActive = {{ request()->routeIs('it_admin.users*') || request()->routeIs('it_admin.roles*') ? 'true' : 'false' }};
                const savedOpen = localStorage.getItem('userMenuOpen') === 'true';

                if (userActive || savedOpen) {
                    userSubmenu.style.display = 'block';
                    userChevron.style.transform = 'rotate(180deg)';
                }
            }
        });
    </script>
</aside>
