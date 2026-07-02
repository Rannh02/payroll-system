function openModal(modalId) {
    document.getElementById(modalId).classList.add('show');
    lucide.createIcons();
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

window.addEventListener('click', function (e) {
    ['pagibigModal', 'pagibigEditModal', 'philhealthModal', 'philhealthEditModal', 'sssModal', 'sssEditModal', 'taxModal', 'taxEditModal'].forEach(function (id) {
        const modal = document.getElementById(id);
        if (modal && e.target === modal) {
            closeModal(id);
        }
    });
});