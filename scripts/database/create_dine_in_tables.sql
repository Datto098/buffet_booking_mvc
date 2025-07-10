-- Script tạo bảng cho chức năng đặt món tại bàn (Dine-in)

-- Bảng đơn hàng tại bàn
CREATE TABLE IF NOT EXISTS `dine_in_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_id` int(11) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`),
  CONSTRAINT `dine_in_orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chi tiết đơn hàng tại bàn
CREATE TABLE IF NOT EXISTS `dine_in_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `food_id` (`food_id`),
  CONSTRAINT `dine_in_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `dine_in_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dine_in_order_items_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm cột trạng thái vào bảng tables nếu chưa có
ALTER TABLE `tables`
ADD COLUMN IF NOT EXISTS `status` enum('available','occupied','reserved') NOT NULL DEFAULT 'available' AFTER `capacity`,
ADD COLUMN IF NOT EXISTS `qr_code` varchar(255) DEFAULT NULL AFTER `status`;

-- Thêm indexes
CREATE INDEX IF NOT EXISTS `idx_dine_in_orders_status` ON `dine_in_orders` (`status`);
CREATE INDEX IF NOT EXISTS `idx_dine_in_orders_created_at` ON `dine_in_orders` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_tables_status` ON `tables` (`status`);

-- Thêm cột vào bảng food_items nếu chưa có
ALTER TABLE food_items ADD COLUMN IF NOT EXISTS is_popular BOOLEAN DEFAULT FALSE AFTER spice_level;
ALTER TABLE food_items ADD COLUMN IF NOT EXISTS is_new BOOLEAN DEFAULT FALSE AFTER is_popular;

-- Tạo trigger để tự động cập nhật updated_at
DROP TRIGGER IF EXISTS update_dine_in_orders_updated_at;
CREATE TRIGGER update_dine_in_orders_updated_at
    BEFORE UPDATE ON dine_in_orders
    FOR EACH ROW
    SET NEW.updated_at = CURRENT_TIMESTAMP;

-- Tạo view để thống kê đơn hàng
CREATE OR REPLACE VIEW dine_in_order_stats AS
SELECT
    DATE(created_at) as order_date,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN status = 'preparing' THEN 1 END) as preparing_orders,
    COUNT(CASE WHEN status = 'served' THEN 1 END) as served_orders,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders
FROM dine_in_orders
GROUP BY DATE(created_at)
ORDER BY order_date DESC;

-- Tạo view để thống kê theo bàn
CREATE OR REPLACE VIEW table_order_stats AS
SELECT
    t.id as table_id,
    t.table_number,
    t.capacity,
    t.location,
    COUNT(dio.id) as total_orders,
    SUM(dio.total_amount) as total_revenue,
    AVG(dio.total_amount) as avg_order_value,
    MAX(dio.created_at) as last_order_time
FROM tables t
LEFT JOIN dine_in_orders dio ON t.id = dio.table_id
GROUP BY t.id, t.table_number, t.capacity, t.location
ORDER BY t.table_number;

-- Thêm dữ liệu mẫu cho một số món phổ biến (nếu có dữ liệu)
UPDATE food_items SET is_popular = TRUE WHERE id IN (1, 2, 3, 4, 5);
UPDATE food_items SET is_new = TRUE WHERE id IN (6, 7, 8, 9, 10);
