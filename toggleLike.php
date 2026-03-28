<?php
// toggleLike.php – Bấm tim thêm/bỏ liked song
include "auth_check.php";
include "config.php";
header('Content-Type: application/json; charset=utf-8');
$songId = isset($_GET['song_id']) ? (int)$_GET['song_id'] : 0;
$userId = (int)$_SESSION['user_id'];
if (!$songId||!$userId){ echo json_encode(['error'=>'Invalid']); exit; }
// Kiểm tra đã like chưa
$check = mysqli_query($conn,"SELECT id FROM liked_songs WHERE user_id=$userId AND song_id=$songId");
if (mysqli_num_rows($check)>0) {
    // Đã like → bỏ like
    mysqli_query($conn,"DELETE FROM liked_songs WHERE user_id=$userId AND song_id=$songId");
    echo json_encode(['liked'=>false,'msg'=>'Đã bỏ khỏi Liked Songs']);
} else {
    // Chưa like → thêm
    mysqli_query($conn,"INSERT INTO liked_songs (user_id,song_id) VALUES ($userId,$songId)");
    echo json_encode(['liked'=>true,'msg'=>'Đã thêm vào Liked Songs ♥']);
}
mysqli_close($conn);
?>
