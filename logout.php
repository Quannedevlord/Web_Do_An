<?php

// 1. khởi động session để có thể thao tác với nó
session_start();

// 2. xóa toàn bộ dữ liệu trong mảng $_SESSION
// (xóa thông tin user, user_id và các dữ liệu session khác)
$_SESSION = [];

// 3. xóa cookie session trên trình duyệt của người dùng
// (để đảm bảo session bị hủy hoàn toàn)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. hủy session trên server
session_destroy();

// 5. chuyển hướng về trang đăng nhập sau khi đăng xuất
header("Location: login.php");
exit;

?>
