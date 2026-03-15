/**
 * ui.js – Xử lý giao diện
 * Bao gồm: nút lên đầu trang, menu mobile, dark mode, CSS động
 */

/* ----------------------------------------------------------------
   NÚT LÊN ĐẦU TRANG
---------------------------------------------------------------- */
(function initBackToTop() {

    // tạo nút nếu chưa có trong HTML
    let btn = document.getElementById('topBtn');
    if (!btn) {
        btn = document.createElement('button');
        btn.id = 'topBtn';
        btn.innerHTML = '↑';
        Object.assign(btn.style, {
            position:       'fixed',
            right:          '24px',
            bottom:         '110px',
            width:          '44px',
            height:         '44px',
            borderRadius:   '50%',
            background:     '#42a7f0',
            color:          '#fff',
            border:         'none',
            fontSize:       '18px',
            cursor:         'pointer',
            display:        'none',
            alignItems:     'center',
            justifyContent: 'center',
            boxShadow:      '0 4px 16px rgba(66,167,240,0.4)',
            zIndex:         '998',
            transition:     'opacity 0.3s, transform 0.3s',
        });
        document.body.appendChild(btn);
    }

    const mainEl = document.querySelector('main') || document.documentElement;
    const scrollTarget = (mainEl !== document.documentElement) ? mainEl : window;

    // hiện/ẩn nút khi cuộn
    scrollTarget.addEventListener('scroll', () => {
        const scrolled = (mainEl.scrollTop || window.scrollY) > 200;
        btn.style.display = scrolled ? 'flex' : 'none';
    });

    // cuộn về đầu trang khi bấm
    btn.addEventListener('click', () => {
        if (mainEl !== document.documentElement) {
            mainEl.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
})();


/* ----------------------------------------------------------------
   MENU MOBILE – ẩn/hiện sidebar
---------------------------------------------------------------- */
(function initMobileMenu() {
    const btn     = document.getElementById('menuBtn');
    const sidebar = document.querySelector('aside') || document.getElementById('sidebar');
    if (!btn || !sidebar) return;

    // bấm hamburger → toggle sidebar
    btn.addEventListener('click', () => {
        const isOpen = sidebar.classList.contains('menu-open');
        sidebar.classList.toggle('menu-open', !isOpen);
        sidebar.style.transform = isOpen ? 'translateX(-100%)' : 'translateX(0)';
    });

    // bấm ra ngoài → đóng sidebar trên mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 &&
            !sidebar.contains(e.target) &&
            !btn.contains(e.target)) {
            sidebar.classList.remove('menu-open');
            sidebar.style.transform = 'translateX(-100%)';
        }
    });
})();


/* ----------------------------------------------------------------
   DARK MODE – lưu vào localStorage
---------------------------------------------------------------- */
(function initDarkMode() {
    const btn = document.getElementById('darkModeBtn');
    if (!btn) return;

    // khôi phục trạng thái lần trước
    if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
    }

    btn.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        const isDark = document.documentElement.classList.contains('dark');
        localStorage.setItem('darkMode', isDark);

        const icon = btn.querySelector('.material-symbols-outlined');
        if (icon) icon.textContent = isDark ? 'light_mode' : 'dark_mode';
    });
})();


/* ----------------------------------------------------------------
   CSS ĐỘNG – inject style cần thiết vào trang
---------------------------------------------------------------- */
(function injectStyles() {
    if (document.getElementById('dynamic-styles')) return;

    const style = document.createElement('style');
    style.id = 'dynamic-styles';
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* highlight bài đang phát */
        .song-row.playing td { color: #42a7f0 !important; }
        .song-row.playing .song-num { font-weight: 700; }
        .song-row { transition: background 0.2s, opacity 0.3s, transform 0.3s; }

        /* icon ▶ nhấp nháy */
        .eq-icon {
            display: inline-block;
            color: #42a7f0;
            animation: pulse-play 0.8s ease-in-out infinite alternate;
        }
        @keyframes pulse-play {
            from { opacity: 0.5; }
            to   { opacity: 1; }
        }

        /* genre pill active */
        .active-pill {
            background: #42a7f0 !important;
            color: #fff !important;
            border-color: #42a7f0 !important;
        }

        /* sidebar ẩn trên mobile */
        @media (max-width: 768px) {
            aside {
                position: fixed;
                left: 0; top: 0;
                height: 100vh;
                z-index: 200;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            aside.menu-open { transform: translateX(0); }
        }

        /* scrollbar tùy chỉnh */
        main::-webkit-scrollbar       { width: 6px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    `;
    document.head.appendChild(style);
})();
