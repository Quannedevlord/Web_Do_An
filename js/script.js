
// Menu toggle + Back to top + Login validation + Popup
// Audio player + Fetch songs

(function () {
  'use strict';

  function $(sel, ctx = document) { return ctx.querySelector(sel); }
  function $all(sel, ctx = document) { return Array.from(ctx.querySelectorAll(sel)); }

  /* ---------------- MENU MOBILE ---------------- */

  (function () {

    const menuBtn = $('#menuBtn');
    const sidebar = $('#sidebar') || document.querySelector('aside');

    if (!menuBtn || !sidebar) return;

    menuBtn.addEventListener('click', function () {

      if (sidebar.style.display === 'none') {
        sidebar.style.display = 'block';
      } else {
        sidebar.style.display = 'none';
      }

    });

  })();


  /* ---------------- BACK TO TOP ---------------- */

  (function () {

    let topBtn = $('#topBtn');

    if (!topBtn) {

      topBtn = document.createElement("button");
      topBtn.id = "topBtn";
      topBtn.innerText = "↑";

      topBtn.style.position = "fixed";
      topBtn.style.right = "20px";
      topBtn.style.bottom = "80px";
      topBtn.style.padding = "10px";
      topBtn.style.display = "none";

      document.body.appendChild(topBtn);
    }

    window.addEventListener("scroll", function () {

      if (window.scrollY > 200) {
        topBtn.style.display = "block";
      } else {
        topBtn.style.display = "none";
      }

    });

    topBtn.addEventListener("click", function () {

      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });

    });

  })();


  /* ---------------- POPUP ---------------- */

  function showPopup(text) {

    const box = document.createElement("div");

    box.innerText = text;

    box.style.position = "fixed";
    box.style.top = "20px";
    box.style.right = "20px";
    box.style.background = "#16a34a";
    box.style.color = "#fff";
    box.style.padding = "10px 15px";
    box.style.borderRadius = "6px";

    document.body.appendChild(box);

    setTimeout(() => {
      box.remove();
    }, 2000);

  }


  /* ---------------- LOGIN VALIDATION ---------------- */

  (function () {

    const form = document.querySelector('form[data-purpose="login-form"]');

    if (!form) return;

    const user = $("#username");
    const pass = $("#password");

    form.addEventListener("submit", function (e) {

      if (user.value.trim() === "" || pass.value.trim() === "") {

        e.preventDefault();
        showPopup("Vui lòng nhập đầy đủ");

        return;
      }

      if (pass.value.length < 6) {

        e.preventDefault();
        showPopup("Password phải >= 6 ký tự");

      }

    });

  })();


  /* ---------------- AUDIO PLAYER ---------------- */

  let mainAudio = $("#mainAudio");

  if (!mainAudio) {

    mainAudio = document.createElement("audio");
    mainAudio.id = "mainAudio";

    document.body.appendChild(mainAudio);

  }


  window.selectSong = function (file, title) {

    mainAudio.src = file;

    mainAudio.play();

    showPopup("Đang phát: " + title);

  };


  /* ---------------- FETCH SONGS ---------------- */

  (function () {

    const container = $("#songList");

    if (!container) return;

    fetch("songs.php")

      .then(res => res.text())

      .then(html => {

        container.innerHTML = html;

        bindSongs();

      });

  })();


  /* ---------------- CLICK SONG ---------------- */

  function bindSongs() {

    const audios = $all("#songList audio");

    audios.forEach(audio => {

      const parent = audio.parentElement;

      parent.style.cursor = "pointer";

      parent.addEventListener("click", function () {

        const src = audio.querySelector("source").src;

        let title = parent.textContent;

        title = title.replace("Tên bài hát:", "")
        title = title.split("Ca sĩ")[0].trim();

        window.selectSong(src, title);

      });

    });

  }

})();
