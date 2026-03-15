<?php
// 1. khởi động session để lưu trạng thái đăng nhập
session_start();

// 2. kết nối database
include "config.php";

// 3. biến lưu thông báo lỗi
$error = '';

// 4. kiểm tra khi người dùng bấm nút đăng nhập
if (isset($_POST['login'])) {

    // 5. lấy dữ liệu từ form
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    // 6. kiểm tra dữ liệu nhập
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ email và mật khẩu';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';

    } else {
        // 7. dùng prepared statement để tránh SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // 8. kiểm tra email có tồn tại không
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // 9. kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {

                // 10. lưu thông tin vào session
                $_SESSION['user']    = $user['username'];
                $_SESSION['user_id'] = $user['id'];

                // 11. lưu role vào session để phân quyền
                // nếu cột role chưa có → mặc định là 'user'
                $_SESSION['role'] = $user['role'] ?? 'user';

                // 12. flash message theo role
                if ($_SESSION['role'] === 'admin') {
                    $_SESSION['flash']      = 'Chào mừng Admin ' . $user['username'] . '!';
                } else {
                    $_SESSION['flash']      = 'Chào mừng ' . $user['username'] . '!';
                }
                $_SESSION['flash_type'] = 'success';

                // 13. chuyển hướng về trang chủ
                header("Location: index.php");
                exit;

            } else {
                $error = 'Sai mật khẩu';
            }
        } else {
            $error = 'Email không tồn tại';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Đăng nhập – Chill Guy</title>
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
                <p class="text-xs text-slate-500">Premium Listening</p>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-slate-800 mb-1">Chào mừng trở lại</h2>
        <p class="text-sm text-slate-500 mb-6">Nhập thông tin để đăng nhập.</p>

        <!-- hiển thị lỗi từ server -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
                ⚠ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- form đăng nhập -->
        <form method="POST" data-purpose="login-form" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input id="email" name="email" type="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="your@email.com"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="text-sm font-medium text-slate-700">Mật khẩu</label>
                </div>
                <input id="password" name="password" type="password" required
                       placeholder="••••••••"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm"/>
            </div>

            <button type="submit" name="login"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200 transition-all duration-200 mt-2">
                Đăng nhập
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Chưa có tài khoản?
            <a href="register.php" class="font-semibold text-blue-500 hover:text-blue-700 underline underline-offset-2">
                Đăng ký ngay
            </a>
        </p>

        <!-- về trang chủ không cần đăng nhập -->
        <div class="mt-4 text-center">
            <a href="index.php" class="text-sm text-slate-400 hover:text-slate-600 transition-colors">
                ← Về trang chủ không cần đăng nhập
            </a>
        </div>
    </main>

<script src="js/popup.js"></script>
<script src="js/validation.js"></script>
</body>
</html>
