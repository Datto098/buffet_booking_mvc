<?php
echo "🧪 Bắt đầu test đơn giản...\n";

// Test 1: Kiểm tra file config
if (file_exists(__DIR__ . '/../../config/database.php')) {
    echo "✅ File config/database.php tồn tại\n";
    require_once __DIR__ . '/../../config/database.php';
} else {
    echo "❌ File config/database.php không tồn tại\n";
    exit(1);
}

// Test 2: Kiểm tra constants
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
    echo "✅ Các constants database đã được định nghĩa\n";
    echo "   - Host: " . DB_HOST . "\n";
    echo "   - Database: " . DB_NAME . "\n";
    echo "   - User: " . DB_USER . "\n";
} else {
    echo "❌ Các constants database chưa được định nghĩa\n";
    exit(1);
}

// Test 3: Kết nối database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Kết nối database thành công\n";
} catch (PDOException $e) {
    echo "❌ Lỗi kết nối database: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Kiểm tra bảng
try {
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
    echo "❌ Lỗi kiểm tra bảng: " . $e->getMessage() . "\n";
}

// Test 5: Kiểm tra dữ liệu
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tables");
    $tableCount = $stmt->fetch()['count'];
    echo "📋 Số bàn: $tableCount\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM foods");
    $foodCount = $stmt->fetch()['count'];
    echo "🍽️ Số món ăn: $foodCount\n";
} catch (PDOException $e) {
    echo "❌ Lỗi kiểm tra dữ liệu: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test hoàn thành!\n";
?>
