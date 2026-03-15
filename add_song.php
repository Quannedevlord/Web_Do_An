<?php

// 1. kết nối database
include "config.php";

// 2. biến lưu thông báo kết quả
$thongBao = '';

// 3. kiểm tra khi người dùng bấm nút thêm bài hát
if (isset($_POST['add'])) {

    // 4. lấy dữ liệu từ form, trim() để xóa khoảng trắng thừa đầu/cuối
    $title  = trim($_POST['title']  ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $file   = trim($_POST['file']   ?? '');

    // 5. kiểm tra không được để trống
    if (empty($title) || empty($artist) || empty($file)) {
        $thongBao = 'error:Vui lòng điền đầy đủ thông tin';

    } else {
        // 6. xử lý upload hình ảnh (nếu có)
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            // lấy thông tin file ảnh
            $tmpPath   = $_FILES['image']['tmp_name'];
            $fileName  = $_FILES['image']['name'];
            $fileSize  = $_FILES['image']['size'];
            $fileExt   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // chỉ cho phép upload ảnh jpg, jpeg, png, webp
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($fileExt, $allowedExt)) {
                $thongBao = 'error:Chỉ được upload ảnh jpg, jpeg, png, webp';
            } elseif ($fileSize > 2 * 1024 * 1024) {
                // giới hạn dung lượng 2MB
                $thongBao = 'error:Ảnh không được vượt quá 2MB';
            } else {
                // tạo tên file mới để tránh trùng lặp
                $image    = time() . '_' . preg_replace('/\s+/', '_', $fileName);
                $savePath = 'images/' . $image;

                // tạo thư mục images nếu chưa có
                if (!is_dir('images')) mkdir('images', 0755, true);

                // lưu file ảnh vào thư mục images/
                if (!move_uploaded_file($tmpPath, $savePath)) {
                    $thongBao = 'error:Lỗi khi lưu ảnh, thử lại';
                    $image = '';
                }
            }
        }

        // 7. câu lệnh SQL thêm bài hát vào bảng songs (nếu chưa có lỗi ảnh)
        if (empty($thongBao)) {
            $stmt = $conn->prepare("INSERT INTO songs (title, artist, file, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $artist, $file, $image);

            // 8. thực hiện truy vấn
            if ($stmt->execute()) {
                $thongBao = 'success:Thêm bài hát thành công!';
            } else {
                $thongBao = 'error:Lỗi khi thêm bài hát, thử lại';
            }
        }
    }
}

// 8. tách loại thông báo và nội dung (success / error)
$loai = '';
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

        <!-- logo + tiêu đề -->
        <div class="flex items-center gap-3 mb-8">
            <div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">♪</div>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Chill Guy</h1>
                <p class="text-xs text-slate-500">Thêm bài hát mới</p>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-slate-800 mb-6">Thêm bài hát</h2>

        <!-- hiển thị thông báo thành công từ PHP -->
        <?php if ($loai === 'success'): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">
                ✓ <?= htmlspecialchars($noiDung) ?>
            </div>
        <?php endif; ?>

        <!-- hiển thị thông báo lỗi từ PHP -->
        <?php if ($loai === 'error'): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
                ⚠ <?= htmlspecialchars($noiDung) ?>
            </div>
        <?php endif; ?>

        <!-- form thêm bài hát -->
        <!-- enctype="multipart/form-data" bắt buộc phải có khi upload file -->
        <form method="POST" enctype="multipart/form-data" class="space-y-5">

            <!-- ô nhập tên bài hát -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tên bài hát</label>
                <input type="text" name="title" required
                       placeholder="VD: Jumpy Pants"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <!-- ô nhập tên ca sĩ -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ca sĩ</label>
                <input type="text" name="artist" required
                       placeholder="VD: FreePD"
                       value="<?= htmlspecialchars($_POST['artist'] ?? '') ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <!-- ô nhập tên file mp3 -->
            <!-- tên file phải khớp chính xác với file trong thư mục music/ -->
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

            <!-- ô upload hình ảnh bìa (không bắt buộc) -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Ảnh bìa <span class="text-slate-400 font-normal">(không bắt buộc)</span>
                </label>

                <!-- ô chọn file ảnh -->
                <input type="file" name="image" accept="image/*"
                       onchange="previewImage(this)"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 file:text-xs file:font-semibold cursor-pointer"/>
                <p class="text-xs text-slate-400 mt-1">Định dạng: jpg, png, webp – Tối đa 2MB</p>

                <!-- preview ảnh trước khi upload -->
                <div id="imagePreview" class="hidden mt-3">
                    <img id="previewImg" src="" alt="Preview"
                         class="w-24 h-24 rounded-xl object-cover border border-slate-200 shadow-sm"/>
                    <p class="text-xs text-slate-400 mt-1">Preview ảnh bìa</p>
                </div>
            </div>

            <!-- nút thêm bài hát -->
            <button type="submit" name="add"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200 transition-all duration-200">
                + Thêm bài hát
            </button>
        </form>

        <!-- link quay về trang chủ -->
        <p class="text-center text-sm text-slate-500 mt-6">
            <a href="index.php" class="font-semibold text-blue-500 hover:text-blue-700 underline underline-offset-2">
                ← Quay về trang chủ
            </a>
        </p>
    </main>

</body>
</html>
