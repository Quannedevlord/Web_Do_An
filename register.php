<?php
// 1. khởi động session
session_start();

// 2. kết nối database
include "config.php";

// 3. biến lưu thông báo lỗi
$error = '';

// 4. kiểm tra khi người dùng bấm nút đăng ký
if (isset($_POST['register'])) {

    // 5. lấy dữ liệu từ form, trim() để xóa khoảng trắng thừa
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    // 6. kiểm tra dữ liệu nhập phía server
    if (strlen($username) < 3) {
        $error = 'Username phải từ 3 ký tự trở lên';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';

    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải từ 6 ký tự trở lên';

    } else {
        // 7. kiểm tra email đã được đăng ký chưa
        // dùng prepared statement để tránh SQL injection
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // email đã tồn tại → báo lỗi
            $error = 'Email này đã được đăng ký, vui lòng dùng email khác';

        } else {
            // 8. mã hóa mật khẩu bằng bcrypt trước khi lưu vào database
            // KHÔNG BAO GIỜ lưu mật khẩu dạng text thuần
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // 9. thêm user mới vào database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hash);

            if ($stmt->execute()) {
                // 10. đăng ký thành công – lưu flash message
                $_SESSION['flash']      = 'Đăng ký thành công! Vui lòng đăng nhập.';
                $_SESSION['flash_type'] = 'success';

                // 11. chuyển hướng sang trang đăng nhập
                header("Location: login.php");
                exit;

            } else {
                $error = 'Lỗi hệ thống, vui lòng thử lại sau';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Đăng ký – Chill Guy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style> body { font-family: 'Spline Sans', sans-serif; } </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4">

    <main class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-100">

        <!-- logo -->
        <div class="flex items-center gap-3 mb-8">
            <div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">♪</div>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Chill Guy</h1>
                <p class="text-xs text-slate-500">Tạo tài khoản mới</p>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-slate-800 mb-1">Đăng ký</h2>
        <p class="text-sm text-slate-500 mb-6">Điền thông tin để tạo tài khoản.</p>

        <!-- hiển thị lỗi từ server nếu có -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
                ⚠ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- form đăng ký -->
        <!-- data-purpose="register-form" để JS nhận biết và gắn validation -->
        <form method="POST" data-purpose="register-form" class="space-y-5">

            <!-- ô nhập username -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                <input name="username" type="text" required
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Tên người dùng (tối thiểu 3 ký tự)"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <!-- ô nhập email -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input name="email" type="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="your@email.com"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <!-- ô nhập mật khẩu -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
                <input name="password" type="password" required
                       placeholder="Tối thiểu 6 ký tự"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
                <p class="text-xs text-slate-400 mt-1">Mật khẩu ít nhất 6 ký tự</p>
            </div>

            <!-- nút đăng ký -->
            <button type="submit" name="register"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200 transition-all duration-200">
                Tạo tài khoản
            </button>
        </form>

        <!-- link sang trang đăng nhập -->
        <p class="text-center text-sm text-slate-500 mt-6">
            Đã có tài khoản?
            <a href="login.php" class="font-semibold text-blue-500 hover:text-blue-700 underline underline-offset-2">
                Đăng nhập
            </a>
        </p>
    </main>

<!-- nhúng JS để chạy form validation phía client -->
<script src="js/popup.js"></script>
<script src="js/validation.js"></script>
</body>
</html>
