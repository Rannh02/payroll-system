function openModal() {
    document.getElementById('departmentModal').classList.add('show');
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function closeModal() {
    document.getElementById('departmentModal').classList.remove('show');
}

window.onclick = function (event) {
    const modal = document.getElementById('departmentModal');
    if (event.target === modal) closeModal();
};

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
});

document.addEventListener('DOMContentLoaded', () => {
    if (document.body.dataset.openDepartmentModal === '1') {
        openModal();
    }
});