<?php

// 1. khởi động session để kiểm tra đăng nhập và role
session_start();

// 2. kết nối database
include "config.php";

// 3. thiết lập header JSON
header('Content-Type: application/json; charset=utf-8');

// 4. phân quyền:
// isAdmin = true  → tài khoản admin → JS hiện nút Sửa/Xóa
// isAdmin = false → khách / user thường → JS ẩn nút Sửa/Xóa
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// 5. lấy tất cả bài hát, bài mới nhất lên đầu
$sql    = "SELECT id, title, artist, file, image FROM songs ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$songs  = array();

// 6. lặp qua từng bài hát
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id'] = (int)$row['id'];
        $songs[]   = $row;
    }
}

// 7. trả về JSON gồm:
// isAdmin → JS dùng để quyết định hiện/ẩn nút Sửa/Xóa
// songs   → danh sách bài hát
echo json_encode([
    'isAdmin' => $isAdmin,
    'songs'   => $songs
], JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>
