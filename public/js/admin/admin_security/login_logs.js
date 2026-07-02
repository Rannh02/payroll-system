document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.log-row').forEach(function (row) {
        row.addEventListener('click', function () {
            const log = JSON.parse(this.dataset.log);
            const userName = this.dataset.user || 'Unknown';
            openLogModal(log, userName);
        });
    });
});

function openLogModal(log, userName) {
    document.getElementById('logModal').classList.add('show');

    const dateObj = new Date(log.created_at);
    document.getElementById('modalDate').textContent = dateObj.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });

    document.getElementById('modalUser').textContent = userName;
    document.getElementById('modalEmail').textContent = log.email;
    document.getElementById('modalIp').textContent = log.ip_address;
    document.getElementById('modalBrowser').textContent = log.browser || 'Unknown';
    document.getElementById('modalUserAgent').textContent = log.user_agent;

    let statusHtml = '';
    if (log.status === 'SUCCESS') statusHtml = '<span class="badge badge-success">Success</span>';
    else if (log.status === 'FAILED') statusHtml = '<span class="badge badge-danger">Failed</span>';
    else if (log.status === 'LOCKED') statusHtml = '<span class="badge badge-warning">Locked</span>';
    else if (log.status === 'UNLOCKED') statusHtml = '<span class="badge badge-info">Unlocked</span>';
    else if (log.status === 'SUSPENDED') statusHtml = '<span class="badge badge-danger">Suspended</span>';
    document.getElementById('modalStatus').innerHTML = statusHtml;

    if (log.locked_until) {
        const lockedDate = new Date(log.locked_until);
        document.getElementById('modalLockedUntil').innerHTML = '<span class="locked-time-text">' + lockedDate.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        }) + '</span>';
    } else {
        document.getElementById('modalLockedUntil').innerHTML = '<span class="text-muted">N/A</span>';
    }
}

function closeLogModal() {
    document.getElementById('logModal').classList.remove('show');
}

window.addEventListener('click', function (e) {
    const modal = document.getElementById('logModal');
    if (e.target === modal) {
        closeLogModal();
    }
});