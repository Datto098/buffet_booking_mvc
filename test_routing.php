<?php
// Test direct access to invoice route
echo "Testing direct access to invoice create...\n";

// Simulate the routing
$segments = ['admin', 'invoice', 'create'];

// Test routing logic
if (isset($segments[0]) && $segments[0] === 'admin') {
    if (isset($segments[1]) && $segments[1] === 'invoice') {
        echo "✓ Admin route detected\n";
        echo "✓ Invoice route detected\n";
        echo "Action would be: " . ($segments[2] ?? 'default') . "\n";
    }
}

// Test actual URL
echo "\nTesting actual URL access...\n";
$url = "http://localhost/buffet_booking_mvc/admin/invoice/create";
echo "URL: $url\n";

// Test if controller file exists
$controllerPath = __DIR__ . '/controllers/InvoiceController.php';
echo "Controller exists: " . (file_exists($controllerPath) ? "YES" : "NO") . "\n";

if (file_exists($controllerPath)) {
    echo "Controller file size: " . filesize($controllerPath) . " bytes\n";
}
?>
