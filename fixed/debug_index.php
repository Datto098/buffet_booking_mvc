<?php
/**
 * Debug index file - Front Controller
 */

require_once 'config/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "DEBUG: Starting index.php<br>";

// Get the current URI and remove query string
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

echo "DEBUG: Original URI: " . $uri . "<br>";

// Remove base path if running in subdirectory
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $uri = substr($uri, strlen($basePath));
}

echo "DEBUG: Base path: " . $basePath . "<br>";
echo "DEBUG: Final URI: " . $uri . "<br>";

// Split URI into segments
$segments = array_filter(explode('/', $uri));
$segments = array_values($segments);

echo "DEBUG: Segments: " . print_r($segments, true) . "<br>";

// Route the request
try {
    routeRequest($segments);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "TRACE: " . $e->getTraceAsString() . "<br>";
    error_log($e->getMessage());
    http_response_code(500);
    include 'views/errors/500.php';
}

function routeRequest($segments) {
    echo "DEBUG: In routeRequest<br>";
    $page = $segments[0] ?? 'home';
    $action = $segments[1] ?? 'index';
    $param = $segments[2] ?? null;

    echo "DEBUG: page=$page, action=$action, param=$param<br>";

    // Handle admin routes
    if ($page === 'admin') {
        echo "DEBUG: Handling admin route<br>";
        handleAdminRoute($segments);
        return;
    }

    // Handle API routes
    if ($page === 'api') {
        echo "DEBUG: Handling API route<br>";
        handleApiRoute($segments);
        return;
    }

    echo "DEBUG: Handling customer route<br>";
    // Handle customer routes
    handleCustomerRoute($page, $action, $param);
}

function handleAdminRoute($segments) {
    echo "DEBUG: In handleAdminRoute<br>";
    // Require admin authentication
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['manager', 'super_admin'])) {
        echo "DEBUG: Admin auth failed, redirecting to login<br>";
        header('Location: /auth/login');
        exit;
    }

    echo "DEBUG: Admin auth passed<br>";
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();

    if (count($segments) === 1) {
        echo "DEBUG: Loading dashboard<br>";
        // /admin -> dashboard
        $controller->dashboard();
        return;
    }

    $section = $segments[1] ?? 'dashboard';
    $action = $segments[2] ?? 'index';
    $param = $segments[3] ?? null;

    echo "DEBUG: section=$section, action=$action, param=$param<br>";

    switch ($section) {
        case 'orders':
            echo "DEBUG: Handling orders route<br>";
            handleAdminOrdersRoute($controller, $action, $param);
            break;
        default:
            echo "DEBUG: Default dashboard<br>";
            $controller->dashboard();
    }
}

function handleAdminOrdersRoute($controller, $action, $param) {
    echo "DEBUG: In handleAdminOrdersRoute - action=$action, param=$param<br>";
    switch ($action) {
        case 'details':
            echo "DEBUG: Calling orderDetails($param)<br>";
            $controller->orderDetails($param);
            break;
        default:
            echo "DEBUG: Calling orders()<br>";
            $controller->orders();
    }
}

function handleCustomerRoute($page, $action, $param) {
    echo "DEBUG: In handleCustomerRoute - page=$page<br>";
    // Simple home controller for testing
    require_once 'controllers/HomeController.php';
    $controller = new HomeController();
    $controller->index();
}
?>
