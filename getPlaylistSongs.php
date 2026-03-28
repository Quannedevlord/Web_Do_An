<?php
// getPlaylistSongs.php – Lấy bài hát trong playlist
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$playlistId=(int)($_GET['id']??0);
$userId=(int)$_SESSION['user_id'];
// Kiểm tra playlist thuộc về user này
$check=mysqli_query($conn,"SELECT name FROM playlists WHERE id=$playlistId AND user_id=$userId");
if(!mysqli_num_rows($check)){ echo json_encode(['error'=>'Không tìm thấy playlist']); exit; }
$plRow=mysqli_fetch_assoc($check);
$sql="SELECT s.* FROM songs s
      INNER JOIN playlist_songs ps ON ps.song_id=s.id
      WHERE ps.playlist_id=$playlistId AND s.is_deleted=0
      ORDER BY ps.added_at DESC";
$result=mysqli_query($conn,$sql); $songs=[];
while($row=mysqli_fetch_assoc($result)){ $row['id']=(int)$row['id']; $songs[]=$row; }
echo json_encode(['name'=>$plRow['name'],'songs'=>$songs],JSON_UNESCAPED_UNICODE);
mysqli_close($conn);
?>
