<?php
// header.php – Navbar dùng chung (for about, contact)
if(session_status()===PHP_SESSION_NONE)session_start();
$isLoggedIn=isset($_SESSION['user']); $username=$isLoggedIn?htmlspecialchars($_SESSION['user']):'';
$isAdmin=isset($_SESSION['role'])&&$_SESSION['role']==='admin';
?><!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Chill Wave</title><script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>tailwind.config={theme:{extend:{colors:{"primary":"#42a7f0","cream":"#fdfbf7"},fontFamily:{"display":["Spline Sans"]}}}}</script>
<style>body{font-family:'Spline Sans',sans-serif;background:#fdfbf7;}</style></head>
<body class="text-slate-900">
<nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200/60 px-6 py-4 flex items-center justify-between sticky top-0 z-50">
    <a href="index.php" class="flex items-center gap-3">
        <div class="size-9 rounded-full bg-primary flex items-center justify-center text-white shadow-md"><span class="material-symbols-outlined text-[18px]">eco</span></div>
        <div><h1 class="text-base font-bold">Chill Wave</h1><p class="text-[10px] text-slate-500">Premium Listening</p></div>
    </a>
    <div class="flex items-center gap-6">
        <?php foreach(['index.php'=>'Home','about.php'=>'About','contact.php'=>'Contact'] as $p=>$l):?>
        <a href="<?=$p?>" class="text-sm font-medium transition-colors <?=(basename($_SERVER['PHP_SELF'])===$p?'text-primary font-semibold':'text-slate-600 hover:text-primary')?>"><?=$l?></a>
        <?php endforeach;?>
    </div>
    <div class="flex items-center gap-3">
        <?php if($isLoggedIn):?>
        <div class="size-9 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold"><?=strtoupper(substr($username,0,1))?></div>
        <a href="logout.php" class="text-sm text-red-400 hover:text-red-600">Đăng xuất</a>
        <?php else:?>
        <a href="login.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Đăng nhập</a>
        <a href="register.php" class="text-sm font-bold bg-primary text-white px-5 py-2 rounded-full hover:bg-blue-600 transition-colors">Đăng ký</a>
        <?php endif;?>
    </div>
</nav>
