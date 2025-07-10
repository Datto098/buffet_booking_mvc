<?php
require_once '../../config/config.php';
require_once '../../models/DineInOrder.php';
require_once '../../models/Food.php';

echo "=== Debug Cart Issues ===\n\n";

// 1. Kiểm tra bảng dine_in_orders
echo "1. Kiểm tra bảng dine_in_orders:\n";
$dineInOrder = new DineInOrder();
$orders = $dineInOrder->getOrdersByTableId(7, 5); // table_id = 7 (D1)

foreach ($orders as $order) {
    echo "- Order #{$order['id']}: Status={$order['status']}, Total={$order['total_amount']}, Created={$order['created_at']}\n";
}

// 2. Kiểm tra bảng dine_in_order_items
echo "\n2. Kiểm tra bảng dine_in_order_items:\n";
$sql = "SELECT oi.*, f.name as food_name, f.price as food_price
        FROM dine_in_order_items oi
        LEFT JOIN food_items f ON oi.food_id = f.id
        WHERE oi.order_id IN (SELECT id FROM dine_in_orders WHERE table_id = 7)
        ORDER BY oi.order_id DESC";

$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$stmt = $db->prepare($sql);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    echo "- Order #{$item['order_id']}: {$item['food_name']} x{$item['quantity']} = {$item['total']} (price: {$item['food_price']})\n";
}

// 3. Kiểm tra session cart
echo "\n3. Kiểm tra session cart:\n";
session_start();
if (isset($_SESSION['dine_in_cart'])) {
    foreach ($_SESSION['dine_in_cart'] as $table_id => $cart_items) {
        echo "- Table $table_id:\n";
        foreach ($cart_items as $food_id => $quantity) {
            echo "  * Food $food_id: $quantity\n";
        }
    }
} else {
    echo "- No cart in session\n";
}

echo "\nDebug completed!\n";
?>
