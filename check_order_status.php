<?php
// Check order status
echo "Checking order status...\n";

require_once 'config/config.php';
require_once 'models/Order.php';

try {
    $orderModel = new Order();
    $order = $orderModel->findById(8);
    
    if ($order) {
        echo "Order found:\n";
        echo "  ID: {$order['id']}\n";
        echo "  User ID: {$order['user_id']}\n";
        echo "  Status: {$order['status']}\n";
        echo "  Total: {$order['total']}\n";
        echo "  Created: {$order['created_at']}\n";
        
        echo "\nCan be cancelled: " . (in_array($order['status'], ['pending', 'confirmed']) ? 'Yes' : 'No') . "\n";
        echo "Valid statuses for cancellation: pending, confirmed\n";
        echo "Current status: {$order['status']}\n";
    } else {
        echo "Order with ID 8 not found!\n";
        
        // Check what orders exist
        echo "\nChecking available orders...\n";
        $orders = $orderModel->getOrdersByUser(1, 10, 0);
        echo "Orders for user 1: " . count($orders) . "\n";
        foreach ($orders as $order) {
            echo "  Order {$order['id']}: status = {$order['status']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
