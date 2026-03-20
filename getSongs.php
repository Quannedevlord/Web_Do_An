<?php
// 1. Khởi động session
session_start();

// 2. Kết nối database
include "config.php";

// 3. Tắt hiển thị lỗi để không hỏng giao diện
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// 4. LẤY NHỮNG GÌ BẢNG CÓ, KHÔNG ĐÒI HỎI THÊM! (Sắp xếp theo ID mới nhất)
$sql = "SELECT * FROM songs ORDER BY id DESC";

try {
    $result = mysqli_query($conn, $sql);
    $songs = array();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['id'] = (int)$row['id'];
            
            // Tự động bù đắp nếu database thiếu cột
            if (!isset($row['image'])) {
                $row['image'] = '';
            }
            if (!isset($row['genre'])) {
                $row['genre'] = 'other';
            }
            
            $songs[] = $row;
        }
    }

    echo json_encode([
        'isAdmin' => $isAdmin,
        'songs'   => $songs
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Nếu có lỗi, trả về danh sách rỗng thay vì sập 500
    echo json_encode([
        'isAdmin' => $isAdmin,
        'songs'   => []
    ]);
}

mysqli_close($conn);
?>