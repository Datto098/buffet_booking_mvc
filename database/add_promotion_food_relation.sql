-- Tạo bảng quan hệ giữa Promotions và Food Items
-- File: database/add_promotion_food_relation.sql

-- Bảng quan hệ many-to-many giữa promotions và food_items
CREATE TABLE IF NOT EXISTS `promotion_food_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `promotion_id` INT NOT NULL,
    `food_item_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_promotion_food` (`promotion_id`, `food_item_id`),
    FOREIGN KEY (`promotion_id`) REFERENCES `promotions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`food_item_id`) REFERENCES `food_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng quan hệ many-to-many giữa promotions và categories
CREATE TABLE IF NOT EXISTS `promotion_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `promotion_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_promotion_category` (`promotion_id`, `category_id`),
    FOREIGN KEY (`promotion_id`) REFERENCES `promotions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm cột application_type vào bảng promotions để phân loại
ALTER TABLE `promotions`
ADD COLUMN `application_type` ENUM('all', 'specific_items', 'categories') DEFAULT 'all' AFTER `type`;

-- Index để tối ưu hóa truy vấn
CREATE INDEX `idx_promotion_food_promotion` ON `promotion_food_items` (`promotion_id`);
CREATE INDEX `idx_promotion_food_food` ON `promotion_food_items` (`food_item_id`);
CREATE INDEX `idx_promotion_category_promotion` ON `promotion_categories` (`promotion_id`);
CREATE INDEX `idx_promotion_category_category` ON `promotion_categories` (`category_id`);
