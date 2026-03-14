<?php

// 1. kết nối database
include "config.php";

// 2. thiết lập header để trình duyệt hiểu đây là dữ liệu JSON (có hỗ trợ tiếng Việt)
header('Content-Type: application/json; charset=utf-8');

// 3. tạo câu lệnh SQL lấy tất cả bài hát
// ORDER BY created_at DESC = bài mới thêm sẽ hiển thị trên cùng
$sql = "SELECT id, title, artist, file, image FROM songs ORDER BY created_at DESC";

// 4. thực hiện truy vấn database
$result = mysqli_query($conn, $sql);

// 5. tạo mảng rỗng để lưu trữ dữ liệu bài hát
$data = array();

// 6. kiểm tra database có dữ liệu không
if (mysqli_num_rows($result) > 0) {

    // 7. lặp qua từng bài hát và thêm vào mảng
    while ($row = mysqli_fetch_assoc($result)) {

        // ép kiểu id về số nguyên cho đúng chuẩn JSON (tránh trả về dạng string "1")
        $row['id'] = (int)$row['id'];

        // thêm bài hát vào mảng data
        $data[] = $row;
    }

} else {
    // nếu chưa có bài hát nào trong database
    $data = array("error" => "Chưa có bài hát nào");
}

// 8. trả về dữ liệu dưới dạng JSON
// JSON_UNESCAPED_UNICODE giúp tiếng Việt hiển thị đúng thay vì bị encode thành \uXXXX
echo json_encode($data, JSON_UNESCAPED_UNICODE);

// 9. đóng kết nối database
mysqli_close($conn);

?>
