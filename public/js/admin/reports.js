document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('report-modal');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const modalTitle = document.getElementById('modal-report-title');
    const modalLoading = document.getElementById('modal-loading');
    const modalTableContainer = document.getElementById('modal-table-container');

    document.querySelectorAll('.btn-generate').forEach(button => {
        const type = button.getAttribute('data-type');
        if (!type) return;

        button.addEventListener('click', function () {
            const title = button.getAttribute('data-title') || 'Report Details';
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;

            modalTitle.textContent = title;
            modalTableContainer.innerHTML = '';
            modalLoading.style.display = 'block';
            modal.classList.add('show');

            if (type === 'custom') {
                modalLoading.style.display = 'none';
                renderCustomConfigPanel();
                return;
            }

            fetch(`/reports/details/${type}?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(res => res.json())
                .then(res => {
                    modalLoading.style.display = 'none';
                    if (res.success && res.data.length > 0) {
                        buildReportTable(type, res.data);
                    } else {
                        modalTableContainer.innerHTML = `
                            <div class="reports-empty-state">
                                <i data-lucide="info" class="reports-empty-icon"></i>
                                <p class="reports-empty-title">No records found</p>
                                <p class="reports-empty-text">There is no processed payroll data in the database matching the selected dates.</p>
                            </div>
                        `;
                        if (window.lucide) window.lucide.createIcons();
                    }
                })
                .catch(err => {
                    console.error(err);
                    modalLoading.style.display = 'none';
                    modalTableContainer.innerHTML = `<p class="reports-error-text">Error fetching report details from server.</p>`;
                });
        });
    });

    btnCloseModal.addEventListener('click', () => modal.classList.remove('show'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('show');
    });

    function buildReportTable(type, data) {
        let html = '<table class="modal-table"><thead><tr>';
        if (type === 'payroll') {
            html += '<th>Employee</th><th>Period Start</th><th>Period End</th><th>Pay Date</th><th class="text-right">Basic Salary</th><th class="text-right">Overtime</th><th class="text-right">Gross Pay</th><th class="text-right">Deductions</th><th class="text-right">Net Pay</th></tr></thead><tbody>';
            let sumBasic = 0, sumOvertime = 0, sumGross = 0, sumDeductions = 0, sumNet = 0;
            data.forEach(p => {
                sumBasic += p.basic_salary;
                sumOvertime += p.overtime_pay;
                sumGross += p.gross_pay;
                sumDeductions += p.total_deductions;
                sumNet += p.net_pay;
                html += `<tr><td class="font-600">${p.employee}</td><td>${p.start_date}</td><td>${p.end_date}</td><td>${p.pay_date}</td><td class="text-right">₱${p.basic_salary.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${p.overtime_pay.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right font-600">₱${p.gross_pay.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-red">₱${p.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right font-700 text-green">₱${p.net_pay.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td></tr>`;
            });
            html += `<tr class="modal-table-totals"><td colspan="4">Total Summary</td><td class="text-right">₱${sumBasic.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${sumOvertime.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${sumGross.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-red">₱${sumDeductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-green">₱${sumNet.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td></tr>`;
        } else if (type === 'tax') {
            html += '<th>Employee</th><th>Pay Date</th><th class="text-right">SSS</th><th class="text-right">PhilHealth</th><th class="text-right">Pag-IBIG</th><th class="text-right">Withholding Tax</th><th class="text-right">Total Withheld</th></tr></thead><tbody>';
            let sumSSS = 0, sumPhil = 0, sumHdmf = 0, sumTax = 0, sumTotal = 0;
            data.forEach(p => {
                sumSSS += p.sss;
                sumPhil += p.philhealth;
                sumHdmf += p.hdmf;
                sumTax += p.tax;
                sumTotal += p.total_deductions;
                html += `<tr><td class="font-600">${p.employee}</td><td>${p.pay_date}</td><td class="text-right">₱${p.sss.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${p.philhealth.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${p.hdmf.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-amber">₱${p.tax.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right font-700 text-red">₱${p.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td></tr>`;
            });
            html += `<tr class="modal-table-totals"><td colspan="2">Total Summary</td><td class="text-right">₱${sumSSS.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${sumPhil.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${sumHdmf.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-amber">₱${sumTax.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-red">₱${sumTotal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td></tr>`;
        } else if (type === 'departmental') {
            html += '<th>Department</th><th class="text-center">Active Employees Paid</th><th class="text-right">Total Basic Salary</th><th class="text-right">Total Overtime</th><th class="text-right">Total Gross Expenses</th><th class="text-right">Total Deductions</th><th class="text-right">Total Net Payroll</th></tr></thead><tbody>';
            let sumEmployees = 0, sumBasic = 0, sumOvertime = 0, sumGross = 0, sumDeductions = 0, sumNet = 0;
            data.forEach(d => {
                sumEmployees += d.employee_count;
                sumBasic += d.total_basic;
                sumOvertime += d.total_overtime;
                sumGross += d.total_gross;
                sumDeductions += d.total_deductions;
                sumNet += d.total_net;
                html += `<tr><td class="font-600">${d.department_name}</td><td class="text-center font-600">${d.employee_count}</td><td class="text-right">₱${d.total_basic.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${d.total_overtime.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right font-600">₱${d.total_gross.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-red">₱${d.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right font-700 text-green">₱${d.total_net.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td></tr>`;
            });
            html += `<tr class="modal-table-totals"><td>Total Summary</td><td class="text-center">${sumEmployees}</td><td class="text-right">₱${sumBasic.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${sumOvertime.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right">₱${sumGross.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-red">₱${sumDeductions.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td><td class="text-right text-green">₱${sumNet.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td></tr>`;
        }
        html += '</tbody></table>';
        modalTableContainer.innerHTML = html;
    }

    function renderCustomConfigPanel() {
        modalTableContainer.innerHTML = `
            <div class="reports-custom-panel">
                <p class="reports-custom-intro">Customize and build your report structure. Choose the datasets you would like to include in your custom exported spreadsheet:</p>
                <div class="custom-report-grid">
                    <label class="custom-option"><input type="checkbox" id="opt-basic" checked><span>Basic Salaries</span></label>
                    <label class="custom-option"><input type="checkbox" id="opt-overtime" checked><span>Overtime Pay</span></label>
                    <label class="custom-option"><input type="checkbox" id="opt-contributions" checked><span>Gov Contributions (SSS, Philhealth, Pag-IBIG)</span></label>
                    <label class="custom-option"><input type="checkbox" id="opt-tax" checked><span>Withholding Tax</span></label>
                </div>
                <div class="reports-custom-actions">
                    <button id="btn-cancel-custom" class="btn-secondary">Cancel</button>
                    <button id="btn-export-custom" class="btn-primary"><i data-lucide="download" class="h-4 w-4 mr-2"></i> Download CSV</button>
                </div>
            </div>
        `;
        if (window.lucide) window.lucide.createIcons();
        document.getElementById('btn-cancel-custom').addEventListener('click', () => modal.classList.remove('show'));
        document.getElementById('btn-export-custom').addEventListener('click', () => {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            modal.classList.remove('show');
            window.location.href = `/reports/export/payroll?date_from=${dateFrom}&date_to=${dateTo}`;
        });
    }

    document.querySelectorAll('.btn-download').forEach(button => {
        button.addEventListener('click', function () {
            const type = button.closest('.report-card').querySelector('.btn-generate').getAttribute('data-type');
            if (!type) return;
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            window.location.href = `/reports/export/${type}?date_from=${dateFrom}&date_to=${dateTo}`;
        });
    });
});