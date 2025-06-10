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

class SuperAdminController extends BaseController
{
    private $userModel;
    private $foodModel;
    private $categoryModel;
    private $orderModel;
    private $bookingModel;
    private $tableModel;
    private $promotionModel;

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
            $allUsers = array_filter($allUsers, function($user) use ($search) {
                return stripos($user['first_name'] . ' ' . $user['last_name'], $search) !== false ||
                       stripos($user['email'], $search) !== false ||
                       stripos($user['phone'], $search) !== false;
            });
        }

        if ($role) {
            $allUsers = array_filter($allUsers, function($user) use ($role) {
                return $user['role'] === $role;
            });
        }

        if ($status !== '') {
            $allUsers = array_filter($allUsers, function($user) use ($status) {
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
        }        $data = [
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
            }            $userData = [
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
            if (empty($userData['first_name']) || empty($userData['last_name']) ||
                empty($userData['email']) || empty($_POST['password'])) {
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
    }    public function editUser($id)
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
            }// Check if email already exists for other users
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
        $status = $_GET['status'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $orders = $this->orderModel->getAllOrdersWithCustomer($limit, $offset, $status);
        $totalOrders = $this->orderModel->count($status);
        $totalPages = ceil($totalOrders / $limit);

        // Calculate statistics
        $allOrders = $this->orderModel->getAllOrdersWithCustomer();
        $pendingOrders = count(array_filter($allOrders, function($order) {
            return $order['status'] === 'pending';
        }));

        $processingOrders = count(array_filter($allOrders, function($order) {
            return $order['status'] === 'processing';
        }));

        $todayOrders = count(array_filter($allOrders, function($order) {
            return date('Y-m-d', strtotime($order['created_at'])) === date('Y-m-d');
        }));       $totalRevenue = array_sum(array_column($allOrders, 'total_amount'));
        $completedOrders = count(array_filter($allOrders, function($order) {
            return in_array($order['status'], ['completed', 'delivered']);
        }));

        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'stats' => [
                'total_orders' => $totalOrders,
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
    }    public function orderDetails($id)
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
        $confirmedBookings = count(array_filter($allBookings, function($booking) {
            return $booking['status'] === 'confirmed';
        }));

        $pendingBookings = count(array_filter($allBookings, function($booking) {
            return $booking['status'] === 'pending';
        }));

        $todayBookings = count(array_filter($allBookings, function($booking) {
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
    }    public function updateBookingStatus($id)
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
    }    public function assignTable($bookingId)
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
            ]);        }
    }

    // ==========================================
    // TABLE MANAGEMENT
    // ==========================================

    public function tables()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $tables = $this->tableModel->getAllTables($limit, $offset);
        $totalTables = $this->tableModel->count();
        $totalPages = ceil($totalTables / $limit);        // Calculate statistics
        $allTables = $this->tableModel->getAllTables();
        $availableTables = count(array_filter($allTables, function($table) {
            return ($table['is_available'] ?? 1) == 1;
        }));

        $totalCapacity = array_sum(array_column($allTables, 'capacity'));

        $data = [
            'title' => 'Table Management',
            'tables' => $tables,
            'stats' => [
                'total_tables' => $totalTables,
                'available_tables' => $availableTables,
                'occupied_tables' => $totalTables - $availableTables,
                'total_capacity' => $totalCapacity
            ],
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTables' => $totalTables,
            'availableTables' => $availableTables,
            'totalCapacity' => $totalCapacity,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('tables/index', $data);
    }    public function createTable()
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
        ];        $this->loadSuperAdminView('tables/create', $data);
    }    public function createTableAjax()
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
                ];

                // Handle logo upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $logoPath = $this->handleRestaurantImageUpload($_FILES['logo'], 'logo');
                    if ($logoPath) {
                        $restaurantData['logo'] = $logoPath;
                    }
                }

                // Handle cover image upload
                if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                    $coverPath = $this->handleRestaurantImageUpload($_FILES['cover_image'], 'cover');
                    if ($coverPath) {
                        $restaurantData['cover_image'] = $coverPath;
                    }
                }

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
                'logo' => '',
                'cover_image' => '',
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }        // Get statistics for restaurant info page
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
                cover_image VARCHAR(255) DEFAULT '',
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

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type for $type. Only JPG, PNG, and GIF are allowed.");
        }

        // Validate file size (2MB for logo, 5MB for cover)
        $maxSize = ($type === 'logo') ? 2 * 1024 * 1024 : 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $maxSizeMB = $maxSize / (1024 * 1024);
            throw new Exception("File size for $type must be less than {$maxSizeMB}MB.");
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $type . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
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
                'discount_value' => (float)$_POST['discount_value'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
                'minimum_amount' => !empty($_POST['minimum_amount']) ? (float)$_POST['minimum_amount'] : null
            ];

            // Validate required fields
            if (empty($promotionData['name']) || empty($promotionData['code']) ||
                empty($promotionData['type']) || $promotionData['discount_value'] <= 0) {
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

    public function editPromotion($id)
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
                'discount_value' => (float)$_POST['discount_value'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
                'minimum_amount' => !empty($_POST['minimum_amount']) ? (float)$_POST['minimum_amount'] : null
            ];

            // Validate required fields
            if (empty($promotionData['name']) || empty($promotionData['code']) ||
                empty($promotionData['type']) || $promotionData['discount_value'] <= 0) {
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
        } else {
            // GET request - return promotion data for editing
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

        $currentMonthUsers = count(array_filter($allUsers, function($user) use ($currentMonth) {
            return strpos($user['created_at'], $currentMonth) === 0;
        }));

        $lastMonthUsers = count(array_filter($allUsers, function($user) use ($lastMonth) {
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

        $currentMonthBookings = count(array_filter($allBookings, function($booking) use ($currentMonth) {
            return strpos($booking['created_at'], $currentMonth) === 0;
        }));

        $lastMonthBookings = count(array_filter($allBookings, function($booking) use ($lastMonth) {
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
        $orderStats = $this->orderModel->getDashboardStats();
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
            'active_users' => count(array_filter($this->userModel->findAll(), function($user) {
                return ($user['is_active'] ?? 1) == 1;
            })),
            'monthly_revenue' => $monthlyRevenue,
            'recent_orders' => $recentOrders,
            'recent_bookings' => $recentBookings,
            'recent_users' => $recentUsers
        ];
    }
}
?>
