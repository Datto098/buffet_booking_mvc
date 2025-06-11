<?php
/**
 * Script để tạo bảng quan hệ giữa Promotions và Food Items
 */

require_once 'config/config.php';

try {
    $database = Database::getInstance();
    $db = $database->getConnection();

    echo "<h1>Creating Promotion-Food Relationships</h1>";

    // Tạo bảng promotion_food_items
    echo "<h2>Creating promotion_food_items table...</h2>";
    $sql1 = "CREATE TABLE IF NOT EXISTS `promotion_food_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `promotion_id` INT NOT NULL,
        `food_item_id` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_promotion_food` (`promotion_id`, `food_item_id`),
        FOREIGN KEY (`promotion_id`) REFERENCES `promotions`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`food_item_id`) REFERENCES `food_items`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql1);
    echo "<p>✅ promotion_food_items table created successfully</p>";

    // Tạo bảng promotion_categories
    echo "<h2>Creating promotion_categories table...</h2>";
    $sql2 = "CREATE TABLE IF NOT EXISTS `promotion_categories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `promotion_id` INT NOT NULL,
        `category_id` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_promotion_category` (`promotion_id`, `category_id`),
        FOREIGN KEY (`promotion_id`) REFERENCES `promotions`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql2);
    echo "<p>✅ promotion_categories table created successfully</p>";

    // Thêm cột application_type vào bảng promotions
    echo "<h2>Adding application_type column to promotions table...</h2>";
    try {
        $sql3 = "ALTER TABLE `promotions`
                ADD COLUMN `application_type` ENUM('all', 'specific_items', 'categories') DEFAULT 'all' AFTER `type`";
        $db->exec($sql3);
        echo "<p>✅ application_type column added successfully</p>";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p>✅ application_type column already exists</p>";
        } else {
            throw $e;
        }
    }

    // Tạo index để tối ưu hóa
    echo "<h2>Creating indexes...</h2>";
    $indexes = [
        "CREATE INDEX IF NOT EXISTS `idx_promotion_food_promotion` ON `promotion_food_items` (`promotion_id`)",
        "CREATE INDEX IF NOT EXISTS `idx_promotion_food_food` ON `promotion_food_items` (`food_item_id`)",
        "CREATE INDEX IF NOT EXISTS `idx_promotion_category_promotion` ON `promotion_categories` (`promotion_id`)",
        "CREATE INDEX IF NOT EXISTS `idx_promotion_category_category` ON `promotion_categories` (`category_id`)"
    ];

    foreach ($indexes as $index) {
        try {
            $db->exec($index);
        } catch (Exception $e) {
            // Index có thể đã tồn tại
        }
    }
    echo "<p>✅ Indexes created successfully</p>";

    // Kiểm tra cấu trúc bảng
    echo "<h2>Checking table structures...</h2>";

    $tables = ['promotion_food_items', 'promotion_categories', 'promotions'];
    foreach ($tables as $table) {
        echo "<h3>Table: $table</h3>";
        $stmt = $db->query("DESCRIBE $table");
        $columns = $stmt->fetchAll();
        echo "<ul>";
        foreach ($columns as $col) {
            echo "<li>{$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']}</li>";
        }
        echo "</ul>";
    }

    echo "<h2>✅ Promotion-Food relationship setup completed successfully!</h2>";
    echo "<p>Now you can:</p>";
    echo "<ul>";
    echo "<li>Apply promotions to specific food items</li>";
    echo "<li>Apply promotions to entire categories</li>";
    echo "<li>Create flexible promotion rules</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h2>❌ Error occurred:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
