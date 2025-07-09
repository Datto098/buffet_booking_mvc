-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th7 08, 2025 lúc 01:32 PM
-- Phiên bản máy phục vụ: 8.2.0
-- Phiên bản PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `buffet_booking`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `table_id` int DEFAULT NULL,
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `guest_count` int NOT NULL,
  `special_requests` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','seated','completed','cancelled','no_show') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `booking_reference` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_reference` (`booking_reference`),
  KEY `fk_bookings_user` (`user_id`),
  KEY `fk_bookings_table` (`table_id`),
  KEY `idx_booking_date` (`booking_date`),
  KEY `idx_status` (`status`),
  KEY `idx_customer_email` (`customer_email`),
  KEY `idx_bookings_date_status` (`booking_date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `food_item_id` int NOT NULL,
  `quantity` int NOT NULL,
  `special_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cart_user` (`user_id`),
  KEY `fk_cart_food` (`food_item_id`),
  KEY `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_active_sort` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Appetizersss', 'Starter dishes and small platess', NULL, 0, 1, '2025-06-05 17:39:52', '2025-06-12 21:33:30'),
(3, 'Desserts', 'Sweet treats and desserts', NULL, 0, 1, '2025-06-05 17:39:52', '2025-06-05 17:39:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `food_items`
--

DROP TABLE IF EXISTS `food_items`;
CREATE TABLE IF NOT EXISTS `food_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `subcategory_id` int DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ingredients` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `allergens` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dietary_info` json DEFAULT NULL,
  `nutrition_info` json DEFAULT NULL,
  `preparation_time` int DEFAULT NULL,
  `spice_level` enum('none','mild','medium','hot','very_hot') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `is_popular` tinyint(1) DEFAULT '0',
  `is_new` tinyint(1) DEFAULT '0',
  `is_seasonal` tinyint(1) DEFAULT '0',
  `is_available` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_food_category` (`category_id`),
  KEY `fk_food_subcategory` (`subcategory_id`),
  KEY `idx_available` (`is_available`),
  KEY `idx_popular` (`is_popular`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `food_items`
--

INSERT INTO `food_items` (`id`, `category_id`, `subcategory_id`, `name`, `description`, `price`, `image`, `ingredients`, `allergens`, `dietary_info`, `nutrition_info`, `preparation_time`, `spice_level`, `is_popular`, `is_new`, `is_seasonal`, `is_available`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Deluxe Buffet', 'All-you-can-eat buffet with premium dishes', 200000.00, NULL, NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-06 04:19:41', '2025-06-09 00:37:26'),
(2, 1, NULL, 'Standard Buffet', 'Traditional buffet experience', 100000.00, NULL, NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-06 04:19:41', '2025-06-09 00:37:38'),
(3, 1, NULL, 'Vegetarian Special', 'Vegetarian-only buffet options', 100000.00, NULL, NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-06 04:19:41', '2025-06-09 00:38:37'),
(5, 1, NULL, 'Ba chỉ bò Mỹ', 'Thịt bò Mỹ mềm, cắt lát mỏng, ướp sốt BBQ đậm đà', 100000.00, 'food_5_1749454675.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 1, 0, 0, 1, 0, '2025-06-08 03:26:53', '2025-06-09 00:38:45'),
(6, 1, NULL, 'Sườn non bò non', 'Cắt miếng sườn nhỏ, thấm đều sốt tiêu đen', 10000.00, '1749379117_IMG_6763.png', NULL, NULL, NULL, NULL, NULL, 'none', 1, 0, 0, 1, 0, '2025-06-08 03:38:37', '2025-06-09 00:38:52'),
(7, 1, NULL, 'Sushi cá hồi', 'Cuộn cơm giấm, cá hồi tươi, rong biển', 20000.00, '1749449920_a32af133-cover.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 1, 0, 0, 1, 0, '2025-06-08 23:18:40', '2025-06-09 00:38:59'),
(8, 1, NULL, 'Gimbap Hàn', 'Cơm cuộn với trứng, xúc xích, dưa leo', 30000.00, '1749449983_thumb-31.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 1, 0, 0, 1, 0, '2025-06-08 23:19:43', '2025-06-09 00:39:09'),
(9, 1, NULL, 'Tempura tôm', 'Tôm chiên giòn vỏ bột mỏng', 30000.00, '1749450059_huong-dan-lam-mon-chien-tempura.webp', NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-08 23:20:59', '2025-06-09 06:20:59'),
(10, 1, NULL, 'Há cảo hấp', 'Nhân tôm, thịt, hành lá', 20000.00, '1749450115_ha-cao-1.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-08 23:21:55', '2025-06-09 06:21:55'),
(11, 1, NULL, 'Cơm chiên Nhật', 'Cơm chiên cùng trứng, đậu, cà rốt', 20000.00, '1749450187_0-0000-1200x675.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-08 23:23:07', '2025-06-09 06:23:07'),
(12, 1, NULL, 'Mì udon xào bò', 'Mì Nhật dai, xào cùng thịt bò và rau', 30000.00, '1749450250_lam-mi-udon-thit-bo-1.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-08 23:24:10', '2025-06-09 06:24:10'),
(13, 3, NULL, 'Bánh flan', 'Mềm, béo, thơm mùi caramel', 10000.00, '1749450389_banh-flan-recipe-f3.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 1, 0, 0, 1, 0, '2025-06-08 23:26:29', '2025-06-09 00:39:17'),
(14, 3, NULL, 'Trà đào cam sả', 'Thơm mát, chua ngọt, thanh', 10000.00, '1749450500_huong-dan-cong-thuc-tra-dao-cam-sa-hut-khach-ngon-kho-cuong_20240526180626.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 1, 0, 0, 1, 0, '2025-06-08 23:28:20', '2025-06-09 00:39:28'),
(15, 3, NULL, 'Trà tắc', 'Vị trà nhài thơm nhẹ, tắc chua', 10000.00, '1749450708_tra-tac-bao-nhieu-calo.jpg', NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-08 23:31:48', '2025-06-09 06:31:48'),
(16, 3, NULL, 'Bia', 'Có thể thêm bia lon Heineken, Tiger', 15000.00, '1749450774_bia-tiger-sleek-5-abv-lon-330ml-281124-112850-1732768166826.webp', NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-08 23:32:54', '2025-06-09 06:32:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_id` int NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `is_featured` tinyint(1) DEFAULT '0',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `views_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_news_author` (`author_id`),
  KEY `idx_published` (`is_published`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `excerpt`, `author_id`, `image_url`, `is_published`, `is_featured`, `meta_title`, `meta_description`, `views_count`, `created_at`, `updated_at`) VALUES
(2, '🍳 Cách Làm Cơm Chiên Trứng Kiểu Nhật – Ngon, Nhanh, Đẹp Như Trong Anime!', '<h2>🍳 C&aacute;ch L&agrave;m Cơm Chi&ecirc;n Trứng Kiểu Nhật &ndash; Ngon, Nhanh, Đẹp Như Trong Anime!</h2>\r\n\r\n<p>![thumbnail đang được tạo...]</p>\r\n\r\n<h3>🧂 Nguy&ecirc;n liệu (cho 1-2 người ăn):</h3>\r\n\r\n<ul>\r\n	<li>\r\n	<p>1 ch&eacute;n cơm nguội (c&agrave;ng nguội c&agrave;ng ngon!)</p>\r\n	</li>\r\n	<li>\r\n	<p>2 quả trứng g&agrave;</p>\r\n	</li>\r\n	<li>\r\n	<p>1/2 củ h&agrave;nh t&acirc;y (băm nhỏ)</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng canh nước tương Nhật (shoyu)</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng c&agrave; ph&ecirc; dầu m&egrave;</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng c&agrave; ph&ecirc; đường</p>\r\n	</li>\r\n	<li>\r\n	<p>Một &iacute;t ti&ecirc;u, h&agrave;nh l&aacute;</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng canh dầu ăn</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3>🔪 C&aacute;ch l&agrave;m:</h3>\r\n\r\n<ol>\r\n	<li>\r\n	<p><strong>L&agrave;m trứng Tamagoyaki trước</strong>:</p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>Đ&aacute;nh tan trứng với 1 muỗng c&agrave; ph&ecirc; đường v&agrave; t&iacute; x&iacute;u muối.</p>\r\n		</li>\r\n		<li>\r\n		<p>Đun n&oacute;ng chảo chống d&iacute;nh, cho một lớp mỏng trứng v&agrave;o, nghi&ecirc;ng chảo cho trứng d&agrave;n đều.</p>\r\n		</li>\r\n		<li>\r\n		<p>Khi gần ch&iacute;n, cuộn lại rồi đổ th&ecirc;m một lớp trứng, lặp lại đến hết. Cuộn trứng xong để sang một b&ecirc;n.</p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>Chi&ecirc;n cơm</strong>:</p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>Trong c&ugrave;ng chảo, cho dầu ăn v&agrave;o rồi x&agrave;o h&agrave;nh t&acirc;y đến khi trong.</p>\r\n		</li>\r\n		<li>\r\n		<p>Cho cơm v&agrave;o, đảo đều tay.</p>\r\n		</li>\r\n		<li>\r\n		<p>N&ecirc;m nước tương, dầu m&egrave;, ch&uacute;t ti&ecirc;u, trộn đều. Chi&ecirc;n đến khi cơm săn lại, dậy m&ugrave;i thơm.</p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>Ho&agrave;n thiện m&oacute;n ăn</strong>:</p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>Cắt trứng Tamagoyaki th&agrave;nh l&aacute;t.</p>\r\n		</li>\r\n		<li>\r\n		<p>B&agrave;y cơm ra đĩa, xếp trứng l&ecirc;n tr&ecirc;n.</p>\r\n		</li>\r\n		<li>\r\n		<p>Rắc h&agrave;nh l&aacute;, th&ecirc;m t&iacute; tương ớt nếu th&iacute;ch ăn cay.</p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n</ol>\r\n\r\n<h3>💡 Tips nhỏ:</h3>\r\n\r\n<ul>\r\n	<li>\r\n	<p>D&ugrave;ng cơm nguội để chi&ecirc;n sẽ gi&uacute;p hạt cơm tơi, kh&ocirc;ng bị nh&atilde;o.</p>\r\n	</li>\r\n	<li>\r\n	<p>C&oacute; thể th&ecirc;m topping như x&uacute;c x&iacute;ch, thanh cua hoặc bắp để tăng vị v&agrave; m&agrave;u sắc.</p>\r\n	</li>\r\n</ul>\r\n\r\n<hr />\r\n<p>🍽 M&oacute;n n&agrave;y l&agrave; ch&acirc;n &aacute;i buổi tối sau giờ l&agrave;m/học! Vừa dễ l&agrave;m, vừa giống mấy m&oacute;n anime hay c&oacute;. Ai m&ecirc; Nhật l&agrave; phải thử!</p>\r\n', '🍳 Cách Làm Cơm Chiên Trứng Kiểu Nhật – Ngon, Nhanh, Đẹp Như Trong Anime!\r\n![thumbnail đang được tạo...]', 1, '6846b3eedb6e0_0-0000-1200x675.jpg', 1, 0, '🍳 Cách Làm Cơm Chiên Trứng Kiểu Nhật – Ngon, Nhanh, Đẹp Như Trong Anime!', '', 0, '2025-06-06 07:52:00', '2025-06-09 10:14:06'),
(4, 'zddsdf', '<p><em><strong>zddsdf</strong></em></p>\r\n', 'zddsdf', 1, '6848351e61e95_banh-flan-recipe-f3.jpg', 0, 0, 'zddsdf', '', 0, '2025-06-10 13:37:34', '2025-06-10 13:37:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notifications_user` (`user_id`),
  KEY `idx_unread` (`is_read`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `is_read`, `created_at`) VALUES
(1, 4, 'new_order', 'New Order #1001', 'New order from John Doe - $125.50', '{\"url\": \"/superadmin/orders/view/1001\", \"order_id\": 1001, \"order_type\": \"delivery\", \"total_amount\": 125.5, \"customer_name\": \"John Doe\"}', 1, '2025-06-11 14:14:04'),
(2, 4, 'new_booking', 'New Table Booking', 'New booking for Table 5 - Jane Smith (4 guests)', '{\"url\": \"/superadmin/bookings/view/501\", \"booking_id\": 501, \"guest_count\": 4, \"table_number\": 5, \"customer_name\": \"Jane Smith\"}', 1, '2025-06-11 14:14:04'),
(3, 4, 'system', 'System Maintenance', 'Scheduled maintenance will occur tonight at 2 AM', '{\"duration\": \"30 minutes\", \"scheduled_time\": \"2025-06-12 02:00:00\"}', 1, '2025-06-11 14:14:04'),
(6, 4, 'new_order', 'New Order Received', 'New order #6 from Super Admin - 135,000.00 VND', '{\"url\": \"/superadmin/orders/view/6\", \"order_id\": \"6\", \"order_type\": \"delivery\", \"total_amount\": 135000, \"customer_name\": \"Super Admin\"}', 1, '2025-06-11 14:51:56'),
(7, 4, 'new_order', 'New Order Received', 'New order #7 from Super Admin - 135,000.00 VND', '{\"url\": \"/superadmin/orders/view/7\", \"order_id\": \"7\", \"order_type\": \"delivery\", \"total_amount\": 135000, \"customer_name\": \"Super Admin\"}', 1, '2025-06-11 14:56:58'),
(9, 4, 'new_order', 'New Order Received', 'New order #9 from Super Admin - 135,000.00 VND', '{\"url\": \"/superadmin/orders/view/9\", \"order_id\": \"9\", \"order_type\": \"delivery\", \"total_amount\": 135000, \"customer_name\": \"Super Admin\"}', 1, '2025-06-13 09:02:28'),
(10, 4, 'new_order', 'New Order Received', 'New order #10 from Nguyen Dat - 145,500.00 VND', '{\"url\": \"/superadmin/orders/view/10\", \"order_id\": \"10\", \"order_type\": \"delivery\", \"total_amount\": 145500, \"customer_name\": \"Nguyen Dat\"}', 1, '2025-06-13 09:25:52'),
(11, 4, 'new_order', 'New Order Received', 'New order #11 from Đỗ Ngọc Hiếu - 40,500.00 VND', '{\"url\": \"/superadmin/orders/view/11\", \"order_id\": \"11\", \"order_type\": \"delivery\", \"total_amount\": 40500, \"customer_name\": \"Đỗ Ngọc Hiếu\"}', 1, '2025-06-14 14:27:59'),
(12, 4, 'new_order', 'New Order Received', 'New order #12 from Đỗ Ngọc Hiếu - 187,500.00 VND', '{\"url\": \"/superadmin/orders/view/12\", \"order_id\": \"12\", \"order_type\": \"delivery\", \"total_amount\": 187500, \"customer_name\": \"Đỗ Ngọc Hiếu\"}', 1, '2025-06-14 15:42:59'),
(13, 4, 'new_order', 'New Order Received', 'New order #13 from Đỗ Ngọc Hiếu - 40,500.00 VND', '{\"url\": \"/superadmin/orders/view/13\", \"order_id\": \"13\", \"order_type\": \"delivery\", \"total_amount\": 40500, \"customer_name\": \"Đỗ Ngọc Hiếu\"}', 0, '2025-07-07 10:58:01'),
(14, 4, 'new_order', 'New Order Received', 'New order #14 from Đỗ Ngọc Hiếu - 40,500.00 VND', '{\"url\": \"/superadmin/orders/view/14\", \"order_id\": \"14\", \"order_type\": \"delivery\", \"total_amount\": 40500, \"customer_name\": \"Đỗ Ngọc Hiếu\"}', 0, '2025-07-07 11:18:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `booking_id` int DEFAULT NULL,
  `order_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_type` enum('dine_in','takeout','delivery') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cod','cash','credit_card','debit_card','digital_wallet','bank_transfer','vnpay') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_id` int DEFAULT NULL,
  `status` enum('pending','confirmed','preparing','ready','completed','delivered','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `special_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `delivery_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `order_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estimated_ready_time` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delivery_ward` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `delivery_fee` decimal(10,2) DEFAULT '0.00',
  `service_fee` decimal(10,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `coupon_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `fk_orders_user` (`user_id`),
  KEY `fk_orders_booking` (`booking_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_order_type` (`order_type`),
  KEY `idx_customer_email` (`customer_email`),
  KEY `idx_orders_date_status` (`created_at`,`status`),
  KEY `fk_orders_payment` (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `booking_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`, `order_type`, `subtotal`, `tax_amount`, `total_amount`, `payment_method`, `payment_status`, `payment_id`, `status`, `special_instructions`, `delivery_address`, `order_notes`, `estimated_ready_time`, `completed_at`, `created_at`, `updated_at`, `delivery_ward`, `delivery_district`, `delivery_city`, `delivery_notes`, `delivery_fee`, `service_fee`, `discount_amount`, `coupon_code`) VALUES
(1, NULL, NULL, 'ORD2025060901', 'Nguyễn Văn Test', 'test@example.com', '0123456789', 'delivery', 597.00, 59.70, 656.70, 'digital_wallet', 'paid', NULL, 'confirmed', 'Không cay', '123 Đường Test, Phường Test', 'Đơn hàng test', NULL, NULL, '2025-06-09 12:35:13', '2025-06-09 06:23:00', 'Phường Test', 'Quận Test', 'TP.Test', 'Giao tại cổng chính', 30.00, 20.00, 50.00, 'TEST50'),
(2, 4, NULL, 'ORD684833f3b4a0c', 'Super Admin', 'superadmin@buffet.com', '+1234567890', 'delivery', 100000.00, 0.00, 135000.00, '', 'paid', NULL, 'confirmed', NULL, 'Daluo Town, Mãnh Hải, Vân Nam, Trung Quốc', '', NULL, NULL, '2025-06-10 06:32:35', '2025-06-10 06:37:51', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(3, 4, NULL, 'ORD68498afaf233d', 'Super Admin', 'superadmin@buffet.com', '+1234567890', 'delivery', 200000.00, 0.00, 240000.00, '', 'pending', NULL, 'pending', NULL, 'Phường 7, Quận 8, Thành phố Hồ Chí Minh, 72900, Việt Nam', '', NULL, NULL, '2025-06-11 06:56:10', '2025-06-11 06:56:10', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(4, 4, NULL, 'ORD68498fb88fbc5', 'Super Admin', 'superadmin@buffet.com', '+1234567890', 'delivery', 110000.00, 0.00, 145500.00, '', 'pending', NULL, 'pending', NULL, 'Phường Nguyễn Cư Trinh, Quận 1, Thành phố Hồ Chí Minh, 72406, Việt Nam', '', NULL, NULL, '2025-06-11 07:16:24', '2025-06-11 07:16:24', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(5, 4, NULL, 'ORD684997b8024ae', 'Super Admin', 'superadmin@buffet.com', '+1234567890', 'delivery', 110000.00, 0.00, 145500.00, '', 'pending', NULL, 'pending', NULL, 'Phan Văn Khoẻ, Phường 2, Quận 6, Thành phố Hồ Chí Minh, 73000, Việt Nam', '', NULL, NULL, '2025-06-11 07:50:32', '2025-06-11 07:50:32', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(6, 4, NULL, 'ORD6849980c97b99', 'Super Admin', 'superadmin@buffet.com', '+1234567890', 'delivery', 100000.00, 0.00, 135000.00, '', 'pending', NULL, 'pending', NULL, 'Mắt Kính Tâm Đức - Chi Nhánh Quận 11, 199, Đường Lê Đại Hành, Phường 11, Quận 11, Thành phố Hồ Chí Minh, 72415, Việt Nam', '', NULL, NULL, '2025-06-11 07:51:56', '2025-06-11 07:51:56', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(7, 4, NULL, 'ORD6849993aae8ed', 'Super Admin', 'superadmin@buffet.com', '+1234567890', 'delivery', 100000.00, 0.00, 135000.00, '', 'pending', NULL, 'pending', NULL, 'Xã Phước Kiển, Huyện Nhà Bè, Thành phố Hồ Chí Minh, 72915, Việt Nam', '', NULL, NULL, '2025-06-11 07:56:58', '2025-06-11 07:56:58', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(8, 1, NULL, 'ORD684ba23656706', 'Adminn User', 'admin@buffet.com', '0000000000', 'delivery', 200000.00, 0.00, 240000.00, '', 'pending', NULL, 'pending', NULL, 'ACB, Đường Bình Tiên, Phường 8, Quận 6, Thành phố Hồ Chí Minh, 73118, Việt Nam', '', NULL, NULL, '2025-06-12 20:59:50', '2025-06-12 20:59:50', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(9, 4, NULL, 'ORD684be92432e50', 'Super Admin', 'superadmin@buffet.com', '+1234567890', 'delivery', 100000.00, 0.00, 135000.00, 'vnpay', 'paid', 4, 'preparing', NULL, 'Khum Tralach, Treang District, Takeo, Cao Miên', '', NULL, NULL, '2025-06-13 02:02:28', '2025-06-13 02:20:01', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(10, 8, NULL, 'ORD684beea0c522f', 'Nguyen Dat', 'dat61222@gmail.com', '0999999999', 'delivery', 110000.00, 0.00, 145500.00, 'vnpay', 'paid', 5, 'confirmed', NULL, 'Đường Tân Phước, Phường 7, Quận 11, Thành phố Hồ Chí Minh, 72415, Việt Nam', '', NULL, NULL, '2025-06-13 02:25:52', '2025-06-13 09:26:53', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(11, 9, NULL, 'ORD684d86efb457e', 'Đỗ Ngọc Hiếu', 'dongochieu333@gmail.com', '0384946973', 'delivery', 10000.00, 0.00, 40500.00, 'vnpay', 'paid', 6, 'confirmed', NULL, 'Đường Lý Thường Kiệt, Cư xá Diên Hồng, Phường 14, Quận 10, Thành phố Hồ Chí Minh, 72000, Việt Nam', '', NULL, NULL, '2025-06-14 07:27:59', '2025-06-14 14:31:39', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(12, 9, NULL, 'ORD684d988388537', 'Đỗ Ngọc Hiếu', 'dongochieu333@gmail.com', '0384946973', 'delivery', 150000.00, 0.00, 187500.00, 'vnpay', 'paid', 7, 'cancelled', NULL, 'Hẻm 184/38/49/37 Âu Dương Lân, Phường Rạch Ông, Quận 8, Thành phố Hồ Chí Minh, 73009, Việt Nam', '', NULL, NULL, '2025-06-14 08:42:59', '2025-06-14 08:46:48', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(13, 9, NULL, 'ORD686ba839e0918', 'Đỗ Ngọc Hiếu', 'dongochieu333@gmail.com', '00000000000', 'delivery', 10000.00, 0.00, 40500.00, 'cod', 'pending', NULL, 'completed', NULL, 'Lớp Học Nhạc - Hoạ Trí, 467/31, Hẻm 467 Lê Đại Hành, Quận 11, Thành phố Hồ Chí Minh, 70000, Việt Nam', '', NULL, NULL, '2025-07-07 03:58:01', '2025-07-07 08:31:50', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL),
(14, 9, NULL, 'ORD686bad0fd999f', 'Đỗ Ngọc Hiếu', 'dongochieu333@gmail.com', '00000000000', 'delivery', 10000.00, 0.00, 40500.00, 'cod', 'pending', NULL, 'completed', NULL, '38, Đường Hà Tôn Quyền, Phường Chợ Lớn, Quận 5, Thành phố Hồ Chí Minh, 72415, Việt Nam', '', NULL, NULL, '2025-07-07 04:18:39', '2025-07-07 08:32:06', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `food_item_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `special_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_items_order` (`order_id`),
  KEY `fk_order_items_food` (`food_item_id`),
  KEY `idx_order_items_order_food` (`order_id`,`food_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`, `created_at`) VALUES
(1, 1, 1, 2, 299.00, 598.00, 'Thêm tôm nướng', '2025-06-09 12:35:13'),
(2, 2, 5, 1, 100000.00, 100000.00, NULL, '2025-06-10 06:32:35'),
(3, 3, 5, 2, 100000.00, 200000.00, NULL, '2025-06-11 06:56:11'),
(4, 4, 5, 1, 100000.00, 100000.00, NULL, '2025-06-11 07:16:24'),
(5, 4, 13, 1, 10000.00, 10000.00, NULL, '2025-06-11 07:16:24'),
(6, 5, 5, 1, 100000.00, 100000.00, NULL, '2025-06-11 07:50:32'),
(7, 5, 13, 1, 10000.00, 10000.00, NULL, '2025-06-11 07:50:32'),
(8, 6, 5, 1, 100000.00, 100000.00, NULL, '2025-06-11 07:51:56'),
(9, 7, 5, 1, 100000.00, 100000.00, NULL, '2025-06-11 07:56:58'),
(10, 8, 5, 2, 100000.00, 200000.00, NULL, '2025-06-12 20:59:50'),
(11, 9, 5, 1, 100000.00, 100000.00, NULL, '2025-06-13 02:02:28'),
(12, 10, 5, 1, 100000.00, 100000.00, NULL, '2025-06-13 02:25:52'),
(13, 10, 13, 1, 10000.00, 10000.00, NULL, '2025-06-13 02:25:52'),
(14, 11, 13, 1, 10000.00, 10000.00, NULL, '2025-06-14 07:27:59'),
(15, 12, 13, 5, 10000.00, 50000.00, NULL, '2025-06-14 08:42:59'),
(16, 12, 5, 1, 100000.00, 100000.00, NULL, '2025-06-14 08:42:59'),
(17, 13, 14, 1, 10000.00, 10000.00, NULL, '2025-07-07 03:58:01'),
(18, 14, 14, 1, 10000.00, 10000.00, NULL, '2025-07-07 04:18:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vnpay',
  `vnp_txn_ref` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vnp_amount` bigint NOT NULL,
  `vnp_order_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `vnp_response_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnp_transaction_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnp_bank_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnp_pay_date` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnp_secure_hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_status` enum('pending','processing','completed','failed','cancelled','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vnp_txn_ref` (`vnp_txn_ref`),
  KEY `fk_payments_order` (`order_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_vnp_transaction_no` (`vnp_transaction_no`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `vnp_txn_ref`, `vnp_amount`, `vnp_order_info`, `vnp_response_code`, `vnp_transaction_no`, `vnp_bank_code`, `vnp_pay_date`, `vnp_secure_hash`, `payment_status`, `payment_data`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 9, 'vnpay', '1749805364_9', 13500000, 'Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking', NULL, NULL, NULL, NULL, NULL, 'pending', '{\"vnp_Amount\": 13500000, \"vnp_IpAddr\": \"::1\", \"vnp_Locale\": \"vn\", \"vnp_TxnRef\": \"1749805364_9\", \"vnp_Command\": \"pay\", \"vnp_TmnCode\": \"YOUR_TMN_CODE\", \"vnp_Version\": \"2.1.0\", \"vnp_BankCode\": \"NCB\", \"vnp_CurrCode\": \"VND\", \"vnp_OrderInfo\": \"Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking\", \"vnp_OrderType\": \"billpayment\", \"vnp_ReturnUrl\": \"http://localhost/buffet_booking_mvc/payment/vnpay_return\", \"vnp_CreateDate\": \"20250613160244\", \"vnp_ExpireDate\": \"20250613161744\"}', '2025-06-13 09:02:44', '2025-06-13 09:02:44', NULL),
(2, 9, 'vnpay', '1749805724_9', 13500000, 'Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking', NULL, NULL, NULL, NULL, NULL, 'pending', '{\"vnp_Amount\": 13500000, \"vnp_IpAddr\": \"::1\", \"vnp_Locale\": \"vn\", \"vnp_TxnRef\": \"1749805724_9\", \"vnp_Command\": \"pay\", \"vnp_TmnCode\": \"YOUR_TMN_CODE\", \"vnp_Version\": \"2.1.0\", \"vnp_BankCode\": \"NCB\", \"vnp_CurrCode\": \"VND\", \"vnp_OrderInfo\": \"Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking\", \"vnp_OrderType\": \"billpayment\", \"vnp_ReturnUrl\": \"http://localhost/buffet_booking_mvc/payment/vnpay_return\", \"vnp_CreateDate\": \"20250613160844\", \"vnp_ExpireDate\": \"20250613162344\"}', '2025-06-13 09:08:44', '2025-06-13 09:08:44', NULL),
(3, 9, 'vnpay', '1749806177_9', 13500000, 'Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking', NULL, NULL, NULL, NULL, NULL, 'pending', '{\"vnp_Amount\": 13500000, \"vnp_IpAddr\": \"::1\", \"vnp_Locale\": \"vn\", \"vnp_TxnRef\": \"1749806177_9\", \"vnp_Command\": \"pay\", \"vnp_TmnCode\": \"YOUR_TMN_CODE\", \"vnp_Version\": \"2.1.0\", \"vnp_BankCode\": \"NCB\", \"vnp_CurrCode\": \"VND\", \"vnp_OrderInfo\": \"Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking\", \"vnp_OrderType\": \"billpayment\", \"vnp_ReturnUrl\": \"http://localhost/buffet_booking_mvc/payment/vnpay_return\", \"vnp_CreateDate\": \"20250613161617\", \"vnp_ExpireDate\": \"20250613163117\"}', '2025-06-13 09:16:17', '2025-06-13 09:16:17', NULL),
(4, 9, 'vnpay', '1749806270_9', 13500000, 'Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking', '00', '15016588', 'NCB', '20250613161917', '06db863d18ddd9934bae3fc73e9d8d2a68afe133e5017cc709606a7db6b101546e3f1f24d99a59fbb5c78d62bae2a4ac5318643d10461592a8dadcf5ae91e4ce', 'completed', '{\"vnp_Amount\": \"13500000\", \"vnp_TxnRef\": \"1749806270_9\", \"vnp_PayDate\": \"20250613161917\", \"vnp_TmnCode\": \"NJJ0R8FS\", \"vnp_BankCode\": \"NCB\", \"vnp_CardType\": \"ATM\", \"vnp_OrderInfo\": \"Thanh toán đơn hàng #ORD684be92432e50 tại Buffet Booking\", \"vnp_BankTranNo\": \"VNP15016588\", \"vnp_SecureHash\": \"06db863d18ddd9934bae3fc73e9d8d2a68afe133e5017cc709606a7db6b101546e3f1f24d99a59fbb5c78d62bae2a4ac5318643d10461592a8dadcf5ae91e4ce\", \"vnp_ResponseCode\": \"00\", \"vnp_TransactionNo\": \"15016588\", \"vnp_TransactionStatus\": \"00\"}', '2025-06-13 09:17:50', '2025-06-13 02:18:48', '2025-06-13 02:18:48'),
(5, 10, 'vnpay', '1749806759_10', 14550000, 'Thanh toán đơn hàng #ORD684beea0c522f tại Buffet Booking', '00', '15016606', 'NCB', '20250613162726', 'c1b1b79479e1ca646965ec0002fcd61fac19724d61ebfbade59a32256c5d684627dc87caccafe62336e8fd899057ce87aaff88fa10cbf3ff47d8c58773e59efd', 'completed', '{\"vnp_Amount\": \"14550000\", \"vnp_TxnRef\": \"1749806759_10\", \"vnp_PayDate\": \"20250613162726\", \"vnp_TmnCode\": \"NJJ0R8FS\", \"vnp_BankCode\": \"NCB\", \"vnp_CardType\": \"ATM\", \"vnp_OrderInfo\": \"Thanh toán đơn hàng #ORD684beea0c522f tại Buffet Booking\", \"vnp_BankTranNo\": \"VNP15016606\", \"vnp_SecureHash\": \"c1b1b79479e1ca646965ec0002fcd61fac19724d61ebfbade59a32256c5d684627dc87caccafe62336e8fd899057ce87aaff88fa10cbf3ff47d8c58773e59efd\", \"vnp_ResponseCode\": \"00\", \"vnp_TransactionNo\": \"15016606\", \"vnp_TransactionStatus\": \"00\"}', '2025-06-13 09:25:59', '2025-06-13 02:26:53', '2025-06-13 02:26:53'),
(6, 11, 'vnpay', '1749911284_11', 4050000, 'Thanh toán đơn hàng #ORD684d86efb457e tại Buffet Booking', '00', '15018521', 'NCB', '20250614213212', 'bd07e8a6e9482cde0c334e4c4718ebdd2c28ddcd9ceff94eeccbe66c9c966dca134a8cbac4fdf2dfe67c273abdc14870465bbe7e3ba51604903b2aa69f4ade96', 'completed', '{\"vnp_Amount\": \"4050000\", \"vnp_TxnRef\": \"1749911284_11\", \"vnp_PayDate\": \"20250614213212\", \"vnp_TmnCode\": \"NJJ0R8FS\", \"vnp_BankCode\": \"NCB\", \"vnp_CardType\": \"ATM\", \"vnp_OrderInfo\": \"Thanh toán đơn hàng #ORD684d86efb457e tại Buffet Booking\", \"vnp_BankTranNo\": \"VNP15018521\", \"vnp_SecureHash\": \"bd07e8a6e9482cde0c334e4c4718ebdd2c28ddcd9ceff94eeccbe66c9c966dca134a8cbac4fdf2dfe67c273abdc14870465bbe7e3ba51604903b2aa69f4ade96\", \"vnp_ResponseCode\": \"00\", \"vnp_TransactionNo\": \"15018521\", \"vnp_TransactionStatus\": \"00\"}', '2025-06-14 14:28:04', '2025-06-14 07:31:39', '2025-06-14 07:31:39'),
(7, 12, 'vnpay', '1749915809_12', 18750000, 'Thanh toán đơn hàng #ORD684d988388537 tại Buffet Booking', '00', '15018609', 'NCB', '20250614224519', '3d3503a367d59722874e82486df3cb12f3938664ca47cd3c4ba3b601dce3ca7f27215debea3a73ff64e42c4465f105ea16579bd5a7ccb82b80d545a09f96e7a5', 'completed', '{\"vnp_Amount\": \"18750000\", \"vnp_TxnRef\": \"1749915809_12\", \"vnp_PayDate\": \"20250614224519\", \"vnp_TmnCode\": \"NJJ0R8FS\", \"vnp_BankCode\": \"NCB\", \"vnp_CardType\": \"ATM\", \"vnp_OrderInfo\": \"Thanh toán đơn hàng #ORD684d988388537 tại Buffet Booking\", \"vnp_BankTranNo\": \"VNP15018609\", \"vnp_SecureHash\": \"3d3503a367d59722874e82486df3cb12f3938664ca47cd3c4ba3b601dce3ca7f27215debea3a73ff64e42c4465f105ea16579bd5a7ccb82b80d545a09f96e7a5\", \"vnp_ResponseCode\": \"00\", \"vnp_TransactionNo\": \"15018609\", \"vnp_TransactionStatus\": \"00\"}', '2025-06-14 15:43:29', '2025-06-14 08:44:46', '2025-06-14 08:44:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

DROP TABLE IF EXISTS `promotions`;
CREATE TABLE IF NOT EXISTS `promotions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text,
  `type` enum('percentage','fixed','buy_one_get_one') NOT NULL DEFAULT 'percentage',
  `application_type` enum('all','specific_items','categories') DEFAULT 'all',
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int DEFAULT '0',
  `minimum_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `code`, `description`, `type`, `application_type`, `discount_value`, `start_date`, `end_date`, `usage_limit`, `used_count`, `minimum_amount`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Welcome Discount', 'WELCOME10', 'Get 10% off your first order', 'percentage', 'specific_items', 10.00, '2025-06-10', '2025-07-10', 100, 0, 50.00, 1, '2025-06-10 04:09:55', '2025-06-11 10:46:47'),
(2, 'Weekend Speciall', 'WEEKEND20', '20% off weekend buffet', 'percentage', 'specific_items', 20.00, '2025-06-10', '2025-08-09', NULL, 0, 100.00, 1, '2025-06-10 04:09:55', '2025-06-11 10:47:51'),
(3, 'Family Deal', 'FAMILY15', '$15 off family packages', 'fixed', 'all', 15000.00, '2025-06-10', '2025-09-08', 50, 0, 80.00, 1, '2025-06-10 04:09:55', '2025-06-11 10:48:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion_categories`
--

DROP TABLE IF EXISTS `promotion_categories`;
CREATE TABLE IF NOT EXISTS `promotion_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promotion_id` int NOT NULL,
  `category_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_promotion_id` (`promotion_id`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion_food_items`
--

DROP TABLE IF EXISTS `promotion_food_items`;
CREATE TABLE IF NOT EXISTS `promotion_food_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promotion_id` int NOT NULL,
  `food_item_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_promotion_id` (`promotion_id`),
  KEY `idx_food_item_id` (`food_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `promotion_food_items`
--

INSERT INTO `promotion_food_items` (`id`, `promotion_id`, `food_item_id`, `created_at`) VALUES
(12, 2, 15, '2025-06-11 10:47:51'),
(11, 2, 14, '2025-06-11 10:47:51'),
(10, 1, 13, '2025-06-11 10:46:47'),
(9, 1, 5, '2025-06-11 10:46:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `table_id` int DEFAULT NULL,
  `reservation_time` datetime NOT NULL,
  `number_of_guests` int NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `table_id` (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `customer_name`, `phone_number`, `table_id`, `reservation_time`, `number_of_guests`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'sadf', '23434444', NULL, '2025-06-19 19:30:00', 17, 'cancelled', '', '2025-06-06 07:03:24', '2025-06-14 11:38:15'),
(2, 4, 'Admin', '09999999', NULL, '2025-06-19 19:30:00', 5, 'confirmed', '', '2025-06-13 17:03:57', '2025-06-14 09:21:34'),
(3, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-27 12:00:00', 13, 'cancelled', 'hello', '2025-06-14 06:48:32', '2025-06-14 11:44:48'),
(4, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 12:30:00', 12, 'confirmed', 'jhfjhg', '2025-06-14 06:50:26', '2025-06-14 07:07:27'),
(5, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 12:30:00', 12, 'confirmed', 'jhfjhg', '2025-06-14 06:51:08', '2025-06-14 08:11:28'),
(6, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-20 11:30:00', 12, 'confirmed', 'ss', '2025-06-14 06:57:19', '2025-06-14 07:38:35'),
(7, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 19:30:00', 4, 'confirmed', 's', '2025-06-14 07:03:30', '2025-06-14 07:42:18'),
(8, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-26 17:00:00', 14, 'confirmed', 'ss', '2025-06-14 07:29:28', '2025-06-14 07:32:18'),
(9, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 17:00:00', 11, 'confirmed', 'dad', '2025-06-14 07:48:23', '2025-06-14 07:48:43'),
(10, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-19 17:30:00', 15, 'confirmed', '', '2025-06-14 08:20:52', '2025-06-14 09:08:40'),
(11, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 13:30:00', 6, 'confirmed', 'ssd', '2025-06-14 08:23:37', '2025-06-14 09:38:37'),
(12, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-18 20:00:00', 4, 'confirmed', 's', '2025-06-14 08:28:08', '2025-06-14 09:05:16'),
(13, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-26 20:00:00', 10, 'confirmed', 's', '2025-06-14 08:36:42', '2025-06-14 09:22:33'),
(14, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-27 11:30:00', 6, 'cancelled', 'a', '2025-06-14 08:40:14', '2025-06-14 11:47:15'),
(15, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-25 19:00:00', 9, 'confirmed', 'nm', '2025-06-14 08:46:50', '2025-06-14 09:21:45'),
(16, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-19 17:00:00', 9, 'confirmed', 's', '2025-06-14 08:51:27', '2025-06-14 09:21:25'),
(17, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 17:00:00', 7, 'confirmed', 's', '2025-06-14 08:54:55', '2025-06-14 09:36:07'),
(18, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-20 17:00:00', 14, 'confirmed', 'd', '2025-06-14 08:59:03', '2025-06-14 09:37:20'),
(19, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-18 17:00:00', 4, 'confirmed', 's', '2025-06-14 09:29:00', '2025-06-14 09:40:14'),
(20, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-18 17:00:00', 4, 'confirmed', 's', '2025-06-14 09:29:14', '2025-06-14 09:32:32'),
(21, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-18 17:00:00', 4, 'pending', 's', '2025-06-14 09:31:06', NULL),
(22, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-26 12:30:00', 12, 'pending', 's', '2025-06-14 10:00:10', NULL),
(23, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-14 11:30:00', 11, 'pending', 's', '2025-06-14 10:02:08', NULL),
(24, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-25 13:30:00', 7, 'pending', 'd', '2025-06-14 10:05:24', NULL),
(25, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 11:30:00', 9, 'pending', 'dfd', '2025-06-14 10:59:03', NULL),
(26, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-15 12:00:00', 6, 'pending', '', '2025-06-14 15:38:22', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `restaurant_info`
--

DROP TABLE IF EXISTS `restaurant_info`;
CREATE TABLE IF NOT EXISTS `restaurant_info` (
  `id` int NOT NULL DEFAULT '1',
  `restaurant_name` varchar(255) NOT NULL DEFAULT 'Buffet Restaurant',
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `description` text,
  `opening_hours` varchar(100) DEFAULT '09:00 - 22:00',
  `capacity` int DEFAULT '100',
  `logo_url` varchar(255) DEFAULT '',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `website` varchar(255) DEFAULT '',
  `cover_image` varchar(255) DEFAULT '',
  `facebook` varchar(255) DEFAULT '',
  `instagram` varchar(255) DEFAULT '',
  `twitter` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `restaurant_info`
--

INSERT INTO `restaurant_info` (`id`, `restaurant_name`, `address`, `phone`, `email`, `description`, `opening_hours`, `capacity`, `logo_url`, `created_at`, `updated_at`, `website`, `cover_image`, `facebook`, `instagram`, `twitter`) VALUES
(1, 'Buffet Paradise Restaurant', '123 Food Street, City Center', '+1234567890', 'info@buffetparadise.com', 'Welcome to our amazing buffet restaurant with the finest cuisine and excellent service.', '09:00 - 22:00', 100, '', '2025-06-10 04:09:55', '2025-06-10 15:28:22', 'http://localhost/buffet_booking_mvc/', '', 'http://localhost/buffet_booking_mvc/', 'http://localhost/buffet_booking_mvc/', 'http://localhost/buffet_booking_mvc/');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `food_item_id` int DEFAULT NULL,
  `rating` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_verified` tinyint(1) DEFAULT '0',
  `is_approved` tinyint(1) DEFAULT '0',
  `helpful_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `photos` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reviews_user` (`user_id`),
  KEY `fk_reviews_order` (`order_id`),
  KEY `fk_reviews_food` (`food_item_id`),
  KEY `idx_rating` (`rating`),
  KEY `idx_approved` (`is_approved`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `order_id`, `food_item_id`, `rating`, `title`, `comment`, `is_verified`, `is_approved`, `helpful_count`, `created_at`, `updated_at`, `photos`) VALUES
(22, 9, 13, 14, 1, '', 'á', 1, 1, 1, '2025-07-07 14:47:23', '2025-07-07 14:47:57', '[\"review_686bde194d3bb.jpg\"]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `review_likes`
--

DROP TABLE IF EXISTS `review_likes`;
CREATE TABLE IF NOT EXISTS `review_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `review_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_review_like` (`user_id`,`review_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_review_id` (`review_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `review_likes`
--

INSERT INTO `review_likes` (`id`, `user_id`, `review_id`, `created_at`) VALUES
(9, 9, 21, '2025-07-07 12:01:34'),
(10, 9, 22, '2025-07-07 14:47:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('string','integer','boolean','json') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subcategories`
--

DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_subcategories_category` (`category_id`),
  KEY `idx_active_sort` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sub_categories`
--

DROP TABLE IF EXISTS `sub_categories`;
CREATE TABLE IF NOT EXISTS `sub_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(2, 1, 'Soup', 'Hot soups', '2025-06-05 17:39:52', '2025-06-05 17:39:52'),
(3, 1, 'Salad', 'Fresh salads', '2025-06-05 17:39:52', '2025-06-05 17:39:52'),
(7, 3, 'Ice Cream', 'Cold desserts', '2025-06-05 17:39:52', '2025-06-05 17:39:52'),
(8, 3, 'Cakes', 'Sweet cakes and pastries', '2025-06-05 17:39:52', '2025-06-05 17:39:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tables`
--

DROP TABLE IF EXISTS `tables`;
CREATE TABLE IF NOT EXISTS `tables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_available` tinyint(1) DEFAULT '1',
  `status` enum('available','occupied','reserved','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_number` (`table_number`),
  KEY `idx_capacity` (`capacity`),
  KEY `idx_available` (`is_available`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `capacity`, `location`, `description`, `is_available`, `status`, `created_at`, `updated_at`) VALUES
(7, 'D1', 10, '', '', 1, 'available', '2025-06-06 06:25:52', '2025-06-10 11:40:05'),
(8, 'D2', 10, NULL, NULL, 0, 'available', '2025-06-06 06:25:52', '2025-06-11 11:29:21'),
(9, 'M4', 10, 'Private Room', '', 1, 'available', '2025-06-09 15:04:48', '2025-06-09 15:04:48'),
(10, 'M5', 15, '', '', 1, 'available', '2025-06-09 17:09:14', '2025-06-09 17:09:29'),
(11, 'M6', 20, 'NEW', 'NEW', 0, 'available', '2025-06-10 12:05:04', '2025-06-10 12:05:04'),
(12, 'TABLE_5', 5, 'Main Dining', '', 1, 'available', '2025-06-10 13:38:46', '2025-06-10 13:38:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('customer','manager','super_admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `email_verified` tinyint(1) DEFAULT '0',
  `email_verification_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_expires` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `preferences` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `date_of_birth`, `address`, `avatar`, `role`, `email_verified`, `email_verification_token`, `password_reset_token`, `password_reset_expires`, `last_login`, `is_active`, `preferences`, `created_at`, `updated_at`) VALUES
(1, 'Adminn', 'User', 'admin@buffet.com', '$2y$10$5gjA8CuwdXg3/7DFmUC8AOdVRhej2W402IRBfnsWPjIAhw/A4csce', '0000000000', NULL, '', NULL, 'manager', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-06 04:03:21', '2025-06-10 06:53:18'),
(4, 'Super', 'Admin', 'superadmin@buffet.com', '$2y$10$2IA71pot7oZqhM5DTwMRteOACsDF1F5TIKA3FLfshyU/3zpFvYFPa', '+1234567890', NULL, NULL, NULL, 'super_admin', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-10 04:09:56', '2025-06-10 04:09:56'),
(5, 'New', 'New', 'new@gmail.com', '$2y$10$xwTmHBrRyc/LGhDsM7QYwuPIJxZtaLqewWnQV8QxI/K.CQUTYRUI2', '009999999', NULL, 'HCM', NULL, 'customer', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-10 00:19:41', '2025-06-10 07:19:41'),
(7, 'hehe', 'heheheh', 'hehe@gmail.com', '$2y$10$p48sD//5xANycMpPG1L5eurklilL12lG6rnaZWsNgKyusYqdewjhW', '0999999', NULL, '', NULL, 'manager', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-10 06:45:59', '2025-06-10 13:46:09'),
(8, 'Nguyen', 'Dat', 'dat61222@gmail.com', '$2y$10$rFOl1nEFPjnedRtkSrlC1O5yCIwwkg3tcjtvY9SO0BNkJfOiKpEz2', '099999999', NULL, 'HCM', NULL, 'customer', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-13 02:24:20', '2025-06-13 09:24:20'),
(9, 'Đỗ', 'Ngọc Hiếu', 'dongochieu333@gmail.com', '$2y$10$ovSlhNQssm1r3QLW1bXy9eGDVn99dorGAtfAT0/2HqsaIJtWIhgn.', '00000000000', '2004-06-16', '', NULL, 'customer', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-13 23:35:59', '2025-06-14 15:58:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `address_line` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_addresses_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_bookings_with_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_bookings_with_details`;
CREATE TABLE IF NOT EXISTS `v_bookings_with_details` (
`id` int
,`user_id` int
,`table_id` int
,`customer_name` varchar(100)
,`customer_email` varchar(100)
,`customer_phone` varchar(20)
,`booking_date` date
,`booking_time` time
,`guest_count` int
,`special_requests` text
,`status` enum('pending','confirmed','seated','completed','cancelled','no_show')
,`booking_reference` varchar(20)
,`notes` text
,`created_at` timestamp
,`updated_at` timestamp
,`table_number` varchar(10)
,`table_capacity` int
,`table_location` varchar(50)
,`first_name` varchar(50)
,`last_name` varchar(50)
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_orders_with_totals`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_orders_with_totals`;
CREATE TABLE IF NOT EXISTS `v_orders_with_totals` (
`id` int
,`user_id` int
,`booking_id` int
,`order_number` varchar(20)
,`customer_name` varchar(100)
,`customer_email` varchar(100)
,`customer_phone` varchar(20)
,`order_type` enum('dine_in','takeout','delivery')
,`subtotal` decimal(10,2)
,`tax_amount` decimal(10,2)
,`total_amount` decimal(10,2)
,`payment_method` enum('cod','cash','credit_card','debit_card','digital_wallet','bank_transfer','vnpay')
,`payment_status` enum('pending','paid','failed','refunded')
,`status` enum('pending','confirmed','preparing','ready','completed','delivered','cancelled')
,`special_instructions` text
,`estimated_ready_time` timestamp
,`completed_at` timestamp
,`created_at` timestamp
,`updated_at` timestamp
,`total_items` bigint
,`first_name` varchar(50)
,`last_name` varchar(50)
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_payments_with_orders`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_payments_with_orders`;
CREATE TABLE IF NOT EXISTS `v_payments_with_orders` (
`id` int
,`order_id` int
,`payment_method` varchar(50)
,`vnp_txn_ref` varchar(100)
,`vnp_amount` bigint
,`vnp_order_info` text
,`vnp_response_code` varchar(2)
,`vnp_transaction_no` varchar(100)
,`vnp_bank_code` varchar(20)
,`vnp_pay_date` varchar(14)
,`vnp_secure_hash` text
,`payment_status` enum('pending','processing','completed','failed','cancelled','refunded')
,`payment_data` json
,`created_at` timestamp
,`updated_at` timestamp
,`completed_at` timestamp
,`order_number` varchar(20)
,`customer_name` varchar(100)
,`customer_email` varchar(100)
,`customer_phone` varchar(20)
,`order_total` decimal(10,2)
,`order_status` enum('pending','confirmed','preparing','ready','completed','delivered','cancelled')
,`order_created_at` timestamp
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_bookings_with_details`
--
DROP TABLE IF EXISTS `v_bookings_with_details`;

DROP VIEW IF EXISTS `v_bookings_with_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_bookings_with_details`  AS SELECT `b`.`id` AS `id`, `b`.`user_id` AS `user_id`, `b`.`table_id` AS `table_id`, `b`.`customer_name` AS `customer_name`, `b`.`customer_email` AS `customer_email`, `b`.`customer_phone` AS `customer_phone`, `b`.`booking_date` AS `booking_date`, `b`.`booking_time` AS `booking_time`, `b`.`guest_count` AS `guest_count`, `b`.`special_requests` AS `special_requests`, `b`.`status` AS `status`, `b`.`booking_reference` AS `booking_reference`, `b`.`notes` AS `notes`, `b`.`created_at` AS `created_at`, `b`.`updated_at` AS `updated_at`, `t`.`table_number` AS `table_number`, `t`.`capacity` AS `table_capacity`, `t`.`location` AS `table_location`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name` FROM ((`bookings` `b` left join `tables` `t` on((`b`.`table_id` = `t`.`id`))) left join `users` `u` on((`b`.`user_id` = `u`.`id`))) ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_orders_with_totals`
--
DROP TABLE IF EXISTS `v_orders_with_totals`;

DROP VIEW IF EXISTS `v_orders_with_totals`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_orders_with_totals`  AS SELECT `o`.`id` AS `id`, `o`.`user_id` AS `user_id`, `o`.`booking_id` AS `booking_id`, `o`.`order_number` AS `order_number`, `o`.`customer_name` AS `customer_name`, `o`.`customer_email` AS `customer_email`, `o`.`customer_phone` AS `customer_phone`, `o`.`order_type` AS `order_type`, `o`.`subtotal` AS `subtotal`, `o`.`tax_amount` AS `tax_amount`, `o`.`total_amount` AS `total_amount`, `o`.`payment_method` AS `payment_method`, `o`.`payment_status` AS `payment_status`, `o`.`status` AS `status`, `o`.`special_instructions` AS `special_instructions`, `o`.`estimated_ready_time` AS `estimated_ready_time`, `o`.`completed_at` AS `completed_at`, `o`.`created_at` AS `created_at`, `o`.`updated_at` AS `updated_at`, count(`oi`.`id`) AS `total_items`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name` FROM ((`orders` `o` left join `order_items` `oi` on((`o`.`id` = `oi`.`order_id`))) left join `users` `u` on((`o`.`user_id` = `u`.`id`))) GROUP BY `o`.`id` ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_payments_with_orders`
--
DROP TABLE IF EXISTS `v_payments_with_orders`;

DROP VIEW IF EXISTS `v_payments_with_orders`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_payments_with_orders`  AS SELECT `p`.`id` AS `id`, `p`.`order_id` AS `order_id`, `p`.`payment_method` AS `payment_method`, `p`.`vnp_txn_ref` AS `vnp_txn_ref`, `p`.`vnp_amount` AS `vnp_amount`, `p`.`vnp_order_info` AS `vnp_order_info`, `p`.`vnp_response_code` AS `vnp_response_code`, `p`.`vnp_transaction_no` AS `vnp_transaction_no`, `p`.`vnp_bank_code` AS `vnp_bank_code`, `p`.`vnp_pay_date` AS `vnp_pay_date`, `p`.`vnp_secure_hash` AS `vnp_secure_hash`, `p`.`payment_status` AS `payment_status`, `p`.`payment_data` AS `payment_data`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`completed_at` AS `completed_at`, `o`.`order_number` AS `order_number`, `o`.`customer_name` AS `customer_name`, `o`.`customer_email` AS `customer_email`, `o`.`customer_phone` AS `customer_phone`, `o`.`total_amount` AS `order_total`, `o`.`status` AS `order_status`, `o`.`created_at` AS `order_created_at` FROM (`payments` `p` left join `orders` `o` on((`p`.`order_id` = `o`.`id`))) ORDER BY `p`.`created_at` DESC ;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_table` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_food` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `food_items`
--
ALTER TABLE `food_items`
  ADD CONSTRAINT `fk_food_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_food_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_food` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`),
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `fk_subcategories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
