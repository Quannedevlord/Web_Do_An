/**
 * player.js – Audio Player hoàn chỉnh
 * Fix: prev không random, shuffle/repeat loại trừ nhau
 * Fix: stream qua stream.php để phát nhanh, hỗ trợ Range Request
 */
const Player = (function(){
    let songs=[],current=-1,isPlaying=false,isShuffled=false,repeatMode=0;
    let history=[];
    const audio=new Audio(),dom={};

    // ✅ Preload metadata thôi, không tải cả file
    audio.preload = 'metadata';

    function resolveDOM(){
        ['playBtn','prevBtn','nextBtn','shuffleBtn','repeatBtn','progressBar','progressTrack',
         'currentTime','totalTime','volumeTrack','volumeBar','playerTitle','playerArtist','playerCover']
        .forEach(id=>dom[id]=document.getElementById(id));
    }

    function fmt(s){
        if(isNaN(s)||s===Infinity)return'0:00';
        return Math.floor(s/60)+':'+Math.floor(s%60).toString().padStart(2,'0');
    }

    function updateUI(){
        const s=songs[current];if(!s)return;
        if(dom.playerTitle) dom.playerTitle.textContent=s.title||'Unknown';
        if(dom.playerArtist)dom.playerArtist.textContent=s.artist||'Unknown';
        if(dom.playerCover) dom.playerCover.style.backgroundImage=s.image
            ?`url('images/${s.image}')`:`url('https://placehold.co/56x56/42a7f0/fff?text=♪')`;
        if(dom.playBtn){
            const ic=dom.playBtn.querySelector('.material-symbols-outlined');
            if(ic)ic.textContent=isPlaying?'pause':'play_arrow';
        }
        if(dom.shuffleBtn)dom.shuffleBtn.style.color=isShuffled?'#42a7f0':'';
        if(dom.repeatBtn){
            const ic=dom.repeatBtn.querySelector('.material-symbols-outlined');
            dom.repeatBtn.style.color=repeatMode>0?'#42a7f0':'';
            if(ic)ic.textContent=repeatMode===2?'repeat_one':'repeat';
        }
        document.querySelectorAll('.song-row').forEach((row,i)=>{
            row.classList.toggle('playing',i===current);
            const n=row.querySelector('.song-num');
            if(n)n.innerHTML=(i===current&&isPlaying)?'<span class="eq-icon">▶</span>':(i+1);
        });
        const likeBtn=document.getElementById('likeBtn');
        if(likeBtn&&songs[current]){
            const liked=songs[current].liked;
            const ic=likeBtn.querySelector('.material-symbols-outlined');
            if(ic)ic.style.fontVariationSettings=liked?`'FILL' 1`:`'FILL' 0`;
            likeBtn.style.color=liked?'#ef4444':'';
        }
        window.__playerCurrent=current;
        if(window.__renderQueue)window.__renderQueue();
    }

    function playCurrent(){
        const s=songs[current];if(!s)return;
        window.__playerCurrent=current;
        window.__playerSongs=songs;

        // ✅ Trỏ thẳng file MP3 (bỏ stream.php vì InfinityFree chặn 302)
        // Nếu file là URL đầy đủ (http...) thì dùng thẳng, còn lại thêm music/
        const filename = s.file || s.file_path || s.filename || '';
        audio.src = filename.startsWith('http') ? filename : `music/${filename}`;

        // Hiện loading trong title
        if(dom.playerTitle) dom.playerTitle.textContent = '⏳ ' + (s.title || 'Unknown');

        audio.oncanplay = () => {
            if(dom.playerTitle) dom.playerTitle.textContent = s.title || 'Unknown';
            audio.play()
                .then(()=>{isPlaying=true;updateUI();showPopup(`▶ ${s.title} – ${s.artist}`,'info');})
                .catch(()=>showPopup('Không thể phát bài này','error'));
            audio.oncanplay = null;
        };

        audio.load();
    }

    function toggle(){
        if(!songs.length){showPopup('Chưa có bài hát nào','warning');return;}
        if(current===-1){current=0;history=[0];playCurrent();return;}
        if(isPlaying){audio.pause();isPlaying=false;}
        else{audio.play();isPlaying=true;}
        updateUI();
    }

    function prev(){
        if(!songs.length)return;
        if(isShuffled){
            if(history.length>1){
                history.pop();
                current=history[history.length-1];
            } else {
                current=Math.floor(Math.random()*songs.length);
                history=[current];
            }
        } else {
            current=(current-1+songs.length)%songs.length;
            history.push(current);
        }
        playCurrent();
    }

    function next(){
        if(!songs.length)return;
        if(isShuffled){
            current=Math.floor(Math.random()*songs.length);
        } else {
            current=(current+1)%songs.length;
        }
        history.push(current);
        if(history.length>50)history.shift();
        playCurrent();
    }

    function selectSong(i){
        current=i;
        history.push(i);
        playCurrent();
    }

    function toggleShuffle(){
        isShuffled=!isShuffled;
        if(isShuffled && repeatMode>0) repeatMode=0;
        history=[current];
        updateUI();
        showPopup(isShuffled?'🔀 Shuffle bật':'Shuffle tắt','info');
    }

    function toggleRepeat(){
        repeatMode=(repeatMode+1)%3;
        if(repeatMode>0 && isShuffled){
            isShuffled=false;
            history=[current];
        }
        updateUI();
        showPopup(['Repeat tắt','🔁 Lặp tất cả','🔂 Lặp 1 bài'][repeatMode],'info');
    }

    function setVolume(v){
        audio.volume=Math.max(0,Math.min(1,v));
        if(dom.volumeBar)dom.volumeBar.style.width=(audio.volume*100)+'%';
    }

    audio.addEventListener('timeupdate',()=>{
        if(!audio.duration)return;
        const p=(audio.currentTime/audio.duration)*100;
        if(dom.progressTrack)dom.progressTrack.style.width=p+'%';
        if(dom.currentTime)dom.currentTime.textContent=fmt(audio.currentTime);
        if(dom.totalTime)dom.totalTime.textContent=fmt(audio.duration);
    });

    audio.addEventListener('ended',()=>{
        if(repeatMode===2){
            audio.currentTime=0; audio.play();
        } else if(repeatMode===1){
            next();
        } else {
            if(isShuffled){
                next();
            } else if(current<songs.length-1){
                next();
            } else {
                isPlaying=false; updateUI();
            }
        }
    });

    audio.addEventListener('error',()=>{
        showPopup('Lỗi phát nhạc – kiểm tra file!','error');
        isPlaying=false; updateUI();
    });

    function bindControls(){
        resolveDOM();
        if(dom.playBtn)   dom.playBtn.addEventListener('click',toggle);
        if(dom.prevBtn)   dom.prevBtn.addEventListener('click',prev);
        if(dom.nextBtn)   dom.nextBtn.addEventListener('click',next);
        if(dom.shuffleBtn)dom.shuffleBtn.addEventListener('click',toggleShuffle);
        if(dom.repeatBtn) dom.repeatBtn.addEventListener('click',toggleRepeat);
        if(dom.progressBar){
            dom.progressBar.addEventListener('click',e=>{
                if(!audio.duration)return;
                const r=dom.progressBar.getBoundingClientRect();
                audio.currentTime=((e.clientX-r.left)/r.width)*audio.duration;
            });
        }
        if(dom.volumeTrack){
            dom.volumeTrack.addEventListener('click',e=>{
                const r=dom.volumeTrack.getBoundingClientRect();
                setVolume((e.clientX-r.left)/r.width);
            });
        }
    }

    return{
        selectSong,toggle,prev,next,toggleShuffle,toggleRepeat,bindControls,setVolume,
        getCurrentSong(){return songs[current]||null;},
        getCurrentIndex(){return current;},
        setSongs(arr){
            songs.splice(0,songs.length,...arr);
            window.__playerSongs=songs;
            window.__playerCurrent=current;
        },
        playList(arr,startIndex=0){
            songs.splice(0,songs.length,...arr);
            window.__playerSongs=songs;
            current=startIndex;
            history=[startIndex];
            playCurrent();
        },
    };
})();