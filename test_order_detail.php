<?php
// Test order detail retrieval
echo "Testing order detail retrieval...\n";

require_once 'config/config.php';
require_once 'models/Order.php';

try {
    $orderModel = new Order();
    $order = $orderModel->getOrderWithItems(8);
    
    if ($order) {
        echo "Order retrieved with getOrderWithItems():\n";
        echo "  ID: {$order['id']}\n";
        echo "  User ID: {$order['user_id']}\n";
        echo "  Status: {$order['status']}\n";
        echo "  Created: {$order['created_at']}\n";
        
        echo "\nOrder items:\n";
        if (isset($order['items']) && is_array($order['items'])) {
            foreach ($order['items'] as $item) {
                echo "  - {$item['food_name']} x {$item['quantity']}\n";
            }
        } else {
            echo "  No items found or items not properly retrieved\n";
        }
        
        echo "\nShould show cancel button: " . ($order['status'] === 'pending' ? 'Yes' : 'No') . "\n";
        
    } else {
        echo "Order not found with getOrderWithItems(8)\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
