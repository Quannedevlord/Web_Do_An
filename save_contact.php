<?php
// save_contact.php – Lưu tin nhắn liên hệ vào database
include "config.php";
header('Content-Type: application/json; charset=utf-8');

// Tạo bảng nếu chưa có
mysqli_query($conn,"CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$message = trim($_POST['message'] ?? '');

if(empty($name)||empty($email)||empty($message)){
    echo json_encode(['error'=>'Vui lòng điền đầy đủ thông tin'], JSON_UNESCAPED_UNICODE);
    exit;
}
if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    echo json_encode(['error'=>'Email không hợp lệ'], JSON_UNESCAPED_UNICODE);
    exit;
}

$name    = mysqli_real_escape_string($conn,$name);
$email   = mysqli_real_escape_string($conn,$email);
$message = mysqli_real_escape_string($conn,$message);

if(mysqli_query($conn,"INSERT INTO contact_messages (name,email,message) VALUES ('$name','$email','$message')")){
    echo json_encode(['success'=>true,'msg'=>'Gửi thành công! Chúng tôi sẽ phản hồi sớm.'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error'=>'Lỗi hệ thống, vui lòng thử lại.'], JSON_UNESCAPED_UNICODE);
}
mysqli_close($conn);
?>