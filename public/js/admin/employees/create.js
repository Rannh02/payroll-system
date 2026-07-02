document.addEventListener('DOMContentLoaded', () => {
    const posSelect = document.getElementById('position_select');
    const salInput = document.getElementById('salary_input');
    if (posSelect && salInput) {
        const sync = () => {
            const opt = posSelect.options[posSelect.selectedIndex];
            salInput.value = opt?.dataset?.salary ?? '';
        };
        posSelect.addEventListener('change', sync);
        if (posSelect.value) sync();
    }

    const photoInput = document.getElementById('profile_photo_input');
    const avatarPreview = document.getElementById('avatar_preview');
    const avatarIcon = document.getElementById('avatar_icon');
    const uploadButton = document.getElementById('upload_photo_btn');
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

    const sssInput = document.getElementById('sss_input');
    const philInput = document.getElementById('philhealth_input');
    const pagibigInput = document.getElementById('pagibig_input');
    const phoneInputMask = document.getElementById('phone_input');

    if (sssInput) formatInput(sssInput, '00-0000000-0');
    if (philInput) formatInput(philInput, '00-000000000-0');
    if (pagibigInput) formatInput(pagibigInput, '0000-0000-0000');
    if (phoneInputMask) formatInput(phoneInputMask, '+63 900 000 0000');

    const pwInput = document.getElementById('emp_password');
    if (pwInput) {
        pwInput.addEventListener('input', function () {
            checkPasswordStrength(this.value);
        });
    }

    function checkPasswordStrength(val) {
        const wrap = document.getElementById('pw-strength-bar-wrap');
        const seg1 = document.getElementById('pw-seg-1');
        const seg2 = document.getElementById('pw-seg-2');
        const seg3 = document.getElementById('pw-seg-3');
        const text = document.getElementById('pw-strength-text');
        const hint = document.getElementById('pw-strength-hint');

        if (!val) { wrap.style.display = 'none'; return; }
        wrap.style.display = 'block';

        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSymbol = /[^A-Za-z0-9]/.test(val);
        const longEnough = val.length >= 15;

        const score = [hasUpper, hasLower, hasNumber, hasSymbol, longEnough].filter(Boolean).length;
        const hints = [];
        if (!hasUpper) hints.push('uppercase letter');
        if (!hasLower) hints.push('lowercase letter');
        if (!hasNumber) hints.push('number');
        if (!hasSymbol) hints.push('symbol');
        if (!longEnough) hints.push('at least 15 characters');

        let level, color, segs;
        if (score <= 2) { level = 'Weak'; color = '#ef4444'; segs = 1; }
        else if (score <= 3) { level = 'Moderate'; color = '#f59e0b'; segs = 2; }
        else { level = 'Strong'; color = '#22c55e'; segs = 3; }

        seg1.style.background = segs >= 1 ? color : '#e2e8f0';
        seg2.style.background = segs >= 2 ? color : '#e2e8f0';
        seg3.style.background = segs >= 3 ? color : '#e2e8f0';
        text.textContent = level;
        text.style.color = color;
        hint.textContent = hints.length ? 'Missing: ' + hints.join(', ') : '✓ All requirements met';
        hint.style.color = hints.length ? '#94a3b8' : '#22c55e';
    }

    const duplicateError = document.querySelector('[data-duplicate-error]')?.dataset.duplicateError;
    if (duplicateError) {
        alert(duplicateError);
    }
});