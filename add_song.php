<?php
// chỉ admin mới vào được trang này
include "auth_admin.php";

// kết nối database
include "config.php";

// biến lưu thông báo
$thongBao = '';

// kiểm tra khi người dùng bấm nút thêm bài hát
if (isset($_POST['add'])) {

    // lấy dữ liệu từ form
    $title  = trim($_POST['title']  ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $file   = trim($_POST['file']   ?? '');

    // kiểm tra không được để trống
    if (empty($title) || empty($artist) || empty($file)) {
        $thongBao = 'error:Vui lòng điền đầy đủ thông tin';

    } else {
        // xử lý upload hình ảnh (nếu có)
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $tmpPath  = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // chỉ cho phép ảnh jpg, jpeg, png, webp
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($fileExt, $allowedExt)) {
                $thongBao = 'error:Chỉ được upload ảnh jpg, jpeg, png, webp';

            } elseif ($fileSize > 2 * 1024 * 1024) {
                $thongBao = 'error:Ảnh không được vượt quá 2MB';

            } else {
                // tạo tên file mới để tránh trùng lặp
                $image    = time() . '_' . preg_replace('/\s+/', '_', $fileName);
                $savePath = 'images/' . $image;

                // tạo thư mục images nếu chưa có
                if (!is_dir('images')) mkdir('images', 0755, true);

                if (!move_uploaded_file($tmpPath, $savePath)) {
                    $thongBao = 'error:Lỗi khi lưu ảnh';
                    $image    = '';
                }
            }
        }

        // thêm bài hát vào database nếu không có lỗi ảnh
        if (empty($thongBao)) {
            $stmt = $conn->prepare("INSERT INTO songs (title, artist, file, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $artist, $file, $image);

            if ($stmt->execute()) {
                $thongBao = 'success:Thêm bài hát thành công!';
            } else {
                $thongBao = 'error:Lỗi khi thêm bài hát';
            }
        }
    }
}

// tách loại thông báo và nội dung
$loai    = '';
$noiDung = '';
if ($thongBao) {
    [$loai, $noiDung] = explode(':', $thongBao, 2);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Thêm bài hát – Chill Guy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style> body { font-family: 'Spline Sans', sans-serif; } </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4">

    <main class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-100">

        <!-- logo + badge Admin -->
        <div class="flex items-center gap-3 mb-8">
            <div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">♪</div>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Chill Guy</h1>
                <p class="text-xs text-slate-500">Thêm bài hát mới</p>
            </div>
            <!-- badge admin -->
            <span class="ml-auto text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">
                👑 Admin
            </span>
        </div>

        <h2 class="text-2xl font-bold text-slate-800 mb-6">Thêm bài hát</h2>

        <?php if ($loai === 'success'): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">
                ✓ <?= htmlspecialchars($noiDung) ?>
            </div>
        <?php endif; ?>

        <?php if ($loai === 'error'): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
                ⚠ <?= htmlspecialchars($noiDung) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tên bài hát</label>
                <input type="text" name="title" required
                       placeholder="VD: Jumpy Pants"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ca sĩ</label>
                <input type="text" name="artist" required
                       placeholder="VD: FreePD"
                       value="<?= htmlspecialchars($_POST['artist'] ?? '') ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tên file MP3</label>
                <input type="text" name="file" required
                       placeholder="VD: Jumpy Pants - Freepd.mp3"
                       value="<?= htmlspecialchars($_POST['file'] ?? '') ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
                <p class="text-xs text-slate-400 mt-1">
                    ⚠ Tên file phải khớp chính xác với file trong thư mục <code class="bg-slate-100 px-1 rounded">music/</code>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Ảnh bìa <span class="text-slate-400 font-normal">(không bắt buộc)</span>
                </label>
                <input type="file" name="image" accept="image/*"
                       onchange="previewImage(this)"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 file:text-xs file:font-semibold cursor-pointer"/>
                <p class="text-xs text-slate-400 mt-1">Định dạng: jpg, png, webp – Tối đa 2MB</p>

                <div id="imagePreview" class="hidden mt-3">
                    <img id="previewImg" src="" alt="Preview"
                         class="w-24 h-24 rounded-xl object-cover border border-slate-200 shadow-sm"/>
                </div>
            </div>

            <button type="submit" name="add"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200 transition-all duration-200">
                + Thêm bài hát
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            <a href="index.php" class="font-semibold text-blue-500 hover:text-blue-700 underline underline-offset-2">
                ← Quay về trang chủ
            </a>
        </p>
    </main>

<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const img     = document.getElementById('previewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; preview.classList.remove('hidden'); };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    }
</script>
<script src="js/script.js"></script>
</body>
</html>
