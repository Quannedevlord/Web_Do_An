/**
 * script.js – Toàn bộ xử lý JavaScript cho web nghe nhạc
 * Bao gồm: Audio Player, AJAX tải nhạc, Form Validation, UI tương tác
 */

// dùng IIFE để tránh xung đột biến với các file JS khác
(function () {
    'use strict';

    /* ================================================================
       TIỆN ÍCH – viết tắt để gọi nhanh querySelector
    ================================================================ */

    // tìm 1 phần tử theo selector (giống document.querySelector)
    const $ = (selector, context = document) => context.querySelector(selector);

    // tìm tất cả phần tử theo selector, trả về mảng
    const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));


    /* ================================================================
       POPUP THÔNG BÁO
       Hiện hộp thông báo góc trên phải, tự biến mất sau 3 giây
    ================================================================ */
    function showPopup(message, type = 'success') {

        // xóa popup cũ nếu đang hiển thị
        const old = $('#popup-notify');
        if (old) old.remove();

        // màu sắc và icon theo từng loại thông báo
        const colors = {
            success: { bg: '#16a34a', icon: '✓' },   // xanh lá – thành công
            error:   { bg: '#dc2626', icon: '✕' },   // đỏ – lỗi
            info:    { bg: '#2563eb', icon: 'ℹ' },   // xanh dương – thông tin
            warning: { bg: '#d97706', icon: '⚠' },   // cam – cảnh báo
        };
        const c = colors[type] || colors.success;

        // tạo phần tử div để hiển thị thông báo
        const box = document.createElement('div');
        box.id = 'popup-notify';
        box.innerHTML = `<span style="font-size:16px;font-weight:700;">${c.icon}</span> ${message}`;

        // css cho popup
        Object.assign(box.style, {
            position:   'fixed',
            top:        '20px',
            right:      '20px',
            background: c.bg,
            color:      '#fff',
            padding:    '12px 20px',
            borderRadius: '12px',
            fontFamily: "'Spline Sans', sans-serif",
            fontSize:   '14px',
            fontWeight: '500',
            display:    'flex',
            alignItems: 'center',
            gap:        '8px',
            boxShadow:  '0 8px 24px rgba(0,0,0,0.18)',
            zIndex:     '9999',
            opacity:    '0',
            transform:  'translateY(-10px)',
            transition: 'all 0.3s ease',
        });
        document.body.appendChild(box);

        // hiệu ứng xuất hiện (fade in + trượt xuống)
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


    /* ================================================================
       NÚT LÊN ĐẦU TRANG
       Hiện khi cuộn xuống hơn 200px, bấm để cuộn lên đầu trang
    ================================================================ */
    (function initBackToTop() {

        // tạo nút nếu chưa có trong HTML
        let btn = $('#topBtn');
        if (!btn) {
            btn = document.createElement('button');
            btn.id = 'topBtn';
            btn.innerHTML = '↑';
            Object.assign(btn.style, {
                position:     'fixed',
                right:        '24px',
                bottom:       '110px',
                width:        '44px',
                height:       '44px',
                borderRadius: '50%',
                background:   '#42a7f0',
                color:        '#fff',
                border:       'none',
                fontSize:     '18px',
                cursor:       'pointer',
                display:      'none',
                alignItems:   'center',
                justifyContent: 'center',
                boxShadow:    '0 4px 16px rgba(66,167,240,0.4)',
                zIndex:       '998',
                transition:   'opacity 0.3s, transform 0.3s',
            });
            document.body.appendChild(btn);
        }

        // lắng nghe sự kiện cuộn trang
        const mainEl = $('main') || document.documentElement;
        const scrollTarget = (mainEl !== document.documentElement) ? mainEl : window;

        scrollTarget.addEventListener('scroll', () => {
            // lấy vị trí cuộn hiện tại
            const scrolled = (mainEl.scrollTop || window.scrollY) > 200;
            btn.style.display = scrolled ? 'flex' : 'none';
        });

        // bấm nút → cuộn về đầu trang mượt
        btn.addEventListener('click', () => {
            if (mainEl !== document.documentElement) {
                mainEl.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    })();


    /* ================================================================
       MENU MOBILE – ẩn/hiện sidebar khi bấm nút hamburger
    ================================================================ */
    (function initMobileMenu() {
        const btn     = $('#menuBtn');
        const sidebar = $('aside') || $('#sidebar');
        if (!btn || !sidebar) return;

        // bấm nút hamburger → toggle sidebar
        btn.addEventListener('click', () => {
            const isOpen = sidebar.classList.contains('menu-open');
            sidebar.classList.toggle('menu-open', !isOpen);
            sidebar.style.transform = isOpen ? 'translateX(-100%)' : 'translateX(0)';
        });

        // bấm ra ngoài sidebar trên mobile → đóng lại
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 &&
                !sidebar.contains(e.target) &&
                !btn.contains(e.target)) {
                sidebar.classList.remove('menu-open');
                sidebar.style.transform = 'translateX(-100%)';
            }
        });
    })();


    /* ================================================================
       KIỂM TRA FORM (VALIDATION)
       Kiểm tra dữ liệu người dùng nhập trước khi gửi lên server
    ================================================================ */

    // hàm kiểm tra định dạng email có hợp lệ không
    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // --- Validation form đăng nhập ---
    (function initLoginValidation() {
        const form = $('form[data-purpose="login-form"]');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const emailEl = $('[name="email"]', form);
            const passEl  = $('[name="password"]', form);

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

    // --- Validation form đăng ký ---
    (function initRegisterValidation() {
        const form = $('form[data-purpose="register-form"]');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const usernameEl = $('[name="username"]', form);
            const emailEl    = $('[name="email"]', form);
            const passEl     = $('[name="password"]', form);

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


    /* ================================================================
       AUDIO PLAYER – xử lý phát nhạc
       Bao gồm: play/pause, next/prev, thanh tiến trình, âm lượng
    ================================================================ */
    const Player = (function () {

        let songs     = [];   // mảng chứa danh sách bài hát [{id, title, artist, file, image}]
        let current   = -1;   // chỉ số bài đang phát (-1 = chưa chọn bài nào)
        let isPlaying = false; // trạng thái đang phát hay không

        // tạo đối tượng Audio để phát nhạc
        const audio = new Audio();

        // tham chiếu đến các phần tử DOM trong player bar
        const dom = {};

        // hàm lấy các phần tử DOM (chạy sau khi trang load xong)
        function resolveDOM() {
            dom.playBtn       = $('#playBtn');
            dom.prevBtn       = $('#prevBtn');
            dom.nextBtn       = $('#nextBtn');
            dom.shuffleBtn    = $('#shuffleBtn');
            dom.repeatBtn     = $('#repeatBtn');
            dom.progressBar   = $('#progressBar');
            dom.progressTrack = $('#progressTrack');
            dom.currentTime   = $('#currentTime');
            dom.totalTime     = $('#totalTime');
            dom.volumeTrack   = $('#volumeTrack');
            dom.volumeBar     = $('#volumeBar');
            dom.songTitle     = $('#playerTitle');
            dom.songArtist    = $('#playerArtist');
            dom.songCover     = $('#playerCover');
        }

        // chuyển giây thành định dạng phút:giây (ví dụ: 185 → "3:05")
        function formatTime(seconds) {
            if (isNaN(seconds) || seconds === Infinity) return '0:00';
            const m   = Math.floor(seconds / 60);
            const sec = Math.floor(seconds % 60).toString().padStart(2, '0');
            return `${m}:${sec}`;
        }

        // cập nhật giao diện player bar theo bài đang phát
        function updateUI() {
            const song = songs[current];
            if (!song) return;

            // cập nhật tên bài hát và ca sĩ trên player bar
            if (dom.songTitle)  dom.songTitle.textContent  = song.title  || 'Unknown';
            if (dom.songArtist) dom.songArtist.textContent = song.artist || 'Unknown';

            // cập nhật ảnh bìa bài hát
            if (dom.songCover) {
                dom.songCover.style.backgroundImage = song.image
                    ? `url('images/${song.image}')`
                    : `url('https://via.placeholder.com/56x56/42a7f0/ffffff?text=♪')`;
            }

            // đổi icon nút play ↔ pause
            if (dom.playBtn) {
                const icon = dom.playBtn.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = isPlaying ? 'pause' : 'play_arrow';
            }

            // đánh dấu bài đang phát trong danh sách (highlight màu xanh)
            $$('.song-row').forEach((row, i) => {
                row.classList.toggle('playing', i === current);

                // thay số thứ tự bằng icon ▶ khi đang phát
                const numCell = row.querySelector('.song-num');
                if (numCell) {
                    numCell.innerHTML = (i === current && isPlaying)
                        ? `<span class="eq-icon">▶</span>`
                        : (i + 1);
                }
            });
        }

        // phát bài hát tại vị trí current trong mảng songs
        function playCurrent() {
            const song = songs[current];
            if (!song) return;

            // gán đường dẫn file nhạc cho Audio
            audio.src = `music/${song.file}`;

            // phát nhạc
            audio.play()
                .then(() => {
                    isPlaying = true;
                    updateUI();
                    showPopup(`▶ ${song.title} – ${song.artist}`, 'info');
                })
                .catch(() => {
                    // báo lỗi nếu không phát được (file không tồn tại, sai định dạng...)
                    showPopup('Không thể phát bài này, kiểm tra lại file nhạc', 'error');
                });
        }

        // toggle play / pause
        function toggle() {
            if (songs.length === 0) {
                showPopup('Chưa có bài hát nào trong danh sách', 'warning');
                return;
            }

            // nếu chưa chọn bài nào → phát bài đầu tiên
            if (current === -1) {
                current = 0;
                playCurrent();
                return;
            }

            // đang phát → tạm dừng
            if (isPlaying) {
                audio.pause();
                isPlaying = false;
            } else {
                // đang tạm dừng → phát tiếp
                audio.play();
                isPlaying = true;
            }
            updateUI();
        }

        // chuyển về bài trước (nếu đang ở bài đầu thì quay về bài cuối)
        function prev() {
            if (songs.length === 0) return;
            current = (current - 1 + songs.length) % songs.length;
            playCurrent();
        }

        // chuyển sang bài tiếp theo (nếu đang ở bài cuối thì quay về bài đầu)
        function next() {
            if (songs.length === 0) return;
            current = (current + 1) % songs.length;
            playCurrent();
        }

        // chọn và phát bài hát theo chỉ số index trong mảng songs
        function selectSong(index) {
            current = index;
            playCurrent();
        }

        // điều chỉnh âm lượng (v từ 0.0 đến 1.0)
        function setVolume(v) {
            audio.volume = Math.max(0, Math.min(1, v));
            // cập nhật thanh âm lượng trên giao diện
            if (dom.volumeBar) {
                dom.volumeBar.style.width = (audio.volume * 100) + '%';
            }
        }

        // --- Sự kiện của Audio ---

        // cập nhật thanh tiến trình khi bài đang phát
        audio.addEventListener('timeupdate', () => {
            if (!audio.duration) return;

            // tính phần trăm đã phát
            const pct = (audio.currentTime / audio.duration) * 100;

            // cập nhật thanh tiến trình
            if (dom.progressTrack) dom.progressTrack.style.width = pct + '%';

            // cập nhật thời gian hiển thị
            if (dom.currentTime) dom.currentTime.textContent = formatTime(audio.currentTime);
            if (dom.totalTime)   dom.totalTime.textContent   = formatTime(audio.duration);
        });

        // tự động chuyển bài khi phát xong
        audio.addEventListener('ended', () => {
            next();
        });

        // xử lý lỗi phát nhạc
        audio.addEventListener('error', () => {
            showPopup('Lỗi phát nhạc – kiểm tra lại file!', 'error');
            isPlaying = false;
            updateUI();
        });

        // gắn sự kiện vào các nút điều khiển trên giao diện
        function bindControls() {
            resolveDOM();

            // nút play/pause
            if (dom.playBtn) dom.playBtn.addEventListener('click', toggle);

            // nút bài trước
            if (dom.prevBtn) dom.prevBtn.addEventListener('click', prev);

            // nút bài tiếp theo
            if (dom.nextBtn) dom.nextBtn.addEventListener('click', next);

            // bấm vào thanh tiến trình để tua nhạc
            if (dom.progressBar) {
                dom.progressBar.addEventListener('click', (e) => {
                    if (!audio.duration) return;
                    const rect = dom.progressBar.getBoundingClientRect();
                    const pct  = (e.clientX - rect.left) / rect.width;
                    audio.currentTime = pct * audio.duration;
                });
            }

            // bấm vào thanh âm lượng để điều chỉnh
            if (dom.volumeTrack) {
                dom.volumeTrack.addEventListener('click', (e) => {
                    const rect = dom.volumeTrack.getBoundingClientRect();
                    const v    = (e.clientX - rect.left) / rect.width;
                    setVolume(v);
                });
            }
        }

        // trả về các hàm public để dùng bên ngoài
        return {
            selectSong,
            toggle,
            prev,
            next,
            bindControls,
            setVolume,

            // gán danh sách bài hát mới vào player
            setSongs(arr) {
                songs.splice(0, songs.length, ...arr);
            },
        };
    })();


    /* ================================================================
       HIỂN THỊ DANH SÁCH BÀI HÁT
       Render HTML cho từng bài hát trong bảng
    ================================================================ */

    // hàm escape ký tự đặc biệt HTML để tránh XSS
    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    // render danh sách bài hát vào bảng HTML
    function renderSongs(songs) {
        const container = $('#songList');
        if (!container) return;

        // nếu không có bài hát nào
        if (!songs || songs.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align:center;padding:32px;color:#94a3b8;">
                        Chưa có bài hát nào.
                        <a href="add_song.php" style="color:#42a7f0;">Thêm ngay</a>
                    </td>
                </tr>`;
            return;
        }

        // tạo HTML cho từng bài hát
        container.innerHTML = songs.map((song, i) => `
            <tr class="song-row group hover:bg-white/80 transition-all cursor-pointer"
                data-index="${i}"
                onclick="window.__player.selectSong(${i})">

                <!-- số thứ tự -->
                <td class="py-4 px-4 text-center text-slate-400 song-num">${i + 1}</td>

                <!-- tên bài hát và ảnh bìa -->
                <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-lg bg-cover bg-center shadow-sm song-cover"
                             style="background-image:url('images/${song.image || ''}');background-color:#e2e8f0;">
                        </div>
                        <span class="text-slate-900 font-semibold">${escHtml(song.title)}</span>
                    </div>
                </td>

                <!-- tên ca sĩ -->
                <td class="py-4 px-4 text-slate-500">${escHtml(song.artist)}</td>

                <!-- nút sửa và xóa – chỉ hiện khi đã đăng nhập -->
                <td class="py-4 px-4 text-right" id="action-${song.id}">
                </td>
            </tr>
        `).join('');

        // thêm nút Sửa/Xóa nếu đã đăng nhập (tránh lỗi backtick lồng nhau)
        if (window.isLoggedIn) {
            songs.forEach(song => {
                const cell = document.getElementById('action-' + song.id);
                if (!cell) return;
                cell.innerHTML = `
                    <div class="flex items-center justify-end gap-2">
                        <a href="edit_song.php?id=${song.id}"
                           onclick="event.stopPropagation()"
                           class="text-blue-400 hover:text-blue-600 text-xs px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
                           ✏ Sửa
                        </a>
                        <button onclick="event.stopPropagation(); deleteSong(${song.id}, this)"
                                class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded-lg bg-red-50 hover:bg-red-100 transition-colors">
                            🗑 Xóa
                        </button>
                    </div>`;
            });
        }

        // truyền danh sách bài hát vào Player để phát nhạc
        Player.setSongs(songs);
    }


    /* ================================================================
       TẢI DANH SÁCH BÀI HÁT BẰNG AJAX
       Gọi getSongs.php để lấy dữ liệu, không cần tải lại trang
    ================================================================ */
    function loadSongs() {
        const container = $('#songList');
        if (!container) return;

        // hiển thị trạng thái đang tải
        container.innerHTML = `
            <tr>
                <td colspan="4" style="text-align:center;padding:32px;color:#94a3b8;">
                    <span style="animation:spin 1s linear infinite;display:inline-block;">⏳</span>
                    Đang tải bài hát...
                </td>
            </tr>`;

        // gọi API lấy danh sách bài hát
        fetch('getSongs.php')
            .then(res => {
                // kiểm tra server có phản hồi thành công không
                if (!res.ok) throw new Error('Lỗi server: ' + res.status);
                return res.json();
            })
            .then(data => {
                // window.isLoggedIn đã được PHP set sẵn trong index.php
                // không override ở đây để tránh lệch

                // kiểm tra có bài hát không
                if (!data.songs || data.songs.length === 0) {
                    renderSongs([]);
                } else {
                    // render danh sách bài hát lên giao diện
                    renderSongs(data.songs);
                }
            })
            .catch(err => {
                // hiển thị lỗi nếu không kết nối được server
                container.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align:center;padding:32px;color:#ef4444;">
                            ⚠ Không thể tải dữ liệu.
                            <button onclick="loadSongs()"
                                    style="color:#42a7f0;background:none;border:none;cursor:pointer;">
                                Thử lại
                            </button>
                        </td>
                    </tr>`;
                console.error('loadSongs lỗi:', err);
            });
    }


    /* ================================================================
       XÓA BÀI HÁT BẰNG AJAX
       Xóa không cần tải lại trang, có animation
    ================================================================ */
    window.deleteSong = function (id, btn) {

        // hỏi xác nhận trước khi xóa
        if (!confirm('Bạn có chắc muốn xóa bài hát này không?')) return;

        // lấy dòng chứa bài hát cần xóa
        const row = btn.closest('tr');

        // làm mờ dòng trong khi đang xử lý
        row.style.opacity       = '0.5';
        row.style.pointerEvents = 'none';

        // gọi API xóa bài hát theo id
        fetch(`delete_song.php?id=${id}`)
            .then(res => res.text())
            .then(msg => {
                if (msg.includes('thành công')) {
                    // animation biến mất trước khi xóa khỏi DOM
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity    = '0';
                    row.style.transform  = 'translateX(20px)';

                    setTimeout(() => {
                        row.remove();
                        showPopup('Đã xóa bài hát thành công', 'success');
                        loadSongs(); // tải lại danh sách sau khi xóa
                    }, 400);
                } else {
                    // khôi phục dòng nếu xóa thất bại
                    row.style.opacity       = '1';
                    row.style.pointerEvents = '';
                    showPopup('Lỗi khi xóa bài hát', 'error');
                }
            })
            .catch(() => {
                row.style.opacity       = '1';
                row.style.pointerEvents = '';
                showPopup('Lỗi kết nối server', 'error');
            });
    };


    /* ================================================================
       THÊM BÀI HÁT BẰNG AJAX
       Submit form không reload trang
    ================================================================ */
    (function initAddSongForm() {
        const form = $('#addSongForm') || $('form[action*="add_song"]');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault(); // ngăn form gửi theo cách thông thường

            const titleEl  = $('[name="title"]',  form);
            const artistEl = $('[name="artist"]', form);
            const fileEl   = $('[name="file"]',   form);

            // kiểm tra dữ liệu nhập
            if (!titleEl.value.trim()) {
                showPopup('Vui lòng nhập tên bài hát', 'error');
                titleEl.focus();
                return;
            }
            if (!artistEl.value.trim()) {
                showPopup('Vui lòng nhập tên ca sĩ', 'error');
                artistEl.focus();
                return;
            }
            if (!fileEl.value.trim()) {
                showPopup('Vui lòng nhập tên file mp3', 'error');
                fileEl.focus();
                return;
            }

            // lấy dữ liệu từ form
            const data = new FormData(form);
            data.append('add', '1'); // để PHP nhận biết đây là yêu cầu thêm bài

            // gửi dữ liệu lên server
            fetch('add_song.php', { method: 'POST', body: data })
                .then(res => res.text())
                .then(msg => {
                    if (msg.includes('thành công')) {
                        showPopup('✓ Thêm bài hát thành công!', 'success');
                        form.reset();    // xóa trắng form sau khi thêm
                        loadSongs();     // tải lại danh sách bài hát
                    } else {
                        showPopup('Lỗi: ' + msg, 'error');
                    }
                })
                .catch(() => showPopup('Lỗi kết nối server', 'error'));
        });
    })();


    /* ================================================================
       TÌM KIẾM BÀI HÁT (lọc phía client, không cần reload)
    ================================================================ */
    (function initSearch() {
        const input = $('#searchInput');
        if (!input) return;

        let debounceTimer; // timer để không lọc liên tục khi đang gõ

        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);

            // chờ 300ms sau khi ngừng gõ mới lọc (tránh lag)
            debounceTimer = setTimeout(() => {
                const keyword = input.value.trim().toLowerCase();
                const rows    = $$('.song-row');

                // hiển thị hoặc ẩn từng dòng theo từ khóa tìm kiếm
                rows.forEach(row => {
                    const text    = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });

                // kiểm tra có kết quả nào không
                const visible   = rows.filter(r => r.style.display !== 'none');
                const noResult  = $('#noSearchResult');

                if (visible.length === 0 && keyword !== '') {
                    // hiển thị thông báo không tìm thấy
                    if (!noResult) {
                        const tbody = $('tbody');
                        if (tbody) {
                            const tr = document.createElement('tr');
                            tr.id = 'noSearchResult';
                            tr.innerHTML = `
                                <td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">
                                    Không tìm thấy "<strong>${escHtml(keyword)}</strong>"
                                </td>`;
                            tbody.appendChild(tr);
                        }
                    }
                } else {
                    // xóa thông báo không tìm thấy nếu có kết quả
                    if (noResult) noResult.remove();
                }
            }, 300);
        });
    })();


    /* ================================================================
       LỌC THEO THỂ LOẠI (GENRE PILLS)
    ================================================================ */
    (function initGenrePills() {
        const pills = $$('[data-genre]');
        if (!pills.length) return;

        pills.forEach(pill => {
            pill.addEventListener('click', () => {
                // bỏ active pill cũ, set active pill mới
                pills.forEach(p => p.classList.remove('active-pill'));
                pill.classList.add('active-pill');

                const genre = pill.dataset.genre;
                const rows  = $$('.song-row');

                rows.forEach(row => {
                    if (genre === 'all') {
                        // "Tất cả" → hiển thị hết
                        row.style.display = '';
                    } else {
                        // chỉ hiển thị bài có tên khớp thể loại
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(genre.toLowerCase()) ? '' : 'none';
                    }
                });
            });
        });
    })();


    /* ================================================================
       DARK MODE – chế độ tối/sáng
       Lưu vào localStorage để nhớ khi tải lại trang
    ================================================================ */
    (function initDarkMode() {
        const btn = $('#darkModeBtn');
        if (!btn) return;

        // khôi phục trạng thái dark mode từ lần trước
        const isDarkSaved = localStorage.getItem('darkMode') === 'true';
        if (isDarkSaved) document.documentElement.classList.add('dark');

        btn.addEventListener('click', () => {
            // toggle dark mode
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');

            // lưu trạng thái vào localStorage
            localStorage.setItem('darkMode', isDark);

            // đổi icon nút
            const icon = btn.querySelector('.material-symbols-outlined');
            if (icon) icon.textContent = isDark ? 'light_mode' : 'dark_mode';
        });
    })();


    /* ================================================================
       CSS ĐỘNG – inject style vào trang
    ================================================================ */
    (function injectStyles() {
        if ($('#dynamic-styles')) return; // tránh inject 2 lần

        const style = document.createElement('style');
        style.id = 'dynamic-styles';
        style.textContent = `
            /* animation loading */
            @keyframes spin {
                from { transform: rotate(0deg); }
                to   { transform: rotate(360deg); }
            }

            /* highlight bài đang phát */
            .song-row.playing td { color: #42a7f0 !important; }
            .song-row.playing .song-num { font-weight: 700; }

            /* animation dòng bài hát */
            .song-row { transition: background 0.2s, opacity 0.3s, transform 0.3s; }

            /* icon ▶ nhấp nháy khi đang phát */
            .eq-icon {
                display: inline-block;
                color: #42a7f0;
                animation: pulse-play 0.8s ease-in-out infinite alternate;
            }
            @keyframes pulse-play {
                from { opacity: 0.5; }
                to   { opacity: 1; }
            }

            /* genre pill đang active */
            .active-pill {
                background: #42a7f0 !important;
                color: #fff !important;
                border-color: #42a7f0 !important;
            }

            /* responsive – sidebar ẩn trên mobile */
            @media (max-width: 768px) {
                aside {
                    position: fixed;
                    left: 0; top: 0;
                    height: 100vh;
                    z-index: 200;
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }
                aside.menu-open {
                    transform: translateX(0);
                }
            }

            /* scrollbar tùy chỉnh */
            main::-webkit-scrollbar       { width: 6px; }
            main::-webkit-scrollbar-track { background: transparent; }
            main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        `;
        document.head.appendChild(style);
    })();


    /* ================================================================
       KHỞI TẠO – chạy sau khi trang load xong
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {

        // gắn sự kiện vào các nút điều khiển player
        Player.bindControls();

        // đăng ký player vào biến global để onclick trong HTML dùng được
        window.__player = Player;

        // tải danh sách bài hát nếu trang có bảng songList
        if ($('#songList')) {
            loadSongs();
        }

        // hiển thị flash message từ PHP (login thành công, đăng ký thành công...)
        const flashMsg  = document.body.dataset.flash;
        const flashType = document.body.dataset.flashType || 'success';
        if (flashMsg) showPopup(flashMsg, flashType);
    });

    // expose ra ngoài để các file PHP khác có thể gọi
    window.loadSongs  = loadSongs;
    window.showPopup  = showPopup;

})();
