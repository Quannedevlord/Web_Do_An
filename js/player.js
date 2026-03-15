/**
 * player.js – Audio Player
 * Xử lý: play/pause, next/prev, thanh tiến trình, âm lượng
 */

const Player = (function () {

    let songs     = [];    // danh sách bài hát
    let current   = -1;    // chỉ số bài đang phát (-1 = chưa chọn)
    let isPlaying = false; // trạng thái đang phát

    const audio = new Audio(); // đối tượng phát nhạc
    const dom   = {};          // tham chiếu DOM

    // lấy các phần tử DOM trong player bar
    function resolveDOM() {
        dom.playBtn       = document.getElementById('playBtn');
        dom.prevBtn       = document.getElementById('prevBtn');
        dom.nextBtn       = document.getElementById('nextBtn');
        dom.progressBar   = document.getElementById('progressBar');
        dom.progressTrack = document.getElementById('progressTrack');
        dom.currentTime   = document.getElementById('currentTime');
        dom.totalTime     = document.getElementById('totalTime');
        dom.volumeTrack   = document.getElementById('volumeTrack');
        dom.volumeBar     = document.getElementById('volumeBar');
        dom.songTitle     = document.getElementById('playerTitle');
        dom.songArtist    = document.getElementById('playerArtist');
        dom.songCover     = document.getElementById('playerCover');
    }

    // đổi giây → phút:giây (185 → "3:05")
    function formatTime(seconds) {
        if (isNaN(seconds) || seconds === Infinity) return '0:00';
        const m   = Math.floor(seconds / 60);
        const sec = Math.floor(seconds % 60).toString().padStart(2, '0');
        return `${m}:${sec}`;
    }

    // cập nhật giao diện player bar
    function updateUI() {
        const song = songs[current];
        if (!song) return;

        if (dom.songTitle)  dom.songTitle.textContent  = song.title  || 'Unknown';
        if (dom.songArtist) dom.songArtist.textContent = song.artist || 'Unknown';

        if (dom.songCover) {
            dom.songCover.style.backgroundImage = song.image
                ? `url('images/${song.image}')`
                : `url('https://via.placeholder.com/56x56/42a7f0/ffffff?text=♪')`;
        }

        // đổi icon play ↔ pause
        if (dom.playBtn) {
            const icon = dom.playBtn.querySelector('.material-symbols-outlined');
            if (icon) icon.textContent = isPlaying ? 'pause' : 'play_arrow';
        }

        // highlight bài đang phát trong bảng
        document.querySelectorAll('.song-row').forEach((row, i) => {
            row.classList.toggle('playing', i === current);
            const numCell = row.querySelector('.song-num');
            if (numCell) {
                numCell.innerHTML = (i === current && isPlaying)
                    ? `<span class="eq-icon">▶</span>`
                    : (i + 1);
            }
        });
    }

    // phát bài tại vị trí current
    function playCurrent() {
        const song = songs[current];
        if (!song) return;

        audio.src = `music/${song.file}`;
        audio.play()
            .then(() => {
                isPlaying = true;
                updateUI();
                showPopup(`▶ ${song.title} – ${song.artist}`, 'info');
            })
            .catch(() => {
                showPopup('Không thể phát bài này, kiểm tra lại file nhạc', 'error');
            });
    }

    // toggle play / pause
    function toggle() {
        if (songs.length === 0) {
            showPopup('Chưa có bài hát nào', 'warning');
            return;
        }
        if (current === -1) { current = 0; playCurrent(); return; }

        if (isPlaying) { audio.pause(); isPlaying = false; }
        else           { audio.play();  isPlaying = true;  }
        updateUI();
    }

    // bài trước
    function prev() {
        if (!songs.length) return;
        current = (current - 1 + songs.length) % songs.length;
        playCurrent();
    }

    // bài tiếp theo
    function next() {
        if (!songs.length) return;
        current = (current + 1) % songs.length;
        playCurrent();
    }

    // chọn bài theo index
    function selectSong(index) {
        current = index;
        playCurrent();
    }

    // điều chỉnh âm lượng
    function setVolume(v) {
        audio.volume = Math.max(0, Math.min(1, v));
        if (dom.volumeBar) dom.volumeBar.style.width = (audio.volume * 100) + '%';
    }

    // --- Sự kiện Audio ---

    // cập nhật thanh tiến trình
    audio.addEventListener('timeupdate', () => {
        if (!audio.duration) return;
        const pct = (audio.currentTime / audio.duration) * 100;
        if (dom.progressTrack) dom.progressTrack.style.width = pct + '%';
        if (dom.currentTime)   dom.currentTime.textContent   = formatTime(audio.currentTime);
        if (dom.totalTime)     dom.totalTime.textContent     = formatTime(audio.duration);
    });

    // tự chuyển bài khi hết
    audio.addEventListener('ended', next);

    // lỗi phát nhạc
    audio.addEventListener('error', () => {
        showPopup('Lỗi phát nhạc – kiểm tra lại file!', 'error');
        isPlaying = false;
        updateUI();
    });

    // gắn sự kiện vào các nút điều khiển
    function bindControls() {
        resolveDOM();

        if (dom.playBtn) dom.playBtn.addEventListener('click', toggle);
        if (dom.prevBtn) dom.prevBtn.addEventListener('click', prev);
        if (dom.nextBtn) dom.nextBtn.addEventListener('click', next);

        // bấm thanh tiến trình để tua
        if (dom.progressBar) {
            dom.progressBar.addEventListener('click', (e) => {
                if (!audio.duration) return;
                const rect = dom.progressBar.getBoundingClientRect();
                audio.currentTime = ((e.clientX - rect.left) / rect.width) * audio.duration;
            });
        }

        // bấm thanh âm lượng
        if (dom.volumeTrack) {
            dom.volumeTrack.addEventListener('click', (e) => {
                const rect = dom.volumeTrack.getBoundingClientRect();
                setVolume((e.clientX - rect.left) / rect.width);
            });
        }
    }

    return {
        selectSong,
        toggle,
        prev,
        next,
        bindControls,
        setVolume,
        setSongs(arr) { songs.splice(0, songs.length, ...arr); },
    };
})();
