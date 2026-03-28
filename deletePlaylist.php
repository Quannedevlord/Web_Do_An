<?php
// deletePlaylist.php – Xóa playlist
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$id=(int)($_GET['id']??0);
$userId=(int)$_SESSION['user_id'];
if(mysqli_query($conn,"DELETE FROM playlists WHERE id=$id AND user_id=$userId"))
    echo json_encode(['success'=>true]);
else echo json_encode(['error'=>'Lỗi xóa playlist']);
mysqli_close($conn);
?>
