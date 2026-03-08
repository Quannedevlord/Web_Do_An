<?php
<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "music_web";

$conn = mysqli_connect($host,$user,$password,$database);

if(!$conn){
    die("Kết nối thất bại");
}
?>