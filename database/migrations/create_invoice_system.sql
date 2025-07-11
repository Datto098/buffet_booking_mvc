-- SQL script để thêm buffet pricing system và invoice generation
-- Ngày tạo: 2025-07-11

-- 1. Tạo bảng buffet_pricing để lưu giá vé buffet
CREATE TABLE IF NOT EXISTS `buffet_pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('adult','child') NOT NULL COMMENT 'Loại khách: người lớn hoặc trẻ em',
  `price` decimal(10,2) NOT NULL COMMENT 'Giá vé buffet',
  `age_from` int(11) DEFAULT NULL COMMENT 'Độ tuổi từ (cho trẻ em)',
  `age_to` int(11) DEFAULT NULL COMMENT 'Độ tuổi đến (cho trẻ em)',
  `description` varchar(255) DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái hoạt động',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng giá vé buffet';

-- 2. Insert giá vé mặc định
INSERT INTO `buffet_pricing` (`type`, `price`, `age_from`, `age_to`, `description`, `is_active`) VALUES
('adult', 299000.00, 12, NULL, 'Người lớn và trẻ em từ 12 tuổi trở lên', 1),
('child', 199000.00, 6, 11, 'Trẻ em từ 6-11 tuổi', 1),
('child', 99000.00, 3, 5, 'Trẻ em từ 3-5 tuổi', 1),
('child', 0.00, 0, 2, 'Trẻ em dưới 3 tuổi (miễn phí)', 1);

-- 3. Tạo bảng invoices để lưu hóa đơn
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT 'ID đơn hàng dine-in',
  `invoice_number` varchar(50) NOT NULL COMMENT 'Số hóa đơn (unique)',
  `adult_count` int(11) DEFAULT 0 COMMENT 'Số lượng người lớn',
  `child_count` int(11) DEFAULT 0 COMMENT 'Số lượng trẻ em',
  `adult_price` decimal(10,2) DEFAULT 0.00 COMMENT 'Đơn giá người lớn',
  `child_price` decimal(10,2) DEFAULT 0.00 COMMENT 'Đơn giá trẻ em',
  `buffet_total` decimal(10,2) DEFAULT 0.00 COMMENT 'Tổng tiền buffet',
  `food_total` decimal(10,2) DEFAULT 0.00 COMMENT 'Tổng tiền món gọi thêm',
  `additional_charges` json DEFAULT NULL COMMENT 'Các khoản phí phát sinh (khăn ướt, etc.)',
  `additional_total` decimal(10,2) DEFAULT 0.00 COMMENT 'Tổng phí phát sinh',
  `subtotal` decimal(10,2) DEFAULT 0.00 COMMENT 'Tổng trước thuế',
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Thuế VAT (%)',
  `tax_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Số tiền thuế',
  `total_amount` decimal(10,2) NOT NULL COMMENT 'Tổng cộng phải thanh toán',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Phương thức thanh toán',
  `payment_status` enum('pending','paid','cancelled') DEFAULT 'pending' COMMENT 'Trạng thái thanh toán',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú hóa đơn',
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin tạo hóa đơn',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `fk_invoices_order` (`order_id`),
  KEY `fk_invoices_created_by` (`created_by`),
  CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `dine_in_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_invoices_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng hóa đơn buffet';

-- 4. Tạo bảng additional_charge_types để quản lý các loại phí phát sinh
CREATE TABLE IF NOT EXISTS `additional_charge_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Tên loại phí',
  `price` decimal(10,2) NOT NULL COMMENT 'Đơn giá',
  `unit` varchar(20) DEFAULT 'cái' COMMENT 'Đơn vị tính',
  `description` varchar(255) DEFAULT NULL COMMENT 'Mô tả',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái hoạt động',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Loại phí phát sinh';

-- 5. Insert các loại phí phát sinh phổ biến
INSERT INTO `additional_charge_types` (`name`, `price`, `unit`, `description`, `is_active`) VALUES
('Khăn ướt', 5000.00, 'gói', 'Khăn ướt làm sạch tay', 1),
('Nước suối', 15000.00, 'chai', 'Nước suối đóng chai 500ml', 1),
('Nước ngọt', 25000.00, 'lon', 'Các loại nước ngọt có gas', 1),
('Bia', 35000.00, 'lon', 'Bia các loại', 1),
('Phí phục vụ', 20000.00, 'bàn', 'Phí phục vụ đặc biệt', 1),
('Đậu xe', 10000.00, 'xe', 'Phí đậu xe trong khu vực', 1);

-- 6. Trigger để tự động tạo invoice number
DELIMITER $$
CREATE TRIGGER `generate_invoice_number` BEFORE INSERT ON `invoices`
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    SELECT AUTO_INCREMENT INTO next_id
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices';

    SET NEW.invoice_number = CONCAT('INV', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(next_id, 4, '0'));
END$$
DELIMITER ;

-- 7. Update dine_in_orders table để thêm cột invoice status
ALTER TABLE `dine_in_orders`
ADD COLUMN `invoice_status` enum('none','generated','paid') DEFAULT 'none' COMMENT 'Trạng thái hóa đơn'
AFTER `status`;
