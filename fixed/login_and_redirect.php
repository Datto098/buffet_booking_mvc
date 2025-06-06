<?php
// Login and redirect to admin orders
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

// Redirect to admin orders
header('Location: /admin/orders');
exit;
?>
