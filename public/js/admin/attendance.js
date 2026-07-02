function openModal() {
    document.getElementById('attendanceModal').classList.add('show');
    lucide.createIcons();
}

function closeModal() {
    document.getElementById('attendanceModal').classList.remove('show');
}

window.onclick = function (e) {
    if (e.target === document.getElementById('attendanceModal')) {
        closeModal();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (document.body.dataset.openAttendanceModal === '1') {
        openModal();
    }
});