<?php

/**
 * SuperAdmin Controller
 * Complete implementation for Super Admin system
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Promotion.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/InternalMessage.php';

class SuperAdminController extends BaseController
{
    private $userModel;
    private $foodModel;
    private $categoryModel;
    private $orderModel;
    private $bookingModel;
    private $tableModel;
    private $promotionModel;
    private $reviewModel;
    private $notificationModel;
    private $internalMessageModel;

    public function __construct()
    {
        $this->requireRole(['super_admin']);
        $this->userModel = new User();
        $this->foodModel = new Food();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
        $this->bookingModel = new Booking();
        $this->tableModel = new Table();
        $this->promotionModel = new Promotion();
        $this->reviewModel = new Review();
        $this->notificationModel = new Notification();
        $this->internalMessageModel = new InternalMessage();
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Super Admin Dashboard',
            'stats' => $this->getDashboardStats()
        ];

        $this->loadSuperAdminView('dashboard', $data);
    }

    public function dashboardStats()
    {
        header('Content-Type: application/json');
        $stats = $this->getDashboardStats();
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    }

    // ==========================================
    // USER MANAGEMENT
    // ==========================================
    public function users()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';

        // Get all users with filters
        $allUsers = $this->userModel->getAllForAdmin();

        // Apply filters
        if ($search) {
            $allUsers = array_filter($allUsers, function ($user) use ($search) {
                return stripos($user['first_name'] . ' ' . $user['last_name'], $search) !== false ||
                    stripos($user['email'], $search) !== false ||
                    stripos($user['phone'], $search) !== false;
            });
        }

        if ($role) {
            $allUsers = array_filter($allUsers, function ($user) use ($role) {
                return $user['role'] === $role;
            });
        }

        if ($status !== '') {
            $allUsers = array_filter($allUsers, function ($user) use ($status) {
                return (string)$user['is_active'] === $status;
            });
        }

        $totalUsers = count($allUsers);
        $users = array_slice($allUsers, $offset, $limit);
        $totalPages = ceil($totalUsers / $limit);

        // Calculate statistics
        $roleStats = [];
        foreach ($this->userModel->getAllForAdmin() as $user) {
            $roleStats[$user['role']] = ($roleStats[$user['role']] ?? 0) + 1;
        }
        $data = [
            'title' => 'User Management',
            'users' => $users,
            'stats' => [
                'customers' => $roleStats['customer'] ?? 0,
                'managers' => $roleStats['manager'] ?? 0,
                'super_admins' => $roleStats['super_admin'] ?? 0,
                'total' => array_sum($roleStats)
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalUsers
            ],
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'search' => $search,
            'selectedRole' => $role,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('users/index', $data);
    }

    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/superadmin/users');
                return;
            }
            $userData = [
                'first_name' => $this->sanitize($_POST['first_name']),
                'last_name' => $this->sanitize($_POST['last_name']),
                'email' => $this->sanitize($_POST['email']),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'address' => $this->sanitize($_POST['address'] ?? ''),
                'role' => $this->sanitize($_POST['role']),
                'is_active' => (int)($_POST['is_active'] ?? 1),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Validate required fields
            if (
                empty($userData['first_name']) || empty($userData['last_name']) ||
                empty($userData['email']) || empty($_POST['password'])
            ) {
                $this->setFlash('error', 'Please fill in all required fields.');
                $this->redirect('/superadmin/users/create');
                return;
            }            // Check if email already exists
            if ($this->userModel->findByEmail($userData['email'])) {
                $this->setFlash('error', 'Email already exists.');
                $this->redirect('/superadmin/users/create');
                return;
            }

            if ($this->userModel->create($userData)) {
                $this->setFlash('success', 'User created successfully.');
                $this->redirect('/superadmin/users');
            } else {
                $this->setFlash('error', 'Failed to create user.');
                $this->redirect('/superadmin/users/create');
            }
            return;
        }

        $data = [
            'title' => 'Create User',
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('users/create', $data);
    }
    public function editUser($id)
    {
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/superadmin/users');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/superadmin/users');
                return;
            }

            $userData = [
                'first_name' => $this->sanitize($_POST['first_name']),
                'last_name' => $this->sanitize($_POST['last_name']),
                'email' => $this->sanitize($_POST['email']),
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'address' => $this->sanitize($_POST['address'] ?? ''),
                'role' => $this->sanitize($_POST['role']),
                'is_active' => (int)($_POST['is_active'] ?? 0)
            ];

            // Update password only if provided
            if (!empty($_POST['password'])) {
                $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } // Check if email already exists for other users
            $existingUser = $this->userModel->findByEmail($userData['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                $this->setFlash('error', 'Email already exists.');
                $this->redirect('/superadmin/users/edit/' . $id);
                return;
            }

            if ($this->userModel->update($id, $userData)) {
                $this->setFlash('success', 'User updated successfully.');
                $this->redirect('/superadmin/users');
            } else {
                $this->setFlash('error', 'Failed to update user.');
                $this->redirect('/superadmin/users/edit/' . $id);
            }
            return;
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('users/edit', $data);
    }

    public function deleteUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
                return;
            }

            $user = $this->userModel->findById($id);
            if (!$user) {
                $this->jsonResponse(['success' => false, 'message' => 'User not found'], 404);
                return;
            }

            // Prevent deletion of current user
            if ($user['id'] == $_SESSION['user']['id']) {
                $this->jsonResponse(['success' => false, 'message' => 'Cannot delete your own account'], 400);
                return;
            }

            if ($this->userModel->delete($id)) {
                $this->jsonResponse(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete user'], 500);
            }
        }
    }

    // ==========================================
    // ORDER MANAGEMENT
    // ==========================================
    public function orders()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        // Use filtered method if available, otherwise fallback
        if (method_exists($this->orderModel, 'getFilteredOrders')) {
            $filters = compact('status', 'search', 'dateFrom', 'dateTo');
            $orders = $this->orderModel->getFilteredOrders($filters, $limit, $offset);
            $totalOrders = $this->orderModel->countFilteredOrders($filters);
        } else {
            // Fallback to original method
            $orders = $this->orderModel->getAllOrdersWithCustomer($limit, $offset, $status);
            $totalOrders = $this->orderModel->count($status);
        }

        $totalPages = ceil($totalOrders / $limit);

        // Calculate statistics from unfiltered data
        $allOrders = $this->orderModel->getAllOrdersWithCustomer();
        $pendingOrders = count(array_filter($allOrders, function ($order) {
            return $order['status'] === 'pending';
        }));

        $processingOrders = count(array_filter($allOrders, function ($order) {
            return $order['status'] === 'processing';
        }));

        $todayOrders = count(array_filter($allOrders, function ($order) {
            return date('Y-m-d', strtotime($order['created_at'])) === date('Y-m-d');
        }));
        $totalRevenue = array_sum(array_column($allOrders, 'total_amount'));
        $completedOrders = count(array_filter($allOrders, function ($order) {
            return in_array($order['status'], ['completed', 'delivered']);
        }));
        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => count($allOrders), // Total unfiltered
            'filteredCount' => $totalOrders, // Filtered count
            'stats' => [
                'total_orders' => count($allOrders),
                'pending_orders' => $pendingOrders,
                'processing_orders' => $processingOrders,
                'completed_orders' => $completedOrders,
                'today_orders' => $todayOrders,
                'total_revenue' => $totalRevenue
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalOrders
            ],
            'selectedStatus' => $status,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('orders/index', $data);
    }

    public function updateOrderStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';
            $validStatuses = ['pending', 'processing', 'preparing', 'ready', 'delivered', 'cancelled'];

            if (!in_array($status, $validStatuses)) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
                return;
            }

            if ($this->orderModel->updateStatus($id, $status)) {
                $this->jsonResponse(['success' => true, 'message' => 'Order status updated successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update order status'], 500);
            }
        }
    }
    public function orderDetails($id)
    {
        $order = $this->orderModel->getOrderWithItems($id);

        if (!$order) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
            return;
        }

        // Capture the HTML output
        ob_start();
        $this->view('superadmin/orders/details_modal', ['order' => $order], false);
        $html = ob_get_clean();

        $this->jsonResponse([
            'success' => true,
            'html' => $html
        ]);
    }

    // ==========================================
    // BOOKING MANAGEMENT
    // ==========================================
    public function bookings()
    {
        $page = (int)($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $bookings = $this->bookingModel->getAllForAdmin($limit, $offset, $status);
        $totalBookings = $this->bookingModel->count($status);
        $totalPages = ceil($totalBookings / $limit);

        // Calculate statistics
        $allBookings = $this->bookingModel->getAllForAdmin();
        $confirmedBookings = count(array_filter($allBookings, function ($booking) {
            return $booking['status'] === 'confirmed';
        }));

        $pendingBookings = count(array_filter($allBookings, function ($booking) {
            return $booking['status'] === 'pending';
        }));

        $todayBookings = count(array_filter($allBookings, function ($booking) {
            return date('Y-m-d', strtotime($booking['reservation_time'])) === date('Y-m-d');
        }));

        $data = [
            'title' => 'Booking Management',
            'bookings' => $bookings,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalBookings' => $totalBookings,
            'confirmedBookings' => $confirmedBookings,
            'pendingBookings' => $pendingBookings,
            'todayBookings' => $todayBookings,
            'selectedStatus' => $status,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('bookings/index', $data);
    }
    public function updateBookingStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';
            $validStatuses = ['pending', 'confirmed', 'cancelled'];

            if (!in_array($status, $validStatuses)) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
                return;
            }

            if ($this->bookingModel->updateStatus($id, $status)) {
                $this->jsonResponse(['success' => true, 'message' => 'Booking status updated successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update booking status'], 500);
            }
        }
    }

    public function bookingDetails($id)
    {
        $booking = $this->bookingModel->getBookingDetails($id);

        if (!$booking) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
            return;
        }        // Capture the HTML output
        ob_start();
        $this->view('superadmin/bookings/details_modal', ['booking' => $booking], false);
        $html = ob_get_clean();

        $this->jsonResponse([
            'success' => true,
            'html' => $html
        ]);
    }
    public function assignTable($bookingId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle table assignment save
            $input = json_decode(file_get_contents('php://input'), true);
            $tableId = $input['table_id'] ?? '';

            if (empty($tableId)) {
                $this->jsonResponse(['success' => false, 'message' => 'Table ID is required'], 400);
                return;
            }

            if ($this->bookingModel->assignTable($bookingId, $tableId)) {
                $this->jsonResponse(['success' => true, 'message' => 'Table assigned successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to assign table'], 500);
            }
        } else {
            // Handle getting available tables (GET request)
            $booking = $this->bookingModel->getBookingDetails($bookingId);
            if (!$booking) {
                $this->jsonResponse(['success' => false, 'message' => 'Booking not found'], 404);
                return;
            }

            // Get available tables that can accommodate the party size
            $availableTables = $this->tableModel->getAvailableTables($booking['number_of_guests']);

            $this->jsonResponse([
                'success' => true,
                'tables' => $availableTables,
                'booking' => $booking
            ]);
        }
    }

    // ==========================================
    // TABLE MANAGEMENT
    // ==========================================
    public function tables()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $location = $_GET['location'] ?? '';

        // Get all tables for filtering
        $allTables = $this->tableModel->getAllTables();

        // Apply filters
        if ($search) {
            $allTables = array_filter($allTables, function ($table) use ($search) {
                return stripos($table['table_number'], $search) !== false ||
                    stripos($table['location'], $search) !== false ||
                    stripos($table['description'], $search) !== false;
            });
        }

        if ($status !== '') {
            $allTables = array_filter($allTables, function ($table) use ($status) {
                return ($status === 'available' && $table['is_available'] == 1) ||
                    ($status === 'unavailable' && $table['is_available'] == 0);
            });
        }

        if ($location) {
            $allTables = array_filter($allTables, function ($table) use ($location) {
                return $table['location'] === $location;
            });
        }

        $totalTables = count($allTables);
        $totalPages = ceil($totalTables / $limit);

        // Apply pagination to filtered data
        $tables = array_slice($allTables, $offset, $limit);

        // Calculate statistics from unfiltered data
        $allTablesForStats = $this->tableModel->getAllTables();
        $availableTables = count(array_filter($allTablesForStats, function ($table) {
            return ($table['is_available'] ?? 1) == 1;
        }));

        $totalCapacity = array_sum(array_column($allTablesForStats, 'capacity'));
        $locationStats = $this->tableModel->getTablesByLocation();
        $data = [
            'title' => 'Table Management',
            'tables' => $tables,
            'stats' => [
                'total_tables' => count($allTablesForStats),
                'available_tables' => $availableTables,
                'occupied_tables' => count($allTablesForStats) - $availableTables,
                'total_capacity' => $totalCapacity
            ],
            'locationStats' => $locationStats,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTables' => count($allTablesForStats),
            'filteredCount' => $totalTables,
            'availableTables' => $availableTables,
            'totalCapacity' => $totalCapacity,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('tables/index', $data);
    }

    public function createTable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if it's an AJAX request - skip CSRF for AJAX
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

            if (!$isAjax && !$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/superadmin/tables');
                return;
            }

            $tableData = [
                'table_number' => $this->sanitize($_POST['table_number']),
                'capacity' => (int)$_POST['capacity'],
                'location' => $this->sanitize($_POST['location'] ?? ''),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'status' => 'available',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Validate required fields
            if (empty($tableData['table_number']) || $tableData['capacity'] < 1) {
                $message = 'Please fill in all required fields correctly.';
                if ($isAjax) {
                    $this->jsonResponse(['success' => false, 'message' => $message], 400);
                    return;
                }
                $this->setFlash('error', $message);
                $this->redirect('/superadmin/tables/create');
                return;
            }

            // Check if table number already exists
            if ($this->tableModel->findByField('table_number', $tableData['table_number'])) {
                $message = 'Table number already exists.';
                if ($isAjax) {
                    $this->jsonResponse(['success' => false, 'message' => $message], 400);
                    return;
                }
                $this->setFlash('error', $message);
                $this->redirect('/superadmin/tables/create');
                return;
            }

            if ($this->tableModel->createTable($tableData)) {
                $message = 'Table created successfully.';
                if ($isAjax) {
                    $this->jsonResponse(['success' => true, 'message' => $message]);
                    return;
                }
                $this->setFlash('success', $message);
            } else {
                $message = 'Failed to create table.';
                if ($isAjax) {
                    $this->jsonResponse(['success' => false, 'message' => $message], 500);
                    return;
                }
                $this->setFlash('error', $message);
            }

            $this->redirect('/superadmin/tables');
            return;
        }

        $data = [
            'title' => 'Create Table',
            'csrf_token' => $this->generateCSRF()
        ];
        $this->loadSuperAdminView('tables/create', $data);
    }
    public function createTableAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tableData = [
                'table_number' => $this->sanitize($_POST['table_number'] ?? ''),
                'capacity' => (int)($_POST['capacity'] ?? 0),
                'location' => $this->sanitize($_POST['location'] ?? ''),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'is_available' => 1, // Default to available
                'status' => 'available'
            ];

            // Validate required fields
            if (empty($tableData['table_number']) || $tableData['capacity'] < 1) {
                $this->jsonResponse(['success' => false, 'message' => 'Please fill in all required fields correctly.'], 400);
                return;
            }

            // Check if table number already exists
            if ($this->tableModel->findByField('table_number', $tableData['table_number'])) {
                $this->jsonResponse(['success' => false, 'message' => 'Table number already exists.'], 400);
                return;
            }

            if ($this->tableModel->createTable($tableData)) {
                $this->jsonResponse(['success' => true, 'message' => 'Table created successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to create table.'], 500);
            }
        }
    }

    public function getTable($id)
    {
        $table = $this->tableModel->findById($id);

        if (!$table) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Table not found'
            ], 404);
            return;
        }

        $this->jsonResponse([
            'success' => true,
            'table' => $table
        ]);
    }

    public function updateTable($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tableData = [
                'table_number' => $this->sanitize($_POST['table_number'] ?? ''),
                'capacity' => (int)($_POST['capacity'] ?? 0),
                'location' => $this->sanitize($_POST['location'] ?? ''),
                'description' => $this->sanitize($_POST['description'] ?? '')
            ];

            // Validate required fields
            if (empty($tableData['table_number']) || $tableData['capacity'] < 1) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Please fill in all required fields correctly.'
                ], 400);
                return;
            }

            // Check if table number already exists (excluding current table)
            $existingTable = $this->tableModel->findByField('table_number', $tableData['table_number']);
            if ($existingTable && $existingTable['id'] != $id) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Table number already exists.'
                ], 400);
                return;
            }

            if ($this->tableModel->updateTable($id, $tableData)) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Table updated successfully.'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to update table.'
                ], 500);
            }
        }
    }

    public function deleteTable($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->tableModel->deleteTable($id)) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Table deleted successfully.'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to delete table. Table may have existing bookings.'
                ], 500);
            }
        }
    }

    // ==========================================
    // RESTAURANT INFO MANAGEMENT
    // ==========================================
    public function restaurantInfo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/superadmin/restaurant');
                return;
            }

            try {
                // Validate required fields
                $requiredFields = ['restaurant_name', 'phone', 'email', 'address'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required.');
                    }
                }

                // Validate email format
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Invalid email format.');
                }

                $restaurantData = [
                    'restaurant_name' => $this->sanitize($_POST['restaurant_name']),
                    'phone' => $this->sanitize($_POST['phone']),
                    'email' => $this->sanitize($_POST['email']),
                    'website' => $this->sanitize($_POST['website'] ?? ''),
                    'address' => $this->sanitize($_POST['address']),
                    'description' => $this->sanitize($_POST['description'] ?? ''),
                    'opening_hours' => $this->sanitize($_POST['opening_hours'] ?? '09:00 - 22:00'),
                    'capacity' => (int)($_POST['capacity'] ?? 100),
                    'facebook' => $this->sanitize($_POST['facebook'] ?? ''),
                    'instagram' => $this->sanitize($_POST['instagram'] ?? ''),
                    'twitter' => $this->sanitize($_POST['twitter'] ?? '')
                ];                // Handle logo upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $logoPath = $this->handleRestaurantImageUpload($_FILES['logo'], 'logo');
                    if ($logoPath) {
                        $restaurantData['logo_url'] = $logoPath; // Sửa tên field khớp với database
                    }
                }

                // Handle cover image upload
                if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                    $coverPath = $this->handleRestaurantImageUpload($_FILES['cover_image'], 'cover');
                    if ($coverPath) {
                        $restaurantData['cover_image'] = $coverPath;
                    }
                }

                // 1. Lấy ảnh cũ từ DB
                $coverImages = [];
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT cover_images FROM restaurant_info WHERE id = 1");
                $stmt->execute();
                $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!empty($restaurantInfo['cover_images'])) {
                    $decoded = json_decode($restaurantInfo['cover_images'], true);
                    if (is_array($decoded)) {
                        $coverImages = $decoded;
                    }
                }

                // 2. Xóa ảnh theo URL nếu có yêu cầu
                if (!empty($_POST['cover_images_to_remove'])) {
                    $imagesToRemove = explode('|', $_POST['cover_images_to_remove']);
                    foreach ($imagesToRemove as $imgUrl) {
                        $key = array_search($imgUrl, $coverImages);
                        if ($key !== false) {
                            // Xóa file vật lý (nếu cần)
                            $path = str_replace(SITE_URL . '/', '', $coverImages[$key]);
                            if (file_exists($path)) {
                                unlink($path);
                            }
                            unset($coverImages[$key]);
                        }
                    }
                    $coverImages = array_values($coverImages); // Re-index lại
                }

                // 3. Upload ảnh mới (nếu có)
                if (!empty($_FILES['cover_images']['name'][0])) {
                    foreach ($_FILES['cover_images']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['cover_images']['error'][$key] === UPLOAD_ERR_OK) {
                            $coverPath = $this->handleRestaurantImageUpload([
                                'name' => $_FILES['cover_images']['name'][$key],
                                'type' => $_FILES['cover_images']['type'][$key],
                                'tmp_name' => $tmp_name,
                                'error' => $_FILES['cover_images']['error'][$key],
                                'size' => $_FILES['cover_images']['size'][$key]
                            ], 'cover');

                            if ($coverPath) {
                                $coverImages[] = $coverPath;
                            }
                        }
                    }
                }

                // 4. Lưu lại vào DB
                $restaurantData['cover_images'] = json_encode($coverImages);


                // Update or create restaurant info
                $db = Database::getInstance()->getConnection();

                // Check if table exists, create if not
                $this->ensureRestaurantInfoTable($db);

                $stmt = $db->prepare("SELECT COUNT(*) FROM restaurant_info WHERE id = 1");
                $stmt->execute();
                $exists = $stmt->fetchColumn() > 0;

                if ($exists) {
                    // Build dynamic UPDATE query
                    $setParts = [];
                    $values = [];
                    foreach ($restaurantData as $key => $value) {
                        $setParts[] = "$key = ?";
                        $values[] = $value;
                    }
                    $setParts[] = "updated_at = NOW()";

                    $sql = "UPDATE restaurant_info SET " . implode(', ', $setParts) . " WHERE id = 1";
                    $stmt = $db->prepare($sql);
                } else {
                    // Build dynamic INSERT query
                    $columns = array_keys($restaurantData);
                    $columns[] = 'id';
                    $placeholders = array_fill(0, count($restaurantData), '?');
                    $placeholders[] = '1';

                    $sql = "INSERT INTO restaurant_info (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $stmt = $db->prepare($sql);
                    $values = array_values($restaurantData);
                }

                if ($stmt->execute($values)) {
                    $this->setFlash('success', 'Restaurant information updated successfully.');
                } else {
                    throw new Exception('Failed to update restaurant information.');
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
            }

            $this->redirect('/superadmin/restaurant');
            return;
        }

        // Get current restaurant info
        $db = Database::getInstance()->getConnection();
        $this->ensureRestaurantInfoTable($db);

        $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
        $stmt->execute();
        $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$restaurantInfo) {
            $restaurantInfo = [
                'restaurant_name' => 'Buffet Restaurant',
                'phone' => '',
                'email' => '',
                'website' => '',
                'address' => '',
                'description' => '',
                'opening_hours' => '09:00 - 22:00',
                'capacity' => 100,
                'logo_url' => '', 
                'cover_images' => '',
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } // Get statistics for restaurant info page
        $stats = [
            'total_orders' => $this->orderModel->count(),
            'total_customers' => $this->userModel->count('role', 'customer'),
            'total_tables' => $this->tableModel->count(),
            'total_revenue' => $this->orderModel->getTotalRevenue() ?? 0
        ];

        $data = [
            'title' => 'Restaurant Information',
            'info' => $restaurantInfo,
            'stats' => $stats,
            'csrf_token' => $this->generateCSRF()
        ];
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';


        $this->loadSuperAdminView('restaurant', $data);

        

    }


    private function ensureRestaurantInfoTable($db)
    {
        try {
            $stmt = $db->query("SELECT 1 FROM restaurant_info LIMIT 1");
        } catch (Exception $e) {
            // Table doesn't exist, create it
            $sql = "CREATE TABLE restaurant_info (
                id INT PRIMARY KEY DEFAULT 1,
                restaurant_name VARCHAR(255) NOT NULL DEFAULT 'Buffet Restaurant',
                phone VARCHAR(20) DEFAULT '',
                email VARCHAR(255) DEFAULT '',
                website VARCHAR(255) DEFAULT '',
                address TEXT DEFAULT '',
                description TEXT DEFAULT '',
                opening_hours VARCHAR(100) DEFAULT '09:00 - 22:00',
                capacity INT DEFAULT 100,
                logo VARCHAR(255) DEFAULT '',
                cover_images VARCHAR(255) DEFAULT '',
                facebook VARCHAR(255) DEFAULT '',
                instagram VARCHAR(255) DEFAULT '',
                twitter VARCHAR(255) DEFAULT '',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $db->exec($sql);
        }
    }

    private function handleRestaurantImageUpload($file, $type)
    {
        $uploadDir = "uploads/restaurant/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type for $type. Only JPG, PNG, and GIF are allowed.");
        }

        $maxSize = ($type === 'logo') ? 2 * 1024 * 1024 : 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $maxSizeMB = $maxSize / (1024 * 1024);
            throw new Exception("File size for $type must be less than {$maxSizeMB}MB.");
        }

        // ✅ Sửa đoạn này: đặt tên duy nhất
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $type . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return SITE_URL . '/' . $filepath;
        }

        throw new Exception("Failed to upload $type image.");
    }


    public function updateRestaurantInfo()
    {
        // This method is called by the routing for POST requests to /superadmin/restaurant/update
        $this->restaurantInfo();
    }

    // ==========================================
    // PROMOTION MANAGEMENT
    // ==========================================

    public function promotions()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $filters = [
            'status' => $_GET['status'] ?? '',
            'type' => $_GET['type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Get promotions using the model
        $promotions = $this->promotionModel->getAllPromotions($limit, $offset, $filters);
        $totalPromotions = $this->promotionModel->count();
        $totalPages = ceil($totalPromotions / $limit);

        // Get statistics
        $stats = $this->promotionModel->getStats();
        $data = [
            'title' => 'Promotion Management',
            'promotions' => $promotions,
            'stats' => $stats,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPromotions' => $totalPromotions,
            'food_items' => $this->promotionModel->getAllFoodItems(),
            'categories' => $this->promotionModel->getAllCategories(),
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('promotions', $data);
    }

    public function createPromotion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
                return;
            }
            $promotionData = [
                'name' => $this->sanitize($_POST['name']),
                'code' => strtoupper($this->sanitize($_POST['code'])),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'type' => $this->sanitize($_POST['type']),
                'application_type' => $this->sanitize($_POST['application_type'] ?? 'all'),
                'discount_value' => (float)$_POST['discount_value'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
                'minimum_amount' => !empty($_POST['minimum_amount']) ? (float)$_POST['minimum_amount'] : null,
                'food_items' => $_POST['food_items'] ?? [],
                'categories' => $_POST['categories'] ?? []
            ];

            // Validate required fields
            if (
                empty($promotionData['name']) || empty($promotionData['code']) ||
                empty($promotionData['type']) || $promotionData['discount_value'] <= 0
            ) {
                $this->jsonResponse(['success' => false, 'message' => 'Please fill in all required fields correctly.'], 400);
                return;
            }

            // Check if code already exists
            if ($this->promotionModel->codeExists($promotionData['code'])) {
                $this->jsonResponse(['success' => false, 'message' => 'Promotion code already exists.'], 400);
                return;
            }

            if ($this->promotionModel->createPromotion($promotionData)) {
                $this->jsonResponse(['success' => true, 'message' => 'Promotion created successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to create promotion.'], 500);
            }
        }
    }
    public function getPromotion($id)
    {
        try {
            error_log("getPromotion called with ID: " . $id);

            // First, ensure the required tables exist
            $this->ensurePromotionTables();

            $promotion = $this->promotionModel->findById($id);
            error_log("Promotion found: " . ($promotion ? 'YES' : 'NO'));

            if ($promotion) {
                try {
                    // Try to get related data, with fallback if tables don't exist
                    $promotion['food_items'] = $this->promotionModel->getFoodItemIds($id);
                } catch (Exception $e) {
                    error_log("Warning: Could not get food items - " . $e->getMessage());
                    $promotion['food_items'] = [];
                }

                try {
                    $promotion['categories'] = $this->promotionModel->getCategoryIds($id);
                } catch (Exception $e) {
                    error_log("Warning: Could not get categories - " . $e->getMessage());
                    $promotion['categories'] = [];
                }

                error_log("Food items count: " . count($promotion['food_items']));
                error_log("Categories count: " . count($promotion['categories']));

                $this->jsonResponse(['success' => true, 'promotion' => $promotion]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Promotion not found.'], 404);
            }
        } catch (Exception $e) {
            error_log("Error in getPromotion: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Ensure promotion relationship tables exist
     */
    private function ensurePromotionTables()
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Check if promotion_food_items table exists
            $stmt = $db->query("SHOW TABLES LIKE 'promotion_food_items'");
            if ($stmt->rowCount() == 0) {
                $sql = "CREATE TABLE promotion_food_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    promotion_id INT NOT NULL,
                    food_item_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_promotion_id (promotion_id),
                    INDEX idx_food_item_id (food_item_id)
                )";
                $db->exec($sql);
                error_log("Created promotion_food_items table");
            }

            // Check if promotion_categories table exists
            $stmt = $db->query("SHOW TABLES LIKE 'promotion_categories'");
            if ($stmt->rowCount() == 0) {
                $sql = "CREATE TABLE promotion_categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    promotion_id INT NOT NULL,
                    category_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_promotion_id (promotion_id),
                    INDEX idx_category_id (category_id)
                )";
                $db->exec($sql);
                error_log("Created promotion_categories table");
            }
        } catch (Exception $e) {
            error_log("Error creating promotion tables: " . $e->getMessage());
        }
    }
    public function editPromotion($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!$this->validateCSRF()) {
                    $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
                    return;
                }

                // Ensure tables exist before processing
                $this->ensurePromotionTables();

                error_log("editPromotion called for ID: $id with data: " . print_r($_POST, true));

                $promotionData = [
                    'name' => $this->sanitize($_POST['name']),
                    'code' => strtoupper($this->sanitize($_POST['code'])),
                    'description' => $this->sanitize($_POST['description'] ?? ''),
                    'type' => $this->sanitize($_POST['type']),
                    'application_type' => $this->sanitize($_POST['application_type'] ?? 'all'),
                    'discount_value' => (float)$_POST['discount_value'],
                    'start_date' => $_POST['start_date'],
                    'end_date' => $_POST['end_date'],
                    'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
                    'minimum_amount' => !empty($_POST['minimum_amount']) ? (float)$_POST['minimum_amount'] : null,
                    'food_items' => $_POST['food_items'] ?? [],
                    'categories' => $_POST['categories'] ?? []
                ];

                error_log("Promotion data: " . print_r($promotionData, true));

                // Validate required fields
                if (
                    empty($promotionData['name']) || empty($promotionData['code']) ||
                    empty($promotionData['type']) || $promotionData['discount_value'] <= 0
                ) {
                    $this->jsonResponse(['success' => false, 'message' => 'Please fill in all required fields correctly.'], 400);
                    return;
                }

                // Check if code already exists for other promotions
                if ($this->promotionModel->codeExists($promotionData['code'], $id)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Promotion code already exists.'], 400);
                    return;
                }

                if ($this->promotionModel->updatePromotion($id, $promotionData)) {
                    $this->jsonResponse(['success' => true, 'message' => 'Promotion updated successfully.']);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Failed to update promotion.'], 500);
                }
            } catch (Exception $e) {
                error_log("Error in editPromotion: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                $this->jsonResponse(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()], 500);
            }
        } else {
            // GET request - return promotion data for editing (for backward compatibility)
            $promotion = $this->promotionModel->findById($id);

            if ($promotion) {
                $this->jsonResponse(['success' => true, 'promotion' => $promotion]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Promotion not found.'], 404);
            }
        }
    }

    public function deletePromotion($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
                return;
            }

            if ($this->promotionModel->deletePromotion($id)) {
                $this->jsonResponse(['success' => true, 'message' => 'Promotion deleted successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete promotion.'], 500);
            }
        }
    }

    public function togglePromotionStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newStatus = $this->promotionModel->toggleStatus($id);

            if ($newStatus !== false) {
                $this->jsonResponse(['success' => true, 'is_active' => $newStatus]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update status.'], 500);
            }
        }
    }

    // ==========================================
    // STATISTICS & REPORTS
    // ==========================================

    public function statistics()
    {
        // Get comprehensive statistics
        $stats = $this->getDashboardStats();        // Additional statistics for super admin
        $stats['monthly_revenue'] = $this->orderModel->getMonthlyRevenue();
        $stats['popular_foods'] = []; // Placeholder for popular foods
        // Get user growth stats manually
        $allUsers = $this->userModel->findAll();
        $currentMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        $currentMonthUsers = count(array_filter($allUsers, function ($user) use ($currentMonth) {
            return strpos($user['created_at'], $currentMonth) === 0;
        }));

        $lastMonthUsers = count(array_filter($allUsers, function ($user) use ($lastMonth) {
            return strpos($user['created_at'], $lastMonth) === 0;
        }));

        $stats['user_growth'] = [
            'current_month' => $currentMonthUsers,
            'last_month' => $lastMonthUsers,
            'growth_rate' => $lastMonthUsers > 0 ? (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 0
        ];
        // Get booking trends manually
        $allBookings = $this->bookingModel->findAll();
        $currentMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        $currentMonthBookings = count(array_filter($allBookings, function ($booking) use ($currentMonth) {
            return strpos($booking['created_at'], $currentMonth) === 0;
        }));

        $lastMonthBookings = count(array_filter($allBookings, function ($booking) use ($lastMonth) {
            return strpos($booking['created_at'], $lastMonth) === 0;
        }));

        $stats['booking_trends'] = [
            'current_month' => $currentMonthBookings,
            'last_month' => $lastMonthBookings,
            'growth_rate' => $lastMonthBookings > 0 ? (($currentMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100 : 0
        ];

        $data = [
            'title' => 'Advanced Statistics',
            'stats' => $stats
        ];

        $this->loadSuperAdminView('statistics', $data);
    }

    private function getDashboardStats()
    {
        // Get basic counts
        $totalOrders = $this->orderModel->count();
        $totalUsers = $this->userModel->count();
        $totalBookings = $this->bookingModel->count();
        $totalTables = $this->tableModel->count();

        // Get revenue data
        $orderStats = $this->orderModel->getOrderStats();
        $monthlyRevenue = $this->orderModel->getMonthlyRevenue();

        // Get recent data
        $recentOrders = $this->orderModel->getRecentOrdersWithCustomer(5);
        $recentBookings = $this->bookingModel->getRecentBookingsWithCustomer(5);

        // Get recent users by using findAll and limiting results
        $allUsers = $this->userModel->findAll();
        $recentUsers = array_slice(array_reverse($allUsers), 0, 5);

        return [
            'total_users' => $totalUsers,
            'total_foods' => $this->foodModel->count(),
            'total_orders' => $totalOrders,
            'total_bookings' => $totalBookings,
            'total_tables' => $totalTables,
            'total_revenue' => $orderStats['total_revenue'] ?? 0,
            'today_revenue' => $orderStats['today_revenue'] ?? 0,
            'pending_orders' => $this->orderModel->count('pending'),
            'pending_bookings' => $this->bookingModel->count('pending'),
            'active_users' => count(array_filter($this->userModel->findAll(), function ($user) {
                return ($user['is_active'] ?? 1) == 1;
            })),
            'monthly_revenue' => $monthlyRevenue,
            'recent_orders' => $recentOrders,
            'recent_bookings' => $recentBookings,
            'recent_users' => $recentUsers
        ];
    }

    // ==================== REVIEW MANAGEMENT ====================

    /**
     * Display reviews management page
     */
    public function reviews()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'status' => $_GET['status'] ?? '',
            'rating' => $_GET['rating'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $reviews = $this->reviewModel->getAllReviews($limit, $offset, $filters);
        $totalReviews = $this->reviewModel->countReviews($filters);
        $totalPages = ceil($totalReviews / $limit);

        $data = [
            'title' => 'Reviews Management',
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters,
            'stats' => $this->reviewModel->getReviewStats()
        ];

        $this->loadSuperAdminView('reviews/index', $data);
    }
    /**
     * View review details
     */
    public function reviewDetails($id)
    {
        $reviewData = $this->reviewModel->getReviewDetails($id);

        if (!$reviewData) {
            http_response_code(404);
            echo '<div class="alert alert-danger">Review not found</div>';
            return;
        }        // Load the details view
        $this->loadSuperAdminView('reviews/details', ['reviewData' => $reviewData]);
    }
    /**
     * Approve review
     */
    public function approveReview($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->reviewModel->approveReview($id);

            $this->jsonResponse([
                'success' => $result,
                'message' => $result ? 'Review approved successfully' : 'Failed to approve review'
            ]);
        }
    }
    /**
     * Reject/Unapprove review
     */
    public function rejectReview($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->reviewModel->rejectReview($id);

            $this->jsonResponse([
                'success' => $result,
                'message' => $result ? 'Review rejected successfully' : 'Failed to reject review'
            ]);
        }
    }
    /**
     * Verify review
     */
    public function verifyReview($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->reviewModel->verifyReview($id);

            $this->jsonResponse([
                'success' => $result,
                'message' => $result ? 'Review verified successfully' : 'Failed to verify review'
            ]);
        }
    }

    /**
     * Delete review
     */
    public function deleteReview($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->reviewModel->deleteReview($id);

            $this->jsonResponse([
                'success' => $result,
                'message' => $result ? 'Review deleted successfully' : 'Failed to delete review'
            ]);
        }
    }

    /**
     * Bulk actions for reviews
     */
    public function reviewsBulkAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $reviewIds = $_POST['review_ids'] ?? [];

            if (empty($reviewIds)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'No reviews selected'
                ]);
                return;
            }

            $result = false;
            $message = '';
            switch ($action) {
                case 'approve':
                    $result = $this->reviewModel->bulkApproveReviews($reviewIds);
                    $message = $result ? 'Reviews approved successfully' : 'Failed to approve reviews';
                    break;
                case 'delete':
                    $result = $this->reviewModel->bulkDeleteReviews($reviewIds);
                    $message = $result ? 'Reviews deleted successfully' : 'Failed to delete reviews';
                    break;
                default:
                    $message = 'Invalid action';
            }

            $this->jsonResponse([
                'success' => $result,
                'message' => $message
            ]);
        }
    }
    /**
     * Get review statistics for AJAX
     */
    public function reviewStats()
    {
        header('Content-Type: application/json');
        $stats = $this->reviewModel->getReviewStats();
        echo json_encode($stats);
        exit;
    }

    // ==========================================
    // NOTIFICATION MANAGEMENT
    // ==========================================

    /**
     * Display notifications page
     */
    public function notifications()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $type = $_GET['type'] ?? '';
        $unreadOnly = isset($_GET['unread']) && $_GET['unread'] == '1';

        $userId = $_SESSION['user']['id'];

        // Get notifications
        if ($type) {
            $notifications = $this->notificationModel->getNotificationsByType($userId, $type, $limit, $offset);
            $totalNotifications = count($this->notificationModel->getNotificationsByType($userId, $type, 1000)); // Quick count
        } else {
            $notifications = $this->notificationModel->getUserNotifications($userId, $limit, $offset, $unreadOnly);
            $totalNotifications = $this->notificationModel->countUserNotifications($userId, $unreadOnly);
        }

        $totalPages = ceil($totalNotifications / $limit);

        // Get statistics
        $stats = $this->notificationModel->getNotificationStats($userId);

        $data = [
            'title' => 'Notifications',
            'notifications' => $notifications,
            'stats' => $stats,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'currentType' => $type,
            'unreadOnly' => $unreadOnly,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('notifications/index', $data);
    }

    /**
     * Get unread notification count for AJAX
     */
    public function getUnreadCount()
    {
        header('Content-Type: application/json');
        $userId = $_SESSION['user']['id'];
        $count = $this->notificationModel->getUnreadCount($userId);
        echo json_encode(['unread_count' => $count]);
        exit;
    }

    /**
     * Get recent notifications for AJAX
     */
    public function getRecentNotifications()
    {
        header('Content-Type: application/json');
        $userId = $_SESSION['user']['id'];
        $notifications = $this->notificationModel->getRecentNotifications($userId, 10);

        // Format notifications for display
        $formattedNotifications = array_map(function ($notification) {
            return [
                'id' => $notification['id'],
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'data' => json_decode($notification['data'], true),
                'is_read' => $notification['is_read'],
                'created_at' => $notification['created_at'],
                'time_ago' => $this->timeAgo($notification['created_at'])
            ];
        }, $notifications);

        echo json_encode([
            'success' => true,
            'notifications' => $formattedNotifications
        ]);
        exit;
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $result = $this->notificationModel->markAsRead($id, $userId);

            $this->jsonResponse([
                'success' => $result,
                'message' => $result ? 'Notification marked as read' : 'Failed to mark notification as read'
            ]);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $result = $this->notificationModel->markAllAsRead($userId);

            $this->jsonResponse([
                'success' => $result,
                'message' => $result ? 'All notifications marked as read' : 'Failed to mark notifications as read'
            ]);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $userId = $_SESSION['user']['id'];
            $result = $this->notificationModel->deleteNotification($id, $userId);

            $this->jsonResponse([
                'success' => $result,
                'message' => $result ? 'Notification deleted successfully' : 'Failed to delete notification'
            ]);
        }
    }

    /**
     * Handle bulk notification actions
     */
    public function notificationsBulkAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $notificationIds = $_POST['notification_ids'] ?? [];
            $userId = $_SESSION['user']['id'];

            if (empty($notificationIds)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'No notifications selected'
                ]);
                return;
            }

            $result = false;
            $message = '';

            switch ($action) {
                case 'mark_read':
                    $result = true;
                    foreach ($notificationIds as $id) {
                        if (!$this->notificationModel->markAsRead($id, $userId)) {
                            $result = false;
                            break;
                        }
                    }
                    $message = $result ? 'Selected notifications marked as read' : 'Failed to mark some notifications as read';
                    break;

                case 'delete':
                    $result = $this->notificationModel->bulkDelete($notificationIds, $userId);
                    $message = $result ? 'Selected notifications deleted' : 'Failed to delete notifications';
                    break;

                default:
                    $message = 'Invalid action';
            }

            $this->jsonResponse([
                'success' => $result,
                'message' => $message
            ]);
        }
    }

    /**
     * Helper method to format time ago
     */
    private function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time / 60) . ' minutes ago';
        if ($time < 86400) return floor($time / 3600) . ' hours ago';
        if ($time < 2592000) return floor($time / 86400) . ' days ago';

        return date('M j, Y', strtotime($datetime));
    }

    // ==========================================
    // INTERNAL MESSAGES
    // ==========================================

    /**
     * Hiển thị trang gửi thông báo nội bộ
     */
    public function sendInternalMessage()
    {
        $recipients = $this->internalMessageModel->getAvailableRecipients();

        $data = [
            'title' => 'Gửi Thông Báo Nội Bộ',
            'recipients' => $recipients,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('internal_messages/send', $data);
    }

    /**
     * Xử lý gửi thông báo nội bộ
     */
    public function processInternalMessage()
    {
        // Check session structure and output for debugging
        error_log("FULL SESSION DATA: " . print_r($_SESSION, true));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/superadmin/internal-messages/send');
        }

        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Token không hợp lệ');
            $this->redirect('/superadmin/internal-messages/send');
        }

        // Log to help debug
        error_log("Processing internal message - POST data: " . print_r($_POST, true));

        // Validate input
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $messageType = $_POST['message_type'] ?? 'general';
        $priority = $_POST['priority'] ?? 'normal';
        $recipients = $_POST['recipients'] ?? [];
        $isBroadcast = isset($_POST['is_broadcast']) ? 1 : 0;

        if (empty($title) || empty($content)) {
            $this->setFlash('error', 'Tiêu đề và nội dung không được để trống');
            $this->redirect('/superadmin/internal-messages/send');
        }

        if (!$isBroadcast && empty($recipients)) {
            $this->setFlash('error', 'Vui lòng chọn ít nhất một người nhận');
            $this->redirect('/superadmin/internal-messages/send');
        }

        // Xử lý file đính kèm
        $attachmentPath = null;
        $attachmentName = null;

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleFileUpload($_FILES['attachment']);
            if ($uploadResult['success']) {
                $attachmentPath = $uploadResult['path'];
                $attachmentName = $uploadResult['name'];
            } else {
                $this->setFlash('error', 'Lỗi upload file: ' . $uploadResult['message']);
                $this->redirect('/superadmin/internal-messages/send');
            }
        }

        // Nếu là broadcast, lấy tất cả admin
        if ($isBroadcast) {
            $allRecipients = $this->internalMessageModel->getAvailableRecipients();
            $recipients = array_column($allRecipients, 'id');
        }

        // Tạo thông báo
        $messageData = [
            'sender_id' => $_SESSION['user']['id'], // Fix: Using correct session structure
            'title' => $title,
            'content' => $content,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'message_type' => $messageType,
            'priority' => $priority,
            'is_broadcast' => $isBroadcast,
            'recipients' => $recipients
        ];

        // Additional logging
        error_log("Message data before sending: " . print_r($messageData, true));
        error_log("User session data: " . print_r($_SESSION, true));

        $messageId = $this->internalMessageModel->createMessage($messageData);

        if ($messageId) {
            $this->setFlash('success', 'Thông báo đã được gửi thành công!');
            $this->redirect('/superadmin/internal-messages/sent');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi gửi thông báo');
            $this->redirect('/superadmin/internal-messages/send');
        }
    }

    /**
     * Hiển thị danh sách thông báo đã gửi
     */
    public function sentInternalMessages()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $messages = $this->internalMessageModel->getSentMessages($_SESSION['user_id'], $limit, $offset);
        $stats = $this->internalMessageModel->getMessageStats($_SESSION['user_id']);

        $data = [
            'title' => 'Thông Báo Đã Gửi',
            'messages' => $messages,
            'stats' => $stats,
            'page' => $page,
            'limit' => $limit,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('internal_messages/sent', $data);
    }

    /**
     * Xem chi tiết thông báo
     */
    public function viewInternalMessage($messageId)
    {
        $message = $this->internalMessageModel->getMessageDetail($messageId, $_SESSION['user_id']);

        if (!$message) {
            $this->setFlash('error', 'Thông báo không tồn tại');
            $this->redirect('/superadmin/internal-messages/sent');
        }

        // Lấy danh sách người nhận
        $recipients = $this->internalMessageModel->getMessageRecipients($messageId);

        $data = [
            'title' => 'Chi Tiết Thông Báo',
            'message' => $message,
            'recipients' => $recipients
        ];

        $this->loadSuperAdminView('internal_messages/view', $data);
    }

    /**
     * Xóa thông báo
     */
    public function deleteInternalMessage($messageId)
    {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlash('error', 'Phương thức không hợp lệ');
            $this->redirect('/superadmin/internal-messages/sent');
        }

        // Validate message ID
        if (!$messageId || !is_numeric($messageId)) {
            $this->setFlash('error', 'ID thông báo không hợp lệ');
            $this->redirect('/superadmin/internal-messages/sent');
        }

        // Validate CSRF token
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Token bảo mật không hợp lệ');
            $this->redirect('/superadmin/internal-messages/sent');
        }

        // Get current user ID
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            error_log("No user session found for message deletion");
            $this->setFlash('error', 'Phiên làm việc không hợp lệ');
            $this->redirect('/superadmin/internal-messages/sent');
        }

        $userId = $_SESSION['user']['id'];
        error_log("Attempting to delete message {$messageId} by user {$userId}");

        try {
            $success = $this->internalMessageModel->deleteMessage($messageId, $userId);

            if ($success) {
                $this->setFlash('success', 'Thông báo đã được xóa thành công');
            } else {
                error_log("Failed to delete message {$messageId}");
                $this->setFlash('error', 'Không thể xóa thông báo này. Vui lòng kiểm tra quyền hạn hoặc tồn tại của thông báo.');
            }
        } catch (Exception $e) {
            error_log("Error deleting message {$messageId}: " . $e->getMessage());
            $this->setFlash('error', 'Đã xảy ra lỗi khi xóa thông báo. Vui lòng thử lại sau.');
        }

        $this->redirect('/superadmin/internal-messages/sent');
    }

    /**
     * Xử lý upload file
     */
    private function handleFileUpload($file)
    {
        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Kiểm tra kích thước
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
        }

        // Kiểm tra loại file
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            return ['success' => false, 'message' => 'Loại file không được hỗ trợ'];
        }

        // Tạo thư mục upload nếu chưa có
        $uploadDir = 'uploads/internal_messages/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Tạo tên file unique
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => $filepath,
                'name' => $file['name']
            ];
        } else {
            return ['success' => false, 'message' => 'Lỗi upload file'];
        }
    }
}
