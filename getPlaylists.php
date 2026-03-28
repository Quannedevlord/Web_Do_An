<?php
// getPlaylists.php – Lấy danh sách playlist của user
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$userId=(int)$_SESSION['user_id'];
$sql="SELECT p.*, COUNT(ps.id) as song_count
      FROM playlists p
      LEFT JOIN playlist_songs ps ON ps.playlist_id=p.id
      WHERE p.user_id=$userId GROUP BY p.id ORDER BY p.created_at DESC";
$result=mysqli_query($conn,$sql); $playlists=[];
while($row=mysqli_fetch_assoc($result)){ $row['id']=(int)$row['id']; $playlists[]=$row; }
echo json_encode(['playlists'=>$playlists],JSON_UNESCAPED_UNICODE);
mysqli_close($conn);
?>
