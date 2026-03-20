-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th3 15, 2026 lúc 07:54 AM
-- Phiên bản máy phục vụ: 8.4.7
-- Phiên bản PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `music_web`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `songs`
--

DROP TABLE IF EXISTS `songs`;
CREATE TABLE IF NOT EXISTS `songs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb3_vietnamese_ci NOT NULL,
  `artist` varchar(100) COLLATE utf8mb3_vietnamese_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb3_vietnamese_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb3_vietnamese_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `songs`
--

INSERT INTO `songs` (`id`, `title`, `artist`, `file`, `image`, `created_at`) VALUES
(3, 'It Doesn’t Need To Be More Than That', 'pATCHES', 'It Doesn’t Need To Be More Than That - pATCHES.mp3', '', '2026-03-14 12:27:01'),
(9, 'Bạch Nguyệt Quang', 'Táo', 'TÁO - Bạch Nguyệt Quang - Táo.mp3', '1773545169_Screenshot_2026-03-15_102523.png', '2026-03-15 03:26:09'),
(10, 'Tỏa Sáng Trong Đêm Tàn Khóc', 'Shine Through the Darkness', 'Tỏa Sáng Giữa Bóng Đêm (Shine Through the Darkness.mp3', '1773545422_Screenshot_2026-03-15_102845.png', '2026-03-15 03:30:22'),
(11, 'A Free Night in Bushwick', 'William Rosati', 'A Free Night in Bushwick - William Rosati.mp3', '1773546610_Screenshot_2026-03-15_105000.png', '2026-03-15 03:50:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb3_vietnamese_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb3_vietnamese_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_vietnamese_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `role` varchar(10) COLLATE utf8mb3_vietnamese_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(2, 'admin', 'admin@gmail.com', '$2y$10$9U4qgxwEV19uElq9HaCd4ejTk4Gqo1mhg6WgQHeLjv89gFoOfiU8y', '2026-03-14 06:18:07', 'user'),
(3, 'phonct123vn', 'phonct123@gmail.com', '$2y$10$/QaVZrJDwg8GWXh7GypY8.QEqWaJpeG8BYWQf05Et5jhEwEAB125i', '2026-03-15 07:10:08', 'admin'),
(4, 'dat', 'dat123@gmail.co', '$2y$10$wLG0gPDrxNfeO60cXANajeapdeyvey/XYUhg0L.ECB6naHnEJdK6S', '2026-03-14 14:22:39', 'user'),
(5, 'phat', 'phatct2k6@gmail.com', '$2y$10$V26HaU/lzT6JCZgQ6oJyAeEyA2YHcX/cazIZyZTz5dY8EfaPnEHX.', '2026-03-15 03:44:06', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
