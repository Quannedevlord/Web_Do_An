/**
 * main.js – Khởi tạo sau khi tất cả JS load xong
 */
document.addEventListener('DOMContentLoaded',()=>{
    Player.bindControls();
    window.__player=Player;
    if(document.getElementById('songList')) loadSongs();
    const msg=document.body.dataset.flash,type=document.body.dataset.flashType||'success';
    if(msg) showPopup(msg,type);
});
window.loadSongs=loadSongs;
window.showPopup=showPopup;
