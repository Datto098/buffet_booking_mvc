-- Script thêm tính năng phân biệt món buffet và món gọi thêm

-- 1. Thêm cột is_buffet_item vào bảng food_items
ALTER TABLE `food_items`
ADD COLUMN `is_buffet_item` TINYINT(1) DEFAULT 0
COMMENT 'Món buffet miễn phí (1) hay món gọi thêm có phí (0)';

-- 2. Thêm index cho cột mới
ALTER TABLE `food_items`
ADD INDEX `idx_buffet_item` (`is_buffet_item`);

-- 3. Tạo category đặc biệt cho buffet
INSERT INTO `categories` (`name`, `description`, `sort_order`, `is_active`)
VALUES ('Buffet Miễn Phí', 'Các món ăn buffet miễn phí đã bao gồm trong giá vé', -1, 1)
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- 4. Lấy ID của category buffet vừa tạo
SET @buffet_category_id = (SELECT id FROM categories WHERE name = 'Buffet Miễn Phí');

-- 5. Cập nhật một số món hiện có thành món buffet (ví dụ)
UPDATE `food_items`
SET `is_buffet_item` = 1, `category_id` = @buffet_category_id
WHERE `id` IN (1, 2, 3) -- Thay đổi ID theo món bạn muốn làm buffet
AND `is_buffet_item` = 0;

-- 6. Thêm một số món buffet mẫu
INSERT INTO `food_items` (
    `category_id`, `name`, `description`, `price`, `is_buffet_item`,
    `is_popular`, `is_available`, `sort_order`
) VALUES
(@buffet_category_id, 'Salad Trộn', 'Salad tươi ngon với các loại rau củ đa dạng', 0.00, 1, 1, 1, 1),
(@buffet_category_id, 'Súp Rau Củ', 'Súp rau củ thanh mát, bổ dưỡng', 0.00, 1, 1, 1, 2),
(@buffet_category_id, 'Cơm Trắng', 'Cơm trắng dẻo thơm', 0.00, 1, 1, 1, 3),
(@buffet_category_id, 'Thịt Nướng BBQ', 'Thịt nướng kiểu BBQ thơm ngon', 0.00, 1, 1, 1, 4),
(@buffet_category_id, 'Rau Củ Luộc', 'Rau củ luộc tươi ngon', 0.00, 1, 1, 1, 5),
(@buffet_category_id, 'Tráng Miệng Hoa Quả', 'Hoa quả tươi theo mùa', 0.00, 1, 1, 1, 6)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 7. Kiểm tra kết quả
SELECT
    c.name as category_name,
    fi.name as food_name,
    fi.price,
    fi.is_buffet_item,
    CASE WHEN fi.is_buffet_item = 1 THEN 'Buffet Miễn Phí' ELSE 'Món Gọi Thêm' END as type
FROM food_items fi
JOIN categories c ON fi.category_id = c.id
ORDER BY fi.is_buffet_item DESC, c.name, fi.sort_order;
