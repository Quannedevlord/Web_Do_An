<?php
// chỉ admin mới xóa được
include "auth_admin.php";
include "config.php";

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "DELETE FROM songs WHERE id=$id";

if (mysqli_query($conn, $sql)) {
    echo "Xóa bài hát thành công";
} else {
    echo "Lỗi khi xóa";
}

mysqli_close($conn);
?>
