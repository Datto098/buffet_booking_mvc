<?php
// Test với manager role
echo "<h2>Debug InvoiceController với Manager Role</h2>\n";

// Set session manager với format đúng
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'manager@test.com';
$_SESSION['user_role'] = 'manager';
$_SESSION['is_logged_in'] = true;
$_SESSION['user'] = [
    'id' => 1,
    'role' => 'manager',
    'full_name' => 'Test Manager'
];

echo "<h3>Session được set:</h3>\n";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

require_once __DIR__ . '/config/config.php';

// Test authentication functions
echo "<h3>Testing authentication functions:</h3>\n";
require_once __DIR__ . '/helpers/functions.php';

echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "<br>";
echo "isAdmin(): " . (isAdmin() ? 'true' : 'false') . "<br>";
echo "isManager(): " . (isManager() ? 'true' : 'false') . "<br>";

// Test InvoiceController
echo "<h3>Testing InvoiceController với manager:</h3>\n";
try {
    require_once __DIR__ . '/controllers/InvoiceController.php';

    echo "Creating InvoiceController...<br>";
    $controller = new InvoiceController();
    echo "✅ InvoiceController created successfully<br>";

    // Test getInvoiceDetails method
    echo "<h4>Testing getInvoiceDetails method:</h4>\n";
    require_once __DIR__ . '/models/Invoice.php';
    $invoiceModel = new Invoice();

    $invoiceDetails = $invoiceModel->getInvoiceDetails(1);
    if ($invoiceDetails) {
        echo "✅ getInvoiceDetails(1) successful<br>";
        echo "<pre>";
        print_r($invoiceDetails);
        echo "</pre>";
    } else {
        echo "❌ getInvoiceDetails(1) failed<br>";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>
