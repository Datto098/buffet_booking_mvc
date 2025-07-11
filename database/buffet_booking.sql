-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 11, 2025 at 02:06 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buffet_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE IF NOT EXISTS `addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `address`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Hẻm 153 Bà Hom, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', 1, '2025-07-11 05:04:37', '2025-07-11 20:49:34'),
(11, 'GiaiKhat 119 Coffee, Hoàng Hoa Thám, Thạc Gián, Thanh Khê, Phường Thanh Khê, Thành phố Đà Nẵng, Việt Nam', 1, '2025-07-11 05:07:35', '2025-07-11 10:23:36'),
(9, 'Bùi Quốc Khánh, Khu phố 1, Phường Thủ Dầu Một, Thành phố Hồ Chí Minh, Việt Nam', 1, '2025-07-11 05:05:55', '2025-07-11 10:23:37'),
(14, 'Ký Hòa, Cholon, Thủ Đức, Ho Chi Minh City, Vietnam', 1, '2025-07-11 20:49:06', '2025-07-11 20:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
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
  `booking_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `table_id`, `customer_name`, `customer_email`, `customer_phone`, `booking_date`, `booking_time`, `guest_count`, `booking_location`, `special_requests`, `status`, `booking_reference`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'Nguyễn Văn A', 'test1@example.com', '0123456789', '2025-07-08', '18:00:00', 4, NULL, NULL, 'confirmed', '', NULL, '2025-07-10 14:47:54', '2025-07-10 14:47:54'),
(64, NULL, NULL, 'Nguyễn Văn A', 'nguyenvana@example.com', '0123456789', '2025-07-10', '18:00:00', 4, NULL, NULL, 'confirmed', '5586ED9A64', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(65, NULL, NULL, 'Trần Thị B', 'tranthib@example.com', '0987654321', '2025-07-10', '19:30:00', 2, NULL, NULL, 'pending', 'C6501EDB16', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(66, NULL, NULL, 'Lê Văn C', 'levanc@example.com', '0111222333', '2025-07-09', '20:00:00', 6, NULL, NULL, 'confirmed', 'A6DB3F5B04', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(67, NULL, NULL, 'Phạm Thị D', 'phamthid@example.com', '0444555666', '2025-07-09', '17:30:00', 3, NULL, NULL, 'completed', '56611CEDA7', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(68, NULL, NULL, 'Hoàng Văn E', 'hoangvane@example.com', '0777888999', '2025-07-08', '19:30:00', 5, NULL, NULL, 'seated', 'CAE66DD946', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(69, NULL, NULL, 'Vũ Thị F', 'vuthif@example.com', '0555666777', '2025-07-08', '18:30:00', 4, NULL, NULL, 'confirmed', '81ED8AE2AA', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(70, NULL, NULL, 'Đặng Văn G', 'dangvang@example.com', '0333444555', '2025-07-07', '20:30:00', 3, NULL, NULL, 'cancelled', '049988D64B', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(71, NULL, NULL, 'Bùi Thị H', 'buithih@example.com', '0666777888', '2025-07-07', '19:00:00', 2, NULL, NULL, 'confirmed', '3643F3F06D', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(72, NULL, NULL, 'Lý Văn I', 'lyvani@example.com', '0222333444', '2025-07-05', '18:00:00', 6, NULL, NULL, 'completed', '08A09DEBB8', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(73, NULL, NULL, 'Hồ Thị K', 'hothik@example.com', '0888999000', '2025-07-05', '20:00:00', 4, NULL, NULL, 'confirmed', '8BB80E760F', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(74, NULL, NULL, 'Tô Văn L', 'tovanl@example.com', '0111000111', '2025-07-03', '19:30:00', 3, NULL, NULL, 'seated', 'A41B398C03', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(75, NULL, NULL, 'Dương Thị M', 'duongthim@example.com', '0999000999', '2025-07-03', '17:30:00', 5, NULL, NULL, 'confirmed', '4BD09977D9', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(76, NULL, NULL, 'Mai Văn N', 'maivann@example.com', '0123456780', '2025-06-30', '18:30:00', 2, NULL, NULL, 'completed', '361BF96968', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(77, NULL, NULL, 'Lâm Thị O', 'lamthio@example.com', '0987654320', '2025-06-30', '20:30:00', 4, NULL, NULL, 'confirmed', '7139A40FCA', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(78, NULL, NULL, 'Châu Văn P', 'chauvanp@example.com', '0111222330', '2025-06-25', '19:00:00', 3, NULL, NULL, 'cancelled', '206A08E04B', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(79, NULL, NULL, 'Thái Thị Q', 'thaithiq@example.com', '0444555660', '2025-06-25', '18:00:00', 6, NULL, NULL, 'confirmed', '1FCC4A7074', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(80, NULL, NULL, 'Tạ Văn R', 'tavanr@example.com', '0777888990', '2025-06-20', '20:00:00', 2, NULL, NULL, 'completed', '559E120EBB', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(81, NULL, NULL, 'Hà Thị S', 'hathis@example.com', '0555666770', '2025-06-20', '19:30:00', 4, NULL, NULL, 'seated', '5FEC92F0AB', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(82, NULL, NULL, 'Võ Văn T', 'vovant@example.com', '0333444550', '2025-06-15', '18:30:00', 3, NULL, NULL, 'confirmed', '1269B1D770', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(83, NULL, NULL, 'Ngô Thị U', 'ngothiu@example.com', '0666777880', '2025-06-15', '20:30:00', 5, NULL, NULL, 'pending', '5F4DB46B1A', NULL, '2025-07-10 15:29:30', '2025-07-10 15:29:30'),
(84, NULL, NULL, 'Nguyễn Văn A', 'nguyenvana@example.com', '0123456789', '2025-07-10', '18:00:00', 4, NULL, NULL, 'confirmed', 'B61683B9B7', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(85, NULL, NULL, 'Trần Thị B', 'tranthib@example.com', '0987654321', '2025-07-10', '19:30:00', 2, NULL, NULL, 'pending', 'A0D11686C8', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(86, NULL, NULL, 'Lê Văn C', 'levanc@example.com', '0111222333', '2025-07-09', '20:00:00', 6, NULL, NULL, 'confirmed', 'A7CC9EAD87', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(87, NULL, NULL, 'Phạm Thị D', 'phamthid@example.com', '0444555666', '2025-07-09', '17:30:00', 3, NULL, NULL, 'completed', '7E90A2E154', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(88, NULL, NULL, 'Hoàng Văn E', 'hoangvane@example.com', '0777888999', '2025-07-08', '19:30:00', 5, NULL, NULL, 'seated', '3FDD32A5FB', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(89, NULL, NULL, 'Vũ Thị F', 'vuthif@example.com', '0555666777', '2025-07-08', '18:30:00', 4, NULL, NULL, 'confirmed', 'F5B34A7892', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(90, NULL, NULL, 'Đặng Văn G', 'dangvang@example.com', '0333444555', '2025-07-07', '20:30:00', 3, NULL, NULL, 'cancelled', 'F35DEF08B4', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(91, NULL, NULL, 'Bùi Thị H', 'buithih@example.com', '0666777888', '2025-07-07', '19:00:00', 2, NULL, NULL, 'confirmed', '94DA61AF7E', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(92, NULL, NULL, 'Lý Văn I', 'lyvani@example.com', '0222333444', '2025-07-05', '18:00:00', 6, NULL, NULL, 'completed', '360BF6CDB1', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(93, NULL, NULL, 'Hồ Thị K', 'hothik@example.com', '0888999000', '2025-07-05', '20:00:00', 4, NULL, NULL, 'confirmed', '6D46CA3958', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(94, NULL, NULL, 'Tô Văn L', 'tovanl@example.com', '0111000111', '2025-07-03', '19:30:00', 3, NULL, NULL, 'seated', '44320E2284', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(95, NULL, NULL, 'Dương Thị M', 'duongthim@example.com', '0999000999', '2025-07-03', '17:30:00', 5, NULL, NULL, 'confirmed', '9F686D90FC', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(96, NULL, NULL, 'Mai Văn N', 'maivann@example.com', '0123456780', '2025-06-30', '18:30:00', 2, NULL, NULL, 'completed', '5F0E36B78E', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(97, NULL, NULL, 'Lâm Thị O', 'lamthio@example.com', '0987654320', '2025-06-30', '20:30:00', 4, NULL, NULL, 'confirmed', 'D504900014', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(98, NULL, NULL, 'Châu Văn P', 'chauvanp@example.com', '0111222330', '2025-06-25', '19:00:00', 3, NULL, NULL, 'cancelled', '13A62C418A', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(99, NULL, NULL, 'Thái Thị Q', 'thaithiq@example.com', '0444555660', '2025-06-25', '18:00:00', 6, NULL, NULL, 'confirmed', 'D7C6B40E17', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(100, NULL, NULL, 'Tạ Văn R', 'tavanr@example.com', '0777888990', '2025-06-20', '20:00:00', 2, NULL, NULL, 'completed', '95DB9452C4', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(101, NULL, NULL, 'Hà Thị S', 'hathis@example.com', '0555666770', '2025-06-20', '19:30:00', 4, NULL, NULL, 'seated', 'E4C21C1E18', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(102, NULL, NULL, 'Võ Văn T', 'vovant@example.com', '0333444550', '2025-06-15', '18:30:00', 3, NULL, NULL, 'confirmed', 'AF033C79D5', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(103, NULL, NULL, 'Ngô Thị U', 'ngothiu@example.com', '0666777880', '2025-06-15', '20:30:00', 5, NULL, NULL, 'pending', '1D36628F02', NULL, '2025-07-10 15:30:10', '2025-07-10 15:30:10'),
(104, 1, 9, 'admin', 'admin@buffet.com', '666666000', '2025-07-23', '12:00:00', 5, 'Hẻm 153 Bà Hom, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', '', 'confirmed', '19FCB2FC01', NULL, '2025-07-11 10:47:56', '2025-07-11 13:35:55'),
(105, 1, 11, 'admin', 'admin@buffet.com', '666666000', '2025-07-15', '11:30:00', 3, 'Hẻm 153 Bà Hom, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', '', 'confirmed', '1A883BC6A5', NULL, '2025-07-11 13:35:05', '2025-07-11 13:36:12'),
(106, 1, 21, 'admin', 'admin@buffet.com', '666666000', '2025-07-18', '11:00:00', 2, 'GiaiKhat 119 Coffee, Hoàng Hoa Thám, Thạc Gián, Thanh Khê, Phường Thanh Khê, Thành phố Đà Nẵng, Việt Nam', '', 'confirmed', 'E472D48995', NULL, '2025-07-11 13:36:47', '2025-07-11 13:37:17'),
(107, 4, 23, 'Admin', 'superadmin@buffet.com', '666666000', '2025-07-20', '12:00:00', 2, 'GiaiKhat 119 Coffee, Hoàng Hoa Thám, Thạc Gián, Thanh Khê, Phường Thanh Khê, Thành phố Đà Nẵng, Việt Nam', '', 'confirmed', '1C81F75761', NULL, '2025-07-11 13:51:23', '2025-07-11 13:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
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
-- Table structure for table `categories`
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
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Appetizersss', 'Starter dishes and small platess', NULL, 0, 1, '2025-06-05 17:39:52', '2025-06-12 21:33:30'),
(3, 'Desserts', 'Sweet treats and desserts', NULL, 0, 1, '2025-06-05 17:39:52', '2025-06-05 17:39:52');

-- --------------------------------------------------------

--
-- Table structure for table `dine_in_orders`
--

DROP TABLE IF EXISTS `dine_in_orders`;
CREATE TABLE IF NOT EXISTS `dine_in_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','preparing','served','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `special_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_table_id` (`table_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dine_in_orders`
--

INSERT INTO `dine_in_orders` (`id`, `table_id`, `total_amount`, `status`, `special_notes`, `created_at`, `updated_at`, `notes`, `user_id`) VALUES
(13, 11, 190000.00, 'pending', NULL, '2025-07-10 21:04:29', '2025-07-11 04:04:29', '', 9),
(14, 8, 135000.00, 'cancelled', NULL, '2025-07-11 05:23:42', '2025-07-11 13:21:22', '', 1),
(15, 8, 35000.00, 'completed', NULL, '2025-07-11 05:48:44', '2025-07-11 13:17:28', '', 1),
(16, 8, 50000.00, 'completed', NULL, '2025-07-11 06:21:41', '2025-07-11 13:22:58', 'CAY', 1);

--
-- Triggers `dine_in_orders`
--
DROP TRIGGER IF EXISTS `update_dine_in_orders_updated_at`;
DELIMITER $$
CREATE TRIGGER `update_dine_in_orders_updated_at` BEFORE UPDATE ON `dine_in_orders` FOR EACH ROW SET NEW.updated_at = CURRENT_TIMESTAMP
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dine_in_order_items`
--

DROP TABLE IF EXISTS `dine_in_order_items`;
CREATE TABLE IF NOT EXISTS `dine_in_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `food_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `food_id` (`food_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dine_in_order_items`
--

INSERT INTO `dine_in_order_items` (`id`, `order_id`, `food_id`, `quantity`, `price`, `total`, `created_at`) VALUES
(21, 13, 16, 12, 15000.00, 180000.00, '2025-07-11 11:04:29'),
(22, 13, 13, 1, 10000.00, 10000.00, '2025-07-11 11:04:29'),
(23, 14, 5, 1, 100000.00, 100000.00, '2025-07-11 19:23:42'),
(24, 14, 16, 1, 15000.00, 15000.00, '2025-07-11 19:23:42'),
(25, 14, 10, 1, 20000.00, 20000.00, '2025-07-11 19:23:42'),
(26, 15, 16, 1, 15000.00, 15000.00, '2025-07-11 19:48:44'),
(27, 15, 11, 1, 20000.00, 20000.00, '2025-07-11 19:48:44'),
(28, 16, 7, 1, 20000.00, 20000.00, '2025-07-11 20:21:41'),
(29, 16, 9, 1, 30000.00, 30000.00, '2025-07-11 20:21:41');

-- --------------------------------------------------------

--
-- Stand-in structure for view `dine_in_order_stats`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `dine_in_order_stats`;
CREATE TABLE IF NOT EXISTS `dine_in_order_stats` (
`order_date` date
,`total_orders` bigint
,`total_revenue` decimal(32,2)
,`pending_orders` bigint
,`preparing_orders` bigint
,`served_orders` bigint
,`completed_orders` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
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
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `category_id`, `subcategory_id`, `name`, `description`, `price`, `image`, `ingredients`, `allergens`, `dietary_info`, `nutrition_info`, `preparation_time`, `spice_level`, `is_popular`, `is_new`, `is_seasonal`, `is_available`, `sort_order`, `created_at`, `updated_at`) VALUES
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
(16, 3, NULL, 'Bia', 'Có thể thêm bia lon Heineken, Tiger', 15000.00, '1749450774_bia-tiger-sleek-5-abv-lon-330ml-281124-112850-1732768166826.webp', NULL, NULL, NULL, NULL, NULL, 'none', 0, 0, 0, 1, 0, '2025-06-08 23:32:54', '2025-07-10 11:26:28');

-- --------------------------------------------------------

--
-- Table structure for table `internal_messages`
--

DROP TABLE IF EXISTS `internal_messages`;
CREATE TABLE IF NOT EXISTS `internal_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_type` enum('system_update','policy_change','maintenance','personal','general') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `priority` enum('low','normal','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `is_broadcast` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_internal_messages_sender` (`sender_id`),
  KEY `idx_message_type` (`message_type`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_messages`
--

INSERT INTO `internal_messages` (`id`, `sender_id`, `title`, `content`, `attachment_path`, `attachment_name`, `message_type`, `priority`, `is_broadcast`, `created_at`, `updated_at`) VALUES
(1, 4, 'Chào mừng sử dụng hệ thống thông báo nội bộ', 'Đây là thông báo đầu tiên để kiểm tra hệ thống thông báo nội bộ. Hệ thống này cho phép Super Admin gửi thông báo đến các Admin một cách nhanh chóng và hiệu quả.', NULL, NULL, 'general', 'normal', 1, '2025-07-08 16:36:28', '2025-07-08 16:36:28'),
(16, 4, 'Thông báo mới', 'Thông báo mới', 'uploads/internal_messages/68710f3780931_1752239927.jpg', 'download (1).jpg', 'general', 'normal', 0, '2025-07-11 13:18:47', '2025-07-11 13:18:47'),
(17, 4, 'TEST', 'TEST', 'uploads/internal_messages/68710f6695207_1752239974.jpg', 'porsche_911_st_2023_car_4k_hd_cars-3840x2160.jpg', 'general', 'urgent', 0, '2025-07-11 13:19:34', '2025-07-11 13:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `internal_message_recipients`
--

DROP TABLE IF EXISTS `internal_message_recipients`;
CREATE TABLE IF NOT EXISTS `internal_message_recipients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message_id` int NOT NULL,
  `recipient_id` int NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_message_recipient` (`message_id`,`recipient_id`),
  KEY `fk_message_recipients_message` (`message_id`),
  KEY `fk_message_recipients_recipient` (`recipient_id`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_message_recipients`
--

INSERT INTO `internal_message_recipients` (`id`, `message_id`, `recipient_id`, `is_read`, `read_at`, `created_at`) VALUES
(1, 1, 1, 1, '2025-07-10 22:30:19', '2025-07-08 16:36:28'),
(2, 1, 4, 0, NULL, '2025-07-08 16:36:28'),
(17, 16, 1, 1, '2025-07-11 13:18:52', '2025-07-11 13:18:47'),
(18, 17, 1, 1, '2025-07-11 13:19:38', '2025-07-11 13:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `news`
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
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `excerpt`, `author_id`, `image_url`, `is_published`, `is_featured`, `meta_title`, `meta_description`, `views_count`, `created_at`, `updated_at`) VALUES
(2, '🍳 Cách Làm Cơm Chiên Trứng Kiểu Nhật – Ngon, Nhanh, Đẹp Như Trong Anime!', '<h2>🍳 C&aacute;ch L&agrave;m Cơm Chi&ecirc;n Trứng Kiểu Nhật &ndash; Ngon, Nhanh, Đẹp Như Trong Anime!</h2>\r\n\r\n<p>![thumbnail đang được tạo...]</p>\r\n\r\n<h3>🧂 Nguy&ecirc;n liệu (cho 1-2 người ăn):</h3>\r\n\r\n<ul>\r\n	<li>\r\n	<p>1 ch&eacute;n cơm nguội (c&agrave;ng nguội c&agrave;ng ngon!)</p>\r\n	</li>\r\n	<li>\r\n	<p>2 quả trứng g&agrave;</p>\r\n	</li>\r\n	<li>\r\n	<p>1/2 củ h&agrave;nh t&acirc;y (băm nhỏ)</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng canh nước tương Nhật (shoyu)</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng c&agrave; ph&ecirc; dầu m&egrave;</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng c&agrave; ph&ecirc; đường</p>\r\n	</li>\r\n	<li>\r\n	<p>Một &iacute;t ti&ecirc;u, h&agrave;nh l&aacute;</p>\r\n	</li>\r\n	<li>\r\n	<p>1 muỗng canh dầu ăn</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3>🔪 C&aacute;ch l&agrave;m:</h3>\r\n\r\n<ol>\r\n	<li>\r\n	<p><strong>L&agrave;m trứng Tamagoyaki trước</strong>:</p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>Đ&aacute;nh tan trứng với 1 muỗng c&agrave; ph&ecirc; đường v&agrave; t&iacute; x&iacute;u muối.</p>\r\n		</li>\r\n		<li>\r\n		<p>Đun n&oacute;ng chảo chống d&iacute;nh, cho một lớp mỏng trứng v&agrave;o, nghi&ecirc;ng chảo cho trứng d&agrave;n đều.</p>\r\n		</li>\r\n		<li>\r\n		<p>Khi gần ch&iacute;n, cuộn lại rồi đổ th&ecirc;m một lớp trứng, lặp lại đến hết. Cuộn trứng xong để sang một b&ecirc;n.</p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>Chi&ecirc;n cơm</strong>:</p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>Trong c&ugrave;ng chảo, cho dầu ăn v&agrave;o rồi x&agrave;o h&agrave;nh t&acirc;y đến khi trong.</p>\r\n		</li>\r\n		<li>\r\n		<p>Cho cơm v&agrave;o, đảo đều tay.</p>\r\n		</li>\r\n		<li>\r\n		<p>N&ecirc;m nước tương, dầu m&egrave;, ch&uacute;t ti&ecirc;u, trộn đều. Chi&ecirc;n đến khi cơm săn lại, dậy m&ugrave;i thơm.</p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>Ho&agrave;n thiện m&oacute;n ăn</strong>:</p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>Cắt trứng Tamagoyaki th&agrave;nh l&aacute;t.</p>\r\n		</li>\r\n		<li>\r\n		<p>B&agrave;y cơm ra đĩa, xếp trứng l&ecirc;n tr&ecirc;n.</p>\r\n		</li>\r\n		<li>\r\n		<p>Rắc h&agrave;nh l&aacute;, th&ecirc;m t&iacute; tương ớt nếu th&iacute;ch ăn cay.</p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n</ol>\r\n\r\n<h3>💡 Tips nhỏ:</h3>\r\n\r\n<ul>\r\n	<li>\r\n	<p>D&ugrave;ng cơm nguội để chi&ecirc;n sẽ gi&uacute;p hạt cơm tơi, kh&ocirc;ng bị nh&atilde;o.</p>\r\n	</li>\r\n	<li>\r\n	<p>C&oacute; thể th&ecirc;m topping như x&uacute;c x&iacute;ch, thanh cua hoặc bắp để tăng vị v&agrave; m&agrave;u sắc.</p>\r\n	</li>\r\n</ul>\r\n\r\n<hr />\r\n<p>🍽 M&oacute;n n&agrave;y l&agrave; ch&acirc;n &aacute;i buổi tối sau giờ l&agrave;m/học! Vừa dễ l&agrave;m, vừa giống mấy m&oacute;n anime hay c&oacute;. Ai m&ecirc; Nhật l&agrave; phải thử!</p>\r\n', '🍳 Cách Làm Cơm Chiên Trứng Kiểu Nhật – Ngon, Nhanh, Đẹp Như Trong Anime!\r\n![thumbnail đang được tạo...]', 1, '6846b3eedb6e0_0-0000-1200x675.jpg', 1, 0, '🍳 Cách Làm Cơm Chiên Trứng Kiểu Nhật – Ngon, Nhanh, Đẹp Như Trong Anime!', '', 0, '2025-06-06 07:52:00', '2025-06-09 10:14:06'),
(4, 'zddsdf', '<p><em><strong>zddsdf</strong></em></p>\r\n', 'zddsdf', 1, '6848351e61e95_banh-flan-recipe-f3.jpg', 0, 0, 'zddsdf', '', 0, '2025-06-10 13:37:34', '2025-06-10 13:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
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
-- Dumping data for table `notifications`
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
-- Table structure for table `orders`
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
-- Dumping data for table `orders`
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
-- Table structure for table `order_items`
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
-- Dumping data for table `order_items`
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
-- Table structure for table `payments`
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
-- Dumping data for table `payments`
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
-- Table structure for table `promotions`
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `code`, `description`, `type`, `application_type`, `discount_value`, `start_date`, `end_date`, `usage_limit`, `used_count`, `minimum_amount`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Welcome Discount', 'WELCOME10', 'Get 10% off your first order', 'percentage', 'specific_items', 10.00, '2025-06-10', '2025-07-31', 100, 0, 50.00, 1, '2025-06-10 04:09:55', '2025-07-11 11:54:52'),
(2, 'Weekend Speciall', 'WEEKEND20', '20% off weekend buffet', 'percentage', 'specific_items', 20.00, '2025-06-10', '2025-08-09', NULL, 0, 100.00, 1, '2025-06-10 04:09:55', '2025-07-10 21:16:32'),
(3, 'Family Deal', 'FAMILY15', '$15 off family packages', 'fixed', 'specific_items', 15000.00, '2025-06-08', '2025-09-11', 50, 0, 80.00, 1, '2025-06-10 04:09:55', '2025-07-11 11:54:29'),
(4, 'Giảm giá đặc biệt', 'GGDB', 'Giảm giá đặc biệt', 'percentage', 'specific_items', 20.00, '2025-07-09', '2025-07-19', 100, 0, 10000.00, 1, '2025-07-11 12:12:37', '2025-07-11 12:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_categories`
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
-- Table structure for table `promotion_food_items`
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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promotion_food_items`
--

INSERT INTO `promotion_food_items` (`id`, `promotion_id`, `food_item_id`, `created_at`) VALUES
(16, 2, 15, '2025-07-10 21:16:32'),
(15, 2, 14, '2025-07-10 21:16:32'),
(29, 1, 13, '2025-07-11 11:54:52'),
(28, 1, 5, '2025-07-11 11:54:52'),
(27, 3, 9, '2025-07-11 11:54:29'),
(26, 3, 7, '2025-07-11 11:54:29'),
(25, 3, 6, '2025-07-11 11:54:29'),
(30, 4, 5, '2025-07-11 12:12:37'),
(31, 4, 13, '2025-07-11 12:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `customer_name`, `phone_number`, `table_id`, `reservation_time`, `number_of_guests`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'sadf', '23434444', NULL, '2025-06-19 19:30:00', 17, 'confirmed', '', '2025-06-06 07:03:24', '2025-07-10 15:11:56'),
(2, 4, 'Admin', '09999999', NULL, '2025-06-19 19:30:00', 5, 'confirmed', '', '2025-06-13 17:03:57', '2025-06-14 09:21:34'),
(3, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-27 12:00:00', 13, 'completed', 'hello', '2025-06-14 06:48:32', '2025-07-10 15:15:46'),
(4, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 12:30:00', 12, 'confirmed', 'jhfjhg', '2025-06-14 06:50:26', '2025-06-14 07:07:27'),
(5, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 12:30:00', 12, 'confirmed', 'jhfjhg', '2025-06-14 06:51:08', '2025-06-14 08:11:28'),
(6, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-20 11:30:00', 12, 'confirmed', 'ss', '2025-06-14 06:57:19', '2025-06-14 07:38:35'),
(7, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 19:30:00', 4, 'confirmed', 's', '2025-06-14 07:03:30', '2025-06-14 07:42:18'),
(8, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-26 17:00:00', 14, 'completed', 'ss', '2025-06-14 07:29:28', '2025-07-10 15:16:00'),
(9, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 17:00:00', 11, 'confirmed', 'dad', '2025-06-14 07:48:23', '2025-06-14 07:48:43'),
(10, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-19 17:30:00', 15, 'confirmed', '', '2025-06-14 08:20:52', '2025-06-14 09:08:40'),
(11, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 13:30:00', 6, 'confirmed', 'ssd', '2025-06-14 08:23:37', '2025-06-14 09:38:37'),
(12, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-18 20:00:00', 4, 'confirmed', 's', '2025-06-14 08:28:08', '2025-06-14 09:05:16'),
(13, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-26 20:00:00', 10, 'completed', 's', '2025-06-14 08:36:42', '2025-07-10 15:15:56'),
(14, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-27 11:30:00', 6, 'completed', 'a', '2025-06-14 08:40:14', '2025-07-10 15:15:51'),
(15, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-25 19:00:00', 9, 'confirmed', 'nm', '2025-06-14 08:46:50', '2025-06-14 09:21:45'),
(16, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-19 17:00:00', 9, 'confirmed', 's', '2025-06-14 08:51:27', '2025-06-14 09:21:25'),
(17, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 17:00:00', 7, 'confirmed', 's', '2025-06-14 08:54:55', '2025-06-14 09:36:07'),
(18, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-20 17:00:00', 14, 'confirmed', 'd', '2025-06-14 08:59:03', '2025-06-14 09:37:20'),
(19, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-18 17:00:00', 4, 'confirmed', 's', '2025-06-14 09:29:00', '2025-06-14 09:40:14'),
(20, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-18 17:00:00', 4, 'confirmed', 's', '2025-06-14 09:29:14', '2025-06-14 09:32:32'),
(21, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-18 17:00:00', 4, 'confirmed', 's', '2025-06-14 09:31:06', '2025-07-10 15:16:28'),
(22, 9, 'Ngọc Hiếu', '0384946973', 11, '2025-06-26 12:30:00', 12, 'confirmed', 's', '2025-06-14 10:00:10', '2025-07-10 22:33:50'),
(23, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-14 11:30:00', 11, 'confirmed', 's', '2025-06-14 10:02:08', '2025-07-10 15:17:00'),
(24, 9, 'Ngọc Hiếu', '0384946972', NULL, '2025-06-25 13:30:00', 7, 'confirmed', 'd', '2025-06-14 10:05:24', '2025-07-10 15:11:40'),
(25, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-19 11:30:00', 9, 'confirmed', 'dfd', '2025-06-14 10:59:03', '2025-07-10 15:16:07'),
(26, 9, 'Ngọc Hiếu', '0384946973', NULL, '2025-06-15 12:00:00', 6, 'confirmed', '', '2025-06-14 15:38:22', '2025-07-10 15:16:44'),
(27, 1, 'admin', '666666000', 12, '2025-07-12 11:00:00', 1, 'confirmed', '', '2025-07-10 15:23:45', '2025-07-10 18:21:32'),
(28, 1, 'admin', '666666000', 10, '2025-07-19 11:30:00', 3, 'confirmed', '', '2025-07-10 15:24:15', '2025-07-10 22:33:27'),
(29, 1, 'admin', '666666000', 8, '2025-08-01 12:00:00', 6, 'cancelled', '', '2025-07-10 15:24:38', '2025-07-10 18:16:09'),
(30, 1, 'admin', '666666000', NULL, '2025-07-16 11:00:00', 1, 'pending', '', '2025-07-11 10:11:13', NULL),
(31, 1, 'admin', '666666000', NULL, '2025-07-23 12:00:00', 5, 'pending', '', '2025-07-11 10:13:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_info`
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
  `cover_images` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `facebook` varchar(255) DEFAULT '',
  `instagram` varchar(255) DEFAULT '',
  `twitter` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `restaurant_info`
--

INSERT INTO `restaurant_info` (`id`, `restaurant_name`, `address`, `phone`, `email`, `description`, `opening_hours`, `capacity`, `logo_url`, `created_at`, `updated_at`, `website`, `cover_images`, `facebook`, `instagram`, `twitter`) VALUES
(1, 'Buffet Restaurant', '123 Food Street, City Center', '+1234567890', 'info@buffetparadise.com', 'Chào mừng bạn đến với nhà hàng buffet tuyệt vời của chúng tôi với ẩm thực hảo hạng và dịch vụ xuất sắc.', 'Thứ 2 - Thứ 6: 11:00 - 22:00\r\nThứ 7 - Chủ Nhật: 10:00 - 23:00\r\nNgày Lễ: 10:00 - 23:30', 100, '', '2025-06-09 21:09:55', '2025-07-11 13:20:41', 'http://localhost/buffet_booking_mvc/', '[\"http:\\/\\/localhost\\/buffet_booking_mvc\\/uploads\\/restaurant\\/cover_1752184885_6870383566b22.jpg\",\"http:\\/\\/localhost\\/buffet_booking_mvc\\/uploads\\/restaurant\\/cover_1752184885_6870383566d62.jpeg\",\"http:\\/\\/localhost\\/buffet_booking_mvc\\/uploads\\/restaurant\\/cover_1752184885_6870383566f42.png\",\"http:\\/\\/localhost\\/buffet_booking_mvc\\/uploads\\/restaurant\\/cover_1752184885_68703835670d6.jpg\",\"http:\\/\\/localhost\\/buffet_booking_mvc\\/uploads\\/restaurant\\/cover_1752184885_68703835671df.jpg\",\"http:\\/\\/localhost\\/buffet_booking_mvc\\/uploads\\/restaurant\\/cover_1752184885_68703835672f8.png\"]', 'http://localhost/buffet_booking_mvc/', 'http://localhost/buffet_booking_mvc/', 'http://localhost/buffet_booking_mvc/');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
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
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `order_id`, `food_item_id`, `rating`, `title`, `comment`, `is_verified`, `is_approved`, `helpful_count`, `created_at`, `updated_at`, `photos`) VALUES
(22, 9, 13, 14, 1, '', 'á', 1, 1, 1, '2025-07-07 14:47:23', '2025-07-07 14:47:57', '[\"review_686bde194d3bb.jpg\"]');

-- --------------------------------------------------------

--
-- Table structure for table `review_likes`
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
-- Dumping data for table `review_likes`
--

INSERT INTO `review_likes` (`id`, `user_id`, `review_id`, `created_at`) VALUES
(9, 9, 21, '2025-07-07 12:01:34'),
(10, 9, 22, '2025-07-07 14:47:57');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
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
-- Table structure for table `subcategories`
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
-- Table structure for table `sub_categories`
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
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(2, 1, 'Soup', 'Hot soups', '2025-06-05 17:39:52', '2025-06-05 17:39:52'),
(3, 1, 'Salad', 'Fresh salads', '2025-06-05 17:39:52', '2025-06-05 17:39:52'),
(7, 3, 'Ice Cream', 'Cold desserts', '2025-06-05 17:39:52', '2025-06-05 17:39:52'),
(8, 3, 'Cakes', 'Sweet cakes and pastries', '2025-06-05 17:39:52', '2025-06-05 17:39:52');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
CREATE TABLE IF NOT EXISTS `tables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_available` tinyint(1) DEFAULT '1',
  `status` enum('available','occupied','reserved','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_number` (`table_number`),
  KEY `idx_capacity` (`capacity`),
  KEY `idx_available` (`is_available`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `capacity`, `location`, `description`, `is_available`, `status`, `created_at`, `updated_at`) VALUES
(8, 'D2', 10, 'GiaiKhat 119 Coffee, Hoàng Hoa Thám, Thạc Gián, Thanh Khê, Phường Thanh Khê, Thành phố Đà Nẵng, Việt Nam', NULL, 0, 'available', '2025-06-06 06:25:52', '2025-07-11 13:21:41'),
(9, 'M4', 10, 'Hẻm 153 Bà Hom, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', '', 0, 'available', '2025-06-09 15:04:48', '2025-07-11 13:35:55'),
(10, 'M5', 15, 'Hẻm 153 Bà Hom, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', '', 0, 'available', '2025-06-09 17:09:14', '2025-07-11 11:42:00'),
(11, 'M6', 20, 'Hẻm 153 Bà Hom, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', '', 0, 'available', '2025-06-10 12:05:04', '2025-07-11 13:36:12'),
(21, 'K5', 2, 'GiaiKhat 119 Coffee, Hoàng Hoa Thám, Thạc Gián, Thanh Khê, Phường Thanh Khê, Thành phố Đà Nẵng, Việt Nam', '', 0, 'available', '2025-07-10 23:56:01', '2025-07-11 13:37:17'),
(23, 'K10', 12, 'GiaiKhat 119 Coffee, Hoàng Hoa Thám, Thạc Gián, Thanh Khê, Phường Thanh Khê, Thành phố Đà Nẵng, Việt Nam', 'K10', 0, 'available', '2025-07-11 13:50:28', '2025-07-11 13:52:07');

-- --------------------------------------------------------

--
-- Stand-in structure for view `table_order_stats`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `table_order_stats`;
CREATE TABLE IF NOT EXISTS `table_order_stats` (
`table_id` int
,`table_number` varchar(10)
,`capacity` int
,`location` text
,`total_orders` bigint
,`total_revenue` decimal(32,2)
,`avg_order_value` decimal(14,6)
,`last_order_time` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `date_of_birth`, `address`, `avatar`, `role`, `email_verified`, `email_verification_token`, `password_reset_token`, `password_reset_expires`, `last_login`, `is_active`, `preferences`, `created_at`, `updated_at`) VALUES
(1, 'Adminn', 'admin', 'admin@buffet.com', '$2y$10$5gjA8CuwdXg3/7DFmUC8AOdVRhej2W402IRBfnsWPjIAhw/A4csce', '0000000000', NULL, '', NULL, 'manager', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-06 04:03:21', '2025-07-09 08:37:12'),
(4, 'Super', 'Admin', 'superadmin@buffet.com', '$2y$10$2IA71pot7oZqhM5DTwMRteOACsDF1F5TIKA3FLfshyU/3zpFvYFPa', '+1234567890', NULL, NULL, NULL, 'super_admin', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-10 04:09:56', '2025-06-10 04:09:56'),
(5, 'New', 'New', 'new@gmail.com', '$2y$10$xwTmHBrRyc/LGhDsM7QYwuPIJxZtaLqewWnQV8QxI/K.CQUTYRUI2', '009999999', NULL, 'HCM', NULL, 'customer', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-10 00:19:41', '2025-06-10 07:19:41'),
(7, 'hehe', 'heheheh', 'hehe@gmail.com', '$2y$10$p48sD//5xANycMpPG1L5eurklilL12lG6rnaZWsNgKyusYqdewjhW', '0999999', NULL, '', NULL, 'manager', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-10 06:45:59', '2025-06-10 13:46:09'),
(8, 'Nguyen', 'Dat', 'dat61222@gmail.com', '$2y$10$rFOl1nEFPjnedRtkSrlC1O5yCIwwkg3tcjtvY9SO0BNkJfOiKpEz2', '099999999', NULL, 'HCM', NULL, 'customer', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-13 02:24:20', '2025-06-13 09:24:20'),
(9, 'Đỗ', 'Ngọc Hiếu', 'dongochieu333@gmail.com', '$2y$10$ovSlhNQssm1r3QLW1bXy9eGDVn99dorGAtfAT0/2HqsaIJtWIhgn.', '00000000000', '2004-06-16', '', NULL, 'customer', 0, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-13 23:35:59', '2025-06-14 15:58:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
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
-- Stand-in structure for view `v_bookings_with_details`
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
,`table_location` text
,`first_name` varchar(50)
,`last_name` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_orders_with_totals`
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
-- Stand-in structure for view `v_payments_with_orders`
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
-- Structure for view `dine_in_order_stats`
--
DROP TABLE IF EXISTS `dine_in_order_stats`;

DROP VIEW IF EXISTS `dine_in_order_stats`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dine_in_order_stats`  AS SELECT cast(`dine_in_orders`.`created_at` as date) AS `order_date`, count(0) AS `total_orders`, sum(`dine_in_orders`.`total_amount`) AS `total_revenue`, count((case when (`dine_in_orders`.`status` = 'pending') then 1 end)) AS `pending_orders`, count((case when (`dine_in_orders`.`status` = 'preparing') then 1 end)) AS `preparing_orders`, count((case when (`dine_in_orders`.`status` = 'served') then 1 end)) AS `served_orders`, count((case when (`dine_in_orders`.`status` = 'completed') then 1 end)) AS `completed_orders` FROM `dine_in_orders` GROUP BY cast(`dine_in_orders`.`created_at` as date) ORDER BY `order_date` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `table_order_stats`
--
DROP TABLE IF EXISTS `table_order_stats`;

DROP VIEW IF EXISTS `table_order_stats`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `table_order_stats`  AS SELECT `t`.`id` AS `table_id`, `t`.`table_number` AS `table_number`, `t`.`capacity` AS `capacity`, `t`.`location` AS `location`, count(`dio`.`id`) AS `total_orders`, sum(`dio`.`total_amount`) AS `total_revenue`, avg(`dio`.`total_amount`) AS `avg_order_value`, max(`dio`.`created_at`) AS `last_order_time` FROM (`tables` `t` left join `dine_in_orders` `dio` on((`t`.`id` = `dio`.`table_id`))) GROUP BY `t`.`id`, `t`.`table_number`, `t`.`capacity`, `t`.`location` ORDER BY `t`.`table_number` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_bookings_with_details`
--
DROP TABLE IF EXISTS `v_bookings_with_details`;

DROP VIEW IF EXISTS `v_bookings_with_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_bookings_with_details`  AS SELECT `b`.`id` AS `id`, `b`.`user_id` AS `user_id`, `b`.`table_id` AS `table_id`, `b`.`customer_name` AS `customer_name`, `b`.`customer_email` AS `customer_email`, `b`.`customer_phone` AS `customer_phone`, `b`.`booking_date` AS `booking_date`, `b`.`booking_time` AS `booking_time`, `b`.`guest_count` AS `guest_count`, `b`.`special_requests` AS `special_requests`, `b`.`status` AS `status`, `b`.`booking_reference` AS `booking_reference`, `b`.`notes` AS `notes`, `b`.`created_at` AS `created_at`, `b`.`updated_at` AS `updated_at`, `t`.`table_number` AS `table_number`, `t`.`capacity` AS `table_capacity`, `t`.`location` AS `table_location`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name` FROM ((`bookings` `b` left join `tables` `t` on((`b`.`table_id` = `t`.`id`))) left join `users` `u` on((`b`.`user_id` = `u`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `v_orders_with_totals`
--
DROP TABLE IF EXISTS `v_orders_with_totals`;

DROP VIEW IF EXISTS `v_orders_with_totals`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_orders_with_totals`  AS SELECT `o`.`id` AS `id`, `o`.`user_id` AS `user_id`, `o`.`booking_id` AS `booking_id`, `o`.`order_number` AS `order_number`, `o`.`customer_name` AS `customer_name`, `o`.`customer_email` AS `customer_email`, `o`.`customer_phone` AS `customer_phone`, `o`.`order_type` AS `order_type`, `o`.`subtotal` AS `subtotal`, `o`.`tax_amount` AS `tax_amount`, `o`.`total_amount` AS `total_amount`, `o`.`payment_method` AS `payment_method`, `o`.`payment_status` AS `payment_status`, `o`.`status` AS `status`, `o`.`special_instructions` AS `special_instructions`, `o`.`estimated_ready_time` AS `estimated_ready_time`, `o`.`completed_at` AS `completed_at`, `o`.`created_at` AS `created_at`, `o`.`updated_at` AS `updated_at`, count(`oi`.`id`) AS `total_items`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name` FROM ((`orders` `o` left join `order_items` `oi` on((`o`.`id` = `oi`.`order_id`))) left join `users` `u` on((`o`.`user_id` = `u`.`id`))) GROUP BY `o`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `v_payments_with_orders`
--
DROP TABLE IF EXISTS `v_payments_with_orders`;

DROP VIEW IF EXISTS `v_payments_with_orders`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_payments_with_orders`  AS SELECT `p`.`id` AS `id`, `p`.`order_id` AS `order_id`, `p`.`payment_method` AS `payment_method`, `p`.`vnp_txn_ref` AS `vnp_txn_ref`, `p`.`vnp_amount` AS `vnp_amount`, `p`.`vnp_order_info` AS `vnp_order_info`, `p`.`vnp_response_code` AS `vnp_response_code`, `p`.`vnp_transaction_no` AS `vnp_transaction_no`, `p`.`vnp_bank_code` AS `vnp_bank_code`, `p`.`vnp_pay_date` AS `vnp_pay_date`, `p`.`vnp_secure_hash` AS `vnp_secure_hash`, `p`.`payment_status` AS `payment_status`, `p`.`payment_data` AS `payment_data`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`completed_at` AS `completed_at`, `o`.`order_number` AS `order_number`, `o`.`customer_name` AS `customer_name`, `o`.`customer_email` AS `customer_email`, `o`.`customer_phone` AS `customer_phone`, `o`.`total_amount` AS `order_total`, `o`.`status` AS `order_status`, `o`.`created_at` AS `order_created_at` FROM (`payments` `p` left join `orders` `o` on((`p`.`order_id` = `o`.`id`))) ORDER BY `p`.`created_at` DESC ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_table` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_food` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dine_in_orders`
--
ALTER TABLE `dine_in_orders`
  ADD CONSTRAINT `dine_in_orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dine_in_orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dine_in_order_items`
--
ALTER TABLE `dine_in_order_items`
  ADD CONSTRAINT `dine_in_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `dine_in_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dine_in_order_items_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `food_items`
--
ALTER TABLE `food_items`
  ADD CONSTRAINT `fk_food_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_food_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `fk_subcategories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
