function filterTable() {
    const search = document.getElementById('payrollSearch').value.toLowerCase();
    const dept = document.getElementById('deptFilter').value.toLowerCase();
    document.querySelectorAll('.payroll-row').forEach(row => {
        const nameMatch = row.dataset.name.includes(search);
        const deptMatch = dept === '' || row.dataset.dept.toLowerCase() === dept;
        row.style.display = (nameMatch && deptMatch) ? '' : 'none';
    });
}