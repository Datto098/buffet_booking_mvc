<?php
// Script để kiểm tra cấu trúc bảng dine_in_orders
require_once __DIR__ . '/../../config/database.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Kiểm tra bảng dine_in_orders ===\n\n";

    // Kiểm tra cấu trúc bảng
    echo "Cấu trúc bảng dine_in_orders:\n";
    try {
        $stmt = $conn->query("DESCRIBE dine_in_orders");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {$row['Field']}: {$row['Type']}\n";
        }
    } catch (Exception $e) {
        echo "  Lỗi: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // Lấy mẫu dữ liệu
    echo "Dữ liệu mẫu (5 dòng đầu):\n";
    try {
        $stmt = $conn->query("SELECT * FROM dine_in_orders ORDER BY id DESC LIMIT 5");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($orders) {
            foreach ($orders as $index => $order) {
                echo "Order #" . ($index + 1) . ":\n";
                foreach ($order as $field => $value) {
                    echo "  $field: $value\n";
                }
                echo "\n";
            }
        } else {
            echo "Không có dữ liệu\n";
        }
    } catch (Exception $e) {
        echo "  Lỗi: " . $e->getMessage() . "\n";
    }

} catch (PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage() . "\n";
}
?>
