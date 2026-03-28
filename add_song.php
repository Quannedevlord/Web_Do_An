<?php
include "auth_admin.php"; include "config.php";
$thongBao='';

if(isset($_POST['add'])){
    $titles=$_POST['title']??[]; $artists=$_POST['artist']??[];
    $files=$_POST['file']??[];   $genres=$_POST['genre']??[];
    $ok=0; $fail=0;

    // Kiểm tra bảng songs có cột is_deleted không
    $hasIsDeleted = false;
    $colCheck = mysqli_query($conn, "SHOW COLUMNS FROM songs LIKE 'is_deleted'");
    if($colCheck && mysqli_num_rows($colCheck) > 0) $hasIsDeleted = true;

    // Kiểm tra có cột genre không
    $hasGenre = false;
    $genreCheck = mysqli_query($conn, "SHOW COLUMNS FROM songs LIKE 'genre'");
    if($genreCheck && mysqli_num_rows($genreCheck) > 0) $hasGenre = true;

    foreach($titles as $i=>$title){
        $title=trim($title); $artist=trim($artists[$i]??'');
        $file=trim($files[$i]??''); $genre=trim($genres[$i]??'other');
        if(empty($title)||empty($artist)||empty($file)){$fail++;continue;}

        // Upload file nhạc MP3
        $musicKey = 'music_'.$i;
        if(isset($_FILES[$musicKey])&&$_FILES[$musicKey]['error']===UPLOAD_ERR_OK){
            $mExt=strtolower(pathinfo($_FILES[$musicKey]['name'],PATHINFO_EXTENSION));
            if(in_array($mExt,['mp3','wav','ogg','m4a'])){
                if(!is_dir('music'))mkdir('music',0755,true);
                $file=preg_replace('/\s+/','_',$_FILES[$musicKey]['name']);
                if(!move_uploaded_file($_FILES[$musicKey]['tmp_name'],'music/'.$file))
                    $file=basename($_FILES[$musicKey]['name']); // fallback
            }
        }

       // Upload ảnh
$image = ''; $key = 'image_'.$i;
if(isset($_FILES[$key])&&$_FILES[$key]['error']===UPLOAD_ERR_OK){
    $ext = strtolower(pathinfo($_FILES[$key]['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['jpg','jpeg','png','webp'])){
        
        // Tạo thư mục nếu chưa có
        if(!is_dir('images')) mkdir('images',0755,true);
        
        $image = time().'_'.$i.'_'.preg_replace('/\s+/','_',$_FILES[$key]['name']);
        $destPath = 'images/'.$image;
        $tmpPath  = $_FILES[$key]['tmp_name'];
        
        // Resize & nén ảnh về < 500KB
        $info = getimagesize($tmpPath);
        $mime = $info['mime'];
        
        if($mime === 'image/jpeg') $src = imagecreatefromjpeg($tmpPath);
        elseif($mime === 'image/png') $src = imagecreatefrompng($tmpPath);
        elseif($mime === 'image/webp') $src = imagecreatefromwebp($tmpPath);
        else $src = null;
        
        if($src){
            $origW = imagesx($src);
            $origH = imagesy($src);
            $maxW  = 500;
            
            if($origW > $maxW){
                $newW = $maxW;
                $newH = intval($origH * $maxW / $origW);
            } else {
                $newW = $origW;
                $newH = $origH;
            }
            
            $resized = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($resized,$src,0,0,0,0,$newW,$newH,$origW,$origH);
            imagejpeg($resized, $destPath, 75); // nén 75% chất lượng
            imagedestroy($src);
            imagedestroy($resized);
            
            // Đổi tên file thành .jpg
            $image = pathinfo($image, PATHINFO_FILENAME).'.jpg';
            rename($destPath, 'images/'.$image);
        } else {
            // Fallback: upload thẳng nếu không xử lý được
            if(!move_uploaded_file($tmpPath, $destPath)) $image='';
        }
    }
}

        // Build INSERT tùy theo cột database có sẵn
        if($hasGenre && $hasIsDeleted){
            $stmt=$conn->prepare("INSERT INTO songs (title,artist,file,image,genre,is_deleted) VALUES (?,?,?,?,?,0)");
            $stmt->bind_param("sssss",$title,$artist,$file,$image,$genre);
        } elseif($hasGenre){
            $stmt=$conn->prepare("INSERT INTO songs (title,artist,file,image,genre) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss",$title,$artist,$file,$image,$genre);
        } else {
            $stmt=$conn->prepare("INSERT INTO songs (title,artist,file,image) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss",$title,$artist,$file,$image);
        }
        $stmt->execute()?$ok++:$fail++;
    }
    $thongBao=$ok>0?"success:Thêm thành công $ok bài!".($fail>0?" ($fail bài lỗi)":''):'error:Lỗi khi thêm bài hát';
}
[$loai,$noiDung]=$thongBao?explode(':',$thongBao,2):['',''];
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Thêm bài hát – Chill Wave</title><script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style>body{font-family:'Spline Sans',sans-serif;}</style></head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4">
<main class="w-full max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-8 border border-slate-100 my-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white">♪</div>
        <div><h1 class="text-lg font-bold">Chill Wave</h1><p class="text-xs text-slate-500">Thêm bài hát</p></div>
        <span class="ml-auto text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">👑 Admin</span>
    </div>
    <h2 class="text-2xl font-bold mb-1">Thêm bài hát</h2>
    <p class="text-sm text-slate-500 mb-6">Bấm "+ Thêm dòng" để thêm nhiều bài cùng lúc</p>

    <?php if($loai==='success'):?>
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5">✓ <?=htmlspecialchars($noiDung)?></div>
    <?php elseif($loai==='error'):?>
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">⚠ <?=htmlspecialchars($noiDung)?></div>
    <?php endif;?>

    <form method="POST" enctype="multipart/form-data">
        <div class="hidden md:grid grid-cols-12 gap-2 mb-2 text-xs font-bold text-slate-400 uppercase px-1">
            <div class="col-span-3">Tên bài hát *</div><div class="col-span-3">Ca sĩ *</div>
            <div class="col-span-3">File MP3 *</div><div class="col-span-2">Thể loại</div><div class="col-span-1"></div>
        </div>
        <div id="songRows" class="space-y-4">
            <div class="song-row p-4 bg-slate-50 rounded-2xl">
                <div class="grid grid-cols-12 gap-2 mb-3">
                    <input type="text" name="title[]" placeholder="Tên bài hát" required class="col-span-12 md:col-span-3 px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"/>
                    <input type="text" name="artist[]" placeholder="Ca sĩ" required class="col-span-12 md:col-span-3 px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"/>
                    <div class="col-span-12 md:col-span-3 relative">
                        <label for="music_input_0"
                               class="flex items-center gap-2 w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm cursor-pointer bg-white hover:border-blue-400 transition-colors text-slate-400"
                               id="musicLabel_0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/></svg>
                            <span id="musicName_0" class="truncate">Chọn file nhạc...</span>
                        </label>
                        <input type="file" id="music_input_0" name="music_0" accept=".mp3,.wav,.ogg,.m4a" class="hidden"
                               onchange="pickMusic(this,0)"/>
                        <input type="hidden" name="file[]" id="fileVal_0"/>
                    </div>
                    <select name="genre[]" class="col-span-10 md:col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-400 bg-white">
                        <option value="lofi">Lofi</option><option value="pop">Pop</option>
                        <option value="ballad">Ballad</option><option value="edm">Edm</option>
                        <option value="other">Khác</option>
                    </select>
                    <div class="col-span-2 md:col-span-1 flex items-center justify-center"><span class="text-slate-300 text-xs">—</span></div>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Ảnh bìa (không bắt buộc, tối đa 2MB):</p>
                    <input type="file" name="image_0" accept="image/*" onchange="previewImg(this,'p0')"
                           class="text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 cursor-pointer"/>
                    <img id="p0" class="hidden mt-2 w-14 h-14 rounded-xl object-cover border border-slate-200"/>
                </div>
            </div>
        </div>

        <button type="button" id="addRowBtn"
                class="mt-3 w-full border-2 border-dashed border-slate-200 hover:border-blue-400 text-slate-400 hover:text-blue-500 text-sm font-medium py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
            + Thêm dòng bài hát
        </button>

        <div class="flex gap-3 mt-6">
            <button type="submit" name="add" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg transition-all">💾 Lưu tất cả bài hát</button>
            <a href="index.php" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium flex items-center">← Quay về</a>
        </div>
    </form>
</main>
<script>
let rc=1;
function previewImg(input,pid){
    const img=document.getElementById(pid);
    if(input.files&&input.files[0]&&img){
        const r=new FileReader();
        r.onload=e=>{img.src=e.target.result;img.classList.remove('hidden');};
        r.readAsDataURL(input.files[0]);
    }
}
function pickMusic(input, idx){
    if(!input.files||!input.files[0])return;
    const fname = input.files[0].name;
    const label = document.getElementById('musicName_'+idx);
    const hidden = document.getElementById('fileVal_'+idx);
    if(label) label.textContent = fname;
    if(hidden) hidden.value = fname;
    // Khi chọn file nhạc, gắn input file vào label để submit cùng form
    const container = input.closest('div');
    if(container) container.querySelector('label').style.borderColor='#42a7f0';
}
document.getElementById('addRowBtn').addEventListener('click',()=>{
    const i=rc++,pid='p'+i;
    const row=document.createElement('div');
    row.className='song-row p-4 bg-slate-50 rounded-2xl border-t-2 border-white';
    row.innerHTML=`
        <div class="grid grid-cols-12 gap-2 mb-3">
            <input type="text" name="title[]" placeholder="Tên bài hát" required class="col-span-12 md:col-span-3 px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"/>
            <input type="text" name="artist[]" placeholder="Ca sĩ" required class="col-span-12 md:col-span-3 px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"/>
            <div class="col-span-12 md:col-span-3 relative">
            <label for="music_input_${i}"
                   class="flex items-center gap-2 w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm cursor-pointer bg-white hover:border-blue-400 transition-colors text-slate-400"
                   id="musicLabel_${i}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/></svg>
                <span id="musicName_${i}" class="truncate">Chọn file nhạc...</span>
            </label>
            <input type="file" id="music_input_${i}" name="music_${i}" accept=".mp3,.wav,.ogg,.m4a" class="hidden"
                   onchange="pickMusic(this,${i})"/>
            <input type="hidden" name="file[]" id="fileVal_${i}"/>
        </div>
            <select name="genre[]" class="col-span-10 md:col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-400 bg-white">
                <option value="lofi">Lofi</option><option value="pop">Pop</option>
                <option value="ballad">Ballad</option><option value="phonk">Phonk</option>
                <option value="other">Khác</option>
            </select>
            <button type="button" onclick="this.closest('.song-row').remove()"
                    class="col-span-2 md:col-span-1 text-red-400 hover:text-red-600 text-xl font-bold flex items-center justify-center h-10">✕</button>
        </div>
        <div>
            <p class="text-xs text-slate-400 mb-1">Ảnh bìa (không bắt buộc):</p>
            <input type="file" name="image_${i}" accept="image/*" onchange="previewImg(this,'${pid}')"
                   class="text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 cursor-pointer"/>
            <img id="${pid}" class="hidden mt-2 w-14 h-14 rounded-xl object-cover border border-slate-200"/>
        </div>`;
    document.getElementById('songRows').appendChild(row);
    row.querySelector('input[name="title[]"]').focus();
});
</script>
</body></html>
