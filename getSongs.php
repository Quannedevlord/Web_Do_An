<?php
session_start(); include "config.php";
ini_set('display_errors',0); error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
$isAdmin  = isset($_SESSION['role']) && $_SESSION['role']==='admin';
$userId   = $_SESSION['user_id'] ?? 0;
try {
    // Lấy bài hát kèm trạng thái liked của user
    if ($userId) {
        $sql = "SELECT s.*, IF(l.id IS NOT NULL,1,0) AS liked
                FROM songs s
                LEFT JOIN liked_songs l ON l.song_id=s.id AND l.user_id=$userId
                WHERE s.is_deleted=0 ORDER BY s.id DESC";
    } else {
        $sql = "SELECT *, 0 AS liked FROM songs WHERE is_deleted=0 ORDER BY id DESC";
    }
    $result=mysqli_query($conn,$sql);
    $songs=[];
    while($row=mysqli_fetch_assoc($result)){
        $row['id']=(int)$row['id'];
        $row['liked']=(int)($row['liked']??0);
        $row['image']=$row['image']??'';
        $row['genre']=$row['genre']??'other';
        $songs[]=$row;
    }
    echo json_encode(['isAdmin'=>$isAdmin,'songs'=>$songs,'userId'=>$userId],JSON_UNESCAPED_UNICODE);
} catch(Exception $e){ echo json_encode(['isAdmin'=>$isAdmin,'songs'=>[],'userId'=>0]); }
mysqli_close($conn);
?>
