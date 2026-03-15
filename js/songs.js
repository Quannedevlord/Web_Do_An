/**
 * songs.js – Quản lý danh sách bài hát
 * Bao gồm: loadSongs, renderSongs, deleteSong, tìm kiếm, lọc thể loại
 */

// escape HTML để tránh XSS
function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}


/* ----------------------------------------------------------------
   RENDER DANH SÁCH BÀI HÁT
---------------------------------------------------------------- */
function renderSongs(songs) {
    const container = document.getElementById('songList');
    if (!container) return;

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

    // tạo HTML bảng bài hát
    container.innerHTML = songs.map((song, i) => `
        <tr class="song-row group hover:bg-white/80 transition-all cursor-pointer"
            data-index="${i}"
            data-genre="${song.genre || 'other'}"
            onclick="window.__player.selectSong(${i})">

            <td class="py-4 px-4 text-center text-slate-400 song-num">${i + 1}</td>

            <td class="py-4 px-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-cover bg-center shadow-sm song-cover"
                         style="background-image:url('images/${song.image || ''}');background-color:#e2e8f0;">
                    </div>
                    <span class="text-slate-900 font-semibold">${escHtml(song.title)}</span>
                </div>
            </td>

            <td class="py-4 px-4 text-slate-500">${escHtml(song.artist)}</td>

            <!-- ô thao tác – JS điền nút Sửa/Xóa nếu là admin -->
            <td class="py-4 px-4 text-right" id="action-${song.id}"></td>
        </tr>
    `).join('');

    // chỉ admin mới thấy nút Sửa/Xóa
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

    // truyền vào Player để phát
    Player.setSongs(songs);
}


/* ----------------------------------------------------------------
   TẢI DANH SÁCH BÀI HÁT BẰNG AJAX
---------------------------------------------------------------- */
function loadSongs() {
    const container = document.getElementById('songList');
    if (!container) return;

    container.innerHTML = `
        <tr>
            <td colspan="4" style="text-align:center;padding:32px;color:#94a3b8;">
                <span style="animation:spin 1s linear infinite;display:inline-block;">⏳</span>
                Đang tải bài hát...
            </td>
        </tr>`;

    fetch('getSongs.php')
        .then(res => {
            if (!res.ok) throw new Error('Lỗi server: ' + res.status);
            return res.json();
        })
        .then(data => {
            if (!data.songs || data.songs.length === 0) {
                renderSongs([]);
            } else {
                renderSongs(data.songs);
            }
        })
        .catch(err => {
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


/* ----------------------------------------------------------------
   XÓA BÀI HÁT BẰNG AJAX
---------------------------------------------------------------- */
window.deleteSong = function (id, btn) {
    if (!confirm('Bạn có chắc muốn xóa bài hát này không?')) return;

    const row = btn.closest('tr');
    row.style.opacity       = '0.5';
    row.style.pointerEvents = 'none';

    fetch(`delete_song.php?id=${id}`)
        .then(res => res.text())
        .then(msg => {
            if (msg.includes('thành công')) {
                row.style.transition = 'all 0.4s ease';
                row.style.opacity    = '0';
                row.style.transform  = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();
                    showPopup('Đã xóa bài hát thành công', 'success');
                    loadSongs();
                }, 400);
            } else {
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


/* ----------------------------------------------------------------
   TÌM KIẾM BÀI HÁT REAL-TIME
---------------------------------------------------------------- */
(function initSearch() {
    const input = document.getElementById('searchInput');
    if (!input) return;

    let debounceTimer;

    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);

        // chờ 300ms sau khi ngừng gõ mới lọc
        debounceTimer = setTimeout(() => {
            const keyword = input.value.trim().toLowerCase();
            const rows    = document.querySelectorAll('.song-row');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });

            // hiện thông báo không tìm thấy
            const visible  = Array.from(rows).filter(r => r.style.display !== 'none');
            const noResult = document.getElementById('noSearchResult');

            if (visible.length === 0 && keyword !== '') {
                if (!noResult) {
                    const tbody = document.querySelector('tbody');
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
                if (noResult) noResult.remove();
            }
        }, 300);
    });
})();


/* ----------------------------------------------------------------
   LỌC THEO THỂ LOẠI (GENRE PILLS)
---------------------------------------------------------------- */
(function initGenrePills() {
    const pills = document.querySelectorAll('[data-genre]');
    if (!pills.length) return;

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            // bỏ active cũ, set active mới
            pills.forEach(p => p.classList.remove('active-pill'));
            pill.classList.add('active-pill');

            const genre = pill.dataset.genre;
            const rows  = document.querySelectorAll('.song-row');

            rows.forEach(row => {
                if (genre === 'all') {
                    row.style.display = '';
                } else {
                    // lọc theo cột genre từ database
                    const rowGenre = (row.dataset.genre || '').toLowerCase();
                    row.style.display = rowGenre === genre.toLowerCase() ? '' : 'none';
                }
            });
        });
    });
})();
