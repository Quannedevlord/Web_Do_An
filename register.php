<?php
session_start(); include "config.php";
$error='';
if(isset($_POST['register'])){
    $username=trim($_POST['username']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??'';
    if(strlen($username)<3) $error='Username phải từ 3 ký tự';
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Email không hợp lệ';
    elseif(strlen($password)<6) $error='Mật khẩu phải từ 6 ký tự';
    else{
        $check=$conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s",$email); $check->execute(); $check->store_result();
        if($check->num_rows>0) $error='Email đã được đăng ký';
        else{
            $hash=password_hash($password,PASSWORD_DEFAULT);
            $stmt=$conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
            $stmt->bind_param("sss",$username,$email,$hash);
            if($stmt->execute()){$_SESSION['flash']='Đăng ký thành công! Vui lòng đăng nhập.';$_SESSION['flash_type']='success';header("Location: login.php");exit;}
            else $error='Lỗi hệ thống';
        }
    }
}
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Đăng ký – Chill Wave</title><script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style>body{font-family:'Spline Sans',sans-serif;}</style></head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4">
<main class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-100">
<div class="flex items-center gap-3 mb-8">
<div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">♪</div>
<div><h1 class="text-lg font-bold">Chill Wave</h1><p class="text-xs text-slate-500">Tạo tài khoản mới</p></div></div>
<h2 class="text-2xl font-bold mb-1">Đăng ký</h2>
<p class="text-sm text-slate-500 mb-6">Điền thông tin để tạo tài khoản.</p>
<?php if($error):?><div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">⚠ <?=htmlspecialchars($error)?></div><?php endif;?>
<form method="POST" data-purpose="register-form" class="space-y-5">
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
<input name="username" type="text" required value="<?=htmlspecialchars($_POST['username']??'')?>" placeholder="Tối thiểu 3 ký tự"
class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
<input name="email" type="email" required value="<?=htmlspecialchars($_POST['email']??'')?>" placeholder="your@email.com"
class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
<input name="password" type="password" required placeholder="Tối thiểu 6 ký tự"
class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/></div>
<button type="submit" name="register" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg transition-all">Tạo tài khoản</button>
</form>
<p class="text-center text-sm text-slate-500 mt-6">Đã có tài khoản? <a href="login.php" class="font-semibold text-blue-500 hover:underline">Đăng nhập</a></p>
</main>
<script src="js/popup.js"></script><script src="js/validation.js"></script>
</body></html>
