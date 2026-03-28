<?php
// config.php – Kết nối database
// LOCALHOST: bỏ comment 4 dòng dưới và comment 4 dòng hosting
// $host="localhost"; $user="root"; $password=""; $database="music_web";

// INFINITYFREE HOSTING:
$host     = "sql103.infinityfree.com";
$user     = "if0_41444302";
$password = "HoangPhat2k6";
$database = "if0_41444302_music_web";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) die(json_encode(["error"=>"Kết nối thất bại: ".mysqli_connect_error()]));
mysqli_set_charset($conn, "utf8mb4");
?>
