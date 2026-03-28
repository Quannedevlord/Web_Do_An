-- ============================================================
-- Chill Wave Music – Database Backup
-- Ngày backup: 28/03/2026 13:06:08
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Cấu trúc bảng: `contact_messages`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu bảng `contact_messages`

INSERT INTO `contact_messages` VALUES
('3', 'Minh Trọng', 'vot750422@gmail.com', 'web nghe nhạc hay quá admin ơi, em vừa nghe vừa xuất đến bây giờ là 67 lần rồi, hòn dái em sắp nổ tung', '1', '2026-03-22 04:55:28'),
('5', 'quân nè', 'quansiupro1@gmail.com', 'admin làm web như c', '1', '2026-03-22 07:59:46'),
('6', 'giấu tên', 'giauten123@gmail.com', 'web c j toàn nhạc tiếng trung, tiếng anh, thumnail bài hát thì wibu đúng thằng tu tiên lỏ', '1', '2026-03-22 08:16:30'),
('8', 'Hông biết', 'cmtth@gmail.com', 'Web sịn quá , quá chời là đẹp  cỡ này chắc BE FE xịnnnnnnnn lắm luôn á. nhạc tu tiên quá là đỉnh nghe  này xong cảm giác sắp đột phá Kim Đan rồi 😌”', '1', '2026-03-22 20:58:19'),
('12', 'Đại ca của mấy thằng đệ làm web này', 'daicacuamayde@daica.com', 'độ mịa kêu thêm nhạc jack mà đợi hoài đếu thấy đúng là tiếp thu ý kiến người dùng như cái lon, hôm bửa mới khen vừa nghe vừa xuất giờ  vừa nghe vừa ỉa, quá thất vọng về mấy đệ 1* nhé', '0', '2026-03-28 04:21:33');

-- --------------------------------------------------------
-- Cấu trúc bảng: `liked_songs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `liked_songs`;
CREATE TABLE `liked_songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`user_id`,`song_id`),
  KEY `song_id` (`song_id`),
  CONSTRAINT `liked_songs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `liked_songs_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `liked_songs`

INSERT INTO `liked_songs` VALUES
('21', '1', '47', '2026-03-26 04:41:14'),
('22', '1', '46', '2026-03-26 04:41:38'),
('30', '3', '49', '2026-03-26 05:18:03'),
('31', '3', '47', '2026-03-26 05:18:04'),
('32', '3', '46', '2026-03-26 05:18:04'),
('33', '3', '45', '2026-03-26 05:18:05'),
('36', '9', '49', '2026-03-26 06:44:59'),
('37', '9', '47', '2026-03-26 06:45:25'),
('38', '9', '43', '2026-03-26 06:46:02'),
('40', '1', '49', '2026-03-27 08:33:31'),
('43', '7', '57', '2026-03-28 02:04:34'),
('44', '7', '63', '2026-03-28 02:04:39'),
('45', '7', '64', '2026-03-28 02:04:41'),
('47', '7', '68', '2026-03-28 02:05:28'),
('48', '6', '10', '2026-03-28 04:17:41'),
('49', '1', '64', '2026-03-28 05:32:25');

-- --------------------------------------------------------
-- Cấu trúc bảng: `playlist_songs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `playlist_songs`;
CREATE TABLE `playlist_songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ps` (`playlist_id`,`song_id`),
  KEY `song_id` (`song_id`),
  CONSTRAINT `playlist_songs_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `playlist_songs_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `playlist_songs`

INSERT INTO `playlist_songs` VALUES
('41', '35', '49', '2026-03-26 06:54:40'),
('42', '35', '47', '2026-03-26 06:54:41'),
('43', '35', '46', '2026-03-26 06:54:42'),
('51', '35', '44', '2026-03-27 18:47:32'),
('52', '48', '44', '2026-03-27 18:47:34'),
('53', '48', '45', '2026-03-27 18:47:36'),
('57', '52', '57', '2026-03-28 02:06:48'),
('58', '52', '62', '2026-03-28 02:07:17'),
('59', '52', '58', '2026-03-28 02:07:27'),
('60', '7', '68', '2026-03-28 04:18:24'),
('61', '54', '68', '2026-03-28 05:36:03');

-- --------------------------------------------------------
-- Cấu trúc bảng: `playlists`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `playlists`;
CREATE TABLE `playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `playlists`

INSERT INTO `playlists` VALUES
('7', '6', 'Nhạc J97', '2026-03-22 04:50:53'),
('35', '9', 'nhac cua toi', '2026-03-26 06:54:31'),
('48', '9', '123', '2026-03-27 18:47:19'),
('51', '10', 'cc', '2026-03-28 00:10:14'),
('52', '7', 'nhạc cổ trang', '2026-03-28 02:06:41'),
('54', '1', 'nhaccuatoi', '2026-03-28 05:35:53');

-- --------------------------------------------------------
-- Cấu trúc bảng: `songs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `songs`;
CREATE TABLE `songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `artist` varchar(200) NOT NULL,
  `file` varchar(500) NOT NULL,
  `image` varchar(500) NOT NULL DEFAULT '',
  `genre` varchar(20) NOT NULL DEFAULT 'other',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `songs`

INSERT INTO `songs` VALUES
('8', 'SEA OF FEELINGS', 'LOWX', 'LOWX_-_SEA_OF_FEELINGS_-_Lowx.mp3', '1774163963_0_anime-girl-katana-white-hair-4k-wallpaper-uhdpaper.com-399@5@d.jpg', 'Edm', '0', '2026-03-22 00:19:23'),
('10', '徐梦圆 - China-A', 'StarlingEDM', '徐梦圆_-_China-A_♪_-_StarlingEDM.mp3', '1774184730_0_Hinh-anh-avatar-nam-anime-4.jpg', 'Edm', '0', '2026-03-22 06:05:30'),
('11', '徐梦圆 - China-B', 'StarlingEDM', 'Arealy仁辰_-_China-B_♪_-_StarlingEDM.mp3', '1774184730_1_Hinh-anh-avatar-anime-nu-cute-7.jpg', 'Edm', '0', '2026-03-22 06:05:30'),
('12', '徐梦圆 - China-C', 'StarlingEDM', '徐梦圆_-_China-C_-_音乐人徐梦圆YUAN.mp3', '1774184730_2_Hinh-anh-avatar-anime-nu-cute-6.jpg', 'Edm', '0', '2026-03-22 06:05:30'),
('13', '徐梦圆 - China-D', 'StarlingEDM', '「China_EDM」_Arealy仁辰_-_China-D_♪_-_ADVENTURES_MUSIC.mp3', '1774184821_0_Hinh-anh-avatar-anime-nu-cute-2.jpg', 'Edm', '0', '2026-03-22 06:07:01'),
('14', '徐梦圆 - China-E', 'StarlingEDM', '徐梦圆_-_China-E_-_音乐人徐梦圆YUAN.mp3', '1774184821_1_konachan-com-168366-blue_hair-green_eyes-hatsune_miku-japanese_clothes-long_hair-nazu-na-twintails-vocaloid.jpg', 'Edm', '0', '2026-03-22 06:07:01'),
('16', 'Heather x Eyes Blue', 'Fran Vasilić', 'Heather_x_Eyes_Blue_(Lofi_Remix)_-_Fasetya.mp3', '1774186147_1_luigi-manga-kbYD_aSAeg8-unsplash.jpg', 'lofi', '0', '2026-03-22 06:29:07'),
('17', 'Teenage Mona Lisa', 'Gustixa', 'teenage_mona_lisa_(Gustixa_Version)_-_Gustixa.mp3', '1774186238_0_monalisa.jpg', 'lofi', '0', '2026-03-22 06:30:38'),
('18', 'Party Favor (Gustixa Remix)', 'Billie Eilish', 'Billie_Eilish_-_party_favor_(Gustixa_Remix)_-_Gustixa.mp3', '1774190776_18.jpg', 'lofi', '0', '2026-03-22 06:35:08'),
('19', 'Fallen Kingdom (Gustixa ft. Shalom Margaret)', 'Gustixa', 'fallen_kingdom_(Gustixa_ft._Shalom_Margaret)_-_Gustixa.mp3', '1774190721_19.jpg', 'lofi', '0', '2026-03-22 06:35:08'),
('20', 'Tell Me Why I\'m Waiting x I Know You So Well (feat. Shiloh Dynasty)', 'Fasetya', 'Tell_Me_Why_I\'m_Waiting_x_I_Know_You_So_Well_(feat._Shiloh_Dynasty)_-_Fasetya.mp3', '1774186688_0_olegs-jonins-CWnaL_0CWjk-unsplash.jpg', 'lofi', '0', '2026-03-22 06:38:08'),
('21', 'Can We Kiss Forever?  (ft. Adriana Proenza)', 'Kina', 'Kina_-_Can_We_Kiss_Forever_(Lyrics)_ft._Adriana_Proenza_-_7clouds.mp3', '1774186795_0_harry-gillen-t7gbHudO1Xo-unsplash.jpg', 'lofi', '0', '2026-03-22 06:39:55'),
('23', 'Head In The Clouds', 'Hayd', 'Hayd_-_Head_In_The_Clouds_(Official_Video)_-_HaydVEVO.mp3', '1774187058_0_aakanksha-panwar-aCSjQvSStyY-unsplash.jpg', 'lofi', '0', '2026-03-22 06:44:18'),
('24', 'КАМИН - feat. JONY', 'EMIN', 'EMIN_feat._JONY_-_КАМИН_-_EminOfficial.mp3', '1774187199_0_unnamed.jpg', 'lofi', '0', '2026-03-22 06:46:39'),
('25', 'Pastlives', 'sapientdream', 'sapientdream_-_Pastlives_(lyrics)_-_TheGoodVibe.mp3', '1774187303_0_harry-gillen-EMj8UxrdALc-unsplash.jpg', 'lofi', '0', '2026-03-22 06:48:23'),
('26', 'Cardigan (Slowed & Reverb)', 'ReVoice', 'Cardigan_(Slowed_&_Reverb)_-_ReVoice.mp3', '1774187485_0_p1.jpg', 'lofi', '0', '2026-03-22 06:51:25'),
('30', 'Wherever You Would Call Me', 'Zaini', 'wherever_you_would_call_me_-_Zaini.mp3', '1774190686_30.jpg', 'lofi', '0', '2026-03-22 07:07:53'),
('33', 'Someone To You (feat. Shalom Margare)', 'Fasetya', 'Fasetya_-_Someone_To_You_(feat._Shalom_Margaret)_-_Fasetya.mp3', '1774190840_0_astronaud23_unsplash.jpg', 'lofi', '0', '2026-03-22 07:47:22'),
('35', 'KHÔNG BUÔNG ft. Ari', 'Hngle', 'Hngle_-_KHÔNG_BUÔNG_ft._Ari_Official_Music_Video_-_HNGLE___hưng_lê.mp3', '1774356848_0_wao1.jpg', 'pop', '0', '2026-03-24 05:54:08'),
('36', '(xuân): Hư không', 'Kha', '(xuân)_Hư_không_-_Kha____EP_tình_-_Kha.mp3', '1774357031_0_wao2.jpg', 'pop', '0', '2026-03-24 05:57:11'),
('37', 'Phép Màu (Đàn Cá Gỗ OST)', 'Mounter x MAYDAYs, Minh Tốc', 'Phép_Màu_(Đàn_Cá_Gỗ_OST)_-_Mounter_x_MAYDAYs,_Minh_Tốc_Official_MV_-_Mounter.mp3', '1774358153_0_wao3.jpg', 'pop', '0', '2026-03-24 06:15:53'),
('38', 'Em Đừng Khóc', 'Chillies', 'Em_Đừng_Khóc_-_Chillies_(Official_Music_Video)_-_Chillies.mp3', '1774358865_0_wao4.jpg', 'pop', '0', '2026-03-24 06:27:45'),
('39', 'Cảm Ơn Người Đã Thức Cùng Tôi', 'Phùng Khánh Linh', 'Cảm_Ơn_Người_Đã_Thức_Cùng_Tôi_-_Phùng_Khánh_Linh.mp3', '1774359027_0_wao5.jpg', 'pop', '0', '2026-03-24 06:30:27'),
('43', 'Bình Yên - Vũ ft. Binz', 'Vũ', 'bình_yên___Vũ._ft._Binz_(Official_MV)_từ_Album_Bảo_Tàng_Của_Nuối_Tiếc_-_Vũ_Official.mp3', '1774361813_0_wao6.jpg', 'ballad', '0', '2026-03-24 07:16:53'),
('44', 'Những Lời Hứa Bỏ Quên', 'VŨ. x DEAR JANE', 'NHỮNG_LỜI_HỨA_BỎ_QUÊN___VŨ._x_DEAR_JANE_(Official_MV)_từ_Album_Bảo_Tàng_Của_Nuối_Tiếc_-_Vũ_Official.mp3', '1774361813_1_wao8.jpg', 'ballad', '0', '2026-03-24 07:16:53'),
('45', 'Đã Lỡ Yêu Em Nhiều', 'JustaTee', 'JustaTee_-_Đã_Lỡ_Yêu_Em_Nhiều_(Official_MV)_-_JustaTeeMusic.mp3', '1774361813_2_wao7.jpg', 'ballad', '0', '2026-03-24 07:16:53'),
('46', 'Có em (Feat. Low G)', 'Madihu', 'Madihu_-_Có_em_(Feat._Low_G)_[Official_MV]_-_Madihu.mp3', '1774362151_0_wao9.jpg', 'ballad', '0', '2026-03-24 07:22:31'),
('47', 'Nắng Lên', 'Rhymastic x SOOBIN x GONZO x B-wine x MastaL', 'Nắng_Lên_-_Rhymastic_x_SOOBIN_x_GONZO_x_B-wine_x_MastaL_-_Space_Jam_Volume_1_-_Team_Kim_-_SpaceSpeakers_Label.mp3', '1774362341_0_wao10.jpg', 'ballad', '0', '2026-03-24 07:25:41'),
('49', 'LUAR', 'LANCELOT x FYU - RioX', 'LUAR_-_LANCELOT_x_FYU_-_RioX.mp3', '1774403063_0_spider-man-marvel-5120x2880-11025.jpg', 'edm', '0', '2026-03-24 18:44:23'),
('57', '苍穹 | Thương Khung', '仁辰(Nhân Thần)/ 南有乔木(Nam Hữu Kiều Mộc)', '苍穹___Thương_Khung_-_仁辰(Nhân_Thần)__南有乔木(Nam_Hữu_Kiều_Mộc)_[cs43MMwGjh0].mp3', '1774665039_0_wao11.jpg', 'lofi', '0', '2026-03-27 19:30:38'),
('58', '归春颂 | Khúc ca hồi xuân', '路明熹 (Lộ Minh Hy) / 豆糕p (DougaoP)', '归春颂_Khúc_ca_hồi_xuân_music_relax_-_路明熹_(Lộ_Minh_Hy)_豆糕p_(DougaoP).mp3', '1774665444_0_wao12.jpg', 'lofi', '0', '2026-03-27 19:37:23'),
('59', '小圆石 (Pebble) | Viên sỏi nhỏ', 'BLACKDD', '小圆石_(Pebble)___Viên_sỏi_nhỏ___music_relax_-_BLACKDD_[uOO1IcFmdiE].mp3', '1774665444_1_wao13.jpg', 'lofi', '0', '2026-03-27 19:37:23'),
('60', '寻光而至 (Reaching Light) | Tìm kiếm ánh sáng', 'HOPERUI/Greyscale', '寻光而至_(Reaching_Light)___Tìm_kiếm_ánh_sáng___music_relax_-_HOPERUI_Greyscale_[VOpuj7cagLc].mp3', '1774665444_2_wao14.jpg', 'lofi', '0', '2026-03-27 19:37:23'),
('61', 'it\'s 6pm but I miss u already | 慢速版', 'BlueLee, Furyl, Siren', 'it\'s_6pm_but_I_miss_u_already___慢速版_(ver_slowed)___music_relax_-_BlueLee,_Furyl,_Siren_[rOqwUU-0BBk].mp3', '1774665842_0_wao15.jpg', 'lofi', '0', '2026-03-27 19:44:01'),
('62', '我想活出这样的人生 | Tớ muốn sống một cuộc đời như thế', '知晏 (Tri Yên)', '我想活出这样的人生___Tớ_muốn_sống_một_cuộc_đời_như_thế___music_relax_-_知晏_(Tri_Yên)_[UUqhYVAGCvY].mp3', '1774665842_1_wao16.jpg', 'lofi', '0', '2026-03-27 19:44:01'),
('63', '溺悬 | Nịch Huyền', '路灰气球(Bóng Bay Màu Xám Trên Đường)', '溺悬___Nịch_Huyền___music_relax___cổ_phong_-_路灰气球(Bóng_Bay_Màu_Xám_Trên_Đường)_[2BI4qPlrbug].mp3', '1774665842_2_wao17.jpg', 'lofi', '0', '2026-03-27 19:44:01'),
('64', '唢风吟 | Tỏa Phong Ngâm', '李化禹(Lý Hóa Vũ)', '唢风吟___Tỏa_Phong_Ngâm___Nhạc_cổ_phong_không_lời_-_李化禹(Lý_Hóa_Vũ)_[fbR_dnYlzAw].mp3', '1774665980_0_wao18.jpg', 'lofi', '0', '2026-03-27 19:46:20'),
('65', '远光赠予的情书 | Bức thư tình được ánh sáng gửi tặng', 'MoreanP', '远光赠予的情书___Bức_thư_tình_được_ánh_sáng_gửi_tặng_-_MoreanP__Music_relax_[5OcSlPL7SK0].mp3', '1774666933_0_wao19.jpg', 'lofi', '0', '2026-03-27 20:02:12'),
('66', 'Eutopia | Nhạc khí', '布和笛箫 (Bố và Di Tiêu)', 'Eutopia___Nhạc_khí_-_布和笛箫_(Bố_và_Di_Tiêu)_[xAJkrcZ1hjw].mp3', '1774666933_1_wao20.jpg', 'lofi', '0', '2026-03-27 20:02:12'),
('67', 'Beyond the sea is freedom | Call of Silence', 'Clear Sky remix', 'Beyond_the_sea_is_freedom_Call_of_Silence_instrumental_–_Clear_Sky_remix.mp3', '1774667886_67.jpg', 'lofi', '0', '2026-03-27 20:02:12'),
('68', '浮川 | Phù xuyên', '寒柴(Hàn Sài)/ 琉斯(Lưu Tư)/ Warsic', '浮川___Phù_xuyên_–_寒柴(Hàn_Sài)__琉斯(Lưu_Tư)__Warsic_[fKU9TF2N-AI].mp3', '1774667629_0_wao22.jpg', 'lofi', '0', '2026-03-27 20:13:49');

-- --------------------------------------------------------
-- Cấu trúc bảng: `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `users`

INSERT INTO `users` VALUES
('1', 'phonct123vn', 'phonct123@gmail.com', '$2y$10$/QaVZrJDwg8GWXh7GypY8.QEqWaJpeG8BYWQf05Et5jhEwEAB125i', 'admin', '2026-03-21 19:50:05'),
('2', 'dat', 'dat123@gmail.com', '$2y$10$wLG0gPDrxNfeO60cXANajeapdeyvey/XYUhg0L.ECB6naHnEJdK6S', 'user', '2026-03-21 19:50:05'),
('3', 'phat', 'phatct2k6@gmail.com', '$2y$10$V26HaU/lzT6JCZgQ6oJyAeEyA2YHcX/cazIZyZTz5dY8EfaPnEHX.', 'user', '2026-03-21 19:50:05'),
('5', 'quansiupro2', 'quansiupro2@gmail.com', '$2y$10$xG2MJfeMVPrwKGeBq9N5EOStMC54pVwVAqOV2BRtq6U.oCufoxKyy', 'admin', '2026-03-22 00:13:34'),
('6', 'theoythick1', 'vot750422@gmail.com', '$2y$10$SX7e18RReMOyjif5XQ1AsugUxhzZKytRWhfCyBC43R0y4QIu4ogCW', 'user', '2026-03-22 00:39:52'),
('7', 'quansiupro1', 'quansiupro1@gmail.com', '$2y$10$SPpRMihfClZdih.1xrOqhOMYXYDrg7wlUGNvLYpGDAfqH.gxJpRnm', 'user', '2026-03-22 07:59:07'),
('8', 'Gkt', 'pmtrongktpm2411047@student.ctuet.edu.vn', '$2y$10$7d6Cfemx2NI0q5Tk7m7kuOJG.wq0GsOVDQTGKOvbW1RbsHP4hmXX2', 'user', '2026-03-22 20:53:45'),
('9', '222', 'phon123@gmail.com', '$2y$10$5nSzsN2JhLUsgIRttpuLIONnfkXr.qoXCEc/BgcTvL47qO1Jm6iTK', 'user', '2026-03-26 06:38:03'),
('10', 'B2405285', 'tub2405285@student.ctu.edu.vb', '$2y$10$7RvQLcCWmvOhsyAnmCMomuTVZpRm/drpWCnlHn3n9ZtjeFO6xGVJS', 'user', '2026-03-28 00:04:02');

SET FOREIGN_KEY_CHECKS = 1;

-- Backup hoàn thành: 28/03/2026 13:06:08
