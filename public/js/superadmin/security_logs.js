function openLogModal(log, userName, userRole) {
    document.getElementById('logModal').classList.add('show');

    // Format Date
    const dateObj = new Date(log.created_at);
    document.getElementById('modalDate').textContent = dateObj.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });

    document.getElementById('modalUser').textContent = userName;

    // Role Badge
    const roleMap = {
        superadmin: { label: 'Super Admin', cls: 'badge-purple' },
        admin:      { label: 'Admin',       cls: 'badge-info'   },
        hr:         { label: 'HR',          cls: 'badge-teal'   },
        employee:   { label: 'Employee',    cls: 'badge-secondary' },
    };
    const roleKey = userRole ? userRole.toLowerCase() : null;
    const roleInfo = roleKey && roleMap[roleKey] ? roleMap[roleKey] : (userRole ? { label: userRole.charAt(0).toUpperCase() + userRole.slice(1), cls: 'badge-secondary' } : null);
    document.getElementById('modalRole').innerHTML = roleInfo
        ? `<span class="badge ${roleInfo.cls}">${roleInfo.label}</span>`
        : '<span class="text-muted">-</span>';

    document.getElementById('modalEmail').textContent = log.email;
    document.getElementById('modalIp').textContent = log.ip_address;
    document.getElementById('modalBrowser').textContent = log.browser || 'Unknown';
    document.getElementById('modalUserAgent').textContent = log.user_agent;

    // Status Badge
    let statusHtml = '';
    if (log.status === 'SUCCESS') statusHtml = '<span class="badge badge-success">Success</span>';
    else if (log.status === 'FAILED') statusHtml = '<span class="badge badge-danger">Failed</span>';
    else if (log.status === 'LOCKED') statusHtml = '<span class="badge badge-warning">Locked</span>';
    else if (log.status === 'UNLOCKED') statusHtml = '<span class="badge badge-info">Unlocked</span>';
    else if (log.status === 'SUSPENDED') statusHtml = '<span class="badge badge-danger">Suspended</span>';
    document.getElementById('modalStatus').innerHTML = statusHtml;

    // Locked Until
    if (log.locked_until) {
        const lockedDate = new Date(log.locked_until);
        document.getElementById('modalLockedUntil').innerHTML = '<span class="locked-time-text">' + lockedDate.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true }) + '</span>';
    } else {
        document.getElementById('modalLockedUntil').innerHTML = '<span class="text-muted">N/A</span>';
    }

    // Unlock Form
    const unlockForm = document.getElementById('unlockForm');
    if (log.status === 'LOCKED') {
        document.getElementById('unlockLogId').value = log.id;
        unlockForm.style.display = 'block';
    } else {
        unlockForm.style.display = 'none';
    }
}

function closeLogModal() {
    document.getElementById('logModal').classList.remove('show');
}

// Close modal when clicking outside
window.addEventListener('click', function (e) {
    const modal = document.getElementById('logModal');
    if (e.target === modal) {
        closeLogModal();
    }
});
