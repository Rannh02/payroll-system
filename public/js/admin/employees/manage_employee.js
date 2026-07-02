document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('manage-employee-root');
    if (root?.dataset?.oldEdit) {
        try {
            const oldData = JSON.parse(root.dataset.oldEdit);
            window.openEditModal(oldData);
        } catch (error) {
            console.error('Failed to restore employee edit modal state', error);
        }
    }

    document.querySelectorAll('[data-close-view-modal]').forEach(button => button.addEventListener('click', closeViewModal));
    document.querySelectorAll('[data-close-edit-modal]').forEach(button => button.addEventListener('click', closeEditModal));
});

function openArchiveModal(id, name) {
    const modal = document.getElementById('archive-modal');
    const form = document.getElementById('archive-form');
    const nameDisplay = document.getElementById('employee-name-display');

    form.action = `/employees/${id}`;
    nameDisplay.textContent = name;
    modal.classList.add('show');
    if (window.lucide) window.lucide.createIcons();
}

function closeArchiveModal() {
    document.getElementById('archive-modal').classList.remove('show');
}

window.openViewModal = function (data) {
    const modal = document.getElementById('view-modal');
    const avatarImg = document.getElementById('view-header-avatar-img');
    const avatarInitial = document.getElementById('view-header-avatar-initial');

    if (data.photo_url && !data.photo_url.includes('ui-avatars.com')) {
        avatarImg.src = data.photo_url;
        avatarImg.classList.remove('hidden');
        avatarInitial.classList.add('hidden');
    } else {
        avatarImg.classList.add('hidden');
        avatarInitial.classList.remove('hidden');
        avatarInitial.textContent = data.name.charAt(0).toUpperCase();
    }

    document.getElementById('view-header-name').textContent = data.name;
    document.getElementById('view-header-position').textContent = data.position;
    document.getElementById('view-header-id').textContent = `ID: ${data.id}`;

    for (const key in data) {
        const el = document.getElementById(`view-${key}`);
        if (el) el.textContent = data[key];
    }

    modal.classList.add('show');
    if (window.lucide) window.lucide.createIcons();
};

function closeViewModal() {
    document.getElementById('view-modal').classList.remove('show');
}

window.openEditModal = function (data) {
    const modal = document.getElementById('edit-modal');
    const form = document.getElementById('edit-form');

    form.action = `/employees/${data.db_id}`;
    document.getElementById('edit_db_id').value = data.db_id;
    document.getElementById('edit_photo_url_hidden').value = data.photo_url || '';
    document.getElementById('edit_has_custom_photo_hidden').value = data.has_custom_photo ? '1' : '0';
    document.getElementById('edit_employee_id').value = data.employee_number;
    document.getElementById('edit_join_date').value = data.hire_date || '';
    document.getElementById('edit_employee_status').value = data.employment_status || '';
    document.getElementById('edit_department').value = data.department_id || '';
    document.getElementById('edit_position_select').value = data.position_id || '';
    document.getElementById('edit_salary_input').value = data.salary_rate || '';
    document.getElementById('edit_first_name').value = data.first_name || '';
    document.getElementById('edit_middle_name').value = data.middle_name || '';
    document.getElementById('edit_last_name').value = data.last_name || '';
    document.getElementById('edit_suffix').value = data.suffix || '';
    document.getElementById('edit_date_of_birth').value = data.date_of_birth || '';
    document.getElementById('edit_sex').value = data.sex || 'Male';
    document.getElementById('edit_marital_status').value = data.marital_status || '';
    document.getElementById('edit_dependents').value = data.number_of_dependents || 0;
    document.getElementById('edit_phone_input').value = data.contact_info || '';
    document.getElementById('edit_current_street_address').value = data.current_street_address || '';
    document.getElementById('edit_current_barangay').value = data.current_barangay || '';
    document.getElementById('edit_current_city').value = data.current_city_municipality || '';
    document.getElementById('edit_current_province').value = data.current_province || '';
    document.getElementById('edit_current_zip_code').value = data.current_zip_code || '';
    document.getElementById('edit_permanent_street_address').value = data.permanent_street_address || '';
    document.getElementById('edit_permanent_barangay').value = data.permanent_barangay || '';
    document.getElementById('edit_permanent_city').value = data.permanent_city_municipality || '';
    document.getElementById('edit_permanent_province').value = data.permanent_province || '';
    document.getElementById('edit_permanent_zip_code').value = data.permanent_zip_code || '';
    document.getElementById('edit_sss_input').value = data.sss_num || '';
    document.getElementById('edit_philhealth_input').value = data.philhealth_num || '';
    document.getElementById('edit_pagibig_input').value = data.pagibig_num || '';
    document.getElementById('edit_email').value = data.email || '';
    form.querySelectorAll('input[type="password"]').forEach(input => input.value = '');

    const avatarPreview = document.getElementById('edit_avatar_preview');
    const avatarIcon = document.getElementById('edit_avatar_icon');
    if (data.photo_url && data.has_custom_photo) {
        avatarPreview.src = data.photo_url;
        avatarPreview.style.display = 'block';
        avatarIcon.style.display = 'none';
    } else {
        avatarPreview.src = '';
        avatarPreview.style.display = 'none';
        avatarIcon.style.display = 'block';
    }

    ['edit_sss_input', 'edit_philhealth_input', 'edit_pagibig_input', 'edit_phone_input'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.dispatchEvent(new Event('input'));
    });

    modal.classList.add('show');
    if (window.lucide) window.lucide.createIcons();
};

function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('show');
}

document.addEventListener('DOMContentLoaded', () => {
    const posSelect = document.getElementById('edit_position_select');
    const salInput = document.getElementById('edit_salary_input');
    if (posSelect && salInput) {
        const sync = () => {
            const opt = posSelect.options[posSelect.selectedIndex];
            salInput.value = opt?.dataset?.salary ?? '';
        };
        posSelect.addEventListener('change', sync);
    }

    const photoInput = document.getElementById('edit_profile_photo_input');
    const avatarPreview = document.getElementById('edit_avatar_preview');
    const avatarIcon = document.getElementById('edit_avatar_icon');
    const uploadButton = document.getElementById('edit_upload_photo_btn');
    if (uploadButton && photoInput) uploadButton.addEventListener('click', () => photoInput.click());
    if (photoInput && avatarPreview && avatarIcon) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                avatarPreview.src = e.target.result;
                avatarPreview.style.display = 'block';
                avatarIcon.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    const formatInput = (input, pattern) => {
        const applyFormat = () => {
            let val = input.value.replace(/\D/g, '');
            if (val.startsWith('639')) val = val.substring(3);
            else if (val.startsWith('63')) val = val.substring(2);
            else if (val.startsWith('9')) val = val.substring(1);

            let formatted = '';
            let valIdx = 0;
            const firstZero = pattern.indexOf('0');
            if (firstZero !== -1) {
                formatted = pattern.substring(0, firstZero);
                for (let i = firstZero; i < pattern.length && valIdx < val.length; i++) {
                    formatted += pattern[i] === '0' ? val[valIdx++] : pattern[i];
                }
            } else {
                for (let i = 0; i < pattern.length && valIdx < val.length; i++) {
                    formatted += pattern[i] === '0' ? val[valIdx++] : pattern[i];
                }
            }
            input.value = formatted;
        };

        input.addEventListener('input', applyFormat);
        input.addEventListener('keydown', (e) => {
            const firstZero = pattern.indexOf('0');
            if (firstZero !== -1 && input.selectionStart < firstZero) {
                if (e.key === 'Backspace' || e.key === 'Delete' || e.key.length === 1) {
                    input.setSelectionRange(firstZero, firstZero);
                }
            }
        });
        applyFormat();
    };

    const sssInput = document.getElementById('edit_sss_input');
    const philInput = document.getElementById('edit_philhealth_input');
    const pagibigInput = document.getElementById('edit_pagibig_input');
    const phoneInputMask = document.getElementById('edit_phone_input');
    if (sssInput) formatInput(sssInput, '00-0000000-0');
    if (philInput) formatInput(philInput, '00-000000000-0');
    if (pagibigInput) formatInput(pagibigInput, '0000-0000-0000');
    if (phoneInputMask) formatInput(phoneInputMask, '+63 900 000 0000');

    window.onclick = function (event) {
        const archiveModal = document.getElementById('archive-modal');
        const viewModal = document.getElementById('view-modal');
        const editModal = document.getElementById('edit-modal');
        if (event.target === archiveModal) closeArchiveModal();
        if (event.target === viewModal) closeViewModal();
        if (event.target === editModal) closeEditModal();
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeArchiveModal();
            closeViewModal();
            closeEditModal();
        }
    });
});