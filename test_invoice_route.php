<?php
echo "<h2>Testing Invoice View Route</h2>\n";

$url = "http://localhost/buffet_booking_mvc/admin/invoice/view/1";
echo "<h3>Testing URL: $url</h3>\n";

// Test with cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>HTTP Status Code: $httpCode</h3>\n";

if ($error) {
    echo "<h3>cURL Error: $error</h3>\n";
}

echo "<h3>Response Headers and Body:</h3>\n";
echo "<pre>";
echo htmlspecialchars($response);
echo "</pre>";

// Also test if invoice with ID 1 exists
echo "<h3>Checking if Invoice ID 1 exists in database:</h3>\n";
try {
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/models/Invoice.php';

    $invoiceModel = new Invoice();
    $invoice = $invoiceModel->findById(1);

    if ($invoice) {
        echo "✅ Invoice ID 1 found:<br>";
        echo "<pre>";
        print_r($invoice);
        echo "</pre>";
    } else {
        echo "❌ Invoice ID 1 not found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking invoice: " . $e->getMessage() . "<br>";
}
?>
