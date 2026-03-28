<?php
include "config.php";

// Kiểm tra session
if (session_status() === PHP_SESSION_NONE) session_start();
$isLoggedIn = isset($_SESSION['user']);
$userEmail  = ''; // email lấy từ DB theo user đang đăng nhập

// Nếu đã đăng nhập, lấy email thật từ database
if ($isLoggedIn) {
    $uid  = (int)$_SESSION['user_id'];
    $res  = mysqli_query($conn, "SELECT email FROM users WHERE id=$uid LIMIT 1");
    $urow = mysqli_fetch_assoc($res);
    $userEmail = $urow['email'] ?? '';
}

include "header.php";

$ok = false; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {

    // Bắt buộc phải đăng nhập
    if (!$isLoggedIn) {
        $err = 'Bạn cần đăng nhập để gửi tin nhắn.';
    } else {
        $name  = trim($_POST['name']    ?? '');
        $email = trim($_POST['email']   ?? '');
        $msg   = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($msg)) {
            $err = 'Vui lòng điền đầy đủ thông tin.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err = 'Email không hợp lệ.';
        } else {
            // Kiểm tra email phải tồn tại trong bảng users
            $esc   = mysqli_real_escape_string($conn, $email);
            $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$esc' LIMIT 1");
            if (!$check || mysqli_num_rows($check) === 0) {
                $err = 'Email này không tồn tại trong hệ thống. Vui lòng dùng email đã đăng ký tài khoản.';
            } else {
                // Tạo bảng nếu chưa có
                mysqli_query($conn, "CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    message TEXT NOT NULL,
                    is_read TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                $stmt = $conn->prepare("INSERT INTO contact_messages (name,email,message,is_read) VALUES (?,?,?,0)");
                $stmt->bind_param("sss", $name, $email, $msg);
                if ($stmt->execute()) {
                    $ok = true;
                    $_POST = [];
                    $userEmail = $email; // giữ email để không bị xóa
                } else {
                    $err = 'Lỗi khi gửi, vui lòng thử lại.';
                }
                $stmt->close();
            }
        }
    }
}
?>
<main class="max-w-4xl mx-auto px-6 py-12">
    <div class="text-center mb-12">
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Liên hệ</span>
        <h1 class="text-4xl font-bold text-slate-900 mt-2 mb-4">Liên hệ với chúng tôi</h1>
        <p class="text-slate-500 text-lg">Câu hỏi hoặc góp ý? Hãy nhắn cho chúng tôi!</p>
    </div>

    <div class="grid md:grid-cols-2 gap-8">

        <!-- Form gửi -->
        <div class="bg-white/70 rounded-3xl border border-white/60 shadow-sm p-8">
            <h2 class="text-lg font-bold text-slate-800 mb-6">Gửi tin nhắn</h2>

            <?php if (!$isLoggedIn): ?>
            <!-- Chưa đăng nhập: hiện thông báo yêu cầu đăng nhập -->
            <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-4 rounded-xl mb-5 flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">lock</span>
                <div>
                    <p class="font-semibold mb-1">Cần đăng nhập để gửi tin nhắn</p>
                    <p class="text-xs text-amber-600">Bạn phải đăng nhập bằng tài khoản đã đăng ký để sử dụng tính năng này.</p>
                    <div class="flex gap-2 mt-3">
                        <a href="login.php" class="inline-flex items-center gap-1 bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-blue-600 transition-colors">
                            <span class="material-symbols-outlined text-[14px]">login</span>Đăng nhập
                        </a>
                        <a href="register.php" class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 text-xs font-semibold px-4 py-2 rounded-xl hover:bg-slate-200 transition-colors">
                            Đăng ký
                        </a>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- Đã đăng nhập: hiện form -->
            <?php if ($ok): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">
                    ✓ Gửi thành công! Chúng tôi sẽ phản hồi sớm.
                </div>
            <?php endif; ?>
            <?php if ($err): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
                    ⚠ <?=htmlspecialchars($err)?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Họ tên</label>
                    <input type="text" name="name" required
                           value="<?=htmlspecialchars($_POST['name'] ?? '')?>"
                           placeholder="Nguyễn Văn A"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Email
                        <span class="text-xs text-slate-400 font-normal ml-1">(phải dùng email đã đăng ký tài khoản)</span>
                    </label>
                    <input type="email" name="email" required
                           value="<?=htmlspecialchars($ok ? $userEmail : ($_POST['email'] ?? $userEmail))?>"
                           placeholder="<?=htmlspecialchars($userEmail ?: 'your@email.com')?>"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/>
                    <?php if ($userEmail): ?>
                    <p class="text-xs text-slate-400 mt-1">💡 Email tài khoản của bạn: <strong><?=htmlspecialchars($userEmail)?></strong></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nội dung</label>
                    <textarea name="message" required rows="5" placeholder="Nhập nội dung tin nhắn..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm resize-none"><?=htmlspecialchars($_POST['message'] ?? '')?></textarea>
                </div>
                <button type="submit" name="send"
                        class="w-full bg-primary hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">send</span>Gửi tin nhắn
                </button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="space-y-5">
            <div class="bg-white/70 rounded-3xl border border-white/60 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Thông tin</h2>
                <?php foreach([
                    ['school','#42a7f0','Trường','Trường Đại học Kỹ thuật - Công nghệ Cần Thơ'],
                    ['email','#16a34a','Email','nhom4@musicweb.com'],
                    ['code','#7c3aed','GitHub','github.com/Quannedevlord/Web_Do_An'],
                ] as [$ic,$co,$la,$va]):?>
                <div class="flex items-start gap-3 mb-3">
                    <div class="size-9 rounded-xl flex items-center justify-center shrink-0" style="background:<?=$co?>20">
                        <span class="material-symbols-outlined text-[18px]" style="color:<?=$co?>"><?=$ic?></span>
                    </div>
                    <div><p class="text-sm font-medium text-slate-700"><?=$la?></p><p class="text-sm text-slate-500"><?=$va?></p></div>
                </div>
                <?php endforeach;?>
            </div>

            <div class="bg-white/70 rounded-3xl border border-white/60 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">FAQ</h2>
                <?php foreach([
                    ['Hỗ trợ định dạng nhạc nào?','Hiện tại hỗ trợ file MP3.'],
                    ['Cần đăng nhập để nghe không?','Không, khách vẫn nghe được. Chỉ cần đăng nhập để dùng Thư viện.'],
                    ['Playlist ở đâu?','Đăng nhập → Thư viện → Tạo playlist.'],
                    ['Tại sao cần đăng nhập để liên hệ?','Để đảm bảo tin nhắn từ người dùng thật và có thể phản hồi đúng.'],
                ] as [$q,$a]):?>
                <div class="border-b border-slate-100 pb-3 mb-3 last:border-0">
                    <p class="text-sm font-medium text-slate-700"><?=$q?></p>
                    <p class="text-xs text-slate-500 mt-1"><?=$a?></p>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>

    <div class="text-center mt-10">
        <a href="index.php" class="inline-flex items-center gap-2 bg-primary text-white font-semibold px-8 py-3 rounded-full shadow-lg hover:bg-blue-600 transition-colors">
            <span class="material-symbols-outlined text-[18px]">home</span>Về trang chủ
        </a>
    </div>
</main>
<?php include "footer.php"; ?>