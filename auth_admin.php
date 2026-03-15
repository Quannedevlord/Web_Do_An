<?php
// ============================================================
// auth_admin.php – File bảo vệ trang admin
// include file này vào đầu mỗi trang chỉ admin mới vào được
// Cách dùng: include "auth_admin.php";
// ============================================================

// 1. khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. kiểm tra đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    // chưa đăng nhập → về trang login
    header("Location: login.php");
    exit;
}

// 3. kiểm tra có phải admin không
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // đã đăng nhập nhưng không phải admin → về trang chủ + báo lỗi
    $_SESSION['flash']      = 'Bạn không có quyền truy cập trang này';
    $_SESSION['flash_type'] = 'error';
    header("Location: index.php");
    exit;
}
// nếu qua được 2 kiểm tra trên → là admin, cho vào trang
?>
