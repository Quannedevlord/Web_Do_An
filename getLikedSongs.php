<?php
// getLikedSongs.php – Lấy danh sách bài đã tim
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$userId=(int)$_SESSION['user_id'];
$sql="SELECT s.*,1 AS liked FROM songs s
      INNER JOIN liked_songs l ON l.song_id=s.id AND l.user_id=$userId
      WHERE s.is_deleted=0 ORDER BY l.created_at DESC";
$result=mysqli_query($conn,$sql); $songs=[];
while($row=mysqli_fetch_assoc($result)){ $row['id']=(int)$row['id']; $songs[]=$row; }
echo json_encode(['songs'=>$songs],JSON_UNESCAPED_UNICODE);
mysqli_close($conn);
?>
