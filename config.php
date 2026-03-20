<?php
$host     = "sql102.infinityfree.com"; 
$user     = "if0_41436128";
$password = "HoangPhat2k6"; 
$database = "if0_41436128_webmusic"; 

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error()); 
}

mysqli_set_charset($conn, "utf8mb4");
?>