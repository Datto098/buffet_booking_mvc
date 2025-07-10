-- Script thêm các cột notes còn thiếu
-- Chạy file này để sửa lỗi "Column not found: 1054 Unknown column 'notes' in 'field list'"

-- Thêm cột notes vào bảng orders (nếu chưa có)
ALTER TABLE `orders`
ADD COLUMN IF NOT EXISTS `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
AFTER `order_notes`;

-- Thêm cột special_notes vào bảng dine_in_orders (nếu chưa có)
ALTER TABLE `dine_in_orders`
ADD COLUMN IF NOT EXISTS `special_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
AFTER `notes`;

-- Thêm cột total_amount vào bảng dine_in_orders (nếu chưa có)
ALTER TABLE `dine_in_orders`
ADD COLUMN IF NOT EXISTS `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00
AFTER `status`;

-- Thêm cột user_id vào bảng dine_in_orders (nếu chưa có)
ALTER TABLE `dine_in_orders`
ADD COLUMN IF NOT EXISTS `user_id` int(11) DEFAULT NULL
AFTER `table_id`;

-- Thêm foreign key cho user_id nếu chưa có
ALTER TABLE `dine_in_orders`
ADD CONSTRAINT IF NOT EXISTS `fk_dine_in_orders_user`
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Thêm index cho các cột mới
CREATE INDEX IF NOT EXISTS `idx_dine_in_orders_user_id` ON `dine_in_orders` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_dine_in_orders_total_amount` ON `dine_in_orders` (`total_amount`);

-- Cập nhật dữ liệu mẫu nếu cần
-- UPDATE dine_in_orders SET total_amount = 0.00 WHERE total_amount IS NULL;

-- Hiển thị thông báo hoàn thành
SELECT 'Các cột notes đã được thêm thành công!' as message;
