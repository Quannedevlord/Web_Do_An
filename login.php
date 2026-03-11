<?php

// 1. mở session để lưu trạng thái đăng nhập
session_start();

// 2. kết nối database
include "config.php";

// 3. kiểm tra khi người dùng bấm nút login
if(isset($_POST['login'])){

    // 4. lấy dữ liệu từ form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 5. truy vấn tìm user theo email
    $sql = "SELECT * FROM users WHERE email='$email'";

    // 6. thực hiện truy vấn
    $result = mysqli_query($conn,$sql);


    // 7. kiểm tra user có tồn tại không
    if(mysqli_num_rows($result) > 0){

        // 8. lấy dữ liệu user
        $user = mysqli_fetch_assoc($result);

        // 9. kiểm tra mật khẩu
        if(password_verify($password,$user['password'])){

            // 10. tạo session khi đăng nhập thành công
            $_SESSION['user'] = $user['username'];

            echo "Đăng nhập thành công";

        }else{

            echo "Sai mật khẩu";

        }

    }else{

        echo "Email không tồn tại";

    }

}

?>


<h2>Login</h2>

<form method="POST">

<input type="email" name="email" placeholder="Email" required>
<br><br>

<input type="password" name="password" placeholder="Password" required>
<br><br>

<button type="submit" name="login">Đăng nhập</button>

</form>