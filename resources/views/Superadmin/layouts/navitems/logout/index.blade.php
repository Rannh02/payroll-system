<form method="POST" action="{{ route('superadmin.logout') }}" style="width: 100%;">
    @csrf
    <button type="submit" class="sidebar-link" style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer; padding: 0.875rem 1rem; display: flex; align-items: center;">
        <i data-lucide="log-out" class="h-5 w-5"></i>
        <span class="sidebar-text">Log Out</span>
    </button>
</form>
