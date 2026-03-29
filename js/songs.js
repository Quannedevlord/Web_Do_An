/**
 * songs.js - Load, render, delete, search, genre filter
 */
let currentFilteredList = [];
let allSongsData = [];
let activeGenre = "all";

function escHtml(s){const d=document.createElement("div");d.textContent=s||"";return d.innerHTML;}

function syncPlayerSongs(list){
    currentFilteredList = Array.isArray(list) ? [...list] : [];
    window.currentFilteredList = currentFilteredList;
    if(typeof Player !== "undefined" && typeof Player.setSongs === "function"){
        Player.setSongs(currentFilteredList);
    }
}

function getSongsByGenre(genre){
    if(genre === "all") return [...allSongsData];
    return allSongsData.filter(s => (s.genre || "other").toLowerCase() === genre.toLowerCase());
}

function applySearchFilter(){
    const input = document.getElementById("searchInput");
    const kw = (input?.value || "").trim().toLowerCase();
    const rows = document.querySelectorAll(".song-row");

    rows.forEach(r => {
        r.style.display = !kw || r.textContent.toLowerCase().includes(kw) ? "" : "none";
    });

    const visible = [...rows].filter(r => r.style.display !== "none");
    const noResult = document.getElementById("noSearchResult");
    const body = document.getElementById("songList");

    if(!visible.length && kw){
        if(!noResult && body){
            const tr = document.createElement("tr");
            tr.id = "noSearchResult";
            tr.innerHTML = `<td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">Không tìm thấy "<strong>${escHtml(kw)}</strong>"</td>`;
            body.appendChild(tr);
        }
    } else if(noResult){
        noResult.remove();
    }
}

function renderSongs(songs,{keepMaster=false}={}){
    const c = document.getElementById("songList");
    if(!c) return;

    if(!keepMaster){
        allSongsData = Array.isArray(songs) ? [...songs] : [];
    }

    syncPlayerSongs(songs || []);

    if(!songs || !songs.length){
        c.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:32px;color:#94a3b8;">Chưa có bài hát nào. ${window.isLoggedIn ? '<a href="add_song.php" style="color:#42a7f0;">Thêm ngay</a>' : ""}</td></tr>`;
        return;
    }

    c.innerHTML = songs.map((s,i) => `
        <tr class="song-row group hover:bg-white/80 transition-all cursor-pointer"
            data-index="${i}" data-genre="${s.genre || "other"}" data-song-id="${s.id}"
            onclick="window.__player.playList(window.currentFilteredList||[], ${i})">
            <td class="py-4 px-3 text-center text-slate-400 song-num">${i+1}</td>
            <td class="py-4 px-3">
                <div class="flex items-center gap-3">
                    ${s.image
                        /* ✅ Dùng <img loading="lazy"> thay background-image
                           → chỉ load ảnh khi hiện ra màn hình, tiết kiệm băng thông */
                        ? `<img src="images/${escHtml(s.image)}"
                               loading="lazy"
                               decoding="async"
                               width="40" height="40"
                               onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'size-10 rounded-lg bg-slate-200 shrink-0'}))"
                               class="size-10 rounded-lg object-cover shadow-sm shrink-0"
                               alt="${escHtml(s.title)}"/>`
                        : `<div class="size-10 rounded-lg bg-slate-200 shrink-0"></div>`
                    }
                    <span class="text-slate-900 font-semibold">${escHtml(s.title)}</span>
                </div>
            </td>
            <td class="py-4 px-3 text-slate-500">${escHtml(s.artist)}</td>
            <td class="py-4 px-3 text-right" id="act-${s.id}"></td>
        </tr>`).join("");

    songs.forEach(s => {
        const cell = document.getElementById("act-" + s.id);
        if(!cell) return;
        let html = '<div class="flex items-center justify-end gap-1.5">';

        if(window.isUserLogin || window.isLoggedIn){
            html += `<button onclick="event.stopPropagation();Library.toggleLikeSong(${s.id},this)"
                   class="text-slate-300 hover:text-red-400 transition-colors p-1 rounded-lg hover:bg-red-50">
                   <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' ${s.liked ? 1 : 0};color:${s.liked ? "#ef4444" : ""};">favorite</span></button>`;
        }

        if(window.isUserLogin || window.isLoggedIn){
            html += `<button onclick="event.stopPropagation();Library.showAddToPlaylist(${s.id})"
                   class="text-slate-300 hover:text-primary transition-colors p-1 rounded-lg hover:bg-blue-50" title="Thêm vào playlist">
                   <span class="material-symbols-outlined text-[16px]">playlist_add</span></button>`;
        }

        if(window.isLoggedIn){
            html += `<a href="edit_song.php?id=${s.id}" onclick="event.stopPropagation()"
                   class="text-blue-400 hover:text-blue-600 text-xs px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">✏</a>
                   <button onclick="event.stopPropagation();deleteSong(${s.id},this)"
                   class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded-lg bg-red-50 hover:bg-red-100 transition-colors">🗑</button>`;
        }

        html += "</div>";
        cell.innerHTML = html;
    });

    // ✅ Preload nhạc âm thầm khi hover vào bài hát
    // → khi bấm phát sẽ nhanh hơn vì đã tải trước ~500KB đầu
    document.querySelectorAll('.song-row').forEach((row, i) => {
        row.addEventListener('mouseenter', () => {
            const s = songs[i];
            if(!s || !s.file || s._preloaded) return;
            s._preloaded = true;
            const pre = new Audio();
            pre.preload = 'auto';
            pre.src = `stream.php?file=${encodeURIComponent(s.file)}`;
            pre.load();
            setTimeout(() => { pre.src = ''; }, 4000); // Giải phóng sau 4s
        });
    });

    applySearchFilter();
}

function loadSongs(){
    const c = document.getElementById("songList");
    if(!c) return;

    c.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:32px;color:#94a3b8;">⏳ Đang tải...</td></tr>`;

    fetch("getSongs.php")
        .then(r => {
            if(!r.ok) throw new Error("Lỗi server " + r.status);
            return r.json();
        })
        .then(data => {
            allSongsData = data.songs || [];
            renderSongs(getSongsByGenre(activeGenre),{keepMaster:true});
        })
        .catch(err => {
            c.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:32px;color:#ef4444;">⚠ Không thể tải dữ liệu. <button onclick="loadSongs()" style="color:#42a7f0;background:none;border:none;cursor:pointer;">Thử lại</button></td></tr>`;
            console.error(err);
        });
}

window.deleteSong = function(id,btn){
    if(!confirm("Xóa bài hát này không?")) return;

    const row = btn.closest("tr");
    row.style.opacity = "0.5";
    row.style.pointerEvents = "none";

    fetch(`delete_song.php?id=${id}`).then(r => r.text()).then(msg => {
        if(msg.includes("thành công")){
            row.style.transition = "all 0.4s";
            row.style.opacity = "0";
            row.style.transform = "translateX(20px)";
            setTimeout(() => {
                row.remove();
                showPopup("Đã xóa bài hát","success");
                loadSongs();
            },400);
        }else{
            row.style.opacity = "1";
            row.style.pointerEvents = "";
            showPopup("Lỗi khi xóa","error");
        }
    }).catch(() => {
        row.style.opacity = "1";
        row.style.pointerEvents = "";
        showPopup("Lỗi kết nối","error");
    });
};

(function(){
    const input = document.getElementById("searchInput");
    if(!input) return;

    let t;
    input.addEventListener("input",() => {
        clearTimeout(t);
        t = setTimeout(applySearchFilter,300);
    });
})();

(function(){
    const pills = document.querySelectorAll(".genre-pill[data-genre]");
    if(!pills.length) return;

    pills.forEach(pill => {
        pill.addEventListener("click",() => {
            pills.forEach(p => p.classList.remove("active-pill"));
            pill.classList.add("active-pill");
            activeGenre = pill.dataset.genre || "all";
            renderSongs(getSongsByGenre(activeGenre),{keepMaster:true});
        });
    });
})();
