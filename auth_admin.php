<?php
if (session_status()===PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
if (!isset($_SESSION['role'])||$_SESSION['role']!=='admin') {
    $_SESSION['flash']='Bạn không có quyền truy cập'; $_SESSION['flash_type']='error';
    header("Location: index.php"); exit;
}
?>
