<?php
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$data=json_decode(file_get_contents('php://input'),true);
$name=trim($data['name']??'');
$userId=(int)$_SESSION['user_id'];
if(empty($name)){echo json_encode(['error'=>'Tên playlist không được rỗng']);exit;}

// Kiểm tra bảng playlists có tồn tại không
$tableCheck=mysqli_query($conn,"SHOW TABLES LIKE 'playlists'");
if(!$tableCheck||mysqli_num_rows($tableCheck)===0){
    // Tạo bảng nếu chưa có
    mysqli_query($conn,"CREATE TABLE IF NOT EXISTS playlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

$name=mysqli_real_escape_string($conn,$name);
if(mysqli_query($conn,"INSERT INTO playlists (user_id,name) VALUES ($userId,'$name')")){
    $id=(int)mysqli_insert_id($conn);
    echo json_encode(['success'=>true,'id'=>$id,'name'=>$name]);
}else{
    echo json_encode(['error'=>'Lỗi tạo playlist: '.mysqli_error($conn)]);
}
mysqli_close($conn);
?>
