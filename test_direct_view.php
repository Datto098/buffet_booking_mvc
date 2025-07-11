<?php
// Test trực tiếp view invoice mà không qua authentication
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Invoice.php';
require_once 'models/DineInOrder.php';

// Mock session
$_SESSION['user'] = ['id' => 1, 'role' => 'admin'];

try {
    $invoiceModel = new Invoice();
    $dineInOrderModel = new DineInOrder();

    // Test với ID=1
    $id = 1;
    $invoice = $invoiceModel->getById($id);

    if (!$invoice) {
        echo "Không tìm thấy hóa đơn ID: " . $id;
        exit;
    }

    echo "<h3>Invoice found:</h3>";
    echo "<pre>";
    print_r($invoice);
    echo "</pre>";

    $order = $dineInOrderModel->getById($invoice['order_id']);

    echo "<h3>Order found:</h3>";
    echo "<pre>";
    print_r($order);
    echo "</pre>";

    $invoiceDetails = $invoiceModel->getInvoiceDetails($id);

    echo "<h3>Invoice Details:</h3>";
    echo "<pre>";
    print_r($invoiceDetails);
    echo "</pre>";

    // Include view trực tiếp
    echo "<h3>Loading view...</h3>";
    include 'views/admin/invoice/view.php';

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
