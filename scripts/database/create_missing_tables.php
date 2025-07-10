<?php
/**
 * Script tạo bảng còn thiếu cho chức năng Dine-in
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔧 Tạo bảng còn thiếu cho chức năng Dine-in...\n\n";

    // Tạo bảng dine_in_order_items
    $sql = "CREATE TABLE IF NOT EXISTS dine_in_order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        food_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        price DECIMAL(10,2) NOT NULL,
        special_instructions TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES dine_in_orders(id) ON DELETE CASCADE,
        FOREIGN KEY (food_id) REFERENCES food_items(id) ON DELETE CASCADE,
        INDEX idx_order_id (order_id),
        INDEX idx_food_id (food_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "✅ Đã tạo bảng dine_in_order_items\n";

    // Thêm cột vào bảng food_items nếu chưa có
    $sql = "ALTER TABLE food_items ADD COLUMN IF NOT EXISTS is_popular BOOLEAN DEFAULT FALSE AFTER spicy_level";
    $pdo->exec($sql);
    echo "✅ Đã thêm cột is_popular vào bảng food_items\n";

    $sql = "ALTER TABLE food_items ADD COLUMN IF NOT EXISTS is_new BOOLEAN DEFAULT FALSE AFTER is_popular";
    $pdo->exec($sql);
    echo "✅ Đã thêm cột is_new vào bảng food_items\n";

    // Thêm cột vào bảng tables nếu chưa có
    $sql = "ALTER TABLE tables ADD COLUMN IF NOT EXISTS qr_code_url VARCHAR(255) NULL AFTER location";
    $pdo->exec($sql);
    echo "✅ Đã thêm cột qr_code_url vào bảng tables\n";

    // Tạo dữ liệu mẫu cho food_items nếu chưa có
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM food_items");
    $foodCount = $stmt->fetch()['count'];

    if ($foodCount == 0) {
        echo "⚠️  Chưa có món ăn nào, tạo món mẫu...\n";

        // Lấy category đầu tiên
        $stmt = $pdo->query("SELECT id FROM categories LIMIT 1");
        $category = $stmt->fetch();

        if ($category) {
            $categoryId = $category['id'];

            $pdo->exec("INSERT INTO food_items (name, description, price, category_id, image, is_available, is_popular, is_new) VALUES
                       ('Phở bò', 'Phở bò truyền thống với nước dùng đậm đà', 45000, $categoryId, 'foods/pho_bo.jpg', 1, 1, 0),
                       ('Bún chả', 'Bún chả Hà Nội với thịt nướng thơm ngon', 35000, $categoryId, 'foods/bun_cha.jpg', 1, 1, 0),
                       ('Cơm tấm', 'Cơm tấm sườn nướng với chả trứng', 40000, $categoryId, 'foods/com_tam.jpg', 1, 1, 0),
                       ('Bánh mì', 'Bánh mì thịt nướng với rau sống', 25000, $categoryId, 'foods/banh_mi.jpg', 1, 1, 0),
                       ('Gỏi cuốn', 'Gỏi cuốn tôm thịt với nước mắm pha', 30000, $categoryId, 'foods/goi_cuon.jpg', 1, 0, 1)");
            echo "✅ Đã tạo 5 món ăn mẫu\n";
        }
    }

    echo "\n🎉 Hoàn thành tạo bảng còn thiếu!\n";

} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
?>
