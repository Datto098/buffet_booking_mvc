-- Script cập nhật cấu trúc bảng dine_in_orders hoàn chỉnh
-- Chạy file này để đảm bảo bảng dine_in_orders có đầy đủ các cột cần thiết

-- Xóa bảng cũ nếu cần (cẩn thận với dữ liệu)
-- DROP TABLE IF EXISTS `dine_in_order_items`;
-- DROP TABLE IF EXISTS `dine_in_orders`;

-- Tạo lại bảng dine_in_orders với cấu trúc hoàn chỉnh
CREATE TABLE IF NOT EXISTS `dine_in_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `dine_in_orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dine_in_orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo lại bảng dine_in_order_items
CREATE TABLE IF NOT EXISTS `dine_in_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `special_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `food_id` (`food_id`),
  CONSTRAINT `dine_in_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `dine_in_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dine_in_order_items_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo trigger để tự động cập nhật updated_at
DROP TRIGGER IF EXISTS update_dine_in_orders_updated_at;
CREATE TRIGGER update_dine_in_orders_updated_at
    BEFORE UPDATE ON dine_in_orders
    FOR EACH ROW
    SET NEW.updated_at = CURRENT_TIMESTAMP;

-- Tạo trigger để tự động cập nhật total_amount
DROP TRIGGER IF EXISTS update_dine_in_order_total;
CREATE TRIGGER update_dine_in_order_total
    AFTER INSERT ON dine_in_order_items
    FOR EACH ROW
    UPDATE dine_in_orders
    SET total_amount = (
        SELECT SUM(total)
        FROM dine_in_order_items
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;

DROP TRIGGER IF EXISTS update_dine_in_order_total_update;
CREATE TRIGGER update_dine_in_order_total_update
    AFTER UPDATE ON dine_in_order_items
    FOR EACH ROW
    UPDATE dine_in_orders
    SET total_amount = (
        SELECT SUM(total)
        FROM dine_in_order_items
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;

DROP TRIGGER IF EXISTS update_dine_in_order_total_delete;
CREATE TRIGGER update_dine_in_order_total_delete
    AFTER DELETE ON dine_in_order_items
    FOR EACH ROW
    UPDATE dine_in_orders
    SET total_amount = (
        SELECT COALESCE(SUM(total), 0)
        FROM dine_in_order_items
        WHERE order_id = OLD.order_id
    )
    WHERE id = OLD.order_id;

-- Hiển thị thông báo hoàn thành
SELECT 'Cấu trúc bảng dine_in_orders đã được cập nhật thành công!' as message;
