<?php ob_start(); ?>
<?php

session_start();
$isLoggedIn = isset($_SESSION['user']);
$username   = $isLoggedIn ? htmlspecialchars($_SESSION['user']) : '';
$isAdmin    = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId     = (int)($_SESSION['user_id'] ?? 0);
$flash=''; $flashType='success';
if(isset($_SESSION['flash'])){$flash=$_SESSION['flash'];$flashType=$_SESSION['flash_type']??'success';unset($_SESSION['flash'],$_SESSION['flash_type']);}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Chill Wave – Music Streaming</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#42a7f0","sage":"#b8c9b9","cream":"#fdfbf7"},fontFamily:{"display":["Spline Sans"]}}}}</script>
<style>
body{font-family:'Spline Sans',sans-serif;}
.glass-player{background:rgba(255,255,255,0.85);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-top:1px solid rgba(255,255,255,0.4);}
.sidebar-active{background:rgba(66,167,240,0.1);color:#42a7f0;}
@media (max-width:767px){
  #songTableWrap{-webkit-overflow-scrolling:touch;}
}
</style>
</head>

<body class="bg-cream text-slate-900 font-display" data-flash="<?=htmlspecialchars($flash)?>" data-flash-type="<?=$flashType?>">
<div class="flex h-screen min-h-0 overflow-hidden">

<!-- ==================== SIDEBAR ==================== -->
<aside id="sidebar" class="w-64 max-w-[min(100vw,16rem)] flex flex-col border-r border-slate-200/60 bg-white/60 backdrop-blur-sm p-4 sm:p-5 shrink-0 overflow-y-auto">
    <!-- Logo -->
    <div class="flex items-center gap-3 mb-8">
        <div class="size-10 rounded-full bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
            <span class="material-symbols-outlined">eco</span>
        </div>
        <div><h1 class="text-base font-bold text-slate-900">Chill Wave</h1><p class="text-[10px] text-slate-500">Premium Listening</p></div>
    </div>

    <nav class="flex flex-col gap-1">
        <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl sidebar-active text-sm font-semibold">
            <span class="material-symbols-outlined text-[20px]">home</span>Home</a>
        <a href="about.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">info</span>About</a>
        <a href="contact.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">mail</span>Contact</a>
        <?php if($isAdmin):?>
        <a href="add_song.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>Thêm bài hát</a>
        <a href="backup.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-green-50 hover:text-green-600 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">download</span>Backup DB</a>
        <a href="admin_messages.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">mail</span>Tin nhắn</a>
        <?php endif;?>

        <!-- Thư viện – chỉ hiện với user đã đăng nhập -->
        <?php if($isLoggedIn):?>
        <div class="mt-4 mb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Thư viện</div>

        <!-- Liked Songs mặc định -->
        <a href="liked.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-red-50 hover:text-red-500 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]" style="color:#ef4444;">favorite</span>
            <span>Liked Songs</span>
        </a>

        <!-- Danh sách playlist của user -->
        <div id="sidebarPlaylists" class="space-y-0.5"></div>

        <!-- Link xem tất cả playlist -->
        <a href="playlist.php"
           class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-primary text-sm transition-colors font-medium">
            <span class="material-symbols-outlined text-[18px]">library_music</span>Thư viện
        </a>
        <!-- Nút tạo playlist -->
        <button onclick="Library.createPlaylist()"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-primary transition-colors text-sm w-full text-left border-2 border-dashed border-slate-200 hover:border-primary mt-1">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Tạo playlist</span>
        </button>
        <?php endif;?>

        <div class="mt-4 mb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tài khoản</div>
        <?php if($isLoggedIn):?>
        <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-primary/5">
            <span class="material-symbols-outlined text-primary text-[20px]">person</span>
            <div><span class="text-sm font-medium text-slate-700"><?=$username?></span>
            <?php if($isAdmin):?><span class="block text-[10px] font-bold text-blue-500">👑 Admin</span>
            <?php else:?><span class="block text-[10px] text-slate-400">Người dùng</span><?php endif;?></div>
        </div>
        <a href="logout.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-red-400 hover:bg-red-50 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">logout</span>Đăng xuất</a>
        <?php else:?>
        <a href="login.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">login</span>Đăng nhập</a>
        <a href="register.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">person_add</span>Đăng ký</a>
        <?php endif;?>
    </nav>
</aside>

<!-- ==================== MAIN ==================== -->
<main class="flex-1 min-w-0 overflow-y-auto pb-28 sm:pb-32">
<div class="px-4 sm:px-8 pt-4 sm:pt-8">
    <!-- Top bar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6 sm:mb-8">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 w-full sm:w-auto sm:flex-1 sm:max-w-xl">
        <button id="menuBtn" type="button" class="md:hidden shrink-0 size-10 rounded-full bg-white border border-slate-200 flex items-center justify-center">
            <span class="material-symbols-outlined">menu</span></button>
        <div class="flex items-center gap-2 sm:gap-3 bg-white/90 rounded-full px-3 sm:px-6 py-2.5 sm:py-3 border border-slate-100 shadow-lg flex-1 min-w-0">
            <span class="material-symbols-outlined text-slate-400 shrink-0 text-[20px] sm:text-[24px]">search</span>
            <input id="searchInput" class="bg-transparent border-none focus:ring-0 text-sm w-full min-w-0 placeholder:text-slate-400 font-medium"
                   placeholder="Tìm kiếm bài hát, ca sĩ..." type="text"/></div>
        </div>
        <div class="flex items-center justify-end gap-2 sm:gap-3 shrink-0">
            <?php if($isLoggedIn):?>
            <span class="text-sm text-slate-600 font-medium hidden md:block truncate max-w-[8rem]"><?=$username?></span>
            <div class="size-9 sm:size-10 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold"><?=strtoupper(substr($username,0,1))?></div>
            <?php else:?>
            <a href="register.php" class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 px-2 sm:px-4 py-2 transition-colors whitespace-nowrap">Đăng ký</a>
            <a href="login.php" class="text-xs sm:text-sm font-bold bg-primary text-white px-4 sm:px-6 py-2 rounded-full shadow-md shadow-primary/30 hover:bg-blue-600 transition-colors whitespace-nowrap">Đăng nhập</a>
            <?php endif;?>
        </div>
    </div>

    <!-- Hero -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl p-5 sm:p-10 bg-gradient-to-br from-primary/20 via-cream to-sage/20 mb-6 sm:mb-8 border border-white/50 shadow-sm">
        <div class="relative z-10 flex flex-col gap-3 sm:gap-4 max-w-lg">
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] text-primary">FEELING AND CHILLING</span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-slate-900 leading-tight">CHÂN TRỜI DỊU DÀNG</h2>
            <p class="text-slate-600 text-base sm:text-lg">Bộ sưu tập nhạc chill tập trung và thư giãn.</p>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-1 sm:mt-2">
                <button onclick="window.__player&&window.__player.toggle()"
                        class="bg-primary text-white font-bold px-6 sm:px-8 py-2.5 sm:py-3 rounded-full flex items-center justify-center gap-2 shadow-xl shadow-primary/20 hover:bg-blue-500 transition-colors text-sm sm:text-base">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">play_arrow</span>Nghe ngay</button>
                <button id="playlistBtn" class="bg-white/80 text-slate-700 font-bold px-5 sm:px-6 py-2.5 sm:py-3 rounded-full border border-slate-200 hover:bg-white transition-colors flex items-center justify-center gap-2 text-sm sm:text-base">
                    <span class="material-symbols-outlined text-[18px]">queue_music</span>Playlist</button>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 size-64 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-20 bottom-0 size-48 bg-sage/20 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Genre pills -->
    <div class="flex gap-2 sm:gap-3 mb-6 sm:mb-8 flex-wrap">
        <div data-genre="all"    class="genre-pill px-3 py-1.5 sm:px-5 sm:py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold cursor-pointer active-pill">Tất cả</div>
        <div data-genre="lofi"   class="genre-pill px-3 py-1.5 sm:px-5 sm:py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-xs sm:text-sm hover:border-primary/50 cursor-pointer transition-colors">Lofi</div>
        <div data-genre="pop"    class="genre-pill px-3 py-1.5 sm:px-5 sm:py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-xs sm:text-sm hover:border-primary/50 cursor-pointer transition-colors">Pop</div>
        <div data-genre="ballad" class="genre-pill px-3 py-1.5 sm:px-5 sm:py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-xs sm:text-sm hover:border-primary/50 cursor-pointer transition-colors">Ballad</div>
        <div data-genre="edm"  class="genre-pill px-3 py-1.5 sm:px-5 sm:py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-xs sm:text-sm hover:border-primary/50 cursor-pointer transition-colors">EDM</div>
    </div>

    <!-- Song list -->
    <div class="bg-white/50 rounded-2xl sm:rounded-3xl border border-white/60 p-3 sm:p-6 shadow-sm mb-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center mb-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800">Danh sách bài hát</h3>
            <?php if($isAdmin):?>
            <a href="add_song.php" class="flex items-center gap-2 bg-primary text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-md hover:bg-blue-500 transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span>Thêm bài</a>
            <?php elseif(!$isLoggedIn):?>
            <a href="login.php" class="flex items-center gap-2 bg-slate-100 text-slate-600 text-sm font-semibold px-4 py-2 rounded-xl hover:bg-slate-200 transition-colors self-start sm:self-auto">
                <span class="material-symbols-outlined text-[18px]">login</span>Đăng nhập</a>
            <?php endif;?>
        </div>
        <div id="songTableWrap" class="overflow-x-auto -mx-1 sm:mx-0">
        <table class="w-full text-left min-w-[520px] sm:min-w-0">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                    <th class="pb-3 sm:pb-4 px-2 sm:px-3 w-10 text-center">#</th>
                    <th class="pb-3 sm:pb-4 px-2 sm:px-3">Tên bài hát</th>
                    <th class="pb-3 sm:pb-4 px-2 sm:px-3 hidden md:table-cell">Ca sĩ</th>
                    <th class="pb-3 sm:pb-4 px-2 sm:px-3 text-right whitespace-nowrap">Thao tác</th>
                </tr>
            </thead>
            <tbody id="songList" class="text-sm font-medium text-slate-700">
                <tr><td colspan="4" class="text-center py-8 text-slate-400">⏳ Đang tải...</td></tr>
            </tbody>
        </table>
        </div>
    </div>
</div>
</main>

<!-- ==================== PLAYER BAR ==================== -->
<div class="fixed bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-1rem)] sm:w-[94%] max-w-[1400px] min-h-[4.75rem] md:h-24
            glass-player rounded-2xl sm:rounded-[2rem] border border-white/40 shadow-2xl flex items-center px-3 sm:px-6 py-2 md:py-0 z-50 gap-2 md:gap-0">
    <!-- Trái: info -->
    <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0 md:w-1/4 md:flex-initial">
        <div id="playerCover" class="size-11 sm:size-14 rounded-lg sm:rounded-xl bg-cover bg-center shadow-md shrink-0" style="background-color:#e2e8f0;"></div>
        <div class="min-w-0 flex-1">
            <p id="playerTitle"  class="text-slate-900 font-bold text-xs sm:text-sm leading-tight truncate">Chưa phát</p>
            <p id="playerArtist" class="text-slate-500 text-[10px] sm:text-xs truncate">–</p>
        </div>
        <!-- Nút tim – chỉ hiện khi đã đăng nhập -->
        <?php if($isLoggedIn):?>
        <button id="likeBtn" class="md:ml-1 text-slate-300 hover:text-red-400 transition-colors shrink-0" onclick="Library.toggleLike()">
            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">favorite</span>
        </button>
        <?php endif;?>
    </div>
    <!-- Giữa: điều khiển -->
    <div class="flex flex-col items-center flex-[2] min-w-0 px-1 sm:px-4 max-w-full">
        <div class="flex items-center gap-1.5 sm:gap-4 md:gap-6 mb-1 md:mb-2">
            <button id="shuffleBtn" class="hidden sm:block text-slate-400 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px] sm:text-[20px]">shuffle</span></button>
            <button id="prevBtn"    class="text-slate-700 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[22px] sm:text-[26px]">skip_previous</span></button>
            <button id="playBtn"    class="size-10 sm:size-12 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30 hover:scale-105 transition-transform shrink-0">
                <span class="material-symbols-outlined text-[24px] sm:text-[28px]" style="font-variation-settings:'FILL' 1">play_arrow</span></button>
            <button id="nextBtn"   class="text-slate-700 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[22px] sm:text-[26px]">skip_next</span></button>
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
    <!-- Phải: queue (luôn có) + volume (chỉ desktop) -->
    <div class="flex items-center justify-end gap-2 md:gap-3 lg:gap-5 shrink-0 md:w-1/4">
        <button id="queueBtn" type="button" class="text-slate-400 hover:text-primary transition-colors p-0.5 sm:p-0" title="Hàng chờ">
            <span class="material-symbols-outlined text-[20px] sm:text-[20px]">queue_music</span></button>
        <div class="hidden md:flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400 text-[20px]">volume_up</span>
            <div id="volumeTrack" class="w-16 lg:w-20 h-1.5 bg-slate-200/60 rounded-full relative cursor-pointer">
                <div id="volumeBar" class="absolute top-0 left-0 h-full w-[70%] bg-primary rounded-full"></div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Footer -->
<footer class="text-center text-xs text-slate-400 py-4 bg-white/30">
    © <?=date('Y')?> Chill Wave Music – Đồ án lập trình web
</footer>

<script>
window.isLoggedIn  = <?=$isAdmin?'true':'false'?>;
window.isUserLogin = <?=$isLoggedIn?'true':'false'?>;
window.currentUserId = <?=$userId?>;
</script>
<script src="js/popup.js?v=<?=filemtime(__DIR__.'/js/popup.js')?>"></script>
<script src="js/ui.js?v=<?=filemtime(__DIR__.'/js/ui.js')?>"></script>
<script src="js/validation.js?v=<?=filemtime(__DIR__.'/js/validation.js')?>"></script>
<script src="js/player.js?v=<?=filemtime(__DIR__.'/js/player.js')?>"></script>
<script src="js/songs.js?v=<?=filemtime(__DIR__.'/js/songs.js')?>"></script>
<script src="js/library.js?v=<?=filemtime(__DIR__.'/js/library.js')?>"></script>
<script src="js/queue.js?v=<?=filemtime(__DIR__.'/js/queue.js')?>"></script>
<script src="js/main.js?v=<?=filemtime(__DIR__.'/js/main.js')?>"></script>
<script>
// Chặn chuột phải
document.addEventListener('contextmenu', e => e.preventDefault());

// Chặn F12, Ctrl+Shift+I, Ctrl+U
document.addEventListener('keydown', e => {
    if(e.key === 'F12') e.preventDefault();
    if(e.ctrlKey && e.shiftKey && e.key === 'I') e.preventDefault();
    if(e.ctrlKey && e.shiftKey && e.key === 'J') e.preventDefault();
    if(e.ctrlKey && e.key === 'u') e.preventDefault();
});
</script>
</body>
<?php
$html = ob_get_clean();
$html = preg_replace('/<!--(.|\s)*?-->/', '', $html); // xóa comment
$html = preg_replace('/\s+/', ' ', $html);            // xóa khoảng trắng
$html = preg_replace('/>\s+</', '><', $html);          // xóa space giữa tags
echo $html;
?>
</html>
