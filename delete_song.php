<?php
include "auth_admin.php"; include "config.php";
$id=isset($_GET['id'])?(int)$_GET['id']:0;
if(mysqli_query($conn,"UPDATE songs SET is_deleted=1 WHERE id=$id"))
    echo "Xóa bài hát thành công";
else echo "Lỗi khi xóa";
mysqli_close($conn);
?>
