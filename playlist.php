<?php
// playlist.php – Trang danh sách tất cả playlist của user
session_start();
$isLoggedIn = isset($_SESSION['user']);
$username   = $isLoggedIn ? htmlspecialchars($_SESSION['user']) : '';
$isAdmin    = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId     = (int)($_SESSION['user_id'] ?? 0);
if(!$isLoggedIn){ header("Location: login.php"); exit; }

$flash=''; $flashType='success';
if(isset($_SESSION['flash'])){$flash=$_SESSION['flash'];$flashType=$_SESSION['flash_type']??'success';unset($_SESSION['flash'],$_SESSION['flash_type']);}
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Thư viện – Chill Wave</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#42a7f0","sage":"#b8c9b9","cream":"#fdfbf7"},fontFamily:{"display":["Spline Sans"]}}}}</script>
<style>body{font-family:'Spline Sans',sans-serif;}.glass-player{background:rgba(255,255,255,0.85);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);}.sidebar-active{background:rgba(66,167,240,0.1);color:#42a7f0;}</style>
</head>
<body class="bg-cream text-slate-900 font-display" data-flash="<?=htmlspecialchars($flash)?>" data-flash-type="<?=$flashType?>">
<div class="flex h-screen min-h-0 overflow-hidden">

<!-- SIDEBAR -->
<aside id="sidebar" class="w-64 max-w-[min(100vw,16rem)] flex flex-col border-r border-slate-200/60 bg-white/60 backdrop-blur-sm p-4 sm:p-5 shrink-0 overflow-y-auto">
    <div class="flex items-center gap-3 mb-8">
        <div class="size-10 rounded-full bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
            <span class="material-symbols-outlined">eco</span>
        </div>
        <div><h1 class="text-base font-bold text-slate-900">Chill Wave</h1><p class="text-[10px] text-slate-500">Premium Listening</p></div>
    </div>
    <nav class="flex flex-col gap-1 flex-1">
        <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">home</span>Home</a>
        <a href="about.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">info</span>About</a>
        <?php if($isAdmin):?>
        <a href="add_song.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>Thêm bài hát</a>
        <?php endif;?>
        <a href="playlist.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl sidebar-active text-sm font-semibold">
            <span class="material-symbols-outlined text-[20px]">library_music</span>Thư viện</a>
        <div class="mt-4 mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tài khoản</div>
        <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-primary/5">
            <span class="material-symbols-outlined text-primary text-[20px]">person</span>
            <div><span class="text-sm font-medium text-slate-700"><?=$username?></span>
            <?php if($isAdmin):?><span class="block text-[10px] font-bold text-blue-500">👑 Admin</span>
            <?php else:?><span class="block text-[10px] text-slate-400">Người dùng</span><?php endif;?></div>
        </div>
        <a href="logout.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-red-400 hover:bg-red-50 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">logout</span>Đăng xuất</a>
    </nav>
</aside>

<!-- MAIN -->
<main class="flex-1 min-w-0 overflow-y-auto pb-28 sm:pb-32">
    <div class="px-4 sm:px-8 pt-4 sm:pt-8">
        <div class="flex items-start gap-2 sm:gap-4 mb-6 sm:mb-8">
            <button id="menuBtn" type="button" class="md:hidden shrink-0 mt-0.5 size-10 rounded-full bg-white border border-slate-200 flex items-center justify-center">
                <span class="material-symbols-outlined">menu</span></button>
            <div class="min-w-0 flex-1 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Thư viện của bạn</h1>
                <p class="text-slate-500 text-sm mt-1">Tất cả playlist và bài hát yêu thích</p>
            </div>
            <button onclick="createPlaylist()" class="flex items-center justify-center gap-2 bg-primary text-white font-semibold px-4 sm:px-5 py-2.5 rounded-xl shadow-md shadow-primary/20 hover:bg-blue-600 transition-colors text-sm w-full sm:w-auto shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span>Tạo playlist
            </button>
            </div>
        </div>

        <!-- Liked Songs card cố định -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
            <a href="liked.php" class="group bg-gradient-to-br from-purple-500 to-blue-400 rounded-2xl p-5 cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02]">
                <div class="text-4xl mb-4">❤️</div>
                <h3 class="font-bold text-white text-base">Liked Songs</h3>
                <p class="text-white/70 text-xs mt-1">Playlist</p>
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity flex justify-end">
                    <div class="size-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-[20px]" style="font-variation-settings:'FILL' 1">play_arrow</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- User playlists -->
        <div id="playlistGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <p class="text-slate-400 col-span-4 text-center py-8">⏳ Đang tải...</p>
        </div>
    </div>
</main>

<!-- PLAYER BAR -->
<div class="fixed bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-1rem)] sm:w-[94%] max-w-[1400px] min-h-[4.75rem] md:h-24
            glass-player rounded-2xl sm:rounded-[2rem] border border-white/40 shadow-2xl flex items-center px-3 sm:px-6 py-2 md:py-0 z-50 gap-2 md:gap-0">
    <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0 md:w-1/4 md:flex-initial">
        <div id="playerCover" class="size-11 sm:size-14 rounded-lg sm:rounded-xl bg-cover bg-center shadow-md shrink-0" style="background-color:#e2e8f0;"></div>
        <div class="min-w-0 flex-1">
            <p id="playerTitle" class="text-slate-900 font-bold text-xs sm:text-sm truncate">Chưa phát</p>
            <p id="playerArtist" class="text-slate-500 text-[10px] sm:text-xs truncate">–</p>
        </div>
        <button id="likeBtn" class="md:ml-1 text-slate-300 hover:text-red-400 transition-colors shrink-0" onclick="Library.toggleLike()">
            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">favorite</span>
        </button>
    </div>
    <div class="flex flex-col items-center flex-[2] min-w-0 px-1 sm:px-4 max-w-full">
        <div class="flex items-center gap-1.5 sm:gap-4 md:gap-6 mb-1 md:mb-2">
            <button id="shuffleBtn" class="hidden sm:block text-slate-400 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px] sm:text-[20px]">shuffle</span></button>
            <button id="prevBtn" class="text-slate-700 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[22px] sm:text-[26px]">skip_previous</span></button>
            <button id="playBtn" class="size-10 sm:size-12 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30 hover:scale-105 transition-transform shrink-0">
                <span class="material-symbols-outlined text-[24px] sm:text-[28px]" style="font-variation-settings:'FILL' 1">play_arrow</span>
            </button>
            <button id="nextBtn" class="text-slate-700 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[22px] sm:text-[26px]">skip_next</span></button>
            <button id="repeatBtn" class="hidden sm:block text-slate-400 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px] sm:text-[20px]">repeat</span></button>
        </div>
        <div class="w-full flex items-center gap-2 sm:gap-3">
            <span id="currentTime" class="text-[9px] sm:text-[10px] font-bold text-slate-400 w-7 sm:w-8 text-right shrink-0">0:00</span>
            <div id="progressBar" class="flex-1 min-w-0 h-1 sm:h-1.5 bg-slate-200/60 rounded-full relative cursor-pointer">
                <div id="progressTrack" class="absolute top-0 left-0 h-full w-0 bg-primary rounded-full"></div>
            </div>
            <span id="totalTime" class="text-[9px] sm:text-[10px] font-bold text-slate-400 w-7 sm:w-8 shrink-0">0:00</span>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 md:gap-3 lg:gap-5 shrink-0 md:w-1/4">
        <button id="queueBtn" type="button" class="text-slate-400 hover:text-primary transition-colors p-0.5 sm:p-0" title="Hàng chờ">
            <span class="material-symbols-outlined text-[20px]">queue_music</span></button>
        <div class="hidden md:flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400 text-[20px]">volume_up</span>
            <div id="volumeTrack" class="w-16 lg:w-20 h-1.5 bg-slate-200/60 rounded-full relative cursor-pointer">
                <div id="volumeBar" class="absolute top-0 left-0 h-full w-[70%] bg-primary rounded-full"></div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
window.isLoggedIn=<?=$isAdmin?'true':'false'?>;
window.isUserLogin=<?=$isLoggedIn?'true':'false'?>;
window.currentUserId=<?=$userId?>;
</script>
<script src="js/popup.js"></script>
<script src="js/ui.js"></script>
<script src="js/player.js"></script>
<script src="js/library.js"></script>
<script src="js/queue.js"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
    Player.bindControls(); window.__player=Player;
    const msg=document.body.dataset.flash,type=document.body.dataset.flashType||'success';
    if(msg)showPopup(msg,type);
    loadPlaylists();
});

function loadPlaylists(){
    fetch('getPlaylists.php').then(r=>r.json()).then(data=>{
        const grid=document.getElementById('playlistGrid');
        const pls=data.playlists||[];
        if(!pls.length){
            grid.innerHTML='<p class="text-slate-400 col-span-4 text-center py-8">Chưa có playlist nào. Bấm "+ Tạo playlist" để bắt đầu!</p>';
            return;
        }
        const colors=['from-blue-500 to-cyan-400','from-green-500 to-teal-400','from-orange-500 to-yellow-400',
                      'from-pink-500 to-rose-400','from-violet-500 to-purple-400','from-red-500 to-orange-400'];
        grid.innerHTML=pls.map((pl,i)=>`
            <div class="group bg-white/60 rounded-2xl border border-white/60 shadow-sm p-5 cursor-pointer hover:shadow-md transition-all hover:scale-[1.02] relative"
                 onclick="window.location.href='playlist_view.php?id=${pl.id}'">
                <div class="size-14 rounded-xl bg-gradient-to-br ${colors[i%colors.length]} flex items-center justify-center text-2xl mb-4 shadow-md">🎵</div>
                <h3 class="font-bold text-slate-800 text-sm truncate">${pl.name}</h3>
                <p class="text-slate-400 text-xs mt-1">${pl.song_count} bài hát</p>
                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                    <div class="size-9 rounded-full bg-primary flex items-center justify-center shadow-lg shadow-primary/30">
                        <span class="material-symbols-outlined text-white text-[18px]" style="font-variation-settings:'FILL' 1">play_arrow</span>
                    </div>
                </div>
                <button onclick="event.stopPropagation();deletePlaylist(${pl.id})"
                        class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity text-red-400 hover:text-red-600 text-xs">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </div>`).join('');
    });
}

function createPlaylist(){
    const name=prompt('Nhập tên playlist mới:');
    if(!name||!name.trim())return;
    fetch('createPlaylist.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name:name.trim()})})
        .then(r=>r.json()).then(data=>{
            if(data.success){showPopup('Tạo playlist "'+name+'" thành công','success');loadPlaylists();}
            else showPopup(data.error||'Lỗi','error');
        });
}

function deletePlaylist(id){
    if(!confirm('Xóa playlist này?'))return;
    fetch('deletePlaylist.php?id='+id).then(r=>r.json()).then(data=>{
        if(data.success){showPopup('Đã xóa playlist','info');loadPlaylists();}
    });
}
</script>
</body></html>
