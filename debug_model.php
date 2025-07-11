<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/DineInOrder.php';

try {
    $dineInOrderModel = new DineInOrder();

    echo "<h2>Testing DineInOrder Model</h2>\n";

    // Test getOrdersByTableAndUser
    $orders = $dineInOrderModel->getOrdersByTableAndUser('D2', 1);
    echo "<h3>getOrdersByTableAndUser('D2', 1):</h3>\n";
    echo "<pre>";
    print_r($orders);
    echo "</pre>";

    // Test getOrderItems for each order
    foreach ($orders as $order) {
        echo "<h3>Order Items for Order #{$order['id']}:</h3>\n";
        $items = $dineInOrderModel->getOrderItems($order['id']);
        echo "<pre>";
        print_r($items);
        echo "</pre>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
