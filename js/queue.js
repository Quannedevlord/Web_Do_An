/**
 * queue.js – Queue panel (trượt phải) khi bấm nút queue_music
 */
(function(){
    const panel=document.createElement('div');panel.id='queuePanel';
    panel.innerHTML=`
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-weight:700;font-size:15px;color:#1e293b;">🎵 Hàng chờ phát</h3>
            <button id="closeQueue" style="width:30px;height:30px;border-radius:50%;border:none;background:#f1f5f9;cursor:pointer;">✕</button>
        </div>
        <div id="queueList" style="overflow-y:auto;max-height:calc(100vh-220px);"></div>`;
    Object.assign(panel.style,{position:'fixed',right:'-320px',bottom:'108px',width:'300px',
        background:'rgba(255,255,255,0.97)',backdropFilter:'blur(16px)',
        borderRadius:'20px 0 0 20px',border:'1px solid rgba(255,255,255,0.5)',
        boxShadow:'-8px 0 32px rgba(0,0,0,0.12)',padding:'20px',
        zIndex:'100',transition:'right 0.3s ease',fontFamily:"'Spline Sans',sans-serif"});
    document.body.appendChild(panel);
    document.getElementById('closeQueue').onclick=close;
    document.addEventListener('click',e=>{if(!panel.contains(e.target)&&!e.target.closest('#queueBtn'))close();});
    function open(){panel.style.right='0';render();}
    function close(){panel.style.right='-320px';}
    function render(){
        const list=document.getElementById('queueList');if(!list)return;
        const songs=window.__playerSongs||[],cur=window.__playerCurrent??-1;
        if(!songs.length){list.innerHTML='<p style="text-align:center;padding:32px;color:#94a3b8;font-size:14px;">Chưa có bài nào</p>';return;}
        list.innerHTML=songs.map((s,i)=>`
            <div onclick="window.__player.selectSong(${i})"
                 style="display:flex;align-items:center;gap:10px;padding:8px;border-radius:12px;cursor:pointer;margin-bottom:3px;background:${i===cur?'rgba(66,167,240,0.1)':'transparent'};transition:background .15s;"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='${i===cur?'rgba(66,167,240,0.1)':'transparent'}'">
                <div style="width:34px;height:34px;border-radius:8px;background:url('images/${s.image||''}') center/cover #e2e8f0;flex-shrink:0;"></div>
                <div style="min-width:0;flex:1;">
                    <p style="font-size:12px;font-weight:600;color:${i===cur?'#42a7f0':'#1e293b'};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${s.title||''}</p>
                    <p style="font-size:10px;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${s.artist||''}</p>
                </div>
                ${i===cur?'<span style="color:#42a7f0;font-size:12px;">▶</span>':''}
            </div>`).join('');
    }
    window.__renderQueue=render;
    document.addEventListener('DOMContentLoaded',()=>{
        document.getElementById('queueBtn')?.addEventListener('click',e=>{e.stopPropagation();panel.style.right==='0px'?close():open();});
    });
})();
