<?php

// 1. kết nối database
include "config.php";


// 2. kiểm tra khi người dùng bấm nút thêm
if(isset($_POST['add'])){

    // 3. lấy dữ liệu từ form
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $file = $_POST['file'];

    // 4. câu lệnh SQL thêm bài hát vào bảng songs
    $sql = "INSERT INTO songs (title,artist,file)
            VALUES ('$title','$artist','$file')";

    // 5. thực hiện truy vấn
    if(mysqli_query($conn,$sql)){
        echo "Thêm bài hát thành công";
    }else{
        echo "Lỗi khi thêm bài hát";
    }
}

?>

<h2>Thêm bài hát</h2>

<form method="POST">

<!-- nhập tên bài hát -->
<input type="text" name="title" placeholder="Tên bài hát" required>
<br><br>

<!-- nhập tên ca sĩ -->
<input type="text" name="artist" placeholder="Ca sĩ" required>
<br><br>

<!-- nhập tên file nhạc -->
<input type="text" name="file" placeholder="Tên file mp3" required>
<br><br>

<!-- nút thêm -->
<button type="submit" name="add">Thêm bài hát</button>

</form>