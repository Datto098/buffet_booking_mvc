-- SQL script để thêm buffet pricing system và invoice generation cho dine-in orders
-- File: create_invoice_system.sql

-- 1. Tạo bảng buffet_pricing để quản lý giá buffet
CREATE TABLE IF NOT EXISTS `buffet_pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('adult','child','senior') NOT NULL,
  `age_min` int(11) DEFAULT NULL,
  `age_max` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
