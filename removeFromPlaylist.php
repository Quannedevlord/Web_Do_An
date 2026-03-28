<?php
// removeFromPlaylist.php – Xóa bài khỏi playlist
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$data=json_decode(file_get_contents('php://input'),true);
$playlistId=(int)($data['playlist_id']??0);
$songId=(int)($data['song_id']??0);
$userId=(int)$_SESSION['user_id'];
// Kiểm tra playlist thuộc về user này
$check=mysqli_query($conn,"SELECT id FROM playlists WHERE id=$playlistId AND user_id=$userId");
if(!$check||!mysqli_num_rows($check)){echo json_encode(['error'=>'Không có quyền']);exit;}
if(mysqli_query($conn,"DELETE FROM playlist_songs WHERE playlist_id=$playlistId AND song_id=$songId"))
    echo json_encode(['success'=>true,'msg'=>'Đã xóa bài khỏi playlist']);
else echo json_encode(['error'=>'Lỗi xóa bài']);
mysqli_close($conn);
?>
