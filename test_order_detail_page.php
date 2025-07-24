<?php
// Test order detail page output
echo "Testing order detail page output...\n";

// Start session and set up user
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'customer';

// Set up environment
$_SERVER['REQUEST_URI'] = '/buffet_booking_mvc/index.php?page=order&action=detail&id=8';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['page'] = 'order';
$_GET['action'] = 'detail';
$_GET['id'] = '8';

require_once 'config/config.php';
require_once 'controllers/OrderController.php';

echo "Testing OrderController->detail() for order ID 8...\n";

try {
    $controller = new OrderController();

    ob_start();
    $controller->detail();
    $output = ob_get_contents();
    ob_end_clean();

    echo "Page generated successfully!\n";
    echo "Output length: " . strlen($output) . " characters\n";

    // Check if cancel button is present
    $hasCancelButton = strpos($output, 'Cancel Order') !== false;
    echo "Contains 'Cancel Order' button: " . ($hasCancelButton ? 'Yes' : 'No') . "\n";

    // Check order status display
    if (preg_match('/status.*?cancelled/i', $output)) {
        echo "Shows order status as cancelled: Yes\n";
    } else {
        echo "Shows order status as cancelled: No\n";
    }

    // Check if the condition is correct
    if (preg_match('/pending.*?Cancel Order/s', $output)) {
        echo "Cancel button is conditional on pending status: Yes\n";
    } else {
        echo "Cancel button condition check: Not found or different logic\n";
    }

    // Show a snippet around any cancel button
    if ($hasCancelButton) {
        $pos = strpos($output, 'Cancel Order');
        $start = max(0, $pos - 200);
        $length = 400;
        echo "\nContext around 'Cancel Order':\n";
        echo substr($output, $start, $length) . "\n";
    }

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
