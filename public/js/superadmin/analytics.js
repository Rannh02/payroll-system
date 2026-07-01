document.addEventListener('DOMContentLoaded', function () {

    // Common Options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 20, font: { size: 12, family: "'Plus Jakarta Sans', sans-serif" } }
            }
        }
    };

    // 1. Login Attempts Overtime - Line Chart
    new Chart(document.getElementById('loginAttemptsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Login Attempts',
                data: [65, 59, 80, 81, 56, 55, 40],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: commonOptions
    });

    // 2. Successful and Failed Logins - Stacked Bar chart
    new Chart(document.getElementById('loginStatusChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            datasets: [
                { label: 'Successful', data: [50, 45, 60, 55, 48], backgroundColor: '#10b981' },
                { label: 'Failed', data: [5, 10, 2, 8, 4], backgroundColor: '#ef4444' }
            ]
        },
        options: {
            ...commonOptions,
            scales: {
                x: { stacked: true },
                y: { stacked: true }
            }
        }
    });

    // 3. User Roles Distribution - Donut chart
    new Chart(document.getElementById('userRolesChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Admin', 'Manager', 'Employee', 'HR'],
            datasets: [{
                data: [5, 15, 65, 15],
                backgroundColor: ['#6366f1', '#f59e0b', '#14b8a6', '#ec4899']
            }]
        },
        options: commonOptions
    });

    // 4. Security Incidents by Type - Horizontal Bar Chart
    new Chart(document.getElementById('securityIncidentsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Brute Force', 'Unauthorized Access', 'Suspicious IP', 'Data Export'],
            datasets: [{
                label: 'Incidents',
                data: [25, 12, 8, 4],
                backgroundColor: '#f43f5e'
            }]
        },
        options: {
            ...commonOptions,
            indexAxis: 'y',
        }
    });

    // 5. Audit Logs by Module - Bar chart
    new Chart(document.getElementById('auditLogsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Auth', 'Payroll', 'Leaves', 'Users', 'Settings'],
            datasets: [{
                label: 'Log Count',
                data: [120, 350, 200, 150, 80],
                backgroundColor: '#8b5cf6'
            }]
        },
        options: commonOptions
    });

    // 6. Payroll Expenses by Department - Bar chart
    new Chart(document.getElementById('payrollDeptChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Engineering', 'Marketing', 'Sales', 'HR', 'Support'],
            datasets: [{
                label: 'Expenses ($)',
                data: [125000, 45000, 65000, 35000, 42000],
                backgroundColor: '#0ea5e9'
            }]
        },
        options: commonOptions
    });

    // 7. Payroll Trend - Line chart
    new Chart(document.getElementById('payrollTrendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Total Payroll ($)',
                data: [310000, 315000, 312000, 320000, 325000, 330000],
                borderColor: '#84cc16',
                tension: 0.1
            }]
        },
        options: commonOptions
    });

    // 8. Salary Distribution - Histogram (Bar chart)
    new Chart(document.getElementById('salaryDistChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['30k-50k', '50k-70k', '70k-90k', '90k-110k', '110k+'],
            datasets: [{
                label: 'Number of Employees',
                data: [45, 80, 65, 30, 10],
                backgroundColor: '#f59e0b',
                barPercentage: 1.0,
                categoryPercentage: 1.0
            }]
        },
        options: commonOptions
    });

    // 9. Employee Attendance Rate - Line Chart
    new Chart(document.getElementById('attendanceRateChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Attendance %',
                data: [95, 96, 94, 98],
                borderColor: '#14b8a6',
                borderDash: [5, 5],
                tension: 0.3
            }]
        },
        options: commonOptions
    });

    // 10. Leave Request Status - Donut Chart
    new Chart(document.getElementById('leaveStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [70, 20, 10],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: commonOptions
    });

    // 11. Tax Deductions - Pie Chart
    new Chart(document.getElementById('taxDeductionsChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Income Tax', 'Local Tax', 'Other Taxes'],
            datasets: [{
                data: [85, 10, 5],
                backgroundColor: ['#3b82f6', '#8b5cf6', '#d946ef']
            }]
        },
        options: commonOptions
    });

    // 12. Benefits Costs - Bar chart (Government Contributions)
    new Chart(document.getElementById('benefitsCostsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['SSS', 'PhilHealth', 'Pag-IBIG'],
            datasets: [{
                label: 'Company Contribution ($)',
                data: [45000, 32000, 15000],
                backgroundColor: '#14b8a6'
            }]
        },
        options: commonOptions
    });

});
