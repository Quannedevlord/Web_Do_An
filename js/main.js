/**
 * main.js – Khởi tạo web
 * File này chạy sau khi tất cả file JS khác đã load
 * Thứ tự load: popup.js → ui.js → validation.js → player.js → songs.js → main.js
 */

document.addEventListener('DOMContentLoaded', () => {

    // gắn sự kiện vào các nút điều khiển player
    Player.bindControls();

    // đăng ký player vào biến global để onclick trong HTML dùng được
    window.__player = Player;

    // tải danh sách bài hát nếu trang có bảng songList
    if (document.getElementById('songList')) {
        loadSongs();
    }

    // hiển thị flash message từ PHP (login thành công, đăng ký thành công...)
    const flashMsg  = document.body.dataset.flash;
    const flashType = document.body.dataset.flashType || 'success';
    if (flashMsg) showPopup(flashMsg, flashType);
});

// expose ra ngoài để PHP page khác có thể gọi
window.loadSongs  = loadSongs;
window.showPopup  = showPopup;
