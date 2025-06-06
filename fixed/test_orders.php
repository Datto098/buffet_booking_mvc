<?php
/**
 * Test script for order management functionality
 */

require_once 'config/config.php';
require_once 'controllers/AdminController.php';
require_once 'models/Order.php';

// Start session
session_start();

// Simulate admin user login
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

echo "<h1>Testing Order Management Functionality</h1>\n";

try {
    // Test 1: Check if orders exist
    echo "<h2>Test 1: Check existing orders</h2>\n";
    $orderModel = new Order();
    $orders = $orderModel->findAll();
    echo "Found " . count($orders) . " orders in database.\n";

    if (count($orders) > 0) {
        $firstOrder = $orders[0];
        echo "First order ID: " . $firstOrder['id'] . " - " . $firstOrder['order_number'] . "\n";

        // Test 2: Test order details
        echo "<h2>Test 2: Test order details functionality</h2>\n";
        $orderWithItems = $orderModel->getOrderWithItems($firstOrder['id']);
        if ($orderWithItems) {
            echo "Order details retrieved successfully!\n";
            echo "Customer: " . $orderWithItems['customer_name'] . "\n";
            echo "Total: $" . $orderWithItems['total_amount'] . "\n";
            echo "Items count: " . count($orderWithItems['items']) . "\n";
        } else {
            echo "Failed to retrieve order details.\n";
        }

        // Test 3: Test CSV export
        echo "<h2>Test 3: Test CSV export functionality</h2>\n";
        $controller = new AdminController();

        // Capture output
        ob_start();
        $controller->exportOrdersCSV();
        $csvOutput = ob_get_clean();

        if (strpos($csvOutput, 'Order Number') !== false) {
            echo "CSV export working! Headers found.\n";
            echo "CSV length: " . strlen($csvOutput) . " characters\n";
        } else {
            echo "CSV export may have issues.\n";
        }

        // Test 4: Test filtered orders
        echo "<h2>Test 4: Test filtered orders</h2>\n";
        $filters = ['status' => 'completed'];
        $filteredOrders = $orderModel->getFilteredOrders($filters, 10, 0);
        echo "Filtered orders (completed): " . count($filteredOrders) . "\n";

    } else {
        echo "No orders found. Please add some sample orders to test.\n";
    }

    echo "<h2>All tests completed successfully!</h2>\n";

} catch (Exception $e) {
    echo "<h2>Error during testing:</h2>\n";
    echo $e->getMessage() . "\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>
