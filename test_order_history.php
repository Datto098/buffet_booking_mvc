<?php
// Test script to debug order history
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting order history test...\n";

// Include required files
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/functions.php';

echo "Config files loaded...\n";

// Simulate session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['is_logged_in'] = true;

echo "Session simulated...\n";

try {
    require_once 'controllers/BaseController.php';
    require_once 'controllers/OrderController.php';

    echo "Controllers loaded...\n";

    $controller = new OrderController();
    echo "OrderController instantiated...\n";

    echo "Calling history method...\n";
    $controller->history();
    echo "History method completed successfully!\n";

} catch (Exception $e) {
    echo "Error occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
