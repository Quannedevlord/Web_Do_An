/**
 * validation.js – Kiểm tra dữ liệu form trước khi gửi lên server
 * Bao gồm: form đăng nhập, form đăng ký
 */

// kiểm tra định dạng email hợp lệ
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}


/* ----------------------------------------------------------------
   VALIDATION FORM ĐĂNG NHẬP
---------------------------------------------------------------- */
(function initLoginValidation() {
    const form = document.querySelector('form[data-purpose="login-form"]');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const emailEl = form.querySelector('[name="email"]');
        const passEl  = form.querySelector('[name="password"]');

        // kiểm tra email không rỗng
        if (!emailEl.value.trim()) {
            e.preventDefault();
            showPopup('Vui lòng nhập email', 'error');
            emailEl.focus();
            return;
        }

        // kiểm tra email đúng định dạng
        if (!validateEmail(emailEl.value.trim())) {
            e.preventDefault();
            showPopup('Email không hợp lệ', 'error');
            emailEl.focus();
            return;
        }

        // kiểm tra mật khẩu không rỗng
        if (!passEl.value.trim()) {
            e.preventDefault();
            showPopup('Vui lòng nhập mật khẩu', 'error');
            passEl.focus();
            return;
        }

        // kiểm tra mật khẩu đủ 6 ký tự
        if (passEl.value.length < 6) {
            e.preventDefault();
            showPopup('Mật khẩu phải từ 6 ký tự trở lên', 'error');
            passEl.focus();
            return;
        }
    });
})();


/* ----------------------------------------------------------------
   VALIDATION FORM ĐĂNG KÝ
---------------------------------------------------------------- */
(function initRegisterValidation() {
    const form = document.querySelector('form[data-purpose="register-form"]');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const usernameEl = form.querySelector('[name="username"]');
        const emailEl    = form.querySelector('[name="email"]');
        const passEl     = form.querySelector('[name="password"]');

        // kiểm tra username đủ 3 ký tự
        if (usernameEl && usernameEl.value.trim().length < 3) {
            e.preventDefault();
            showPopup('Username phải từ 3 ký tự trở lên', 'error');
            usernameEl.focus();
            return;
        }

        // kiểm tra email đúng định dạng
        if (emailEl && !validateEmail(emailEl.value.trim())) {
            e.preventDefault();
            showPopup('Email không hợp lệ', 'error');
            emailEl.focus();
            return;
        }

        // kiểm tra mật khẩu đủ 6 ký tự
        if (passEl && passEl.value.length < 6) {
            e.preventDefault();
            showPopup('Mật khẩu phải từ 6 ký tự trở lên', 'error');
            passEl.focus();
            return;
        }
    });
})();
