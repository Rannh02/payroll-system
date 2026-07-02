function openModal() {
    document.getElementById('pagibigModal').classList.add('show');
    lucide.createIcons();
}
function closeModal() {
    document.getElementById('pagibigModal').classList.remove('show');
}
function openEditModal(id, salaryFrom, salaryTo, empRate, erRate, maxContrib) {
    const form = document.getElementById('editForm');
    form.action = `/pagibig/${id}`;
    document.getElementById('edit_salary_from').value = salaryFrom;
    document.getElementById('edit_salary_to').value = salaryTo;
    document.getElementById('edit_employee_rate').value = empRate;
    document.getElementById('edit_employer_rate').value = erRate;
    document.getElementById('edit_maximum_contribution').value = maxContrib;
    document.getElementById('pagibigEditModal').classList.add('show');
    lucide.createIcons();
}
function closeEditModal() {
    document.getElementById('pagibigEditModal').classList.remove('show');
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.js-open-add-modal')?.addEventListener('click', openModal);
    document.querySelectorAll('.js-close-add-modal').forEach(button => button.addEventListener('click', closeModal));
    document.querySelectorAll('.js-close-edit-modal').forEach(button => button.addEventListener('click', closeEditModal));
    document.querySelectorAll('.js-edit-pagibig').forEach(button => {
        button.addEventListener('click', () => {
            openEditModal(
                button.dataset.id,
                button.dataset.salaryFrom,
                button.dataset.salaryTo,
                button.dataset.employeeRate,
                button.dataset.employerRate,
                button.dataset.maximumContribution
            );
        });
    });
});

window.onclick = function(e) {
    if (e.target === document.getElementById('pagibigModal')) closeModal();
    if (e.target === document.getElementById('pagibigEditModal')) closeEditModal();
};