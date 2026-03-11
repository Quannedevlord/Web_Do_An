<?php

// 1. kết nối database
include "config.php";


// 2. tạo câu lệnh SQL lấy tất cả bài hát
$sql = "SELECT * FROM songs";


// 3. thực hiện truy vấn database
$result = mysqli_query($conn, $sql);


// 4. kiểm tra database có dữ liệu không
if(mysqli_num_rows($result) > 0){

    // 5. lặp qua từng bài hát
    while($row = mysqli_fetch_assoc($result)){

        // lấy dữ liệu từ từng dòng
        $title = $row['title'];
        $artist = $row['artist'];
        $file = $row['file'];

        // hiển thị dữ liệu
        echo "Tên bài hát: " . $title . "<br>";
        echo "Ca sĩ: " . $artist . "<br>";

        // phát nhạc bằng HTML audio
        echo "<audio controls>";
        echo "<source src='music/".$file."' type='audio/mpeg'>";
        echo "</audio>";

        echo "<hr>";
    }

}else{

    // nếu chưa có bài hát
    echo "Chưa có bài hát nào";

}

?>