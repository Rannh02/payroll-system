function openModal() {
    document.getElementById('taxModal').classList.add('show');
    lucide.createIcons();
}
function closeModal() {
    document.getElementById('taxModal').classList.remove('show');
}
function openEditModal(id, salaryFrom, salaryTo, baseTax, taxRate) {
    const form = document.getElementById('editForm');
    form.action = `/tax/${id}`;
    document.getElementById('edit_salary_from').value = salaryFrom;
    document.getElementById('edit_salary_to').value = salaryTo;
    document.getElementById('edit_base_tax').value = baseTax;
    document.getElementById('edit_tax_rate').value = taxRate;
    document.getElementById('taxEditModal').classList.add('show');
    lucide.createIcons();
}
function closeEditModal() {
    document.getElementById('taxEditModal').classList.remove('show');
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.js-open-add-modal')?.addEventListener('click', openModal);
    document.querySelectorAll('.js-close-add-modal').forEach(button => button.addEventListener('click', closeModal));
    document.querySelectorAll('.js-close-edit-modal').forEach(button => button.addEventListener('click', closeEditModal));
    document.querySelectorAll('.js-edit-tax').forEach(button => {
        button.addEventListener('click', () => {
            openEditModal(
                button.dataset.id,
                button.dataset.salaryFrom,
                button.dataset.salaryTo,
                button.dataset.baseTax,
                button.dataset.taxRate
            );
        });
    });
});

window.onclick = function(e) {
    if (e.target === document.getElementById('taxModal')) closeModal();
    if (e.target === document.getElementById('taxEditModal')) closeEditModal();
};