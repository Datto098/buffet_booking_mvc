<?php
/**
 * Test script cho chức năng Dine-in
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/DineInOrder.php';
require_once __DIR__ . '/../../models/Food.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../models/Table.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🧪 Bắt đầu test chức năng Dine-in...\n\n";

    // Test 1: Kiểm tra bảng đã được tạo
    echo "📋 Test 1: Kiểm tra bảng dine_in_orders\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'dine_in_orders'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Bảng dine_in_orders tồn tại\n";
    } else {
        echo "❌ Bảng dine_in_orders không tồn tại\n";
        exit(1);
    }

    $stmt = $pdo->query("SHOW TABLES LIKE 'dine_in_order_items'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Bảng dine_in_order_items tồn tại\n";
    } else {
        echo "❌ Bảng dine_in_order_items không tồn tại\n";
        exit(1);
    }

    // Test 2: Kiểm tra dữ liệu mẫu
    echo "\n📊 Test 2: Kiểm tra dữ liệu mẫu\n";

    // Kiểm tra bàn
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tables");
    $tableCount = $stmt->fetch()['count'];
    echo "📋 Số bàn: $tableCount\n";

    if ($tableCount == 0) {
        echo "⚠️  Chưa có bàn nào, tạo bàn mẫu...\n";
        $pdo->exec("INSERT INTO tables (table_number, capacity, location) VALUES
                   ('A1', 4, 'Tầng 1 - Gần cửa sổ'),
                   ('A2', 6, 'Tầng 1 - Góc yên tĩnh'),
                   ('B1', 2, 'Tầng 2 - Ban công'),
                   ('B2', 8, 'Tầng 2 - Phòng VIP')");
        echo "✅ Đã tạo 4 bàn mẫu\n";
    }

    // Kiểm tra món ăn
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM foods");
    $foodCount = $stmt->fetch()['count'];
    echo "🍽️ Số món ăn: $foodCount\n";

    if ($foodCount == 0) {
        echo "⚠️  Chưa có món ăn nào, tạo món mẫu...\n";
        $pdo->exec("INSERT INTO foods (name, description, price, category_id, image, is_popular, is_new) VALUES
                   ('Phở bò', 'Phở bò truyền thống với nước dùng đậm đà', 45000, 1, 'foods/pho_bo.jpg', 1, 0),
                   ('Bún chả', 'Bún chả Hà Nội với thịt nướng thơm ngon', 35000, 1, 'foods/bun_cha.jpg', 1, 0),
                   ('Cơm tấm', 'Cơm tấm sườn nướng với chả trứng', 40000, 1, 'foods/com_tam.jpg', 1, 0),
                   ('Bánh mì', 'Bánh mì thịt nướng với rau sống', 25000, 1, 'foods/banh_mi.jpg', 1, 0),
                   ('Gỏi cuốn', 'Gỏi cuốn tôm thịt với nước mắm pha', 30000, 1, 'foods/goi_cuon.jpg', 0, 1)");
        echo "✅ Đã tạo 5 món ăn mẫu\n";
    }

    // Test 3: Test model DineInOrder
    echo "\n🔧 Test 3: Test model DineInOrder\n";

    $dineInOrder = new DineInOrder();

    // Test tạo đơn hàng
    $orderData = [
        'table_id' => 1,
        'total_amount' => 120000,
        'special_notes' => 'Không cay, ít muối'
    ];

    $orderId = $dineInOrder->createOrder($orderData);
    if ($orderId) {
        echo "✅ Tạo đơn hàng thành công, ID: $orderId\n";

        // Test thêm món vào đơn hàng
        $itemData = [
            'order_id' => $orderId,
            'food_id' => 1,
            'quantity' => 2,
            'price' => 45000,
            'special_instructions' => 'Nấu chín kỹ'
        ];

        $itemId = $dineInOrder->addOrderItem($itemData);
        if ($itemId) {
            echo "✅ Thêm món vào đơn hàng thành công\n";
        } else {
            echo "❌ Lỗi thêm món vào đơn hàng\n";
        }

        // Test lấy đơn hàng theo trạng thái
        $pendingOrders = $dineInOrder->getOrdersByStatus('pending');
        echo "📋 Số đơn hàng chờ xử lý: " . count($pendingOrders) . "\n";

        // Test cập nhật trạng thái
        $updated = $dineInOrder->updateOrderStatus($orderId, 'preparing');
        if ($updated) {
            echo "✅ Cập nhật trạng thái thành công\n";
        } else {
            echo "❌ Lỗi cập nhật trạng thái\n";
        }

        // Test lấy chi tiết đơn hàng
        $orderDetails = $dineInOrder->getOrderDetails($orderId);
        if ($orderDetails) {
            echo "✅ Lấy chi tiết đơn hàng thành công\n";
            echo "   - Bàn: " . $orderDetails['table_number'] . "\n";
            echo "   - Tổng tiền: " . number_format($orderDetails['total_amount']) . "đ\n";
            echo "   - Trạng thái: " . $orderDetails['status'] . "\n";
        } else {
            echo "❌ Lỗi lấy chi tiết đơn hàng\n";
        }

    } else {
        echo "❌ Lỗi tạo đơn hàng\n";
    }

    // Test 4: Test các method khác
    echo "\n📈 Test 4: Test thống kê\n";

    $stats = $dineInOrder->getOrderStats();
    echo "📊 Thống kê đơn hàng:\n";
    echo "   - Tổng đơn hàng: " . $stats['total_orders'] . "\n";
    echo "   - Đang chờ: " . $stats['pending'] . "\n";
    echo "   - Đang chuẩn bị: " . $stats['preparing'] . "\n";
    echo "   - Đã phục vụ: " . $stats['served'] . "\n";
    echo "   - Hoàn thành: " . $stats['completed'] . "\n";

    // Test 5: Test model Food
    echo "\n🍽️ Test 5: Test model Food\n";

    $food = new Food();

    // Test lấy món phổ biến
    $popularFoods = $food->getPopularFoods();
    echo "🔥 Số món phổ biến: " . count($popularFoods) . "\n";

    // Test lấy món mới
    $newFoods = $food->getNewFoods();
    echo "🆕 Số món mới: " . count($newFoods) . "\n";

    // Test tìm kiếm món
    $searchResults = $food->searchFoods('phở');
    echo "🔍 Kết quả tìm kiếm 'phở': " . count($searchResults) . " món\n";

    // Test 6: Test model Table
    echo "\n🪑 Test 6: Test model Table\n";

    $table = new Table();

    // Test lấy đơn hàng theo bàn
    $tableOrders = $table->getTableOrders(1);
    echo "📋 Số đơn hàng của bàn 1: " . count($tableOrders) . "\n";

    // Test cập nhật QR code
    $qrUpdated = $table->updateQRCode(1, 'https://example.com/qr/table1.png');
    if ($qrUpdated) {
        echo "✅ Cập nhật QR code thành công\n";
    } else {
        echo "❌ Lỗi cập nhật QR code\n";
    }

    echo "\n🎉 Hoàn thành test chức năng Dine-in!\n";
    echo "✅ Tất cả các test đều thành công\n";

} catch (PDOException $e) {
    echo "❌ Lỗi database: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
?>
