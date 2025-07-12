<?php
// Minimal test to check route handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing route handling...\n";

// Set up the environment similar to the actual request
$_GET['page'] = 'order';
$_GET['action'] = 'history';

// Start session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['is_logged_in'] = true;

echo "Environment set up...\n";

// Include the actual index.php logic
try {
    include 'index.php';
} catch (Exception $e) {
    echo "Error in index.php: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
