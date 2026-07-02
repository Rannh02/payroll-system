function openModal() {
    document.getElementById('sssModal').classList.add('show');
    lucide.createIcons();
}
function closeModal() {
    document.getElementById('sssModal').classList.remove('show');
}
function openEditModal(id, rangeFrom, rangeTo, msc, empShare, erShare) {
    const form = document.getElementById('editForm');
    form.action = `/sss/${id}`;
    document.getElementById('edit_sss_range_from').value = rangeFrom;
    document.getElementById('edit_sss_range_to').value = rangeTo;
    document.getElementById('edit_monthly_salary_credit').value = msc;
    document.getElementById('edit_employee_share').value = empShare;
    document.getElementById('edit_employer_share').value = erShare;
    document.getElementById('sssEditModal').classList.add('show');
    lucide.createIcons();
}
function closeEditModal() {
    document.getElementById('sssEditModal').classList.remove('show');
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.js-open-add-modal')?.addEventListener('click', openModal);
    document.querySelectorAll('.js-close-add-modal').forEach(button => button.addEventListener('click', closeModal));
    document.querySelectorAll('.js-close-edit-modal').forEach(button => button.addEventListener('click', closeEditModal));
    document.querySelectorAll('.js-edit-sss').forEach(button => {
        button.addEventListener('click', () => {
            openEditModal(
                button.dataset.id,
                button.dataset.rangeFrom,
                button.dataset.rangeTo,
                button.dataset.monthlySalaryCredit,
                button.dataset.employeeShare,
                button.dataset.employerShare
            );
        });
    });
});

window.onclick = function(e) {
    if (e.target === document.getElementById('sssModal')) closeModal();
    if (e.target === document.getElementById('sssEditModal')) closeEditModal();
};