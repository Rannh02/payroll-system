function openApproveModal(actionUrl, employeeName) {
    document.getElementById('approve-form').action = actionUrl;
    document.getElementById('approve-employee-name').textContent = employeeName;
    document.getElementById('approve-modal').classList.add('show');
}

function closeApproveModal() {
    document.getElementById('approve-modal').classList.remove('show');
}

function openRejectModal(actionUrl, employeeName) {
    document.getElementById('reject-form').action = actionUrl;
    document.getElementById('reject-employee-name').textContent = employeeName;
    document.getElementById('reject-modal').classList.add('show');
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.remove('show');
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-approve-leave').forEach(button => {
        button.addEventListener('click', () => {
            openApproveModal(button.dataset.actionUrl, button.dataset.employeeName);
        });
    });

    document.querySelectorAll('.js-reject-leave').forEach(button => {
        button.addEventListener('click', () => {
            openRejectModal(button.dataset.actionUrl, button.dataset.employeeName);
        });
    });

    document.querySelector('[data-close-approve-modal]')?.addEventListener('click', closeApproveModal);
    document.querySelector('[data-close-reject-modal]')?.addEventListener('click', closeRejectModal);
});

window.addEventListener('click', function (event) {
    const approveModal = document.getElementById('approve-modal');
    const rejectModal = document.getElementById('reject-modal');
    if (event.target === approveModal) closeApproveModal();
    if (event.target === rejectModal) closeRejectModal();
});