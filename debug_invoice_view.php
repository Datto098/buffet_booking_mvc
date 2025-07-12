<?php
// Bật error reporting để debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Debug InvoiceController::viewInvoice()</h2>\n";

// Tạm thời bỏ qua authentication để test
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'admin']; // Fake admin session

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Invoice.php';
require_once __DIR__ . '/models/DineInOrder.php';

try {
    $invoiceModel = new Invoice();
    $dineInOrderModel = new DineInOrder();

    $id = 1;
    echo "<h3>Testing with Invoice ID: $id</h3>\n";

    // Test getById method
    echo "<h4>1. Testing invoiceModel->getById($id):</h4>\n";
    $invoice = $invoiceModel->getById($id);
    if ($invoice) {
        echo "✅ Invoice found via getById()<br>";
        echo "<pre>";
        print_r($invoice);
        echo "</pre>";
    } else {
        echo "❌ Invoice NOT found via getById()<br>";
    }

    // Test findById method (từ test trước đã thành công)
    echo "<h4>2. Testing invoiceModel->findById($id):</h4>\n";
    $invoiceAlt = $invoiceModel->findById($id);
    if ($invoiceAlt) {
        echo "✅ Invoice found via findById()<br>";
        echo "<pre>";
        print_r($invoiceAlt);
        echo "</pre>";
    } else {
        echo "❌ Invoice NOT found via findById()<br>";
    }

    // Test DineInOrder
    if ($invoice) {
        $orderId = $invoice['order_id'];
        echo "<h4>3. Testing dineInOrderModel->getById($orderId):</h4>\n";
        $order = $dineInOrderModel->getById($orderId);
        if ($order) {
            echo "✅ Order found<br>";
            echo "<pre>";
            print_r($order);
            echo "</pre>";
        } else {
            echo "❌ Order NOT found<br>";
        }
    }

    // Test view file exists
    echo "<h4>4. Testing view file exists:</h4>\n";
    $viewPath = 'views/admin/invoice/view.php';
    if (file_exists($viewPath)) {
        echo "✅ View file exists: $viewPath<br>";
    } else {
        echo "❌ View file missing: $viewPath<br>";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>
