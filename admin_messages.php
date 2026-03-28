<?php
include "auth_admin.php";
include "config.php";

// Tạo bảng nếu chưa có
mysqli_query($conn,"CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Đánh dấu đã đọc
if(isset($_GET['read'])){
    $rid=(int)$_GET['read'];
    mysqli_query($conn,"UPDATE contact_messages SET is_read=1 WHERE id=$rid");
    header("Location: admin_messages.php"); exit;
}
// Xóa tin nhắn
if(isset($_GET['del'])){
    $did=(int)$_GET['del'];
    mysqli_query($conn,"DELETE FROM contact_messages WHERE id=$did");
    header("Location: admin_messages.php"); exit;
}

$result=mysqli_query($conn,"SELECT * FROM contact_messages ORDER BY created_at DESC");
$msgs=[]; while($row=mysqli_fetch_assoc($result)) $msgs[]=$row;
$unread=array_filter($msgs,fn($m)=>!$m['is_read']);
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Tin nhắn liên hệ – Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>body{font-family:'Spline Sans',sans-serif;}</style>
</head>
<body class="min-h-screen bg-slate-50 p-6">
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                <span class="material-symbols-outlined">mail</span>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Tin nhắn liên hệ</h1>
                <p class="text-xs text-slate-500"><?=count($msgs)?> tin nhắn · <?=count($unread)?> chưa đọc</p>
            </div>
        </div>
        <a href="index.php" class="flex items-center gap-2 text-sm text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>Về trang chủ
        </a>
    </div>

    <?php if(empty($msgs)):?>
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <span class="material-symbols-outlined text-6xl text-slate-200 block mb-4">inbox</span>
        <p class="text-slate-400">Chưa có tin nhắn nào</p>
    </div>
    <?php else:?>
    <div class="space-y-3">
        <?php foreach($msgs as $m):?>
        <div class="bg-white rounded-2xl border <?=!$m['is_read']?'border-blue-200 shadow-sm shadow-blue-100':'border-slate-200'?> p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <?php if(!$m['is_read']):?>
                        <span class="size-2 rounded-full bg-blue-500 shrink-0"></span>
                        <?php endif;?>
                        <span class="font-semibold text-slate-800"><?=htmlspecialchars($m['name'])?></span>
                        <span class="text-xs text-slate-400">&lt;<?=htmlspecialchars($m['email'])?>&gt;</span>
                        <span class="text-xs text-slate-300 ml-auto"><?=date('d/m/Y H:i', strtotime($m['created_at']))?></span>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap"><?=htmlspecialchars($m['message'])?></p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <?php if(!$m['is_read']):?>
                    <a href="?read=<?=$m['id']?>" class="text-xs text-blue-500 hover:text-blue-700 px-2 py-1 rounded-lg hover:bg-blue-50 transition-colors" title="Đánh dấu đã đọc">
                        <span class="material-symbols-outlined text-[16px]">mark_email_read</span>
                    </a>
                    <?php endif;?>
                   
                    <a href="?del=<?=$m['id']?>" onclick="return confirm('Xóa tin nhắn này?')"
                       class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 transition-colors" title="Xóa">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>
</div>
</body></html>
