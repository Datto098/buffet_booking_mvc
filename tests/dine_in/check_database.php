<?php
/**
 * Script kiểm tra cấu trúc database thực tế
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔍 Kiểm tra cấu trúc database...\n\n";

    // Liệt kê tất cả bảng
    echo "📋 Danh sách tất cả bảng:\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        echo "   - $table\n";
    }

    echo "\n📊 Kiểm tra bảng cụ thể:\n";

    // Kiểm tra bảng món ăn (có thể có tên khác)
    $foodTables = ['foods', 'food', 'menu', 'menu_items', 'dishes'];
    foreach ($foodTables as $tableName) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Bảng $tableName tồn tại\n";

            // Kiểm tra cấu trúc
            $stmt = $pdo->query("DESCRIBE $tableName");
            $columns = $stmt->fetchAll();
            echo "   Cấu trúc bảng $tableName:\n";
            foreach ($columns as $column) {
                echo "     - {$column['Field']}: {$column['Type']}\n";
            }
            break;
        }
    }

    // Kiểm tra bảng danh mục
    $categoryTables = ['categories', 'category', 'food_categories'];
    foreach ($categoryTables as $tableName) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Bảng $tableName tồn tại\n";
            break;
        }
    }

    // Kiểm tra bảng bàn
    $stmt = $pdo->query("SHOW TABLES LIKE 'tables'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Bảng tables tồn tại\n";

        // Kiểm tra dữ liệu
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tables");
        $count = $stmt->fetch()['count'];
        echo "   Số bàn: $count\n";

        if ($count > 0) {
            $stmt = $pdo->query("SELECT table_number, capacity, location FROM tables LIMIT 3");
            $tables = $stmt->fetchAll();
            foreach ($tables as $table) {
                echo "     - Bàn {$table['table_number']}: {$table['capacity']} người - {$table['location']}\n";
            }
        }
    }

    // Kiểm tra bảng dine-in
    $stmt = $pdo->query("SHOW TABLES LIKE 'dine_in_orders'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Bảng dine_in_orders tồn tại\n";
    } else {
        echo "❌ Bảng dine_in_orders không tồn tại\n";
    }

    $stmt = $pdo->query("SHOW TABLES LIKE 'dine_in_order_items'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Bảng dine_in_order_items tồn tại\n";
    } else {
        echo "❌ Bảng dine_in_order_items không tồn tại\n";
    }

} catch (PDOException $e) {
    echo "❌ Lỗi database: " . $e->getMessage() . "\n";
    exit(1);
}
?>
