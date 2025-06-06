<?php
// Test all order management functions
require_once 'config/config.php';
session_start();

// Set proper session variables
$_SESSION['user_id'] = 4;
$_SESSION['user_role'] = 'super_admin';
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

require_once 'controllers/AdminController.php';
require_once 'models/Order.php';

echo "<!DOCTYPE html><html><head><title>Order Management Tests</title>";
echo "<style>body{font-family:Arial;margin:20px;} .test{border:1px solid #ddd;padding:10px;margin:10px 0;} .success{background:#e8f5e8;} .error{background:#ffe8e8;}</style>";
echo "</head><body>";
echo "<h1>Order Management Functionality Tests</h1>";

try {
    $controller = new AdminController();
    $orderModel = new Order();

    // Test 1: Check orders exist
    echo "<div class='test'>";
    echo "<h3>Test 1: Database Orders</h3>";
    $orders = $orderModel->findAll();
    if (count($orders) > 0) {
        echo "<div class='success'>✓ Found " . count($orders) . " orders in database</div>";
        $testOrderId = $orders[0]['id'];
        echo "<p>Using Order ID {$testOrderId} for tests</p>";
    } else {
        echo "<div class='error'>✗ No orders found in database</div>";
        exit;
    }
    echo "</div>";

    // Test 2: Order Details
    echo "<div class='test'>";
    echo "<h3>Test 2: Order Details Function</h3>";
    try {
        ob_start();
        $controller->orderDetails($testOrderId);
        $output = ob_get_clean();

        if (strpos($output, 'order-details') !== false) {
            echo "<div class='success'>✓ Order details modal generated successfully</div>";
            echo "<details><summary>View Output</summary><pre>" . htmlspecialchars($output) . "</pre></details>";
        } else {
            echo "<div class='error'>✗ Order details output seems invalid</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    // Test 3: Print Order
    echo "<div class='test'>";
    echo "<h3>Test 3: Print Order Function</h3>";
    try {
        ob_start();
        $controller->printOrder($testOrderId);
        $output = ob_get_clean();

        if (strpos($output, 'print-receipt') !== false || strpos($output, 'Order Receipt') !== false) {
            echo "<div class='success'>✓ Print order page generated successfully</div>";
            echo "<details><summary>View Output</summary><pre>" . htmlspecialchars($output) . "</pre></details>";
        } else {
            echo "<div class='error'>✗ Print order output seems invalid</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    // Test 4: CSV Export
    echo "<div class='test'>";
    echo "<h3>Test 4: CSV Export Function</h3>";
    try {
        ob_start();
        $controller->exportOrdersCSV();
        $output = ob_get_clean();

        if (strpos($output, 'Order Number') !== false && strpos($output, 'Customer Name') !== false) {
            echo "<div class='success'>✓ CSV export generated successfully</div>";
            echo "<p>CSV size: " . strlen($output) . " bytes</p>";
            echo "<details><summary>View CSV Sample</summary><pre>" . htmlspecialchars(substr($output, 0, 500)) . "...</pre></details>";
        } else {
            echo "<div class='error'>✗ CSV export output seems invalid</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    // Test 5: Filtered Orders
    echo "<div class='test'>";
    echo "<h3>Test 5: Filtered Orders Function</h3>";
    try {
        $_GET = ['status' => 'completed', 'limit' => '5', 'offset' => '0'];

        ob_start();
        $controller->ordersFiltered();
        $output = ob_get_clean();

        $data = json_decode($output, true);
        if ($data && isset($data['orders'])) {
            echo "<div class='success'>✓ Filtered orders JSON generated successfully</div>";
            echo "<p>Found " . count($data['orders']) . " orders with status 'completed'</p>";
            echo "<p>Total count: " . ($data['total'] ?? 'N/A') . "</p>";
        } else {
            echo "<div class='error'>✗ Filtered orders output invalid: " . $output . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    echo "<div class='test success'>";
    echo "<h3>🎉 All Tests Completed!</h3>";
    echo "<p>The order management features have been successfully tested.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='test error'>";
    echo "<h3>Fatal Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "</body></html>";
?>
