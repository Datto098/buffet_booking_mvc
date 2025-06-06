<?php
// Direct test of print order endpoint
require_once 'config/config.php';
session_start();
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

require_once 'controllers/AdminController.php';
$controller = new AdminController();

try {
    echo "Testing print order for ID 1...\n";
    $controller->printOrder(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
