function openModal() {
    document.getElementById('positionModal').classList.add('show');
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function closeModal() {
    document.getElementById('positionModal').classList.remove('show');
}

window.onclick = function (event) {
    const modal = document.getElementById('positionModal');
    if (event.target === modal) closeModal();
};

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
});

document.addEventListener('DOMContentLoaded', () => {
    if (document.body.dataset.openPositionModal === '1') {
        openModal();
    }
});