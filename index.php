<?php

/**
 * Main index file - Front Controller
 */

// Start session for authentication checks
session_start();

require_once 'config/config.php';

// Get the current URI and remove query string
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// Remove base path if running in subdirectory
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $uri = substr($uri, strlen($basePath));
}

// Split URI into segments
$segments = array_filter(explode('/', $uri));
$segments = array_values($segments); // Re-index array

// Default route
if (empty($segments)) {
    $segments = ['home'];
}

// Route the request
try {
    routeRequest($segments);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    include 'views/errors/500.php';
}

function routeRequest($segments)
{
    // Handle query parameters if no segments are provided
    if (count($segments) === 1 && $segments[0] === 'index.php') {
        $page = $_GET['page'] ?? 'home';
        $action = $_GET['action'] ?? 'index';
        $param = $_GET['param'] ?? null;

        // Handle admin routes
        if ($page === 'admin') {
            // Create segments array for admin routes
            $adminSegments = ['admin'];
            if (!empty($action)) $adminSegments[] = $action;
            if (!empty($param)) $adminSegments[] = $param;
            handleAdminRoute($adminSegments);
            return;
        }

        // Handle API routes
        if ($page === 'api') {
            // Create segments array for api routes
            $apiSegments = ['api'];
            if (!empty($action)) $apiSegments[] = $action;
            if (!empty($param)) $apiSegments[] = $param;
            handleApiRoute($apiSegments);
            return;
        }

        // Handle customer routes
        handleCustomerRoute($page, $action, $param);
        return;
    }

    // Original segment-based routing
    $page = $segments[0] ?? 'home';
    $action = $segments[1] ?? 'index';
    $param = $segments[2] ?? null;    // Handle admin routes
    if ($page === 'admin') {
        error_log("Main router handling admin route: " . print_r($segments, true));
        error_log("Session in main router: " . print_r($_SESSION, true));
        handleAdminRoute($segments);
        return;
    }

    // Handle API routes
    if ($page === 'api') {
        handleApiRoute($segments);
        return;
    }

    // Handle customer routes
    handleCustomerRoute($page, $action, $param);
}

function handleAdminRoute($segments)
{
    // Debug logging for admin authentication
    file_put_contents('debug.log', "handleAdminRoute called with segments: " . print_r($segments, true) . "\n", FILE_APPEND);
    file_put_contents('debug.log', "Session user_id: " . ($_SESSION['user_id'] ?? 'not set') . "\n", FILE_APPEND);
    file_put_contents('debug.log', "Session user_role: " . ($_SESSION['user_role'] ?? 'not set') . "\n", FILE_APPEND);
    file_put_contents('debug.log', "Role check result: " . (in_array($_SESSION['user_role'] ?? '', ['manager', 'super_admin']) ? 'pass' : 'fail') . "\n", FILE_APPEND);
      // Require admin authentication - check both possible session structures
    $isAuthenticated = false;
    $userRole = null;

    // Check standard session structure
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        $userRole = $_SESSION['user_role'];
        $isAuthenticated = in_array($userRole, ['manager', 'super_admin', 'admin']);
    }

    // Also check user array structure (fallback)
    if (!$isAuthenticated && isset($_SESSION['user']['id']) && isset($_SESSION['user']['role'])) {
        $userRole = $_SESSION['user']['role'];
        $isAuthenticated = in_array($userRole, ['manager', 'super_admin', 'admin']);
    }

    file_put_contents('debug.log', "Authentication check - role: $userRole, authenticated: " . ($isAuthenticated ? 'yes' : 'no') . "\n", FILE_APPEND);

    if (!$isAuthenticated) {
        file_put_contents('debug.log', "Admin authentication failed - redirecting to login\n", FILE_APPEND);
        header('Location: ' . SITE_URL . '/auth/login');
        exit;
    }

    file_put_contents('debug.log', "Admin authentication passed - proceeding with routing\n", FILE_APPEND);

    require_once 'controllers/AdminController.php';
    $controller = new AdminController();

    if (count($segments) === 1) {
        // /admin -> dashboard
        $controller->dashboard();
        return;
    }

    $section = $segments[1] ?? 'dashboard';
    $action = $segments[2] ?? 'index';
    $param = $segments[3] ?? null;

    switch ($section) {
        case 'users':
            handleAdminUsersRoute($controller, $action, $param);
            break;
        case 'foods':
            handleAdminFoodsRoute($controller, $action, $param);
            break;
        case 'categories':
            handleAdminCategoriesRoute($controller, $action, $param);
            break;
        case 'orders':
            handleAdminOrdersRoute($controller, $action, $param);
            break;
        case 'bookings':
            handleAdminBookingsRoute($controller, $action, $param);
            break;        case 'tables':
            handleAdminTablesRoute($controller, $action, $param);
            break;
        case 'news':
            handleAdminNewsRoute($action, $param);
            break;
        default:
            $controller->dashboard();
    }
}

function handleAdminUsersRoute($controller, $action, $param)
{
    switch ($action) {
        case 'create':
            $controller->createUser();
            break;
        case 'edit':
            $controller->editUser($param);
            break;
        case 'delete':
            $controller->deleteUser($param);
            break;
        default:
            $controller->users();
    }
}

function handleAdminFoodsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'create':
            $controller->createFood();
            break;
        case 'edit':
            $controller->editFood($param);
            break;
        case 'delete':
            $controller->deleteFood($param);
            break;
        default:
            $controller->foods();
    }
}

function handleAdminCategoriesRoute($controller, $action, $param)
{
    switch ($action) {
        case 'create':
            $controller->createCategory();
            break;
        case 'edit': // Added case for edit
            $controller->editCategory($param);
            break;
        case 'get': // Added case for get
            $controller->getCategory($param);
            break;        case 'update': // Added case for update
            $controller->updateCategory($param);
            break;        case 'delete': // Added case for delete
            $controller->deleteCategory($param);
            break;
        case 'subcategories':
            $controller->getSubcategories($param);
            break;
        default:
            $controller->categories();
    }
}

function handleAdminOrdersRoute($controller, $action, $param)
{
    switch ($action) {
        case 'update-status':
            $controller->updateOrderStatus($param);
            break;
        case 'details':
            $controller->orderDetails($param);
            break;
        case 'print':
            $controller->printOrder($param);
            break;
        case 'export-csv':
            $controller->exportOrdersCSV();
            break;
        case 'get':
            $controller->getOrder($param);
            break;
        case 'update':
            $controller->updateOrder($param);
            break;
        case 'duplicate':
            $controller->duplicateOrder($param);
            break;
        case 'send-email':
            $controller->sendOrderEmail($param);
            break;
        case 'delete':
            $controller->deleteOrder($param);
            break;
        default:
            $controller->orders();
    }
}

function handleAdminBookingsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'create':
            $controller->createBooking();
            break;
        case 'store':
            $controller->storeBooking();
            break;
        case 'update-status':
            $controller->updateBookingStatus();
            break;
        case 'assign-table':
            $controller->assignTable();
            break;
        case 'details':
            $controller->getBookingDetails($param);
            break;
        case 'available-tables':
            $controller->getAvailableTables();
            break;
        case 'edit':
            $controller->editBooking($param);
            break;
        case 'update':
            $controller->updateBooking();
            break;
        default:
            $controller->bookings();
    }
}

function handleAdminTablesRoute($controller, $action, $param)
{
    switch ($action) {
        case 'create':
            $controller->createTable();
            break;
        case 'edit':
            $controller->editTable($param);
            break;
        case 'delete':
            $controller->deleteTable($param);
            break;
        case 'utilization':
            $controller->tableUtilization();
            break;
        default:
            $controller->tables();
    }
}

function handleAdminNewsRoute($action, $param)
{
    require_once 'controllers/NewsController.php';
    $controller = new NewsController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'edit':
            $controller->edit($param);
            break;
        case 'delete':
            $controller->delete($param);
            break;
        default:
            $controller->manage();
    }
}

function handleApiRoute($segments)
{
    header('Content-Type: application/json');

    // Check if this is an admin API route
    if (isset($segments[1]) && $segments[1] === 'admin') {
        handleAdminApiRoute($segments);
        return;
    }

    // Basic API routing
    $endpoint = $segments[1] ?? '';

    switch ($endpoint) {
        case 'subcategories':
            require_once 'controllers/FoodController.php';
            $controller = new FoodController();
            $controller->getSubcategories();
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
    }
}

function handleAdminApiRoute($segments)
{
    // Require admin authentication for API routes
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['manager', 'super_admin'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    require_once 'controllers/AdminController.php';
    $controller = new AdminController();

    $endpoint = $segments[2] ?? '';

    switch ($endpoint) {
        case 'recent-orders':
            $controller->apiRecentOrders();
            break;
        case 'upcoming-bookings':
            $controller->apiUpcomingBookings();
            break;
        case 'order-stats':
            $controller->apiOrderStats();
            break;
        case 'booking-stats':
            $controller->apiBookingStats();
            break;
        case 'categories':
            handleAdminCategoryApi($controller, $segments);
            break;
        case 'foods':
            handleAdminFoodApi($controller, $segments);
            break;
        case 'bookings':
            handleAdminBookingApi($controller, $segments);
            break;
        case 'tables':
            handleAdminTableApi($controller, $segments);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Admin API endpoint not found']);
    }
}

function handleAdminCategoryApi($controller, $segments)
{
    $action = $segments[3] ?? '';

    switch ($action) {
        case 'create':
            $controller->createCategoryApi();
            break;
        case 'update':
            $controller->updateCategory();
            break;
        case 'delete':
            $controller->deleteCategory();
            break;
        case 'get':
            $id = $segments[4] ?? null;
            $controller->getCategory($id);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Category API action not found']);
    }
}

function handleAdminFoodApi($controller, $segments)
{
    $action = $segments[3] ?? '';

    switch ($action) {
        case 'subcategories':
            $controller->getSubcategories($segments[4] ?? null);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Food API action not found']);
    }
}

function handleAdminBookingApi($controller, $segments)
{
    $action = $segments[3] ?? '';

    switch ($action) {
        case 'update-status':
            $controller->updateBookingStatus();
            break;
        case 'assign-table':
            $controller->assignTable();
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Booking API action not found']);
    }
}

function handleAdminTableApi($controller, $segments)
{
    $action = $segments[3] ?? '';
    $param = $segments[4] ?? null;

    switch ($action) {
        case 'history':
            $controller->getTableHistory($param);
            break;
        case 'utilization':
            $controller->tableUtilization();
            break;
        case 'availability':
            $controller->checkTableAvailability($param);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Table API action not found']);
    }
}

function handleCustomerRoute($page, $action, $param)
{    // Customer route mapping
    $routes = [
        'home' => 'controllers/HomeController.php',
        'about' => 'controllers/HomeController.php',
        'promotions' => 'controllers/HomeController.php',
        'menu' => 'controllers/FoodController.php',
        'food' => 'controllers/FoodController.php',
        'cart' => 'controllers/CartController.php',
        'order' => 'controllers/OrderController.php',
        'booking' => 'controllers/BookingController.php',
        'user' => 'controllers/UserController.php',
        'auth' => 'controllers/AuthController.php',
        'news' => 'controllers/NewsController.php'
    ];

    if (!isset($routes[$page])) {
        http_response_code(404);
        include 'views/errors/404.php';
        return;
    }

    $controllerFile = $routes[$page];

    if (!file_exists($controllerFile)) {
        http_response_code(404);
        include 'views/errors/404.php';
        return;
    }
    require_once $controllerFile;    // Create controller instance
    $controllerMap = [
        'about' => 'HomeController',
        'promotions' => 'HomeController',
        'menu' => 'FoodController',
        'food' => 'FoodController',
    ];

    $controllerClass = isset($controllerMap[$page]) ? $controllerMap[$page] : ucfirst($page) . 'Controller';

    if (!class_exists($controllerClass)) {
        throw new Exception("Controller class $controllerClass not found");
    }
    $controller = new $controllerClass();

    // Call the appropriate method
    if ($action === 'update-profile' || $action === 'updateProfile') {
        $controller->updateProfile();
        exit;
    } elseif ($page === 'about' && method_exists($controller, 'about')) {
        $controller->about();
    } elseif ($page === 'promotions' && method_exists($controller, 'promotions')) {
        $controller->promotions();
    } elseif ($param && method_exists($controller, $action)) {
        $controller->$action($param);
    } elseif (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        $controller->index();
    }
}
