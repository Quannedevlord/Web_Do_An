<?php
// 1. khởi động session để kiểm tra trạng thái đăng nhập
session_start();

// 2. kiểm tra người dùng đã đăng nhập chưa
// nếu chưa đăng nhập → chuyển thẳng về trang login, không cho vào
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$isLoggedIn = true;
$username   = htmlspecialchars($_SESSION['user']);

// 3. lấy flash message từ session (thông báo sau khi login/register)
$flash     = '';
$flashType = 'success';
if (isset($_SESSION['flash'])) {
    $flash     = $_SESSION['flash'];
    $flashType = $_SESSION['flash_type'] ?? 'success';
    // xóa flash message sau khi lấy ra để không hiện lại lần sau
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Chill Guy – Music Streaming</title>

    <!-- Tailwind CSS framework -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Font chữ Spline Sans từ Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

    <!-- Icon từ Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <!-- Cấu hình màu sắc và font cho Tailwind -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#42a7f0",   // màu xanh chủ đạo
                        "sage":    "#b8c9b9",   // màu xanh lá nhạt
                        "cream":   "#fdfbf7",   // màu nền kem
                    },
                    fontFamily: {
                        "display": ["Spline Sans"]
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Spline Sans', sans-serif; }

        /* hiệu ứng kính mờ cho player bar ở cuối trang */
        .glass-player {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* style cho menu item đang active */
        .sidebar-item-active {
            background: rgba(66, 167, 240, 0.1);
            color: #42a7f0;
        }
    </style>
</head>

<!-- data-flash và data-flash-type để JS đọc và hiển thị thông báo -->
<body class="bg-cream text-slate-900 font-display"
      data-flash="<?= $flash ?>"
      data-flash-type="<?= $flashType ?>">

<div class="flex h-screen overflow-hidden">

    <!-- ============================================================
         SIDEBAR – thanh điều hướng bên trái
    ============================================================ -->
    <aside id="sidebar"
           class="w-64 flex flex-col border-r border-slate-200/60 bg-white/60 backdrop-blur-sm p-6 shrink-0">

        <!-- Logo và tên web -->
        <div class="flex items-center gap-3 mb-10">
            <div class="size-10 rounded-full bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined">eco</span>
            </div>
            <div>
                <h1 class="text-lg font-bold leading-tight text-slate-900">Chill Guy</h1>
                <p class="text-xs font-medium text-slate-500">Premium Listening</p>
            </div>
        </div>

        <!-- Menu điều hướng -->
        <nav class="flex flex-col gap-1 flex-1">

            <!-- trang chủ -->
            <a href="index.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl sidebar-item-active">
                <span class="material-symbols-outlined">home</span>
                <span class="text-sm font-semibold">Home</span>
            </a>

            <!-- trang thêm bài hát -->
            <a href="add_song.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined">add_circle</span>
                <span class="text-sm font-medium">Thêm bài hát</span>
            </a>

            <!-- thư viện nhạc -->
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined">library_music</span>
                <span class="text-sm font-medium">Thư viện</span>
            </a>

            <!-- phân cách tài khoản -->
            <div class="mt-6 mb-2 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tài khoản</div>

            <?php if ($isLoggedIn): ?>
                <!-- hiển thị tên người dùng khi đã đăng nhập -->
                <div class="flex items-center gap-3 px-4 py-2 rounded-xl bg-primary/5">
                    <span class="material-symbols-outlined text-primary">person</span>
                    <span class="text-sm font-medium text-slate-700"><?= $username ?></span>
                </div>

                <!-- nút đăng xuất -->
                <a href="logout.php"
                   class="flex items-center gap-3 px-4 py-2 rounded-xl text-red-400 hover:bg-red-50 transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-sm font-medium">Đăng xuất</span>
                </a>

            <?php else: ?>
                <!-- nút đăng nhập khi chưa login -->
                <a href="login.php"
                   class="flex items-center gap-3 px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined">login</span>
                    <span class="text-sm font-medium">Đăng nhập</span>
                </a>

                <!-- nút đăng ký -->
                <a href="register.php"
                   class="flex items-center gap-3 px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined">person_add</span>
                    <span class="text-sm font-medium">Đăng ký</span>
                </a>
            <?php endif; ?>

        </nav>

        <!-- card quảng cáo nâng cấp ở cuối sidebar -->
        <div class="mt-auto bg-primary/10 rounded-2xl p-4">
            <p class="text-xs font-bold text-primary mb-1">Upgrade to Gold</p>
            <p class="text-[11px] text-slate-600 mb-3">Chất lượng âm thanh tốt hơn, không quảng cáo.</p>
            <button class="w-full bg-primary text-white text-xs font-bold py-2 px-4 rounded-xl shadow-md shadow-primary/30">
                Go Premium
            </button>
        </div>
    </aside>


    <!-- ============================================================
         MAIN – nội dung chính
    ============================================================ -->
    <main class="flex-1 overflow-y-auto pb-32">
        <header class="px-8 pt-8">

            <!-- thanh trên cùng: nút menu mobile + ô tìm kiếm + avatar -->
            <div class="flex justify-between items-center mb-8 gap-4">

                <!-- nút hamburger – chỉ hiện trên mobile -->
                <button id="menuBtn"
                        class="md:hidden size-10 rounded-full bg-white border border-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">menu</span>
                </button>

                <!-- ô tìm kiếm bài hát -->
                <div class="flex items-center gap-3 bg-white/90 rounded-full px-6 py-3 border border-slate-100 shadow-lg w-full max-w-xl">
                    <span class="material-symbols-outlined text-slate-400">search</span>
                    <input id="searchInput"
                           class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-slate-400 font-medium"
                           placeholder="Tìm kiếm bài hát, ca sĩ..."
                           type="text"/>
                </div>

                <!-- nút dark mode + avatar người dùng -->
                <div class="flex items-center gap-3 shrink-0">
                    <button id="darkModeBtn"
                            class="size-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">dark_mode</span>
                    </button>

                    <?php if ($isLoggedIn): ?>
                        <!-- hiển thị chữ cái đầu tên người dùng làm avatar -->
                        <div class="size-10 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold">
                            <?= strtoupper(substr($username, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- banner hero – giới thiệu playlist nổi bật -->
            <div class="relative overflow-hidden rounded-3xl p-10 bg-gradient-to-br from-primary/20 via-cream to-sage/20 mb-8 border border-white/50 shadow-sm">
                <div class="relative z-10 flex flex-col gap-4 max-w-lg">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Editor's Pick</span>
                    <h2 class="text-5xl font-bold text-slate-900 leading-tight">Pastel Horizons</h2>
                    <p class="text-slate-600 text-lg">Bộ sưu tập nhạc chill cho buổi sáng tập trung và thư giãn.</p>
                    <div class="flex gap-3 mt-2">
                        <!-- nút phát ngay – gọi hàm toggle của Player -->
                        <button onclick="window.__player && window.__player.toggle()"
                                class="bg-primary text-white font-bold px-8 py-3 rounded-full flex items-center gap-2 shadow-xl shadow-primary/20 hover:bg-blue-500 transition-colors">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">play_arrow</span>
                            Nghe ngay
                        </button>
                        <button class="bg-white/80 backdrop-blur-md text-slate-700 font-bold px-8 py-3 rounded-full border border-slate-200 hover:bg-white transition-colors">
                            Lưu playlist
                        </button>
                    </div>
                </div>
                <!-- vòng trang trí nền -->
                <div class="absolute -right-10 -top-10 size-64 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-20 bottom-0 size-48 bg-sage/20 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- các pill lọc thể loại nhạc -->
            <!-- data-genre dùng để JS nhận biết và lọc bài hát -->
            <div class="flex gap-3 mb-8 flex-wrap">
                <div data-genre="all"     class="genre-pill px-5 py-2 rounded-full bg-primary text-white text-sm font-semibold cursor-pointer active-pill">Tất cả</div>
                <div data-genre="chill"   class="genre-pill px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:border-primary/50 cursor-pointer transition-colors">Chill</div>
                <div data-genre="lofi"    class="genre-pill px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:border-primary/50 cursor-pointer transition-colors">Lofi</div>
                <div data-genre="pop"     class="genre-pill px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:border-primary/50 cursor-pointer transition-colors">Pop</div>
                <div data-genre="ballad"  class="genre-pill px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:border-primary/50 cursor-pointer transition-colors">Ballad</div>
            </div>

            <!-- bảng danh sách bài hát -->
            <div class="bg-white/50 rounded-3xl border border-white/60 p-6 shadow-sm mb-8">

                <!-- tiêu đề bảng + nút thêm bài -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Danh sách bài hát</h3>
                    <a href="add_song.php"
                       class="flex items-center gap-2 bg-primary text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-md shadow-primary/20 hover:bg-blue-500 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">add</span> Thêm bài
                    </a>
                </div>

                <table class="w-full text-left">
                    <!-- tiêu đề cột -->
                    <thead>
                        <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                            <th class="pb-4 px-4 w-12 text-center">#</th>
                            <th class="pb-4 px-4">Tên bài hát</th>
                            <th class="pb-4 px-4">Ca sĩ</th>
                            <th class="pb-4 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>

                    <!-- tbody này được JS tự động điền dữ liệu từ getSongs.php -->
                    <tbody id="songList" class="text-sm font-medium text-slate-700">
                        <tr>
                            <td colspan="4" class="text-center py-8 text-slate-400">
                                <span style="animation:spin 1s linear infinite;display:inline-block;">⏳</span>
                                Đang tải...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </header>
    </main>


    <!-- ============================================================
         PLAYER BAR – thanh phát nhạc cố định ở cuối trang
    ============================================================ -->
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[94%] max-w-[1400px] h-24
                glass-player rounded-[2rem] border border-white/40 shadow-2xl
                flex items-center px-6 z-50">

        <!-- phần trái: thông tin bài đang phát -->
        <div class="flex items-center gap-3 w-1/4 min-w-0">

            <!-- ảnh bìa bài hát – JS sẽ cập nhật khi chọn bài -->
            <div id="playerCover"
                 class="size-14 rounded-xl bg-cover bg-center shadow-md shrink-0"
                 style="background-color:#e2e8f0;">
            </div>

            <!-- tên bài hát và ca sĩ – JS sẽ cập nhật -->
            <div class="min-w-0">
                <p id="playerTitle"  class="text-slate-900 font-bold text-sm leading-tight truncate">Chưa phát</p>
                <p id="playerArtist" class="text-slate-500 text-xs truncate">–</p>
            </div>

            <!-- nút yêu thích -->
            <button class="ml-1 text-slate-300 hover:text-red-400 transition-colors shrink-0">
                <span class="material-symbols-outlined text-[20px]">favorite</span>
            </button>
        </div>

        <!-- phần giữa: các nút điều khiển + thanh tiến trình -->
        <div class="flex flex-col items-center flex-1 px-4">

            <!-- hàng nút điều khiển -->
            <div class="flex items-center gap-6 mb-2">

                <!-- nút phát ngẫu nhiên -->
                <button id="shuffleBtn" class="text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[20px]">shuffle</span>
                </button>

                <!-- nút bài trước -->
                <button id="prevBtn" class="text-slate-700 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[26px]">skip_previous</span>
                </button>

                <!-- nút play/pause – JS gắn sự kiện vào id="playBtn" -->
                <button id="playBtn"
                        class="size-12 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30 hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[28px]"
                          style="font-variation-settings:'FILL' 1">play_arrow</span>
                </button>

                <!-- nút bài tiếp theo -->
                <button id="nextBtn" class="text-slate-700 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[26px]">skip_next</span>
                </button>

                <!-- nút lặp lại -->
                <button id="repeatBtn" class="text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[20px]">repeat</span>
                </button>
            </div>

            <!-- thanh tiến trình phát nhạc -->
            <div class="w-full flex items-center gap-3">
                <!-- thời gian đã phát – JS cập nhật -->
                <span id="currentTime" class="text-[10px] font-bold text-slate-400 w-8 text-right">0:00</span>

                <!-- thanh progress – bấm để tua -->
                <div id="progressBar"
                     class="flex-1 h-1.5 bg-slate-200/60 rounded-full relative group cursor-pointer">
                    <!-- phần đã phát – JS điều chỉnh width -->
                    <div id="progressTrack"
                         class="absolute top-0 left-0 h-full w-0 bg-primary rounded-full"></div>
                </div>

                <!-- tổng thời gian bài – JS cập nhật -->
                <span id="totalTime" class="text-[10px] font-bold text-slate-400 w-8">0:00</span>
            </div>
        </div>

        <!-- phần phải: âm lượng và tiện ích -->
        <div class="w-1/4 flex items-center justify-end gap-5">

            <!-- nút xem hàng chờ -->
            <button class="text-slate-400 hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">queue_music</span>
            </button>

            <!-- thanh điều chỉnh âm lượng -->
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-[20px]">volume_up</span>
                <!-- bấm vào đây để điều chỉnh âm lượng -->
                <div id="volumeTrack"
                     class="w-20 h-1.5 bg-slate-200/60 rounded-full relative cursor-pointer">
                    <!-- phần âm lượng hiện tại – JS điều chỉnh width -->
                    <div id="volumeBar"
                         class="absolute top-0 left-0 h-full w-[70%] bg-primary rounded-full">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /flex wrapper -->

<!-- footer -->
<footer class="hidden text-center text-xs text-slate-400 py-4">
    © <?= date('Y') ?> Chill Guy Music – Đồ án lập trình web
</footer>

<!-- nhúng file JavaScript vào cuối trang -->
<script src="js/script.js"></script>
</body>
</html>
