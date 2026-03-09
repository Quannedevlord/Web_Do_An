<?php
include "config.php";

if(isset($_POST['register'])){
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (username,email,password) 
            VALUES ('$username','$email','$password')";

    if(mysqli_query($conn,$sql)){
        echo "Đăng ký thành công";
    }else{
        echo "Lỗi đăng ký";
    }

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
</head>
<body>

<h2>Đăng ký tài khoản</h2>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>
<br><br>

<input type="email" name="email" placeholder="Email" required>
<br><br>

<input type="password" name="password" placeholder="Password" required>
<br><br>

<button type="submit" name="register">Đăng ký</button>

</form>

</body>
</html>