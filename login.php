<?php
session_start(); include "config.php";
$error='';
if(isset($_POST['login'])){
    $email=trim($_POST['email']??''); $password=$_POST['password']??'';
    if(empty($email)||empty($password)) $error='Vui lòng nhập đầy đủ';
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Email không hợp lệ';
    else{
        $stmt=$conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s",$email); $stmt->execute();
        $result=$stmt->get_result();
        if($result->num_rows>0){
            $user=$result->fetch_assoc();
            if(password_verify($password,$user['password'])){
                $_SESSION['user']=$user['username']; $_SESSION['user_id']=$user['id']; $_SESSION['role']=$user['role']??'user';
                $_SESSION['flash']=$_SESSION['role']==='admin'?'Chào mừng Admin '.$user['username'].' 👑':'Chào mừng '.$user['username'].'!';
                $_SESSION['flash_type']='success';
                header("Location: index.php"); exit;
            } else $error='Sai mật khẩu';
        } else $error='Email không tồn tại';
    }
}
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Đăng nhập – Chill Wave</title><script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style>body{font-family:'Spline Sans',sans-serif;}</style></head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4">
<main class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-100">
<div class="flex items-center gap-3 mb-8">
<div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">♪</div>
<div><h1 class="text-lg font-bold">Chill Wave</h1><p class="text-xs text-slate-500">Premium Listening</p></div></div>
<h2 class="text-2xl font-bold mb-1">Chào mừng trở lại</h2>
<p class="text-sm text-slate-500 mb-6">Nhập thông tin để đăng nhập.</p>
<?php if($error):?><div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">⚠ <?=htmlspecialchars($error)?></div><?php endif;?>
<form method="POST" data-purpose="login-form" class="space-y-5">
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
<input id="email" name="email" type="email" required value="<?=htmlspecialchars($_POST['email']??'')?>" placeholder="your@email.com"
class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
<input id="password" name="password" type="password" required placeholder="••••••••"
class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/></div>
<button type="submit" name="login" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg transition-all">Đăng nhập</button>
</form>
<p class="text-center text-sm text-slate-500 mt-6">Chưa có tài khoản? <a href="register.php" class="font-semibold text-blue-500 hover:underline">Đăng ký ngay</a></p>
<div class="mt-4 text-center"><a href="index.php" class="text-sm text-slate-400 hover:text-slate-600">← Về trang chủ không cần đăng nhập</a></div>
</main>
<script src="js/popup.js"></script><script src="js/validation.js"></script>
</body></html>
