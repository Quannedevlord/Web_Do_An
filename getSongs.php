<?php

// 1. kết nối database
include "config.php";

// 2. tạo câu lệnh SQL lấy tất cả bài hát
$sql = "SELECT * FROM songs";

// 3. thực hiện truy vấn database
$result = mysqli_query($conn, $sql);

// 4. tạo mảng để lưu trữ dữ liệu
$data = array();

// 5. lặp qua từng bài hát và thêm vào mảng
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
}else{
    // nếu chưa có bài hát
    $data = array("error" => "Chưa có bài hát nào");
}

// 6. trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($data);

// 7. đóng kết nối database
mysqli_close($conn);

?>