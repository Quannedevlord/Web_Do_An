<?php
// auth_check.php – Dùng trong các trang cần đăng nhập (không cần admin)
if (session_status()===PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    $_SESSION['flash']='Vui lòng đăng nhập để sử dụng tính năng này';
    $_SESSION['flash_type']='warning';
    header("Location: login.php"); exit;
}
?>
