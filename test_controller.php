<?php
session_start();

// Simulate admin login for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user'] = [
    'id' => 1,
    'role' => 'admin',
    'email' => 'admin@test.com'
];

echo "Session setup complete for testing\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'not set') . "\n";
echo "User Role: " . ($_SESSION['user_role'] ?? 'not set') . "\n";

// Now test accessing invoice controller
require_once 'config/config.php';
require_once 'controllers/InvoiceController.php';

try {
    echo "\nTesting InvoiceController instantiation...\n";
    $controller = new InvoiceController();
    echo "✓ InvoiceController created successfully\n";
} catch (Exception $e) {
    echo "✗ Error creating InvoiceController: " . $e->getMessage() . "\n";
}
?>
