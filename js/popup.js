/**
 * popup.js – Hiển thị thông báo popup góc trên phải
 * Dùng: showPopup('Nội dung', 'success' | 'error' | 'info' | 'warning')
 */

function showPopup(message, type = 'success') {

    // xóa popup cũ nếu đang hiển thị
    const old = document.getElementById('popup-notify');
    if (old) old.remove();

    // màu sắc và icon theo từng loại
    const colors = {
        success: { bg: '#16a34a', icon: '✓' },
        error:   { bg: '#dc2626', icon: '✕' },
        info:    { bg: '#2563eb', icon: 'ℹ' },
        warning: { bg: '#d97706', icon: '⚠' },
    };
    const c = colors[type] || colors.success;

    // tạo div thông báo
    const box = document.createElement('div');
    box.id = 'popup-notify';
    box.innerHTML = `<span style="font-size:16px;font-weight:700;">${c.icon}</span> ${message}`;

    Object.assign(box.style, {
        position:     'fixed',
        top:          '20px',
        right:        '20px',
        background:   c.bg,
        color:        '#fff',
        padding:      '12px 20px',
        borderRadius: '12px',
        fontFamily:   "'Spline Sans', sans-serif",
        fontSize:     '14px',
        fontWeight:   '500',
        display:      'flex',
        alignItems:   'center',
        gap:          '8px',
        boxShadow:    '0 8px 24px rgba(0,0,0,0.18)',
        zIndex:       '9999',
        opacity:      '0',
        transform:    'translateY(-10px)',
        transition:   'all 0.3s ease',
    });
    document.body.appendChild(box);

    // hiệu ứng fade in
    requestAnimationFrame(() => {
        box.style.opacity   = '1';
        box.style.transform = 'translateY(0)';
    });

    // tự biến mất sau 3 giây
    setTimeout(() => {
        box.style.opacity   = '0';
        box.style.transform = 'translateY(-10px)';
        setTimeout(() => box.remove(), 300);
    }, 2800);
}
