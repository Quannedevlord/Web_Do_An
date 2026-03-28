<?php include "header.php"; ?>
<main class="max-w-4xl mx-auto px-6 py-12">

    <div class="text-center mb-12">
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Về chúng tôi</span>
        <h1 class="text-4xl font-bold text-slate-900 mt-2 mb-4">Chill Wave</h1>
        <p class="text-slate-500 text-lg max-w-xl mx-auto">Web nghe nhạc trực tuyến – Đồ án môn Lập trình Web</p>
    </div>

    <!-- Về dự án -->
    <div class="bg-white/70 rounded-3xl border border-white/60 shadow-sm p-8 mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Về dự án</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="text-sm text-slate-600 leading-relaxed">
                <p class="mb-4"><strong class="text-slate-800">Chill Wave</strong> là website nghe nhạc trực tuyến
                cho phép người dùng tìm kiếm, nghe nhạc, tạo playlist cá nhân và quản lý bài hát yêu thích.</p>
                <p>Xây dựng theo mô hình MVC đơn giản với PHP thuần và MySQL, kết hợp JavaScript để tạo
                trải nghiệm người dùng mượt mà không cần reload trang.</p>
            </div>
            <!-- Tech stack – KHÔNG dùng bullet -->
            <div>
                <h3 class="text-sm font-bold text-slate-700 mb-4">Công nghệ sử dụng</h3>
                <div class="grid grid-cols-2 gap-3">
                    <?php foreach([
                        ['code','#42a7f0','HTML + CSS','Tailwind CSS, Flexbox/Grid, Responsive'],
                        ['storage','#16a34a','PHP + MySQL','Backend, CRUD, Session, Phân quyền'],
                        ['javascript','#d97706','JavaScript','AJAX, Web Audio API, DOM'],
                        ['cloud_upload','#7c3aed','Deploy','InfinityFree Hosting'],
                    ] as [$ic,$co,$name,$desc]):?>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50">
                        <span class="material-symbols-outlined text-[18px] mt-0.5" style="color:<?=$co?>"><?=$ic?></span>
                        <div>
                            <p class="text-sm font-semibold text-slate-700"><?=$name?></p>
                            <p class="text-xs text-slate-500"><?=$desc?></p>
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tính năng -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6 text-center">Tính năng nổi bật</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <?php foreach([
                ['music_note','#42a7f0','Audio Player','Play/pause, Shuffle, Repeat, tua nhạc, chỉnh volume'],
                ['favorite','#ef4444','Liked Songs','Bấm tim để lưu bài yêu thích'],
                ['playlist_play','#7c3aed','Playlist','Tạo playlist tùy ý, thêm/xóa bài'],
                ['search','#16a34a','Tìm kiếm','Real-time, không reload trang'],
                ['admin_panel_settings','#d97706','Phân quyền','Admin thêm/sửa/xóa, User nghe nhạc'],
                ['devices','#64748b','Responsive','Tương thích điện thoại, tablet, máy tính'],
            ] as [$ic,$co,$ti,$de]):?>
            <div class="bg-white/70 rounded-2xl border border-white/60 shadow-sm p-5 hover:shadow-md transition-shadow">
                <div class="size-10 rounded-xl flex items-center justify-center text-white mb-3" style="background:<?=$co?>">
                    <span class="material-symbols-outlined text-[20px]"><?=$ic?></span>
                </div>
                <h3 class="font-semibold text-slate-800 mb-1"><?=$ti?></h3>
                <p class="text-xs text-slate-500"><?=$de?></p>
            </div>
            <?php endforeach;?>
        </div>
    </div>

    <!-- Thành viên -->
    <div class="bg-white/70 rounded-3xl border border-white/60 shadow-sm p-8 mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6 text-center">Thành viên nhóm</h2>
        <div class="grid md:grid-cols-3 gap-6 text-center">
            <?php foreach([
                ['P','#42a7f0','Nguyễn Lê Hoàng Phúc','Frontend Developer','Giao diện HTML/CSS, Responsive, About, Contact'],
                ['Q','#16a34a','Trần Minh Quân','Backend Developer','PHP, MySQL, CRUD, Session, API'],
                ['P','#7c3aed','Nguyễn Hoàng Phát','JavaScript Developer','Audio Player, AJAX, Playlist, Library'],
            ] as [$n,$c,$name,$role,$desc]):?>
            <div>
                <div class="size-16 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-3 shadow-lg" style="background:<?=$c?>"><?=$n?></div>
                <h3 class="font-semibold text-slate-800 mb-0.5"><?=$name?></h3>
                <p class="text-xs font-medium text-primary mb-2"><?=$role?></p>
                <p class="text-xs text-slate-500"><?=$desc?></p>
            </div>
            <?php endforeach;?>
        </div>
    </div>

    <div class="text-center">
        <a href="index.php" class="inline-flex items-center gap-2 bg-primary text-white font-semibold px-8 py-3 rounded-full shadow-lg hover:bg-blue-600 transition-colors">
            <span class="material-symbols-outlined text-[18px]">home</span>Về trang chủ
        </a>
    </div>
</main>
<?php include "footer.php"; ?>
