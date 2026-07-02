function openModal() {
    document.getElementById('philhealthModal').classList.add('show');
    lucide.createIcons();
}
function closeModal() {
    document.getElementById('philhealthModal').classList.remove('show');
}
function openEditModal(id, salaryFrom, salaryTo, rate) {
    const form = document.getElementById('editForm');
    form.action = `/philhealth/${id}`;
    document.getElementById('edit_salary_from').value = salaryFrom;
    document.getElementById('edit_salary_to').value = salaryTo;
    document.getElementById('edit_contribution_rate').value = rate;
    document.getElementById('philhealthEditModal').classList.add('show');
    lucide.createIcons();
}
function closeEditModal() {
    document.getElementById('philhealthEditModal').classList.remove('show');
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.js-open-add-modal')?.addEventListener('click', openModal);
    document.querySelectorAll('.js-close-add-modal').forEach(button => button.addEventListener('click', closeModal));
    document.querySelectorAll('.js-close-edit-modal').forEach(button => button.addEventListener('click', closeEditModal));
    document.querySelectorAll('.js-edit-philhealth').forEach(button => {
        button.addEventListener('click', () => {
            openEditModal(
                button.dataset.id,
                button.dataset.salaryFrom,
                button.dataset.salaryTo,
                button.dataset.rate
            );
        });
    });
});

window.onclick = function(e) {
    if (e.target === document.getElementById('philhealthModal')) closeModal();
    if (e.target === document.getElementById('philhealthEditModal')) closeEditModal();
};