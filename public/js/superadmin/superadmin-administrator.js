document.addEventListener('DOMContentLoaded', () => {
    const password = document.getElementById('admin_password');

    if (!password) return;

    password.addEventListener('input', function () {
        checkPasswordStrength(this.value);
    });

    function checkPasswordStrength(val) {
        const wrap = document.getElementById('admin-pw-strength-bar-wrap');
        const seg1 = document.getElementById('admin-pw-seg-1');
        const seg2 = document.getElementById('admin-pw-seg-2');
        const seg3 = document.getElementById('admin-pw-seg-3');
        const text = document.getElementById('admin-pw-strength-text');
        const hint = document.getElementById('admin-pw-strength-hint');

        if (!val) {
            wrap.style.display = 'none';
            return;
        }

        wrap.style.display = 'block';

        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSymbol = /[^A-Za-z0-9]/.test(val);
        const longEnough = val.length >= 8;

        const score = [hasUpper, hasLower, hasNumber, hasSymbol, longEnough].filter(Boolean).length;

        const hints = [];
        if (!hasUpper) hints.push('uppercase letter');
        if (!hasLower) hints.push('lowercase letter');
        if (!hasNumber) hints.push('number');
        if (!hasSymbol) hints.push('symbol');
        if (!longEnough) hints.push('at least 8 characters');

        let level;
        let color;
        let segs;
        if (score <= 2) {
            level = 'Weak';
            color = '#ef4444';
            segs = 1;
        } else if (score <= 3) {
            level = 'Moderate';
            color = '#f59e0b';
            segs = 2;
        } else if (score <= 4) {
            level = 'Strong';
            color = '#22c55e';
            segs = 3;
        } else {
            level = 'Very Strong';
            color = '#0f766e';
            segs = 3;
        }

        seg1.style.background = segs >= 1 ? color : '#e2e8f0';
        seg2.style.background = segs >= 2 ? color : '#e2e8f0';
        seg3.style.background = segs >= 3 ? color : '#e2e8f0';

        text.textContent = level;
        text.style.color = color;
        hint.textContent = hints.length ? 'Missing: ' + hints.join(', ') : '✓ Password meets the required complexity';
        hint.style.color = hints.length ? '#94a3b8' : '#22c55e';
    }
});