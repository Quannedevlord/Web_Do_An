<?php
// liked.php – Trang Liked Songs (giống Spotify /collection/tracks)
session_start();
$isLoggedIn = isset($_SESSION['user']);
$username   = $isLoggedIn ? htmlspecialchars($_SESSION['user']) : '';
$isAdmin    = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId     = (int)($_SESSION['user_id'] ?? 0);

if (!$isLoggedIn) {
    $_SESSION['flash'] = 'Vui lòng đăng nhập để xem Liked Songs';
    $_SESSION['flash_type'] = 'warning';
    header("Location: login.php"); exit;
}

$flash=''; $flashType='success';
if(isset($_SESSION['flash'])){$flash=$_SESSION['flash'];$flashType=$_SESSION['flash_type']??'success';unset($_SESSION['flash'],$_SESSION['flash_type']);}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Liked Songs – Chill Wave</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>tailwind.config={theme:{extend:{colors:{"primary":"#42a7f0","sage":"#b8c9b9","cream":"#fdfbf7"},fontFamily:{"display":["Spline Sans"]}}}}</script>
<style>
body{font-family:'Spline Sans',sans-serif;}
.glass-player{background:rgba(255,255,255,0.85);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);}
.sidebar-active{background:rgba(66,167,240,0.1);color:#42a7f0;}.glass-player{background:rgba(255,255,255,0.85);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);}
.song-row.playing td{color:#1db954 !important;}
.eq-icon{color:#1db954;animation:pulse .8s ease-in-out infinite alternate;}
@keyframes pulse{from{opacity:.5}to{opacity:1}}
.active-pill{background:#42a7f0 !important;color:#fff !important;border-color:#42a7f0 !important;}
.liked-track-grid{display:grid;align-items:center;gap:0.35rem;grid-template-columns:28px minmax(0,1fr) minmax(0,4.5rem) 32px;}
@media (min-width:640px){
  .liked-track-grid{gap:0.5rem;grid-template-columns:36px minmax(0,1fr) minmax(0,6rem) 40px;}
}
@media (min-width:768px){
  .liked-track-grid{grid-template-columns:40px minmax(0,1fr) 160px 40px;}
}
</style>
</head>
<body class="bg-cream text-slate-900 font-display" data-flash="<?=htmlspecialchars($flash)?>" data-flash-type="<?=$flashType?>">
<div class="flex h-screen min-h-0 overflow-hidden">

<!-- SIDEBAR -->
<aside id="sidebar" class="w-60 max-w-[min(100vw,15rem)] flex flex-col bg-white/60 border-r border-slate-200/60 backdrop-blur-sm p-4 shrink-0 overflow-y-auto">
    <a href="index.php" class="flex items-center gap-3 mb-6 px-2">
        <div class="size-9 rounded-full bg-primary flex items-center justify-center text-white shadow-lg">
            <span class="material-symbols-outlined text-[18px]">eco</span>
        </div>
        <div><h1 class="text-sm font-bold">Chill Wave</h1><p class="text-[10px] font-bold text-slate-400">Premium Listening</p></div>
    </a>
    <nav class="space-y-1 mb-6">
        <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">home</span>Home</a>
        <a href="about.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">info</span>About</a>
        <?php if($isAdmin):?>
        <a href="add_song.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>Thêm bài hát</a>
        <?php endif;?>
    </nav>

    <!-- Thư viện -->
    <div class="bg-white/50 rounded-xl p-3 flex-1">
        <div class="flex items-center justify-between mb-3 px-1">
            <span class="text-sm font-semibold text-white/70">Thư viện</span>
            <button onclick="Library.createPlaylist()" class="text-white/50 hover:text-white transition-colors" title="Tạo playlist">
                <span class="material-symbols-outlined text-[20px]">add</span>
            </button>
        </div>
        <!-- Liked Songs -->
        <a href="liked.php" class="flex items-center gap-3 px-2 py-2 rounded-xl bg-white/10 mb-1">
            <div class="size-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-400 flex items-center justify-center text-lg">❤️</div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate">Liked Songs</p>
                <p class="text-[11px] text-slate-400">Playlist</p>
            </div>
        </a>
        <!-- Playlists -->
        <div id="sidebarPlaylists" class="space-y-0.5 mt-1"></div>
    </div>

    <!-- User -->
    <div class="mt-4 flex items-center gap-3 px-2 py-2">
        <div class="size-8 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold"><?=strtoupper(substr($username,0,1))?></div>
        <span class="text-sm font-medium text-slate-700 flex-1 truncate"><?=$username?></span>
        <a href="logout.php" class="text-slate-400 hover:text-red-400 transition-colors">
            <span class="material-symbols-outlined text-[18px]">logout</span>
        </a>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-1 min-w-0 overflow-y-auto pb-28 sm:pb-32">
    <!-- Purple gradient header giống Spotify -->
    <div class="bg-gradient-to-br from-primary/20 via-cream to-sage/20 px-4 sm:px-8 pt-6 sm:pt-12 pb-6 sm:pb-8 rounded-2xl sm:rounded-3xl mx-4 sm:mx-6 mt-4 sm:mt-6 border border-white/50 shadow-sm">
        <div class="flex items-start gap-2 sm:gap-3">
            <button id="menuBtn" type="button" class="md:hidden shrink-0 mt-1 size-10 rounded-full bg-white/90 border border-slate-200 flex items-center justify-center">
                <span class="material-symbols-outlined">menu</span></button>
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6 flex-1 min-w-0">
            <div class="size-36 sm:size-48 rounded-xl bg-gradient-to-br from-purple-600 to-blue-400 flex items-center justify-center text-5xl sm:text-7xl shadow-2xl flex-shrink-0 mx-auto sm:mx-0">❤️</div>
            <div class="text-center sm:text-left min-w-0 flex-1">
                <p class="text-xs font-bold uppercase tracking-widest text-primary mb-2">Playlist</p>
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 mb-2 sm:mb-4 break-words">Liked Songs</h1>
                <p class="text-sm text-slate-500 truncate"><?=$username?> · <span id="likedCount">0 bài hát</span></p>
            </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="px-4 sm:px-8 py-3 sm:py-4 flex items-center gap-4">
        <button id="playAllBtn" class="size-14 rounded-full bg-primary text-white flex items-center justify-center shadow-xl shadow-primary/30 hover:bg-blue-500 hover:scale-105 transition-all">
            <span class="material-symbols-outlined text-[28px]" style="font-variation-settings:'FILL' 1">play_arrow</span>
        </button>
    </div>

    <!-- Song table -->
    <div class="px-4 sm:px-8">
        <div class="overflow-x-auto -mx-1 px-1 sm:mx-0 sm:px-0" style="-webkit-overflow-scrolling:touch;">
        <div class="w-full min-w-0 sm:min-w-0">
        <div class="liked-track-grid border-b border-slate-200/80 pb-2 mb-2 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">
            <span class="text-center">#</span>
            <span>Tiêu đề</span>
            <span class="truncate">Ngày thêm</span>
            <span></span>
        </div>
        <div id="likedSongList">
            <p class="text-center text-slate-400 py-16">⏳ Đang tải...</p>
        </div>
        </div>
        </div>
    </div>
</main>

<!-- PLAYER BAR -->
<div class="fixed bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-1rem)] sm:w-[94%] max-w-[1400px] min-h-[4.75rem] md:h-24
            glass-player rounded-2xl sm:rounded-[2rem] border border-white/40 shadow-2xl flex items-center px-3 sm:px-6 py-2 md:py-0 z-50 gap-1 sm:gap-2 md:gap-0">
    <div class="flex items-center gap-1.5 sm:gap-2 flex-1 min-w-0 md:w-[28%] md:flex-initial">
        <div id="playerCover" class="size-11 sm:size-14 rounded-lg sm:rounded-xl bg-cover bg-center shadow-md shrink-0" style="background-color:#333;"></div>
        <div class="min-w-0 flex-1">
            <p id="playerTitle" class="text-slate-900 font-bold text-xs sm:text-sm leading-tight truncate">Chưa phát</p>
            <p id="playerArtist" class="text-slate-500 text-[10px] sm:text-xs truncate">–</p>
        </div>
        <button id="likeBtn" class="text-slate-300 hover:text-red-400 transition-colors shrink-0" onclick="Library.toggleLike()">
            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">favorite</span>
        </button>
        <button id="addToPlBtn" class="text-slate-300 hover:text-primary transition-colors shrink-0" title="Thêm vào playlist"
            onclick="const s=Player.getCurrentSong();if(s)Library.showAddToPlaylist(s.id);else showPopup('Chưa phát bài nào','warning');">
            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">playlist_add</span>
        </button>
    </div>
    <div class="flex flex-col items-center flex-[2] min-w-0 px-0.5 sm:px-4 max-w-full">
        <div class="flex items-center gap-1 sm:gap-4 md:gap-6 mb-1 md:mb-2">
            <button id="shuffleBtn" class="hidden sm:block text-slate-400 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px] sm:text-[20px]">shuffle</span></button>
            <button id="prevBtn" class="text-slate-700 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[22px] sm:text-[26px]">skip_previous</span></button>
            <button id="playBtn" class="size-10 sm:size-11 md:size-12 rounded-full bg-primary text-white flex items-center justify-center hover:scale-105 transition-transform shrink-0">
                <span class="material-symbols-outlined text-[22px] sm:text-[24px]" style="font-variation-settings:'FILL' 1">play_arrow</span>
            </button>
            <button id="nextBtn" class="text-slate-700 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[22px] sm:text-[26px]">skip_next</span></button>
            <button id="repeatBtn" class="hidden sm:block text-slate-400 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px] sm:text-[20px]">repeat</span></button>
        </div>
        <div class="w-full flex items-center gap-2 sm:gap-3">
            <span id="currentTime" class="text-[9px] sm:text-[10px] font-bold text-slate-400 w-7 sm:w-8 text-right shrink-0">0:00</span>
            <div id="progressBar" class="flex-1 min-w-0 h-1 sm:h-1.5 bg-slate-200/60 rounded-full relative cursor-pointer group">
                <div id="progressTrack" class="absolute top-0 left-0 h-full w-0 bg-primary rounded-full group-hover:bg-[#1db954] transition-colors"></div>
            </div>
            <span id="totalTime" class="text-[9px] sm:text-[10px] font-bold text-slate-400 w-7 sm:w-8 shrink-0">0:00</span>
        </div>
    </div>
    <div class="flex items-center justify-end gap-1 sm:gap-3 shrink-0 md:w-1/4">
        <button id="queueBtn" type="button" class="text-slate-400 hover:text-primary transition-colors p-0.5" title="Hàng chờ">
            <span class="material-symbols-outlined text-[20px]">queue_music</span></button>
        <div class="hidden md:flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400 text-[18px]">volume_up</span>
            <div id="volumeTrack" class="w-16 lg:w-20 h-1 sm:h-1.5 bg-slate-200/60 rounded-full relative cursor-pointer">
                <div id="volumeBar" class="absolute top-0 left-0 h-full w-[70%] bg-primary rounded-full"></div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
window.isLoggedIn = <?=$isAdmin?'true':'false'?>;
window.isUserLogin = <?=$isLoggedIn?'true':'false'?>;
window.currentUserId = <?=$userId?>;
</script>
<script src="js/popup.js?v=2"></script>
<script src="js/ui.js?v=2"></script>
<script src="js/player.js?v=2"></script>
<script src="js/library.js?v=2"></script>
<script src="js/queue.js?v=2"></script>
<script>
document.addEventListener('DOMContentLoaded', ()=>{
    Player.bindControls();
    window.__player = Player;

    // Flash
    const msg=document.body.dataset.flash, type=document.body.dataset.flashType||'success';
    if(msg) showPopup(msg, type);

    // Load liked songs
    fetch('getLikedSongs.php').then(r=>r.json()).then(data=>{
        const songs = data.songs || [];
        window.__playerSongs = songs;
        const list = document.getElementById('likedSongList');
        const cnt  = document.getElementById('likedCount');
        if(cnt) cnt.textContent = songs.length + ' bài hát';

        if(!songs.length){
            list.innerHTML='<p class="text-center text-slate-400 py-16">Bạn chưa thích bài nào. Về trang chủ và bấm ♥!</p>';
            return;
        }

        list.innerHTML = songs.map((s,i)=>`
            <div class="song-row liked-track-grid group items-center py-2.5 sm:py-3 px-1 sm:px-2 rounded-xl cursor-pointer transition-colors hover:bg-white/80"
                 data-index="${i}"
                 onclick="Player.selectSong(${i})">
                <span class="song-num text-center text-xs sm:text-sm text-slate-400">${i+1}</span>
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <div class="size-9 sm:size-10 rounded-md bg-cover bg-center flex-shrink-0"
                         style="background-image:url('images/${s.image||''}');background-color:#333;"></div>
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-semibold text-slate-900 truncate">${s.title||''}</p>
                        <p class="text-[11px] sm:text-xs text-slate-500 truncate">${s.artist||''}</p>
                    </div>
                </div>
                <span class="text-[10px] sm:text-xs text-slate-400 truncate tabular-nums">${s.created_at?new Date(s.created_at).toLocaleDateString('vi-VN'):''}</span>
                <button type="button" onclick="event.stopPropagation();Library.toggleLikeSong(${s.id},this)"
                        class="text-red-400 hover:text-red-300 transition-colors opacity-100 sm:opacity-0 sm:group-hover:opacity-100 justify-self-end"
                        style="font-variation-settings:'FILL' 1;">
                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;color:#ef4444;">favorite</span>
                </button>
            </div>`).join('');

        Player.setSongs(songs);

        // Play all
        document.getElementById('playAllBtn')?.addEventListener('click',()=>Player.playList(songs,0));
        // Shuffle all
        document.getElementById('shuffleAllBtn')?.addEventListener('click',()=>{
            const idx=Math.floor(Math.random()*songs.length);
            Player.playList(songs,idx);
        });
    });

    // Sidebar playlists
    Library.loadSidebarPlaylists();
});
</script>
</body></html>