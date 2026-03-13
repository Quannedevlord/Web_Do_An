<?php

// 1. kết nối database
include "config.php";

// 2. lấy id bài hát từ URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// 3. câu lệnh SQL xóa bài hát
$sql = "DELETE FROM songs WHERE id=$id";

// 4. thực hiện truy vấn
if(mysqli_query($conn,$sql)){
    echo "Xóa bài hát thành công";
}else{
    echo "Lỗi khi xóa";
}

?>