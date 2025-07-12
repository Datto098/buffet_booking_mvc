<?php
session_start();
require_once __DIR__ . '/config/config.php';

echo "<h2>Testing Invoice Access</h2>\n";

// Check session
echo "<h3>Session Info:</h3>\n";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test direct access
echo "<h3>Testing Invoice Controller Access:</h3>\n";
try {
    require_once 'controllers/InvoiceController.php';
    $invoiceController = new InvoiceController();
    echo "✅ Invoice Controller created successfully<br>";
} catch (Exception $e) {
    echo "❌ Error creating Invoice Controller: " . $e->getMessage() . "<br>";
}

// Test URL with order_id
if (isset($_GET['order_id'])) {
    echo "<h3>Order ID from URL: " . $_GET['order_id'] . "</h3>\n";

    try {
        require_once 'models/DineInOrder.php';
        $dineInOrderModel = new DineInOrder();
        $order = $dineInOrderModel->findById($_GET['order_id']);

        if ($order) {
            echo "<h4>Order found:</h4>\n";
            echo "<pre>";
            print_r($order);
            echo "</pre>";
        } else {
            echo "❌ Order not found<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error finding order: " . $e->getMessage() . "<br>";
    }
}
?>
