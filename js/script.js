// js/script.js
// - Menu toggle mobile
// - Back to top button
// - Form validation (login)
// - Popup thông báo (login/register)
// - Audio player (play/pause/progress, chọn bài click vào row có data-file)
// - Fetch load danh sách nhạc (tự động chèn nếu có container #songList)

/*
  YÊU CẦU HTML (nên có, nếu không script vẫn cố gắng hoạt động):
  - nút menu mobile: <button id="menuBtn">☰</button> (nên đặt trong header)
  - sidebar: <aside id="sidebar">...</aside>
  - nút back to top (nếu không có, script sẽ tạo): <button id="topBtn">↑</button>
  - login form: <form id="loginForm"> ... <input id="username"> <input id="password"> ... </form>
    nếu không có id, script sẽ tìm form[data-purpose="login-form"]
  - container để load danh sách nhạc bằng fetch: <div id="songList"></div>
  - để audio player trung tâm hoạt động: trong footer player bạn có thể có .glass-player (script tìm tự động)
  - mỗi row bài hát nếu muốn bật click để chọn: <tr data-file="music/song1.mp3">...</tr>
*/

(function () {
  'use strict';

  // ---------------------------------------
  // helper
  // ---------------------------------------
  function $(sel, ctx = document) { return ctx.querySelector(sel); }
  function $all(sel, ctx = document) { return Array.from(ctx.querySelectorAll(sel)); }

  function safeText(el) { return el ? el.textContent.trim() : ''; }

  // ---------------------------------------
  // 1) MENU TOGGLE MOBILE
  // ---------------------------------------
  (function menuToggle() {
    // Tìm nút có id menuBtn hoặc class .menu-btn
    const menuBtn = $('#menuBtn') || $('.menu-btn');
    // Tìm sidebar: #sidebar hoặc first <aside>
    let sidebar = $('#sidebar') || document.querySelector('aside');

    if (!menuBtn) {
      // Nếu không có menuBtn, tạo 1 nút nhỏ ở đầu body (mobile)
      const btn = document.createElement('button');
      btn.id = 'menuBtn';
      btn.type = 'button';
      btn.innerText = '☰';
      btn.style.cssText = 'position:fixed;left:14px;top:12px;z-index:60;padding:6px 8px;border-radius:6px;background:#fff;border:1px solid rgba(0,0,0,0.06);';
      document.body.appendChild(btn);
    }

    const btn = $('#menuBtn');

    if (!sidebar) {
      // Nếu không có sidebar, nothing to toggle
      return;
    }

    // make sidebar responsive: hide on small screens initially using inline style optional
    // (we won't force hide; just toggle display)
    btn.addEventListener('click', function () {
      const cur = window.getComputedStyle(sidebar).display;
      if (cur === 'none') {
        sidebar.style.display = '';
      } else {
        sidebar.style.display = 'none';
      }
    });

    // On resize: if width > 768 ensure visible; if <=768 keep state
    window.addEventListener('resize', function () {
      if (window.innerWidth > 992) {
        sidebar.style.display = '';
      }
    });
  })();

  // ---------------------------------------
  // 2) BACK TO TOP BUTTON
  // ---------------------------------------
  (function backToTop() {
    let topBtn = $('#topBtn');

    if (!topBtn) {
      // tạo nếu chưa có
      topBtn = document.createElement('button');
      topBtn.id = 'topBtn';
      topBtn.innerText = '↑';
      topBtn.setAttribute('aria-label', 'Back to top');
      topBtn.style.cssText = 'position:fixed;right:20px;bottom:90px;padding:10px 12px;border-radius:8px;background:#42a7f0;color:#fff;border:none;display:none;z-index:60;';
      document.body.appendChild(topBtn);
    }

    function checkScroll() {
      const y = document.documentElement.scrollTop || document.body.scrollTop;
      if (y > 200) topBtn.style.display = 'block';
      else topBtn.style.display = 'none';
    }

    window.addEventListener('scroll', checkScroll);
    // initial
    checkScroll();

    topBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  })();

  // ---------------------------------------
  // 3) POPUP THÔNG BÁO (dùng chung)
  // ---------------------------------------
  function showPopup(message = '', type = 'success', timeout = 2500) {
    // type: 'success'|'error'|'info'
    const colors = { success: '#16a34a', error: '#dc2626', info: '#2563eb' };
    const bg = colors[type] || colors.info;

    const box = document.createElement('div');
    box.className = 'js-popup-notification';
    box.innerText = message;
    box.style.cssText = [
      'position:fixed',
      'right:20px',
      'top:20px',
      'padding:12px 16px',
      'color:#fff',
      'border-radius:8px',
      'box-shadow:0 6px 18px rgba(0,0,0,0.12)',
      `background:${bg}`,
      'z-index:1000',
      'font-weight:600',
      'font-family:system-ui,Helvetica,Arial'
    ].join(';');

    document.body.appendChild(box);

    setTimeout(() => {
      box.style.transition = 'opacity 300ms ease, transform 300ms ease';
      box.style.opacity = '0';
      box.style.transform = 'translateY(-8px)';
    }, timeout);

    setTimeout(() => box.remove(), timeout + 350);
  }

  // ---------------------------------------
  // 4) FORM VALIDATION (login)
  // ---------------------------------------
  (function loginValidation() {
    // tìm form: #loginForm hoặc [data-purpose="login-form"] hoặc form[action*="login"]
    let loginForm = $('#loginForm') || document.querySelector('form[data-purpose="login-form"]') || document.querySelector('form[action*="login"]');

    if (!loginForm) return; // không tìm thấy form login => skip

    // tìm các field (flexible)
    const usernameEl = loginForm.querySelector('#username') || loginForm.querySelector('input[name="username"]') || loginForm.querySelector('input[type="text"]');
    const passwordEl = loginForm.querySelector('#password') || loginForm.querySelector('input[name="password"]') || loginForm.querySelector('input[type="password"]');

    loginForm.addEventListener('submit', function (e) {
      const username = usernameEl ? usernameEl.value.trim() : '';
      const password = passwordEl ? passwordEl.value : '';

      if (!username || !password) {
        e.preventDefault();
        showPopup('Vui lòng nhập đầy đủ thông tin', 'error');
        return;
      }

      if (password.length < 6) {
        e.preventDefault();
        showPopup('Mật khẩu phải >= 6 ký tự', 'error');
        return;
      }

      // Nếu hợp lệ: (không preventDefault) cho phép gửi form lên server
      // Nếu bạn muốn submit bằng AJAX, replace bằng fetch() và e.preventDefault()
      showPopup('Đăng nhập hợp lệ. Đang xử lý...', 'success', 1200);
      // allow normal submit
    });
  })();

  // ---------------------------------------
  // 5) FETCH LOAD DANH SÁCH NHẠC
  // ---------------------------------------
  (function fetchSongs() {
    const container = $('#songList'); // nếu bạn có chỗ để load danh sách nhạc
    const songsEndpoint = 'songs.php'; // backend hiện tại
    if (!container) {
      // không có container để chèn => không làm gì
      return;
    }

    // fetch songs.php (nếu backend trả JSON thì parse, nếu HTML thì chèn raw)
    fetch(songsEndpoint, { method: 'GET' })
      .then(async (res) => {
        const text = await res.text();

        // thử parse JSON
        try {
          const json = JSON.parse(text);
          // nếu được JSON => render list
          container.innerHTML = '';
          json.forEach((s, idx) => {
            const div = document.createElement('div');
            div.className = 'song-item p-3 border-b';
            div.innerHTML = `
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-cover bg-center" style="background-image:url(${s.image || ''})"></div>
                <div>
                  <div class="font-semibold">${s.title}</div>
                  <div class="text-sm text-slate-500">${s.artist}</div>
                </div>
              </div>
            `;
            // lưu file path để click chọn bài
            if (s.file) div.dataset.file = s.file;
            container.appendChild(div);
          });
        } catch (err) {
          // không phải JSON -> chèn raw HTML trả về (HTML từ songs.php)
          container.innerHTML = text;
        }

        // sau khi chèn, gắn sự kiện click cho các row có data-file
        bindSongRowClicks();
      })
      .catch((err) => {
        console.error('Fetch songs error:', err);
      });
  })();

  // ---------------------------------------
  // 6) AUDIO PLAYER (center player)
  // ---------------------------------------
  (function audioPlayer() {
    // Tạo 1 audio element trung tâm (nếu đã có audio tag main thì dùng nó)
    let mainAudio = $('#mainAudio');
    if (!mainAudio) {
      mainAudio = document.createElement('audio');
      mainAudio.id = 'mainAudio';
      mainAudio.preload = 'metadata';
      // mainAudio.controls = true; // ta dùng controls UI custom
      mainAudio.style.display = 'none';
      document.body.appendChild(mainAudio);
    }

    // TÌM phần player UI (glass-player) trong giaodien.html
    const glass = document.querySelector('.glass-player') || document.querySelector('.player') || null;

    // Tìm play button trong glass (nhiều theme class khác nhau).
    // fallback: tìm button có icon 'play_arrow' (Material icons)
    function findPlayButton() {
      if (!glass) return null;
      // tìm nút kích thước lớn thường có 'play_arrow' text
      const candidate = Array.from(glass.querySelectorAll('button, [role="button"], .play, .btn-play'))
        .find(el => (el.textContent || '').toLowerCase().includes('play') || el.querySelector('.material-symbols-outlined') && el.querySelector('.material-symbols-outlined').textContent.includes('play_arrow'));
      if (candidate) return candidate;
      // try another: the round primary button (heuristic)
      return glass.querySelector('button.size-12, button.rounded-full, button[class*="play"]');
    }

    const playBtn = findPlayButton();

    // find progress container and fill element
    let progressContainer = null;
    let progressFill = null;
    // look for element with class "group" inside glass (progress wrapper)
    if (glass) {
      progressContainer = glass.querySelector('.group') || glass.querySelector('.progress') || glass.querySelector('.progress-bar') || null;
      if (progressContainer) {
        // inner absolute fill (first child)
        progressFill = progressContainer.querySelector('div') || null;
      }
    }

    // update UI helper
    function updatePlayerUI() {
      if (!glass) return;
      // show current time / duration if spans exist
      const curSpan = glass.querySelector('.text-[10px]') || glass.querySelector('.current-time');
      const durSpan = glass.querySelectorAll('.text-[10px]')[1] || glass.querySelector('.duration');
      if (curSpan) curSpan.textContent = formatTime(mainAudio.currentTime || 0);
      if (durSpan && mainAudio.duration && !isNaN(mainAudio.duration)) durSpan.textContent = formatTime(mainAudio.duration);
      // update progress fill
      if (progressFill && mainAudio.duration) {
        const pct = (mainAudio.currentTime / mainAudio.duration) * 100;
        progressFill.style.width = pct + '%';
      }
      // update play icon text if material icon exists
      if (playBtn) {
        const iconEl = playBtn.querySelector('.material-symbols-outlined');
        if (iconEl) {
          iconEl.textContent = mainAudio.paused ? 'play_arrow' : 'pause';
        } else {
          playBtn.textContent = mainAudio.paused ? 'Play' : 'Pause';
        }
      }
    }

    // format seconds to M:SS
    function formatTime(s) {
      if (!s || isNaN(s)) return '0:00';
      const m = Math.floor(s / 60);
      const sec = Math.floor(s % 60);
      return m + ':' + (sec < 10 ? '0' + sec : sec);
    }

    // bind play/pause
    if (playBtn) {
      playBtn.addEventListener('click', function () {
        if (mainAudio.src) {
          if (mainAudio.paused) {
            mainAudio.play().catch(e => console.warn('Play blocked', e));
          } else {
            mainAudio.pause();
          }
        } else {
          showPopup('Chưa chọn bài hát để phát', 'info', 1800);
        }
        updatePlayerUI();
      });
    }

    // progress click / seek
    if (progressContainer) {
      progressContainer.style.cursor = 'pointer';
      progressContainer.addEventListener('click', function (ev) {
        if (!mainAudio.duration) return;
        const rect = progressContainer.getBoundingClientRect();
        const x = ev.clientX - rect.left;
        const pct = Math.max(0, Math.min(1, x / rect.width));
        mainAudio.currentTime = pct * mainAudio.duration;
        updatePlayerUI();
      });
    }

    // update on timeupdate
    mainAudio.addEventListener('timeupdate', updatePlayerUI);
    mainAudio.addEventListener('loadedmetadata', updatePlayerUI);
    mainAudio.addEventListener('play', updatePlayerUI);
    mainAudio.addEventListener('pause', updatePlayerUI);

    // keyboard space toggle
    window.addEventListener('keydown', function (e) {
      if (e.code === 'Space' && document.activeElement.tagName.toLowerCase() !== 'input' && mainAudio.src) {
        e.preventDefault();
        if (mainAudio.paused) mainAudio.play();
        else mainAudio.pause();
      }
    });

    // expose function to set song
    window.selectSong = function (fileUrl, title, artist) {
      if (!fileUrl) return;
      mainAudio.src = fileUrl;
      mainAudio.play().catch(e => console.warn(e));
      updatePlayerUI();
      // update now playing UI if exists
      if (glass) {
        const titleEl = glass.querySelector('.flex .text-slate-900') || glass.querySelector('.now-title');
        const artistEl = glass.querySelector('.flex .text-slate-500') || glass.querySelector('.now-artist');
        if (titleEl && title) titleEl.textContent = title;
        if (artistEl && artist) artistEl.textContent = artist;
      }
      showPopup('Đang phát: ' + (title || fileUrl), 'success', 1200);
    };

    // bind click for song rows (defined below in bindSongRowClicks)
    // Implementation of binding function:
    window._bindSongRowClicksFn = function bindSongRowClicks() {
      // rows: try table rows in the song list area OR generated items
      const rows = $all('#songList [data-file], tbody tr[data-file], .song-item[data-file], .song-row[data-file]');
      rows.forEach(row => {
        // avoid duplicate
        if (row.dataset.bound === '1') return;
        row.dataset.bound = '1';
        row.style.cursor = 'pointer';
        row.addEventListener('click', function () {
          const f = row.dataset.file;
          // optional title/artist
          const title = row.dataset.title || row.querySelector('.title')?.textContent?.trim() || row.querySelector('td:nth-child(2)')?.textContent?.trim();
          const artist = row.dataset.artist || row.querySelector('.artist')?.textContent?.trim() || row.querySelector('td:nth-child(3)')?.textContent?.trim();
          if (f) {
            // if f is relative path ensure correct prefix
            window.selectSong(f, title, artist);
          } else {
            // try find <audio> inside row
            const audioEl = row.querySelector('audio');
            if (audioEl) {
              window.selectSong(audioEl.currentSrc || audioEl.querySelector('source')?.src, title, artist);
            } else {
              showPopup('Không tìm thấy file bài hát', 'error', 1400);
            }
          }
        });
      });
    };

    // call once to bind existing rows
    setTimeout(() => {
      if (typeof window._bindSongRowClicksFn === 'function') window._bindSongRowClicksFn();
    }, 300);
  })();

  // expose bindSongRowClicks for fetch part
  function bindSongRowClicks() {
    if (typeof window._bindSongRowClicksFn === 'function') window._bindSongRowClicksFn();
  }

  // ensure global function available
  window.bindSongRowClicks = bindSongRowClicks;

  // ---------------------------------------
  // Final: tidy console info
  // ---------------------------------------
  console.log('script.js loaded — features: menu toggle, back-to-top, login validation, popup, audio player, fetch songs');

})();
