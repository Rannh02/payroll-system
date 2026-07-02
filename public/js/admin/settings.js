function switchTab(tabId, event) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
    document.getElementById(tabId + '-tab').classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
    const pwInput = document.getElementById('new_password');
    if (!pwInput) return;

    pwInput.addEventListener('input', function () {
        checkPasswordStrength(this.value);
    });
});

function checkPasswordStrength(val) {
    const wrap = document.getElementById('pw-strength-bar-wrap');
    const seg1 = document.getElementById('pw-seg-1');
    const seg2 = document.getElementById('pw-seg-2');
    const seg3 = document.getElementById('pw-seg-3');
    const text = document.getElementById('pw-strength-text');
    const hint = document.getElementById('pw-strength-hint');

    if (!val) {
        wrap.style.display = 'none';
        return;
    }
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
    } else {
        level = 'Strong';
        color = '#22c55e';
        segs = 3;
    }

    seg1.style.background = segs >= 1 ? color : '#e2e8f0';
    seg2.style.background = segs >= 2 ? color : '#e2e8f0';
    seg3.style.background = segs >= 3 ? color : '#e2e8f0';

    text.textContent = level;
    text.style.color = color;
    hint.textContent = hints.length ? 'Missing: ' + hints.join(', ') : '✓ All requirements met';
    hint.style.color = hints.length ? '#94a3b8' : '#22c55e';
}