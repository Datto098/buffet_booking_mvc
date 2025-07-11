<?php
session_start();

// Simulate logged in user
$_SESSION['user'] = [
    'id' => 1,
    'email' => 'test@example.com',
    'first_name' => 'Test',
    'last_name' => 'User'
];

require_once __DIR__ . '/get_order_status.php';
?>
