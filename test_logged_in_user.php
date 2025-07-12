<?php
// Test with logged in user simulation
echo "Testing with simulated logged-in user...\n";

// Start session first
session_start();

// Set up environment variables to simulate the URL request
$_SERVER['REQUEST_URI'] = '/buffet_booking_mvc/index.php?page=order&action=history';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/buffet_booking_mvc/index.php';
$_GET['page'] = 'order';
$_GET['action'] = 'history';

// Simulate logged in user
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'customer';
$_SESSION['user_name'] = 'Test User';

echo "Environment set up with logged in user...\n";
echo "Session user_id: " . $_SESSION['user_id'] . "\n";

require_once 'config/config.php';
require_once 'helpers/functions.php';

// Get the URI segments the same way index.php does
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $uri = substr($uri, strlen($basePath));
}

$segments = array_filter(explode('/', $uri));
$segments = array_values($segments);

if (empty($segments)) {
    $segments = ['home'];
}

echo "URI segments: " . print_r($segments, true) . "\n";

// Manually parse the route like index.php does
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$param = $_GET['param'] ?? null;

echo "Route: Page=$page, Action=$action, Param=$param\n";

// Load OrderController directly
require_once 'controllers/OrderController.php';
$controller = new OrderController();

echo "Testing OrderController->history() method...\n";

try {
    ob_start();
    $controller->history();
    $output = ob_get_contents();
    ob_end_clean();

    echo "Success! OrderController->history() executed without errors.\n";
    echo "Output length: " . strlen($output) . " characters\n";
    echo "Contains order history HTML: " . (strpos($output, 'order') !== false ? 'Yes' : 'No') . "\n";
    echo "Contains pagination: " . (strpos($output, 'pagination') !== false ? 'Yes' : 'No') . "\n";

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
