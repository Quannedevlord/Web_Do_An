document.addEventListener("DOMContentLoaded", () => {
    console.log("Chill Guy JS Loaded!");

    // ==========================================
    // 1. MOBILE MENU TOGGLE
    // ==========================================
    const sidebar = document.querySelector("aside");
    if (sidebar) {
        // Tạo nút Hamburger menu cho mobile
        const menuBtn = document.createElement("button");
        menuBtn.innerHTML = '<span class="material-symbols-outlined">menu</span>';
        menuBtn.className = "md:hidden fixed top-4 left-4 z-[60] bg-white p-2 rounded-full shadow-lg text-slate-700";
        document.body.appendChild(menuBtn);

        // Mặc định ẩn sidebar trên mobile bằng class của Tailwind
        sidebar.classList.add("transition-transform", "duration-300", "-translate-x-full", "md:translate-x-0", "fixed", "md:relative", "z-50", "h-full");

        menuBtn.addEventListener("click", () => {
            sidebar.classList.toggle("-translate-x-full");
        });
    }

    // ==========================================
    // 2. BACK TO TOP BUTTON
    // ==========================================
    const backToTopBtn = document.createElement("button");
    backToTopBtn.innerHTML = '<span class="material-symbols-outlined">arrow_upward</span>';
    backToTopBtn.className = "fixed bottom-36 right-8 bg-primary text-white p-3 rounded-full shadow-xl shadow-primary/30 z-50 transition-all duration-300 opacity-0 invisible hover:scale-110";
    document.body.appendChild(backToTopBtn);

    window.addEventListener("scroll", () => {
        if (window.scrollY > 300) {
            backToTopBtn.classList.remove("opacity-0", "invisible");
            backToTopBtn.classList.add("opacity-100", "visible");
        } else {
            backToTopBtn.classList.add("opacity-0", "invisible");
            backToTopBtn.classList.remove("opacity-100", "visible");
        }
    });

    backToTopBtn.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });

    // ==========================================
    // 3. POPUP THÔNG BÁO
    // ==========================================
    window.showPopup = function(message, type = "success") {
        const popup = document.createElement("div");
        const bgColor = type === "success" ? "bg-green-500" : "bg-red-500";
        const icon = type === "success" ? "check_circle" : "error";
        
        popup.className = `fixed top-6 right-6 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl z-[100] flex items-center gap-3 transform transition-all duration-300 translate-x-10 opacity-0`;
        popup.innerHTML = `<span class="material-symbols-outlined">${icon}</span> <span class="font-medium">${message}</span>`;
        
        document.body.appendChild(popup);

        // Animation hiện
        setTimeout(() => {
            popup.classList.remove("translate-x-10", "opacity-0");
        }, 10);

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            popup.classList.add("translate-x-10", "opacity-0");
            setTimeout(() => popup.remove(), 300);
        }, 3000);
    };

    // ==========================================
    // 4. FORM VALIDATION (Đăng ký / Đăng nhập)
    // ==========================================
    const forms = document.querySelectorAll("form");
    forms.forEach(form => {
        form.addEventListener("submit", (e) => {
            let isValid = true;
            const inputs = form.querySelectorAll("input[required]");

            inputs.forEach(input => {
                const val = input.value.trim();
                
                // Kiểm tra rỗng
                if (!val) {
                    showPopup(`Vui lòng nhập ${input.placeholder || input.name}`, "error");
                    isValid = false;
                    return;
                }

                // Kiểm tra Email
                if (input.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                    showPopup("Email không hợp lệ!", "error");
                    isValid = false;
                }

                // Kiểm tra Password >= 6 ký tự
                if (input.type === "password" && val.length < 6) {
                    showPopup("Mật khẩu phải từ 6 ký tự trở lên!", "error");
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault(); // Ngăn form submit nếu có lỗi
            } else {
                // Tạm thời hiển thị popup thành công (nếu dùng ajax form, ở đây submit thẳng nên trang sẽ load lại)
                // showPopup("Xử lý thành công!");
            }
        });
    });

    // ==========================================
    // 5. AUDIO PLAYER
    // ==========================================
    const audio = new Audio();
    let isPlaying = false;

    // Lấy các element trên thanh Player (dựa theo giao diện HTML của bạn)
    const playPauseBtn = document.querySelector(".glass-player .text-\\[32px\\]"); 
    const progressBarContainer = document.querySelector(".glass-player .flex-1.h-1\\.5");
    const progressFill = progressBarContainer ? progressBarContainer.querySelector("div") : null;
    const titleEl = document.querySelector(".glass-player p.font-bold");
    const artistEl = document.querySelector(".glass-player p.text-xs");
    const currentTimeEl = document.querySelectorAll(".glass-player .text-\\[10px\\]")[0];
    const durationTimeEl = document.querySelectorAll(".glass-player .text-\\[10px\\]")[1];

    // Hàm chuyển đổi giây sang định dạng mm:ss
    const formatTime = (time) => {
        if (isNaN(time)) return "0:00";
        const minutes = Math.floor(time / 60);
        const seconds = Math.floor(time % 60);
        return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
    };

    // Hàm phát nhạc (Gắn vào global để gọi từ thẻ HTML được load bằng AJAX)
    window.playSong = function(file, title, artist) {
        audio.src = `music/${file}`; // Thư mục chứa nhạc mp3
        audio.play()
            .then(() => {
                isPlaying = true;
                if (playPauseBtn) playPauseBtn.textContent = "pause";
                if (titleEl) titleEl.textContent = title;
                if (artistEl) artistEl.textContent = artist;
                showPopup(`Đang phát: ${title}`);
            })
            .catch(err => {
                console.error(err);
                showPopup("Lỗi phát nhạc, kiểm tra file!", "error");
            });
    };

    // Sự kiện nút Play/Pause
    if (playPauseBtn) {
        playPauseBtn.closest("button").addEventListener("click", () => {
            if (!audio.src) return; // Nếu chưa chọn bài thì bỏ qua
            if (isPlaying) {
                audio.pause();
                playPauseBtn.textContent = "play_arrow";
            } else {
                audio.play();
                playPauseBtn.textContent = "pause";
            }
            isPlaying = !isPlaying;
        });
    }

    // Cập nhật Progress Bar và Thời gian
    audio.addEventListener("timeupdate", () => {
        if (progressFill && audio.duration) {
            const percent = (audio.currentTime / audio.duration) * 100;
            progressFill.style.width = `${percent}%`;
            if (currentTimeEl) currentTimeEl.textContent = formatTime(audio.currentTime);
            if (durationTimeEl) durationTimeEl.textContent = formatTime(audio.duration);
        }
    });

    audio.addEventListener("loadedmetadata", () => {
        if (durationTimeEl) durationTimeEl.textContent = formatTime(audio.duration);
    });

    // Bấm vào thanh Progress để tua nhạc
    if (progressBarContainer) {
        progressBarContainer.addEventListener("click", (e) => {
            if (!audio.src) return;
            const rect = progressBarContainer.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percent = clickX / rect.width;
            audio.currentTime = percent * audio.duration;
        });
    }

    // ==========================================
    // 6. AJAX LOAD DANH SÁCH NHẠC (FETCH API)
    // ==========================================
    async function loadSongs() {
        const tbody = document.querySelector("tbody");
        if (!tbody) return; // Chỉ chạy nếu đang ở trang có bảng danh sách nhạc

        try {
            const response = await fetch("getSongs.php");
            const data = await response.json();

            tbody.innerHTML = ""; // Xóa dữ liệu mẫu đi

            if (data.error) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-slate-500">${data.error}</td></tr>`;
                return;
            }

            data.forEach((song, index) => {
                const tr = document.createElement("tr");
                tr.className = "group hover:bg-white/80 rounded-2xl transition-all cursor-pointer";
                
                // Khi click vào row sẽ phát nhạc
                tr.onclick = () => playSong(song.file, song.title, song.artist);

                tr.innerHTML = `
                    <td class="py-4 px-4 text-center text-slate-400 group-hover:text-primary">${index + 1}</td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-lg bg-slate-200 flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-slate-400">music_note</span>
                            </div>
                            <span class="text-slate-900 font-semibold">${song.title}</span>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-slate-500">${song.artist}</td>
                    <td class="py-4 px-4 text-slate-500">Single</td>
                    <td class="py-4 px-4 text-right text-slate-400">-:-</td>
                    <td class="py-4 px-4 text-center">
                        <button class="material-symbols-outlined text-slate-300 group-hover:text-primary hover:scale-110 transition-transform">play_circle</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            console.error("Lỗi tải danh sách bài hát:", error);
            showPopup("Lỗi kết nối Server!", "error");
        }
    }

    // Gọi hàm load nhạc ngay khi vào trang
    loadSongs();
});
