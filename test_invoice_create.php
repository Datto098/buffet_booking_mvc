<?php
echo "<h2>Test Invoice Create Flow</h2>\n";

// Test URL tạo invoice cho order đã completed
$orderId = 16; // Order completed từ debug_model.php
$createUrl = "http://localhost/buffet_booking_mvc/admin/invoice/create?order_id=$orderId";

echo "<h3>Testing Create Invoice URL:</h3>\n";
echo "<a href='$createUrl' target='_blank'>$createUrl</a><br><br>";

// Test với cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $createUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h3>HTTP Status: $httpCode</h3>\n";

// Kiểm tra redirect
preg_match('/Location:\s*(.+)/i', $response, $matches);
if ($matches) {
    echo "<h3>Redirects to: " . trim($matches[1]) . "</h3>\n";
} else {
    echo "<h3>No redirect - should show create form</h3>\n";
}

// Kiểm tra xem order 16 đã có invoice chưa
echo "<h3>Checking if Order $orderId already has invoice:</h3>\n";
require_once 'config/config.php';
require_once 'models/Invoice.php';

try {
    $invoiceModel = new Invoice();
    $existingInvoice = $invoiceModel->findByOrderId($orderId);

    if ($existingInvoice) {
        echo "❌ Order $orderId already has invoice ID: {$existingInvoice['id']}<br>";
        echo "Invoice Number: {$existingInvoice['invoice_number']}<br>";
        echo "You can view it at: <a href='http://localhost/buffet_booking_mvc/admin/invoice/view/{$existingInvoice['id']}' target='_blank'>View Invoice</a><br>";
    } else {
        echo "✅ Order $orderId does not have invoice yet - can create new invoice<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking invoice: " . $e->getMessage() . "<br>";
}
?>
