<?php
include "auth_admin.php"; include "config.php";
$id=isset($_GET['id'])?(int)$_GET['id']:0;
$r=mysqli_query($conn,"SELECT * FROM songs WHERE id=$id AND is_deleted=0");
$row=mysqli_fetch_assoc($r);
if(!$row){header("Location: index.php");exit;}
$thongBao='';
if(isset($_POST['update'])){
    $title=trim($_POST['title']??''); $artist=trim($_POST['artist']??'');
    $genre=trim($_POST['genre']??'other');
    $file=$row['file']; // giữ file cũ mặc định
    $image=$row['image']??''; // giữ ảnh cũ mặc định

    if(empty($title)||empty($artist)){
        $thongBao='error:Vui lòng điền đầy đủ';
    } else {

        // --- Upload file MP3 mới (nếu có) ---
        if(isset($_FILES['file_mp3'])&&$_FILES['file_mp3']['error']===UPLOAD_ERR_OK){
            $mExt=strtolower(pathinfo($_FILES['file_mp3']['name'],PATHINFO_EXTENSION));
            if(in_array($mExt,['mp3','wav','ogg','m4a'])){
                if(!is_dir('music')) mkdir('music',0755,true);
                $newFile=preg_replace('/\s+/','_',$_FILES['file_mp3']['name']);
                if(move_uploaded_file($_FILES['file_mp3']['tmp_name'],'music/'.$newFile)){
                    $file=$newFile;
                }
            } else {
                $thongBao='error:File nhạc không hợp lệ (chỉ mp3, wav, ogg, m4a)';
            }
        }

        // --- Upload ảnh bìa mới (nếu có) ---
        if(isset($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){
            $ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
            if(in_array($ext,['jpg','jpeg','png','webp'])){
                if(!is_dir('images')) mkdir('images',0755,true);
                $tmpPath=$_FILES['image']['tmp_name'];
                $info=getimagesize($tmpPath);
                $mime=$info['mime'];

                if($mime==='image/jpeg') $src=imagecreatefromjpeg($tmpPath);
                elseif($mime==='image/png') $src=imagecreatefrompng($tmpPath);
                elseif($mime==='image/webp') $src=imagecreatefromwebp($tmpPath);
                else $src=null;

                if($src){
                    $origW=imagesx($src); $origH=imagesy($src); $maxW=500;
                    if($origW>$maxW){ $newW=$maxW; $newH=intval($origH*$maxW/$origW); }
                    else { $newW=$origW; $newH=$origH; }
                    $resized=imagecreatetruecolor($newW,$newH);
                    imagecopyresampled($resized,$src,0,0,0,0,$newW,$newH,$origW,$origH);
                    $imgName=time().'_'.$id.'.jpg';
                    imagejpeg($resized,'images/'.$imgName,75);
                    imagedestroy($src); imagedestroy($resized);
                    $image=$imgName;
                } else {
                    $thongBao='error:Không thể xử lý ảnh';
                }
            } else {
                $thongBao='error:Ảnh không hợp lệ (chỉ jpg, png, webp)';
            }
        }

        // --- Cập nhật DB nếu không có lỗi ---
        if(empty($thongBao)){
            $stmt=$conn->prepare("UPDATE songs SET title=?,artist=?,file=?,image=?,genre=? WHERE id=?");
            $stmt->bind_param("sssssi",$title,$artist,$file,$image,$genre,$id);
            if($stmt->execute()){
                $_SESSION['flash']='Cập nhật thành công!';
                $_SESSION['flash_type']='success';
                header("Location: index.php");exit;
            } else {
                $thongBao='error:Lỗi cập nhật';
            }
        }
    }
}
[$loai,$noiDung]=$thongBao?explode(':',$thongBao,2):['',''];
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Sửa bài hát</title><script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style>body{font-family:'Spline Sans',sans-serif;}</style></head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4">
<main class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-100">
<div class="flex items-center gap-3 mb-6">
<div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">♪</div>
<div><h1 class="text-lg font-bold">Chill Wave</h1><p class="text-xs text-slate-500">Sửa bài hát</p></div>
<span class="ml-auto text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">👑 Admin</span></div>
<h2 class="text-2xl font-bold mb-6">Sửa bài hát</h2>
<?php if($loai==='error'):?>
<div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">⚠ <?=htmlspecialchars($noiDung)?></div>
<?php endif;?>

<form method="POST" enctype="multipart/form-data" class="space-y-4">

    <!-- Tên bài hát -->
    <div><label class="block text-sm font-medium text-slate-700 mb-1">Tên bài hát</label>
    <input type="text" name="title" required value="<?=htmlspecialchars($row['title'])?>"
    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/></div>

    <!-- Ca sĩ -->
    <div><label class="block text-sm font-medium text-slate-700 mb-1">Ca sĩ</label>
    <input type="text" name="artist" required value="<?=htmlspecialchars($row['artist'])?>"
    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm"/></div>

    <!-- File MP3 -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">File MP3</label>
        <p class="text-xs text-slate-400 mb-2">Hiện tại: <span class="text-blue-500"><?=htmlspecialchars($row['file'])?></span></p>
        <input type="file" name="file_mp3" accept=".mp3,.wav,.ogg,.m4a"
        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 file:text-xs cursor-pointer"/>
        <p class="text-xs text-slate-400 mt-1">Để trống nếu không muốn thay đổi</p>
    </div>

    <!-- Ảnh bìa -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Ảnh bìa</label>
        <?php if(!empty($row['image'])):?>
        <div class="mb-2 flex items-center gap-3">
            <img src="images/<?=htmlspecialchars($row['image'])?>" alt="Ảnh hiện tại"
            class="size-14 rounded-lg object-cover border border-slate-200"/>
            <span class="text-xs text-slate-400">Ảnh hiện tại</span>
        </div>
        <?php endif;?>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 file:text-xs cursor-pointer"/>
        <p class="text-xs text-slate-400 mt-1">Để trống nếu không muốn thay đổi. Ảnh sẽ được tự động nén.</p>
    </div>

    <!-- Thể loại -->
    <div><label class="block text-sm font-medium text-slate-700 mb-1">Thể loại</label>
    <select name="genre" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-400 bg-white">
    <?php foreach(['lofi'=>'Lofi','pop'=>'Pop','ballad'=>'Ballad','Edm'=>'EDM','other'=>'Khác'] as $v=>$l):?>
    <option value="<?=$v?>" <?=($row['genre']??'other')===$v?'selected':''?>><?=$l?></option>
    <?php endforeach;?></select></div>

    <button type="submit" name="update" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg transition-all">💾 Cập nhật</button>
</form>
<p class="text-center text-sm mt-4"><a href="index.php" class="text-blue-500 hover:underline">← Quay về trang chủ</a></p>
</main></body></html>
