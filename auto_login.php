<?php
// Auto-login script for testing
session_start();

// Set the session to simulate admin login
$_SESSION['user_id'] = 4;
$_SESSION['user_role'] = 'super_admin';
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

echo "<!DOCTYPE html><html><head><title>Auto Login</title></head><body>";
echo "<h1>Automatic Admin Login</h1>";
echo "<p>You have been logged in as admin user.</p>";
echo "<p><a href='/admin'>Go to Admin Dashboard</a></p>";
echo "<p><a href='/admin/orders'>Go to Orders Management</a></p>";
echo "<p><a href='/admin/orders/details/1'>Test Order Details for ID 1</a></p>";
echo "<p><a href='/admin/orders/print/1'>Test Print Order for ID 1</a></p>";
echo "<p><a href='/admin/orders/export-csv'>Test CSV Export</a></p>";

echo "<h2>Session Info:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</body></html>";
?>
