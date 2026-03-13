<?php

// 1. kết nối database
include "config.php";

// 2. lấy id bài hát từ URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// 3. truy vấn lấy thông tin bài hát cần sửa
$sql = "SELECT * FROM songs WHERE id=$id";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);


// 4. kiểm tra khi bấm nút cập nhật
if(isset($_POST['update'])){

    // 5. lấy dữ liệu mới từ form
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $file = $_POST['file'];

    // 6. câu lệnh SQL cập nhật bài hát
    $update = "UPDATE songs
               SET title='$title', artist='$artist', file='$file'
               WHERE id=$id";

    // 7. thực hiện cập nhật
    if(mysqli_query($conn,$update)){
        echo "Cập nhật thành công";
    }else{
        echo "Lỗi cập nhật";
    }
}

?>

<h2>Sửa bài hát</h2>

<form method="POST">

<!-- hiển thị tên bài hát hiện tại -->
<input type="text" name="title" value="<?php echo $row['title']; ?>">
<br><br>

<!-- hiển thị ca sĩ -->
<input type="text" name="artist" value="<?php echo $row['artist']; ?>">
<br><br>

<!-- hiển thị tên file -->
<input type="text" name="file" value="<?php echo $row['file']; ?>">
<br><br>

<!-- nút cập nhật -->
<button type="submit" name="update">Cập nhật</button>

</form>