<?php
// Test cancel order functionality
echo "Testing cancel order functionality...\n";

// Set up environment
session_start();

// Simulate logged in user
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'customer';

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['order_id'] = 8; // Test with order ID 8

require_once 'config/config.php';
require_once 'controllers/OrderController.php';

echo "Testing OrderController->cancel() method...\n";
echo "User ID: {$_SESSION['user_id']}\n";
echo "Order ID: {$_POST['order_id']}\n";

try {
    $controller = new OrderController();
    
    // Capture output to prevent redirect
    ob_start();
    $controller->cancel();
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "Method executed successfully!\n";
    echo "Session messages:\n";
    if (isset($_SESSION['success'])) {
        echo "  Success: {$_SESSION['success']}\n";
    }
    if (isset($_SESSION['error'])) {
        echo "  Error: {$_SESSION['error']}\n";
    }
    
    echo "Output length: " . strlen($output) . " characters\n";
    if (strlen($output) > 0) {
        echo "Output preview: " . substr($output, 0, 200) . "...\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
