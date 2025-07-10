<?php
/**
 * Script chạy SQL để thêm các cột notes còn thiếu
 */

require_once __DIR__ . '/../../config/database.php';

// Khởi tạo kết nối database
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    echo "✓ Kết nối database thành công\n";
} catch (Exception $e) {
    die("❌ Lỗi kết nối database: " . $e->getMessage() . "\n");
}

try {
    echo "Đang thêm các cột notes còn thiếu...\n";

    // Thêm cột notes vào bảng orders (nếu chưa có)
    $sql1 = "ALTER TABLE `orders`
             ADD COLUMN IF NOT EXISTS `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
             AFTER `order_notes`";

    $pdo->exec($sql1);
    echo "✓ Đã thêm cột notes vào bảng orders\n";

    // Thêm cột special_notes vào bảng dine_in_orders (nếu chưa có)
    $sql2 = "ALTER TABLE `dine_in_orders`
             ADD COLUMN IF NOT EXISTS `special_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
             AFTER `notes`";

    $pdo->exec($sql2);
    echo "✓ Đã thêm cột special_notes vào bảng dine_in_orders\n";

    // Thêm cột total_amount vào bảng dine_in_orders (nếu chưa có)
    $sql3 = "ALTER TABLE `dine_in_orders`
             ADD COLUMN IF NOT EXISTS `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00
             AFTER `status`";

    $pdo->exec($sql3);
    echo "✓ Đã thêm cột total_amount vào bảng dine_in_orders\n";

    // Thêm cột user_id vào bảng dine_in_orders (nếu chưa có)
    $sql4 = "ALTER TABLE `dine_in_orders`
             ADD COLUMN IF NOT EXISTS `user_id` int(11) DEFAULT NULL
             AFTER `table_id`";

    $pdo->exec($sql4);
    echo "✓ Đã thêm cột user_id vào bảng dine_in_orders\n";

    // Thêm foreign key cho user_id nếu chưa có
    try {
        $sql5 = "ALTER TABLE `dine_in_orders`
                 ADD CONSTRAINT `fk_dine_in_orders_user`
                 FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL";

        $pdo->exec($sql5);
        echo "✓ Đã thêm foreign key cho user_id\n";
    } catch (Exception $e) {
        echo "ℹ Foreign key user_id đã tồn tại hoặc có lỗi: " . $e->getMessage() . "\n";
    }

    // Thêm index cho các cột mới
    try {
        $pdo->exec("CREATE INDEX IF NOT EXISTS `idx_dine_in_orders_user_id` ON `dine_in_orders` (`user_id`)");
        echo "✓ Đã thêm index cho user_id\n";
    } catch (Exception $e) {
        echo "ℹ Index user_id đã tồn tại\n";
    }

    try {
        $pdo->exec("CREATE INDEX IF NOT EXISTS `idx_dine_in_orders_total_amount` ON `dine_in_orders` (`total_amount`)");
        echo "✓ Đã thêm index cho total_amount\n";
    } catch (Exception $e) {
        echo "ℹ Index total_amount đã tồn tại\n";
    }

    echo "\n🎉 Hoàn thành! Các cột notes đã được thêm thành công!\n";

    // Hiển thị cấu trúc bảng để kiểm tra
    echo "\n📋 Cấu trúc bảng dine_in_orders:\n";
    $result = $pdo->query("DESCRIBE dine_in_orders");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']} {$row['Null']} {$row['Default']}\n";
    }

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
?>
