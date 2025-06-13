-- Seed data for 10 comprehensive orders with delivery addresses
-- File: seed_comprehensive_orders.sql

-- First, let's clear existing orders and start fresh (optional)
DELETE FROM `order_items` WHERE order_id IN (1, 3, 4, 5);
DELETE FROM `orders` WHERE id IN (1, 3, 4, 5);

-- Reset AUTO_INCREMENT to start from 1
ALTER TABLE `orders` AUTO_INCREMENT = 1;
ALTER TABLE `order_items` AUTO_INCREMENT = 1;

-- Insert 10 comprehensive orders with complete information
INSERT INTO `orders` (
    `user_id`, `booking_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`,
    `order_type`, `subtotal`, `tax_amount`, `delivery_fee`, `service_fee`, `discount_amount`,
    `total_amount`, `payment_method`, `payment_status`, `status`,
    `delivery_address`, `delivery_ward`, `delivery_district`, `delivery_city`,
    `delivery_notes`, `special_instructions`, `order_notes`, `coupon_code`,
    `estimated_ready_time`, `completed_at`, `created_at`, `updated_at`
) VALUES

-- Order 1: Completed delivery order
(2, NULL, 'ORD2025010001', 'Nguyễn Văn An', 'nguyenvanan@gmail.com', '0912345678',
'delivery', 597.00, 59.70, 30.00, 20.00, 50.00, 656.70, 'digital_wallet', 'paid', 'completed',
'123 Đường Nguyễn Huệ, Phường Bến Nghé', 'Phường Bến Nghé', 'Quận 1', 'TP.Hồ Chí Minh',
'Giao tại cổng chính, gọi điện khi đến', 'Không cay, ít dầu mỡ', 'Khách hàng VIP, ưu tiên giao nhanh', 'WELCOME50',
'2025-01-06 12:30:00', '2025-01-06 12:25:00', '2025-01-06 11:15:00', '2025-01-06 12:25:00'),

-- Order 2: Ready takeout order
(1, NULL, 'ORD2025010002', 'Trần Thị Bích', 'tranthbich@yahoo.com', '0987654321',
'takeout', 478.00, 47.80, 0.00, 15.00, 0.00, 540.80, 'credit_card', 'paid', 'ready',
NULL, NULL, NULL, NULL,
'Đóng gói riêng từng món', 'Món chay, không hành tỏi', 'Đặt trước cho buổi trưa', NULL,
'2025-01-06 13:00:00', NULL, '2025-01-06 11:30:00', '2025-01-06 12:45:00'),

-- Order 3: Preparing dine-in order
(2, 1, 'ORD2025010003', 'Lê Minh Tuấn', 'leminhtuan@hotmail.com', '0901234567',
'dine_in', 896.00, 89.60, 0.00, 25.00, 100.00, 910.60, 'cash', 'paid', 'preparing',
NULL, NULL, NULL, NULL,
NULL, 'Bàn số 5, thêm đá', 'Khách đặt bàn trước, party 8 người', 'BIRTHDAY100',
'2025-01-06 14:30:00', NULL, '2025-01-06 12:00:00', '2025-01-06 13:15:00'),

-- Order 4: Confirmed delivery order
(1, NULL, 'ORD2025010004', 'Phạm Thị Hoa', 'phamthihoa@gmail.com', '0934567890',
'delivery', 358.00, 35.80, 25.00, 15.00, 0.00, 433.80, 'debit_card', 'paid', 'confirmed',
'456 Đường Lê Lợi, Phường Bến Thành', 'Phường Bến Thành', 'Quận 1', 'TP.Hồ Chí Minh',
'Tầng 3, căn hộ 301', 'Ít muối, không MSG', 'Giao trong giờ hành chính', NULL,
'2025-01-06 15:00:00', NULL, '2025-01-06 13:30:00', '2025-01-06 13:45:00'),

-- Order 5: Pending delivery order
(2, NULL, 'ORD2025010005', 'Hoàng Văn Nam', 'hoangvannam@outlook.com', '0945678901',
'delivery', 717.00, 71.70, 35.00, 20.00, 0.00, 843.70, 'digital_wallet', 'pending', 'pending',
'789 Đường Võ Văn Tần, Phường 6', 'Phường 6', 'Quận 3', 'TP.Hồ Chí Minh',
'Giao tại sảnh tòa nhà, liên hệ bảo vệ', 'Cay vừa, không cilantro', 'Đơn hàng mới, chờ xác nhận', NULL,
NULL, NULL, '2025-01-06 14:00:00', '2025-01-06 14:00:00'),

-- Order 6: Completed takeout order
(1, NULL, 'ORD2025010006', 'Vũ Thị Lan', 'vuthilan@gmail.com', '0956789012',
'takeout', 478.00, 47.80, 0.00, 15.00, 20.00, 520.80, 'cash', 'paid', 'completed',
NULL, NULL, NULL, NULL,
'Đóng gói cẩn thận', 'Không đường, ít ngọt', 'Khách thường xuyên', 'LOYAL20',
'2025-01-06 16:30:00', '2025-01-06 16:15:00', '2025-01-06 15:00:00', '2025-01-06 16:15:00'),

-- Order 7: Ready delivery order
(2, NULL, 'ORD2025010007', 'Đỗ Minh Quân', 'dominquan@yahoo.com', '0967890123',
'delivery', 597.00, 59.70, 40.00, 20.00, 0.00, 716.70, 'credit_card', 'paid', 'ready',
'321 Đường Pasteur, Phường 6', 'Phường 6', 'Quận 1', 'TP.Hồ Chí Minh',
'Nhà màu xanh, cổng sắt', 'Thêm rau xanh', 'Đơn hàng giao xa', NULL,
'2025-01-06 17:00:00', NULL, '2025-01-06 15:30:00', '2025-01-06 16:45:00'),

-- Order 8: Cancelled order
(1, NULL, 'ORD2025010008', 'Bùi Thị Mai', 'buithimai@hotmail.com', '0978901234',
'delivery', 358.00, 35.80, 30.00, 15.00, 0.00, 438.80, 'digital_wallet', 'failed', 'cancelled',
'654 Đường Điện Biên Phủ, Phường 25', 'Phường 25', 'Quận Bình Thạnh', 'TP.Hồ Chí Minh',
'Chung cư Saigon Pearl, tầng 15', 'Món chay hoàn toàn', 'Khách hủy do thay đổi kế hoạch', NULL,
NULL, NULL, '2025-01-06 16:00:00', '2025-01-06 16:30:00'),

-- Order 9: Preparing dine-in order
(2, 2, 'ORD2025010009', 'Ngô Văn Đức', 'ngovanduc@gmail.com', '0989012345',
'dine_in', 896.00, 89.60, 0.00, 30.00, 0.00, 1015.60, 'cash', 'paid', 'preparing',
NULL, NULL, NULL, NULL,
NULL, 'Bàn VIP, view đẹp', 'Tiệc sinh nhật, cần không gian riêng', NULL,
'2025-01-06 19:00:00', NULL, '2025-01-06 17:00:00', '2025-01-06 18:30:00'),

-- Order 10: Confirmed delivery order
(1, NULL, 'ORD2025010010', 'Lý Thị Hương', 'lythihuong@outlook.com', '0990123456',
'delivery', 537.00, 53.70, 25.00, 18.00, 30.00, 603.70, 'debit_card', 'paid', 'confirmed',
'987 Đường Cách Mạng Tháng 8, Phường 5', 'Phường 5', 'Quận Tân Bình', 'TP.Hồ Chí Minh',
'Khu dân cư Him Lam, block A', 'Ít cay, nhiều rau', 'Giao giờ tan tầm', 'NEWYEAR30',
'2025-01-06 18:30:00', NULL, '2025-01-06 17:30:00', '2025-01-06 17:45:00');

-- Insert order items for each order
-- Order 1 items (3 items - mixed buffet)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(1, 1, 2, 299.00, 598.00, 'Thêm tôm nướng'),
(1, 3, 1, 179.00, 179.00, 'Không hành tây');

-- Order 2 items (2 items - standard + veggie)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(2, 2, 1, 199.00, 199.00, 'Đóng gói riêng'),
(2, 3, 1, 179.00, 179.00, 'Thêm nước chấm');

-- Order 3 items (3 items - deluxe party)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(3, 1, 3, 299.00, 897.00, 'Bàn tiệc lớn');

-- Order 4 items (2 items - standard combo)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(4, 2, 1, 199.00, 199.00, 'Ít muối'),
(4, 3, 1, 179.00, 179.00, 'Thêm chanh');

-- Order 5 items (3 items - mixed large order)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(5, 1, 1, 299.00, 299.00, 'Cay vừa'),
(5, 2, 1, 199.00, 199.00, 'Không MSG'),
(5, 3, 1, 179.00, 179.00, 'Thêm rau xanh');

-- Order 6 items (2 items - takeout combo)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(6, 2, 1, 199.00, 199.00, 'Đóng gói cẩn thận'),
(6, 3, 1, 179.00, 179.00, 'Ít ngọt');

-- Order 7 items (2 items - deluxe delivery)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(7, 1, 2, 299.00, 598.00, 'Giao nóng');

-- Order 8 items (2 items - cancelled order)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(8, 2, 1, 199.00, 199.00, 'Món chay'),
(8, 3, 1, 179.00, 179.00, 'Hoàn toàn chay');

-- Order 9 items (3 items - VIP dine-in)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(9, 1, 3, 299.00, 897.00, 'Bàn VIP phục vụ đặc biệt');

-- Order 10 items (3 items - evening delivery)
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(10, 1, 1, 299.00, 299.00, 'Ít cay'),
(10, 2, 1, 199.00, 199.00, 'Nhiều rau'),
(10, 3, 1, 179.00, 179.00, 'Thêm nước mắm');

-- Update order totals to match item totals (in case of any discrepancies)
UPDATE `orders` o
SET o.subtotal = (
    SELECT IFNULL(SUM(oi.total_price), 0)
    FROM `order_items` oi
    WHERE oi.order_id = o.id
);

-- Add some sample bookings for dine-in orders
INSERT INTO `bookings` (
    `id`, `user_id`, `table_id`, `customer_name`, `customer_email`, `customer_phone`,
    `booking_date`, `booking_time`, `guest_count`, `special_requests`, `status`,
    `booking_reference`, `notes`, `created_at`, `updated_at`
) VALUES
(1, 2, 1, 'Lê Minh Tuấn', 'leminhtuan@hotmail.com', '0901234567',
'2025-01-06', '14:00:00', 8, 'Tiệc sinh nhật, cần không gian riêng', 'confirmed',
'BK2025010001', 'Bàn số 5, trang trí sinh nhật', '2025-01-06 11:00:00', '2025-01-06 12:00:00'),

(2, 2, 2, 'Ngô Văn Đức', 'ngovanduc@gmail.com', '0989012345',
'2025-01-06', '19:00:00', 6, 'Bàn VIP, view đẹp', 'confirmed',
'BK2025010002', 'Bàn VIP với view sông', '2025-01-06 16:00:00', '2025-01-06 17:00:00')

ON DUPLICATE KEY UPDATE
    `customer_name` = VALUES(`customer_name`),
    `customer_email` = VALUES(`customer_email`),
    `customer_phone` = VALUES(`customer_phone`);

-- Add sample tables
INSERT INTO `tables` (`id`, `table_number`, `capacity`, `location`, `status`, `created_at`) VALUES
(1, 'T001', 8, 'Main Hall', 'available', NOW()),
(2, 'VIP01', 6, 'VIP Section', 'available', NOW())
ON DUPLICATE KEY UPDATE
    `table_number` = VALUES(`table_number`),
    `capacity` = VALUES(`capacity`);

-- Final verification query (commented out)
-- SELECT
--     o.id, o.order_number, o.customer_name, o.order_type, o.status,
--     o.total_amount, o.delivery_address, o.delivery_city,
--     COUNT(oi.id) as item_count,
--     SUM(oi.total_price) as items_total
-- FROM orders o
-- LEFT JOIN order_items oi ON o.id = oi.order_id
-- GROUP BY o.id
-- ORDER BY o.id;
