<?php
// chỉ admin mới vào được trang này
include "auth_admin.php";
include "config.php";

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM songs WHERE id=$id";
$result = mysqli_query($conn, $sql);
$row    = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: index.php");
    exit;
}

$thongBao = '';
if (isset($_POST['update'])) {
    $title  = trim($_POST['title']  ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $file   = trim($_POST['file']   ?? '');

    if (empty($title) || empty($artist) || empty($file)) {
        $thongBao = 'error:Vui lòng điền đầy đủ thông tin';
    } else {
        $stmt = $conn->prepare("UPDATE songs SET title=?, artist=?, file=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $artist, $file, $id);

        if ($stmt->execute()) {
            $_SESSION['flash']      = 'Cập nhật bài hát thành công!';
            $_SESSION['flash_type'] = 'success';
            header("Location: index.php");
            exit;
        } else {
            $thongBao = 'error:Lỗi cập nhật';
        }
    }
}

$loai = $noiDung = '';
if ($thongBao) [$loai, $noiDung] = explode(':', $thongBao, 2);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sửa bài hát – Chill Guy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style> body { font-family: 'Spline Sans', sans-serif; } </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4">

    <main class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-100">

        <div class="flex items-center gap-3 mb-8">
            <div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">♪</div>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Chill Guy</h1>
                <p class="text-xs text-slate-500">Sửa bài hát</p>
            </div>
            <span class="ml-auto text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">👑 Admin</span>
        </div>

        <h2 class="text-2xl font-bold text-slate-800 mb-6">Sửa bài hát</h2>

        <?php if ($loai === 'error'): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
                ⚠ <?= htmlspecialchars($noiDung) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tên bài hát</label>
                <input type="text" name="title" required
                       value="<?= htmlspecialchars($row['title']) ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ca sĩ</label>
                <input type="text" name="artist" required
                       value="<?= htmlspecialchars($row['artist']) ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tên file MP3</label>
                <input type="text" name="file" required
                       value="<?= htmlspecialchars($row['file']) ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <button type="submit" name="update"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200 transition-all duration-200">
                💾 Cập nhật
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            <a href="index.php" class="font-semibold text-blue-500 hover:text-blue-700 underline underline-offset-2">
                ← Quay về trang chủ
            </a>
        </p>
    </main>
</body>
</html>
