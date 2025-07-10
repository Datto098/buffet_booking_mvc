<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Main index file - Front Controller
 */

// Start session for authentication checks
session_start();

require_once 'config/config.php';
require_once 'helpers/functions.php';

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
        $param = $_GET['param'] ?? null;        // Handle admin routes
        if ($page === 'admin') {
            // Create segments array for admin routes
            $adminSegments = ['admin'];
            if (!empty($action)) $adminSegments[] = $action;
            if (!empty($param)) $adminSegments[] = $param;
            handleAdminRoute($adminSegments);
            return;
        }

        // Handle super admin routes
        if ($page === 'superadmin') {
            // Create segments array for super admin routes
            $superAdminSegments = ['superadmin'];
            if (!empty($action)) $superAdminSegments[] = $action;
            if (!empty($param)) $superAdminSegments[] = $param;
            handleSuperAdminRoute($superAdminSegments);
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

    // Handle super admin routes
    if ($page === 'superadmin') {
        error_log("Main router handling superadmin route: " . print_r($segments, true));
        error_log("Session in main router: " . print_r($_SESSION, true));
        handleSuperAdminRoute($segments);
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
    // Debug logging for admin authentication (tạm thời tắt)
    // file_put_contents('debug.log', "handleAdminRoute called with segments: " . print_r($segments, true) . "\n", FILE_APPEND);
    // file_put_contents('debug.log', "Session user_id: " . ($_SESSION['user_id'] ?? 'not set') . "\n", FILE_APPEND);
    // file_put_contents('debug.log', "Session user_role: " . ($_SESSION['user_role'] ?? 'not set') . "\n", FILE_APPEND);
    // file_put_contents('debug.log', "Role check result: " . (in_array($_SESSION['user_role'] ?? '', ['manager', 'super_admin']) ? 'pass' : 'fail') . "\n", FILE_APPEND);
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

    // file_put_contents('debug.log', "Authentication check - role: $userRole, authenticated: " . ($isAuthenticated ? 'yes' : 'no') . "\n", FILE_APPEND);

    if (!$isAuthenticated) {
        // file_put_contents('debug.log', "Admin authentication failed - redirecting to login\n", FILE_APPEND);
        header('Location: ' . SITE_URL . '/auth/login');
        exit;
    }

    // file_put_contents('debug.log', "Admin authentication passed - proceeding with routing\n", FILE_APPEND);

    require_once 'controllers/AdminController.php';
    $controller = new AdminController();

    if (count($segments) === 1) {
        // /admin -> dashboard
        $controller->dashboard();
        return;
    }

    $section = $segments[1] ?? 'dashboard';
    $action = $segments[2] ?? 'index';
    $param = $segments[3] ?? null;    switch ($section) {
        case 'dashboard':
            if ($action === 'stats') {
                // Handle dashboard stats API endpoint
                $controller->dashboardStats();
            } else {
                $controller->dashboard();
            }
            break;
        case 'users':
            handleAdminUsersRoute($controller, $action, $param);
            break;
        case 'foods':
            handleAdminFoodsRoute($controller, $action, $param);
            break;
        case 'categories':
            handleAdminCategoriesRoute($controller, $action, $param);
            break;        case 'orders':
            handleAdminOrdersRoute($controller, $action, $param);
            break;
        case 'payments':
            handleAdminPaymentsRoute($controller, $action, $param);
            break;
        case 'bookings':
            handleAdminBookingsRoute($controller, $action, $param);
            break;case 'tables':
            handleAdminTablesRoute($controller, $action, $param);
            break;
        case 'news':
            handleAdminNewsRoute($action, $param);
            break;
        case 'logs':
            handleAdminLogsRoute($controller, $action, $param);
            break;
        case 'internal-messages':
            // Sử dụng InternalMessageController cho internal-messages
            require_once 'controllers/InternalMessageController.php';
            $internalMessageController = new InternalMessageController();
            handleAdminInternalMessagesRoute($internalMessageController, $action, $param);
            break;
        case 'dine-in-orders':
            handleAdminDineInOrdersRoute($controller, $action, $param);
            break;
        default:
            $controller->dashboard();    }
}

function handleSuperAdminRoute($segments)
{
    // Require super admin authentication
    $isAuthenticated = false;
    $userRole = null;

    // Check standard session structure
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        $userRole = $_SESSION['user_role'];
        $isAuthenticated = ($userRole === 'super_admin');
    }

    // Also check user array structure (fallback)
    if (!$isAuthenticated && isset($_SESSION['user']['id']) && isset($_SESSION['user']['role'])) {
        $userRole = $_SESSION['user']['role'];
        $isAuthenticated = ($userRole === 'super_admin');
    }

    if (!$isAuthenticated) {
        error_log("Super Admin authentication failed - user role: " . ($userRole ?? 'none'));
        header('Location: ' . SITE_URL . '/auth/login');
        exit;
    }

    require_once 'controllers/SuperAdminController.php';
    $controller = new SuperAdminController();

    if (count($segments) === 1) {
        // /superadmin -> dashboard
        $controller->dashboard();
        return;
    }

    $section = $segments[1] ?? 'dashboard';
    $action = $segments[2] ?? 'index';
    $param = $segments[3] ?? null;

    switch ($section) {
        case 'dashboard':
            if ($action === 'stats') {
                $controller->dashboardStats();
            } else {
                $controller->dashboard();
            }
            break;
        case 'users':
            handleSuperAdminUsersRoute($controller, $action, $param);
            break;
        case 'orders':
            handleSuperAdminOrdersRoute($controller, $action, $param);
            break;
        case 'bookings':
            handleSuperAdminBookingsRoute($controller, $action, $param);
            break;
        case 'tables':
            handleSuperAdminTablesRoute($controller, $action, $param);
            break;
        case 'restaurant':
            handleSuperAdminRestaurantRoute($controller, $action, $param);
            break;        case 'promotions':
            handleSuperAdminPromotionsRoute($controller, $action, $param);
            break;        case 'reviews':
            handleSuperAdminReviewsRoute($controller, $action, $param);
            break;
        case 'notifications':
            handleSuperAdminNotificationsRoute($controller, $action, $param);
            break;
        case 'internal-messages':
            // Sử dụng InternalMessageController cho internal-messages
            require_once 'controllers/InternalMessageController.php';
            $internalMessageController = new InternalMessageController();
            handleSuperAdminInternalMessagesRoute($internalMessageController, $action, $param);
            break;
        case 'statistics':
            $controller->statistics();
            break;
        default:
            $controller->dashboard();
    }
}

function handleSuperAdminUsersRoute($controller, $action, $param)
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

function handleSuperAdminOrdersRoute($controller, $action, $param)
{
    switch ($action) {
        case 'details':
            $controller->orderDetails($param);
            break;
        case 'updateStatus':
            $controller->updateOrderStatus($param);
            break;
        default:
            $controller->orders();
    }
}

function handleSuperAdminBookingsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'details':
            $controller->bookingDetails($param);
            break;
        case 'updateStatus':
            $controller->updateBookingStatus($param);
            break;
        case 'assignTable':
            $controller->assignTable($param);
            break;
        default:
            $controller->bookings();
    }
}

function handleSuperAdminTablesRoute($controller, $action, $param)
{
    switch ($action) {
        case 'create':
            $controller->createTable();
            break;
        case 'update':
            $controller->updateTable($param);
            break;
        case 'delete':
            $controller->deleteTable($param);
            break;
        case 'get':
            $controller->getTable($param);
            break;
        case 'updateStatus':
            $controller->updateTableStatus($param);
            break;
        case 'available':
            $controller->getAvailableTables($param);
            break;
        case 'bookings':
            $controller->getTableBookings($param);
            break;
        default:
            $controller->tables();
    }
}

function handleSuperAdminRestaurantRoute($controller, $action, $param)
{
    switch ($action) {
        case 'update':
            $controller->updateRestaurantInfo();
            break;
        default:
            $controller->restaurantInfo();
    }
}

function handleSuperAdminPromotionsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'create':
            $controller->createPromotion();
            break;
        case 'get':
            $controller->getPromotion($param);
            break;
        case 'edit':
            $controller->editPromotion($param);
            break;
        case 'delete':
            $controller->deletePromotion($param);
            break;
        case 'toggle':
            $controller->togglePromotionStatus($param);
            break;
        default:
            $controller->promotions();
    }
}

function handleSuperAdminReviewsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'details':
            $controller->reviewDetails($param);
            break;
        case 'approve':
            $controller->approveReview($param);
            break;
        case 'reject':
            $controller->rejectReview($param);
            break;
        case 'verify':
            $controller->verifyReview($param);
            break;
        case 'delete':
            $controller->deleteReview($param);
            break;
        case 'bulk-action':
            $controller->reviewsBulkAction();
            break;
        case 'stats':
            $controller->reviewStats();
            break;        default:
            $controller->reviews();
    }
}

function handleSuperAdminNotificationsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'unread-count':
            $controller->getUnreadCount();
            break;
        case 'recent':
            $controller->getRecentNotifications();
            break;
        case 'mark-read':
            $controller->markNotificationRead($param);
            break;
        case 'mark-all-read':
            $controller->markAllNotificationsRead();
            break;
        case 'delete':
            $controller->deleteNotification($param);
            break;
        case 'bulk-action':
            $controller->notificationsBulkAction();
            break;
        default:
            $controller->notifications();
    }
}

function handleSuperAdminInternalMessagesRoute($controller, $action, $param)
{
    switch ($action) {
        case 'send':
            $controller->sendMessage();
            break;
        case 'process':
            $controller->processSendMessage();
            break;
        case 'sent':
            $controller->sentMessages();
            break;
        case 'view':
            $controller->viewMessage($param);
            break;
        case 'delete':
            $controller->deleteMessage($param);
            break;
        case 'sse':
            $controller->sse();
            break;
        case 'get-unread-count':
            $controller->getUnreadCount();
            break;
        case 'mark-as-read':
            $controller->markAsReadAjax();
            break;
        default:
            $controller->sendMessage();
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
{    switch ($action) {
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
            break;        case 'bulk-update-status':
            $controller->bulkUpdateCategoryStatus();
            break;
        case 'export':
            $controller->exportCategories();
            break;
        case 'subcategories':
            $controller->getSubcategories($param);
            break;
        default:
            $controller->categories();
    }
}

function handleAdminOrdersRoute($controller, $action, $param)
{    switch ($action) {
        case 'update-status':
            $controller->updateOrderStatus($param);
            break;
        case 'update-payment-status':
            $controller->updatePaymentStatus($param);
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
            break;        default:
            $controller->orders();
    }
}

function handleAdminPaymentsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'details':
            $controller->paymentDetails($param);
            break;
        case 'cancel':
            $controller->cancelPayment($param);
            break;
        case 'export':
            $controller->exportPayments();
            break;
        default:
            $controller->payments();
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
            $controller->updateBookingStatus($param);
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
        case 'send-confirmation':
            $controller->sendConfirmationEmail();
            break;
        case 'bulk-update-status':
            $controller->bulkUpdateStatus();
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
            break;        case 'edit':
            $controller->editTable($param);
            break;
        case 'delete':
            $controller->deleteTable($param);
            break;
        case 'toggle-status':
            $controller->toggleTableStatus();
            break;
        case 'utilization':
            $controller->tableUtilization();
            break;
        case 'history':
            $controller->getTableHistory($param);
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
        case 'toggle-status':
            $controller->toggleStatus();
            break;
        case 'bulk-action':
            $controller->bulkAction();
            break;
        default:
            $controller->manage();
    }
}

function handleAdminLogsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'download':
            $controller->downloadLog($param);
            break;
        case 'clear':
            $controller->clearLog($param);
            break;
        case 'view':
            $controller->viewLog($param);
            break;
        default:
            $controller->logs();
    }
}

function handleAdminInternalMessagesRoute($controller, $action, $param)
{
    switch ($action) {
        case 'view':
            $controller->viewMessage($param);
            break;
        case 'download-attachment':
            $controller->downloadAttachment($param);
            break;
        case 'sse':
            $controller->sse();
            break;
        case 'get-unread-count':
            $controller->getUnreadCount();
            break;
        case 'mark-as-read':
            $controller->markAsReadAjax();
            break;
        default:
            $controller->internalMessages();
    }
}

function handleAdminDineInOrdersRoute($controller, $action, $param)
{
    require_once 'controllers/DineInOrderAdminController.php';
    $dineInOrderAdmin = new DineInOrderAdminController();
    switch ($action) {
        case 'view':
            $dineInOrderAdmin->view();
            break;
        case 'update-status':
            $dineInOrderAdmin->updateStatus();
            break;
        default:
            $dineInOrderAdmin->index();
            break;
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
        'payment' => 'controllers/PaymentController.php',
        'booking' => 'controllers/BookingController.php',
        'user' => 'controllers/UserController.php',
        'auth' => 'controllers/AuthController.php',
        'news' => 'controllers/NewsController.php',
        'review' => 'controllers/ReviewController.php',
        'dine-in' => 'controllers/DineInController.php'
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
        'dine-in' => 'DineInController',
    ];

    $controllerClass = isset($controllerMap[$page]) ? $controllerMap[$page] : ucfirst($page) . 'Controller';

    if (!class_exists($controllerClass)) {
        throw new Exception("Controller class $controllerClass not found");
    }
    $controller = new $controllerClass();

    // Debug route handling
    error_log("Customer route - Page: $page, Action: $action, Param: $param");
    error_log("Controller class: $controllerClass");
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);

    // Call the appropriate method
    if ($action === 'update-profile' || $action === 'updateProfile') {
        $controller->updateProfile();
        exit;
    } elseif ($action === 'forgot-password' || $action === 'forgotPassword') {
        $controller->forgotPassword();
        exit;
    } elseif ($page === 'dine-in') {
        // Handle dine-in specific routes
        switch ($action) {
            case 'add-to-cart':
                $controller->addToCart();
                break;
            case 'get-cart':
                $controller->getCart();
                break;
            case 'update-cart-item':
                $controller->updateCartItem();
                break;
            case 'clear-cart':
                $controller->clearCart();
                break;
            case 'submit-order':
                $controller->submitOrder();
                break;
            case 'get-order-status':
                $controller->getOrderStatus();
                break;
            default:
                $controller->index();
                break;
        }
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
