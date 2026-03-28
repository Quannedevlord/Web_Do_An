<?php if(session_status()===PHP_SESSION_NONE)session_start(); ?>
<footer class="bg-white/60 border-t border-slate-200/60 mt-16">
    <div class="max-w-6xl mx-auto px-8 py-10">
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-9 rounded-full bg-primary flex items-center justify-center text-white"><span class="material-symbols-outlined text-[18px]">eco</span></div>
                    <div><h3 class="text-base font-bold">Chill Wave</h3><p class="text-[10px] text-slate-500">Premium Listening</p></div>
                </div>
                <p class="text-sm text-slate-500">Web nghe nhạc trực tuyến – Đồ án lập trình Web.</p>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Điều hướng</h4>
                <ul class="space-y-2">
                    <?php foreach(['index.php'=>'🏠 Trang chủ','about.php'=>'👥 About','contact.php'=>'📬 Contact'] as $href=>$label):?>
                    <li><a href="<?=$href?>" class="text-sm text-slate-500 hover:text-primary transition-colors"><?=$label?></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Nhóm thực hiện</h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li>👤 Nguyễn Lê Hoàng Phúc – Frontend</li>
                    <li>👤 Trần Minh Quân – Backend</li>
                    <li>👤 Nguyễn Hoàng Phát – JavaScript</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-200/60 pt-6 flex md:flex-row flex-col justify-between items-center gap-3">
            <p class="text-xs text-slate-400">© <?=date('Y')?> Chill Wave – Đồ án lập trình web</p>
            <p class="text-xs text-slate-400">Xây dựng với ❤️ PHP + MySQL + JavaScript</p>
        </div>
    </div>
</footer>
<script src="js/popup.js"></script><script src="js/ui.js"></script>
</body></html>
