<?php
// addToPlaylist.php – Thêm bài vào playlist
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$data=json_decode(file_get_contents('php://input'),true);
$playlistId=(int)($data['playlist_id']??0);
$songId=(int)($data['song_id']??0);
$userId=(int)$_SESSION['user_id'];
if(!$playlistId||!$songId){ echo json_encode(['error'=>'Invalid']); exit; }
$check=mysqli_query($conn,"SELECT id FROM playlists WHERE id=$playlistId AND user_id=$userId");
if(!mysqli_num_rows($check)){ echo json_encode(['error'=>'Không có quyền']); exit; }
if(mysqli_query($conn,"INSERT IGNORE INTO playlist_songs (playlist_id,song_id) VALUES ($playlistId,$songId)"))
    echo json_encode(['success'=>true,'msg'=>'Đã thêm vào playlist']);
else echo json_encode(['error'=>'Lỗi thêm bài']);
mysqli_close($conn);
?>
