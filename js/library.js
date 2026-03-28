/**
 * library.js – Thư viện: Liked Songs, Playlists, Queue
 * UI giống Spotify: panel trượt, danh sách có thể phát
 */
const Library = (function(){

    // ============================================================
    // TOGGLE LIKE
    // ============================================================
    function toggleLike(){
        const song=window.__player.getCurrentSong();
        if(!song){showPopup('Chưa có bài đang phát','warning');return;}
        if(!window.isUserLogin&&!window.isLoggedIn){showPopup('Vui lòng đăng nhập','warning');return;}
        _doToggleLike(song.id);
    }

    function toggleLikeSong(songId,btnEl){
        if(!window.isUserLogin&&!window.isLoggedIn){showPopup('Vui lòng đăng nhập','warning');return;}
        _doToggleLike(songId,btnEl);
    }

    function _doToggleLike(songId,btnEl){
        fetch(`toggleLike.php?song_id=${songId}`)
            .then(r=>r.json())
            .then(data=>{
                showPopup(data.msg,data.liked?'success':'info');
                if(window.__playerSongs){
                    const s=window.__playerSongs.find(x=>x.id==songId);
                    if(s)s.liked=data.liked?1:0;
                }
                const likeBtn=document.getElementById('likeBtn');
                if(likeBtn){
                    const ic=likeBtn.querySelector('.material-symbols-outlined');
                    if(ic)ic.style.fontVariationSettings=data.liked?`'FILL' 1`:`'FILL' 0`;
                    likeBtn.style.color=data.liked?'#ef4444':'';
                }
                if(btnEl){
                    const ic=btnEl.querySelector('.material-symbols-outlined');
                    if(ic)ic.style.fontVariationSettings=data.liked?`'FILL' 1`:`'FILL' 0`;
                    if(ic)ic.style.color=data.liked?'#ef4444':'';
                }
            })
            .catch(()=>showPopup('Lỗi kết nối','error'));
    }

    // ============================================================
    // LIKED SONGS PANEL – giống Spotify
    // ============================================================
    function openLiked(){
        if(!window.isUserLogin&&!window.isLoggedIn){showPopup('Vui lòng đăng nhập','warning');return;}
        window.location.href='liked.php';
    }

    // ============================================================
    // PLAYLISTS
    // ============================================================
    function loadSidebarPlaylists(){
        if(!window.isUserLogin&&!window.isLoggedIn)return;
        fetch('getPlaylists.php').then(r=>r.json()).then(data=>{
            const c=document.getElementById('sidebarPlaylists');if(!c)return;
            c.innerHTML=(data.playlists||[]).map(pl=>`
                <a href="playlist_view.php?id=${pl.id}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm w-full text-left group">
                    <span class="material-symbols-outlined text-[20px] text-primary/60">playlist_play</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">${h(pl.name)}</p>
                        <p class="text-[10px] text-slate-400">${pl.song_count} bài</p>
                    </div>
                    <button onclick="event.stopPropagation();Library.deletePlaylist(${pl.id})"
                            class="opacity-0 group-hover:opacity-100 text-red-300 hover:text-red-500 px-1 text-xs transition-all">✕</button>
                </a>`).join('');
        });
    }

    function createPlaylist(){
        if(!window.isUserLogin&&!window.isLoggedIn){showPopup('Vui lòng đăng nhập','warning');return;}
        const name=prompt('Nhập tên playlist mới:');
        if(!name||!name.trim())return;
        fetch('createPlaylist.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name:name.trim()})})
            .then(r=>r.json())
            .then(data=>{
                if(data.success){showPopup('Tạo playlist "'+name+'" thành công','success');loadSidebarPlaylists();}
                else showPopup(data.error||'Lỗi tạo playlist','error');
            });
    }

    function deletePlaylist(id){
        if(!confirm('Xóa playlist này? Không thể hoàn tác.'))return;
        fetch(`deletePlaylist.php?id=${id}`).then(r=>r.json()).then(data=>{
            if(data.success){showPopup('Đã xóa playlist','info');loadSidebarPlaylists();}
        });
    }

    function openPlaylist(id,name){
        openLibPanel('🎵 '+name,'#42a7f0',()=>
            fetch(`getPlaylistSongs.php?id=${id}`).then(r=>r.json()).then(data=>{
                renderLibSongs(data.songs||[],'Playlist này chưa có bài.',id);
            })
        );
    }

    // ============================================================
    // ADD TO PLAYLIST POPUP
    // ============================================================
    function showAddToPlaylist(songId){
        if(!window.isUserLogin&&!window.isLoggedIn){showPopup('Vui lòng đăng nhập','warning');return;}
        fetch('getPlaylists.php').then(r=>r.json()).then(data=>{
            const playlists=data.playlists||[];
            if(!playlists.length){if(confirm('Chưa có playlist. Tạo mới?'))createPlaylist();return;}
            const old=document.getElementById('addToPLPop');if(old)old.remove();
            const pop=document.createElement('div');pop.id='addToPLPop';
            pop.innerHTML=`
                <div style="min-width:220px;">
                    <p style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:10px;text-transform:uppercase;letter-spacing:.05em;">Thêm vào playlist</p>
                    ${playlists.map(pl=>`
                        <button onclick="Library._addToPlaylist(${pl.id},${songId})"
                                style="display:block;width:100%;text-align:left;padding:9px 10px;border-radius:10px;font-size:13px;border:none;background:transparent;cursor:pointer;"
                                onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                            🎵 ${h(pl.name)} <span style="color:#94a3b8;font-size:11px;">(${pl.song_count})</span>
                        </button>`).join('')}
                    <hr style="border:none;border-top:1px solid #f1f5f9;margin:6px 0;"/>
                    <button onclick="Library.createPlaylist()"
                            style="display:block;width:100%;text-align:left;padding:9px 10px;border-radius:10px;font-size:13px;border:none;background:transparent;cursor:pointer;color:#42a7f0;"
                            onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='transparent'">
                        + Tạo playlist mới
                    </button>
                </div>`;
            Object.assign(pop.style,{position:'fixed',top:'50%',left:'50%',transform:'translate(-50%,-50%)',
                background:'#fff',borderRadius:'16px',padding:'14px',boxShadow:'0 20px 60px rgba(0,0,0,0.2)',
                zIndex:'400',fontFamily:"'Spline Sans',sans-serif"});
            document.body.appendChild(pop);
            setTimeout(()=>document.addEventListener('click',function h(){pop.remove();document.removeEventListener('click',h);},true),100);
        });
    }

    function _addToPlaylist(playlistId,songId){
        const pop=document.getElementById('addToPLPop');if(pop)pop.remove();
        fetch('addToPlaylist.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({playlist_id:playlistId,song_id:songId})})
            .then(r=>r.json())
            .then(data=>{showPopup(data.msg||data.error,'success');loadSidebarPlaylists();});
    }

    // ============================================================
    // LIB PANEL – giống Spotify sidebar phải trượt ra
    // ============================================================
    let libPanel=null;
    function openLibPanel(title,accentColor,loadFn){
        if(libPanel){libPanel.remove();libPanel=null;}

        libPanel=document.createElement('div');
        libPanel.innerHTML=`
            <!-- Overlay mờ -->
            <div id="libOverlay" style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:149;"></div>
            <!-- Panel -->
            <div id="libPanelBox" style="
                position:fixed;right:-480px;top:0;bottom:108px;width:420px;max-width:90vw;
                background:#1a1a2e;border-left:1px solid rgba(255,255,255,0.1);
                overflow-y:auto;z-index:150;transition:right 0.35s cubic-bezier(.4,0,.2,1);
                font-family:'Spline Sans',sans-serif;display:flex;flex-direction:column;">

                <!-- Header -->
                <div style="padding:28px 24px 16px;background:linear-gradient(180deg,${accentColor}44 0%,#1a1a2e 100%);flex-shrink:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <button id="closeLibPanel" style="color:rgba(255,255,255,0.7);background:none;border:none;cursor:pointer;font-size:22px;line-height:1;">✕</button>
                    </div>
                    <!-- Cover -->
                    <div style="display:flex;align-items:flex-end;gap:20px;">
                        <div style="width:120px;height:120px;border-radius:8px;background:${accentColor};
                             display:flex;align-items:center;justify-content:center;font-size:48px;flex-shrink:0;
                             box-shadow:0 8px 24px rgba(0,0,0,0.5);">
                            ${accentColor==='#7c3aed'?'❤️':'🎵'}
                        </div>
                        <div>
                            <p style="font-size:11px;color:rgba(255,255,255,0.6);margin-bottom:4px;text-transform:uppercase;letter-spacing:.1em;">Playlist</p>
                            <h2 style="font-size:28px;font-weight:700;color:#fff;margin-bottom:8px;">${title}</h2>
                            <div id="libSongCount" style="font-size:13px;color:rgba(255,255,255,0.5);">Đang tải...</div>
                        </div>
                    </div>
                    <!-- Play all button -->
                    <div style="margin-top:20px;display:flex;align-items:center;gap:12px;">
                        <button id="libPlayAll" style="width:48px;height:48px;border-radius:50%;background:#1db954;border:none;cursor:pointer;
                                display:flex;align-items:center;justify-content:center;font-size:20px;
                                box-shadow:0 4px 16px rgba(29,185,84,0.4);transition:transform .1s;"
                                onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                            ▶
                        </button>
                        <button id="libShuffleAll" style="color:rgba(255,255,255,0.6);background:none;border:none;cursor:pointer;font-size:22px;"
                                title="Phát ngẫu nhiên" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">⇄</button>
                    </div>
                </div>

                <!-- Table header -->
                <div style="padding:8px 24px;border-bottom:1px solid rgba(255,255,255,0.1);
                     display:grid;grid-template-columns:32px 1fr 32px;gap:8px;flex-shrink:0;">
                    <span style="font-size:11px;color:rgba(255,255,255,0.4);text-align:center;">#</span>
                    <span style="font-size:11px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:.05em;">Tiêu đề</span>
                    <span></span>
                </div>

                <!-- Song list -->
                <div id="libSongList" style="flex:1;padding:8px 12px;">
                    <p style="text-align:center;padding:32px;color:rgba(255,255,255,0.3);">⏳ Đang tải...</p>
                </div>
            </div>`;

        Object.assign(libPanel.style,{position:'fixed',inset:'0',zIndex:'149',pointerEvents:'auto'});
        document.body.appendChild(libPanel);

        // Animate in
        setTimeout(()=>{
            const box=document.getElementById('libPanelBox');
            if(box)box.style.right='0';
        },10);

        document.getElementById('closeLibPanel')?.addEventListener('click',closeLibPanel);
        document.getElementById('libOverlay')?.addEventListener('click',closeLibPanel);

        // Load data
        loadFn();
    }

    function closeLibPanel(){
        if(!libPanel)return;
        const box=document.getElementById('libPanelBox');
        if(box){
            box.style.right='-480px';
            setTimeout(()=>{if(libPanel){libPanel.remove();libPanel=null;}},350);
        } else {libPanel.remove();libPanel=null;}
    }

    // ============================================================
    // RENDER SONGS trong panel – giống Spotify
    // ============================================================
    let currentPanelSongs=[];
    let currentPlaylistId=null;

    function renderLibSongs(songs,emptyMsg,playlistId){
        currentPanelSongs=songs;
        currentPlaylistId=playlistId;
        const list=document.getElementById('libSongList');
        const cnt=document.getElementById('libSongCount');
        if(cnt)cnt.textContent=songs.length+' bài hát';
        if(!list)return;

        if(!songs.length){
            list.innerHTML=`<p style="text-align:center;padding:40px;color:rgba(255,255,255,0.3);font-size:14px;">${emptyMsg}</p>`;
            return;
        }

        list.innerHTML=songs.map((s,i)=>`
            <div class="lib-song-row" data-index="${i}" onclick="Library._playSongFromPanel(${i})"
                 style="display:grid;grid-template-columns:32px 1fr ${playlistId?'32px':''};gap:8px;align-items:center;
                        padding:8px 12px;border-radius:8px;cursor:pointer;transition:background .15s;margin-bottom:2px;"
                 onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='transparent'">
                <span style="font-size:13px;color:rgba(255,255,255,0.4);text-align:center;">${i+1}</span>
                <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                    <div style="width:40px;height:40px;border-radius:6px;background:url('images/${s.image||''}') center/cover #2d2d44;flex-shrink:0;"></div>
                    <div style="min-width:0;">
                        <p style="font-size:14px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${h(s.title)}</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.5);">${h(s.artist)}</p>
                    </div>
                </div>
                ${playlistId?`
                <button onclick="event.stopPropagation();Library._removeFromPlaylist(${playlistId},${s.id},this)"
                        title="Xóa khỏi playlist"
                        style="color:rgba(255,255,255,0.3);background:none;border:none;cursor:pointer;font-size:16px;padding:4px;border-radius:4px;opacity:0;transition:opacity .15s;"
                        onmouseover="this.style.color='#ef4444';this.style.opacity='1'"
                        onmouseout="this.style.color='rgba(255,255,255,0.3)';this.style.opacity='0'">✕</button>`:''}
            </div>`).join('');

        // Show remove buttons on row hover
        list.querySelectorAll('.lib-song-row').forEach(row=>{
            const btn=row.querySelector('button');
            if(!btn)return;
            row.addEventListener('mouseenter',()=>btn.style.opacity='1');
            row.addEventListener('mouseleave',()=>btn.style.opacity='0');
        });

        // Play all button
        document.getElementById('libPlayAll')?.addEventListener('click',()=>{
            window.__player.playList(songs,0);
            closeLibPanel();
        });

        // Shuffle all
        document.getElementById('libShuffleAll')?.addEventListener('click',()=>{
            const idx=Math.floor(Math.random()*songs.length);
            window.__player.playList(songs,idx);
            closeLibPanel();
        });
    }

    function _playSongFromPanel(idx){
        if(!currentPanelSongs.length)return;
        window.__player.playList(currentPanelSongs,idx);
    }

    function _removeFromPlaylist(playlistId,songId,btn){
        if(!confirm('Xóa bài này khỏi playlist?'))return;
        fetch('removeFromPlaylist.php',{method:'POST',headers:{'Content-Type':'application/json'},
              body:JSON.stringify({playlist_id:playlistId,song_id:songId})})
            .then(r=>r.json())
            .then(data=>{
                if(data.success){
                    showPopup(data.msg,'info');
                    btn.closest('.lib-song-row').style.opacity='0';
                    setTimeout(()=>btn.closest('.lib-song-row').remove(),300);
                    // Cập nhật count
                    currentPanelSongs=currentPanelSongs.filter(s=>s.id!=songId);
                    const cnt=document.getElementById('libSongCount');
                    if(cnt)cnt.textContent=currentPanelSongs.length+' bài hát';
                    loadSidebarPlaylists();
                } else showPopup(data.error,'error');
            });
    }

    // ============================================================
    // PLAYLIST BUTTON (nút trong hero banner)
    // ============================================================
    function openCurrentPlaylist(){
        openLibPanel('🎵 Danh sách phát','#42a7f0',()=>{
            const songs=window.__playerSongs||[];
            renderLibSongs(songs,'Chưa có bài hát nào trong danh sách.',null);
        });
    }

    // ============================================================
    // HELPERS
    // ============================================================
    function h(s){const d=document.createElement('div');d.textContent=s||'';return d.innerHTML;}
    function esc(s){return(s||'').replace(/'/g,"\\'");}

    // Init
    document.addEventListener('DOMContentLoaded',()=>{
        loadSidebarPlaylists();
        document.getElementById('playlistBtn')?.addEventListener('click',openCurrentPlaylist);
    });

    return{openLiked,toggleLike,toggleLikeSong,createPlaylist,deletePlaylist,
           openPlaylist,showAddToPlaylist,_addToPlaylist,_playSongFromPanel,
           _removeFromPlaylist,loadSidebarPlaylists};
})();
