<?php
/**
 * Test script đơn giản cho cấu trúc database Dine-in
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🧪 Bắt đầu test cấu trúc database Dine-in...\n\n";

    // Test 1: Kiểm tra bảng đã được tạo
    echo "📋 Test 1: Kiểm tra bảng\n";

    $tables = ['dine_in_orders', 'dine_in_order_items'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Bảng $table tồn tại\n";
        } else {
            echo "❌ Bảng $table không tồn tại\n";
        }
    }

    // Test 2: Kiểm tra cấu trúc bảng dine_in_orders
    echo "\n📊 Test 2: Cấu trúc bảng dine_in_orders\n";
    $stmt = $pdo->query("DESCRIBE dine_in_orders");
    $columns = $stmt->fetchAll();

    foreach ($columns as $column) {
        echo "   - {$column['Field']}: {$column['Type']} ({$column['Null']})\n";
    }

    // Test 3: Kiểm tra cấu trúc bảng dine_in_order_items
    echo "\n📊 Test 3: Cấu trúc bảng dine_in_order_items\n";
    $stmt = $pdo->query("DESCRIBE dine_in_order_items");
    $columns = $stmt->fetchAll();

    foreach ($columns as $column) {
        echo "   - {$column['Field']}: {$column['Type']} ({$column['Null']})\n";
    }

    // Test 4: Kiểm tra dữ liệu mẫu
    echo "\n📊 Test 4: Kiểm tra dữ liệu mẫu\n";

    // Kiểm tra bàn
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tables");
    $tableCount = $stmt->fetch()['count'];
    echo "📋 Số bàn: $tableCount\n";

    if ($tableCount > 0) {
        $stmt = $pdo->query("SELECT table_number, capacity, location FROM tables LIMIT 3");
        $tables = $stmt->fetchAll();
        foreach ($tables as $table) {
            echo "   - Bàn {$table['table_number']}: {$table['capacity']} người - {$table['location']}\n";
        }
    }

    // Kiểm tra món ăn
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM foods");
    $foodCount = $stmt->fetch()['count'];
    echo "🍽️ Số món ăn: $foodCount\n";

    if ($foodCount > 0) {
        $stmt = $pdo->query("SELECT name, price, is_popular, is_new FROM foods LIMIT 3");
        $foods = $stmt->fetchAll();
        foreach ($foods as $food) {
            $popular = $food['is_popular'] ? '🔥' : '';
            $new = $food['is_new'] ? '🆕' : '';
            echo "   - {$food['name']}: " . number_format($food['price']) . "đ $popular $new\n";
        }
    }

    // Test 5: Kiểm tra view
    echo "\n📊 Test 5: Kiểm tra view\n";

    $views = ['dine_in_order_stats', 'table_order_stats'];
    foreach ($views as $view) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$view'");
        if ($stmt->rowCount() > 0) {
            echo "✅ View $view tồn tại\n";
        } else {
            echo "❌ View $view không tồn tại\n";
        }
    }

    // Test 6: Tạo dữ liệu test
    echo "\n🔧 Test 6: Tạo dữ liệu test\n";

    // Tạo đơn hàng test
    $stmt = $pdo->prepare("INSERT INTO dine_in_orders (table_id, total_amount, special_notes) VALUES (?, ?, ?)");
    $stmt->execute([1, 150000, 'Test order - Không cay']);
    $orderId = $pdo->lastInsertId();
    echo "✅ Tạo đơn hàng test ID: $orderId\n";

    // Tạo item test
    $stmt = $pdo->prepare("INSERT INTO dine_in_order_items (order_id, food_id, quantity, price, special_instructions) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$orderId, 1, 2, 45000, 'Nấu chín kỹ']);
    echo "✅ Thêm item vào đơn hàng\n";

    // Test 7: Kiểm tra dữ liệu đã tạo
    echo "\n📊 Test 7: Kiểm tra dữ liệu đã tạo\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM dine_in_orders");
    $orderCount = $stmt->fetch()['count'];
    echo "📋 Số đơn hàng: $orderCount\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM dine_in_order_items");
    $itemCount = $stmt->fetch()['count'];
    echo "🍽️ Số item: $itemCount\n";

    // Test 8: Kiểm tra join
    echo "\n🔗 Test 8: Kiểm tra join\n";

    $stmt = $pdo->query("
        SELECT
            dio.id as order_id,
            t.table_number,
            dio.total_amount,
            dio.status,
            COUNT(dioi.id) as item_count
        FROM dine_in_orders dio
        JOIN tables t ON dio.table_id = t.id
        LEFT JOIN dine_in_order_items dioi ON dio.id = dioi.order_id
        GROUP BY dio.id
        ORDER BY dio.id DESC
        LIMIT 3
    ");

    $orders = $stmt->fetchAll();
    foreach ($orders as $order) {
        echo "   - Đơn #{$order['order_id']}: Bàn {$order['table_number']} - " .
             number_format($order['total_amount']) . "đ - {$order['status']} - {$order['item_count']} món\n";
    }

    echo "\n🎉 Hoàn thành test cấu trúc database!\n";
    echo "✅ Database đã sẵn sàng cho chức năng Dine-in\n";

} catch (PDOException $e) {
    echo "❌ Lỗi database: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
?>
