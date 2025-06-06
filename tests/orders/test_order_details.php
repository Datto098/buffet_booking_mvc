<?php
// Direct test of order details endpoint
require_once 'config/config.php';
session_start();
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

// Simulate the admin/orders/details/1 route
$_SERVER['REQUEST_URI'] = '/admin/orders/details/1';
$segments = ['admin', 'orders', 'details', '1'];

// Set proper session variables
$_SESSION['user_id'] = 4;
$_SESSION['user_role'] = 'super_admin';
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

try {
    echo "Testing order details for ID 1...\n";
    $controller->orderDetails(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
