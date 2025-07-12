<?php
// Test thực tế InvoiceController với authentication
echo "<h2>Test Real InvoiceController Flow</h2>\n";

// Không set session để mô phỏng user chưa đăng nhập
session_start();

echo "<h3>Current Session:</h3>\n";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Testing InvoiceController với authentication:</h3>\n";
try {
    // Include các file cần thiết
    require_once __DIR__ . '/controllers/InvoiceController.php';

    // Tạo controller - điều này sẽ gọi requireAdmin()
    echo "Creating InvoiceController...<br>";
    $controller = new InvoiceController();

    echo "✅ InvoiceController created successfully (user has admin access)<br>";

    // Gọi viewInvoice
    echo "Calling viewInvoice(1)...<br>";
    $controller->viewInvoice(1);

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

// Điều này sẽ không được thực thi nếu có redirect
echo "<h3>This line should not appear if redirect happened</h3>\n";
?>
