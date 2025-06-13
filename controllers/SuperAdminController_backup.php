<?php
/**
 * SuperAdmin Controller
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
    // USER MANAGEMENT (Super Admin specific)
    // ==========================================

    public function users()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';

        // Get all users with filters
        $allUsers = $this->userModel->getAllForAdmin();

        // Apply filters
        if ($search) {
            $allUsers = array_filter($allUsers, function($user) use ($search) {
                return stripos($user['full_name'] ?? $user['name'], $search) !== false ||
                       stripos($user['email'], $search) !== false ||
                       stripos($user['phone_number'] ?? '', $search) !== false;
            });
        }

        if ($role) {
            $allUsers = array_filter($allUsers, function($user) use ($role) {
                return $user['role'] === $role;
            });
        }

        $totalUsers = count($allUsers);
        $totalPages = ceil($totalUsers / $limit);
        $users = array_slice($allUsers, $offset, $limit);

        // Calculate statistics
        $activeUsers = count(array_filter($allUsers, function($user) {
            return $user['is_active'] == 1;
        }));

        $adminUsers = count(array_filter($allUsers, function($user) {
            return in_array($user['role'], ['manager', 'super_admin']);
        }));

        $customerUsers = count(array_filter($allUsers, function($user) {
            return $user['role'] === 'customer';
        }));

        $newToday = count(array_filter($allUsers, function($user) {
            return date('Y-m-d', strtotime($user['created_at'])) === date('Y-m-d');
        }));

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalUsers,
                'items_per_page' => $limit
            ],
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'adminUsers' => $adminUsers,
            'customerUsers' => $customerUsers,
            'newToday' => $newToday,
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
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'address' => $this->sanitize($_POST['address'] ?? ''),
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'role' => $this->sanitize($_POST['role']),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Validate required fields
            if (empty($userData['first_name']) || empty($userData['email']) || empty($_POST['password'])) {
                $this->setFlash('error', 'Please fill in all required fields.');
                $this->redirect('/superadmin/users/create');
                return;
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($userData['email'])) {
                $this->setFlash('error', 'Email address already exists.');
                $this->redirect('/superadmin/users/create');
                return;
            }

            if ($this->userModel->create($userData)) {
                $this->setFlash('success', 'User created successfully.');
            } else {
                $this->setFlash('error', 'Failed to create user.');
            }

            $this->redirect('/superadmin/users');
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
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'role' => $_POST['role'] ?? 'customer',
                'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle password update
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $this->setFlash('error', 'Password must be at least 6 characters long.');
                    $this->redirect('/superadmin/users/edit/' . $id);
                    return;
                }
                $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            if ($this->userModel->update($id, $userData)) {
                $this->setFlash('success', 'User updated successfully.');
            } else {
                $this->setFlash('error', 'Failed to update user.');
            }

            $this->redirect('/superadmin/users');
            return;
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/superadmin/users');
            return;
        }

        // Get user statistics
        $user['total_orders'] = $this->orderModel->countByUser($id);
        $user['total_spent'] = $this->orderModel->getTotalSpentByUser($id);
        $user['total_bookings'] = $this->bookingModel->countByUser($id);

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('users/edit', $data);
    }

    public function deleteUser($id)
    {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('/superadmin/users');
            return;
        }

        // Prevent deletion of current user
        if ($id == $_SESSION['user_id']) {
            $this->setFlash('error', 'Cannot delete your own account.');
            $this->redirect('/superadmin/users');
            return;
        }

        if ($this->userModel->delete($id)) {
            $this->setFlash('success', 'User deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete user.');
        }

        $this->redirect('/superadmin/users');
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

        $orders = $this->orderModel->getAllOrders($limit, $offset, $status);
        $totalOrders = $this->orderModel->count($status);
        $totalPages = ceil($totalOrders / $limit);

        // Calculate statistics
        $allOrders = $this->orderModel->getAllOrders();
        $completedOrders = count(array_filter($allOrders, function($order) {
            return $order['status'] === 'completed';
        }));

        $pendingOrders = count(array_filter($allOrders, function($order) {
            return $order['status'] === 'pending';
        }));

        $processingOrders = count(array_filter($allOrders, function($order) {
            return $order['status'] === 'processing';
        }));

        $todayRevenue = array_sum(array_map(function($order) {
            if (date('Y-m-d', strtotime($order['created_at'])) === date('Y-m-d') &&
                $order['status'] === 'completed') {
                return $order['total_amount'];
            }
            return 0;
        }, $allOrders));

        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'todayRevenue' => $todayRevenue,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('orders/index', $data);
    }

    public function updateOrderStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        $status = $_POST['status'] ?? '';
        $validStatuses = ['pending', 'processing', 'delivering', 'completed', 'cancelled'];

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

    // ==========================================
    // BOOKING MANAGEMENT
    // ==========================================

    public function bookings()
    {
        $page = (int)($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $bookings = $this->bookingModel->getAllBookings($limit, $offset, $status);
        $totalBookings = $this->bookingModel->count($status);
        $totalPages = ceil($totalBookings / $limit);

        // Calculate statistics
        $allBookings = $this->bookingModel->getAllBookings();
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
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('bookings/index', $data);
    }

    public function updateBookingStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

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
        $totalPages = ceil($totalTables / $limit);

        // Calculate statistics
        $allTables = $this->tableModel->getAllTables();
        $availableTables = count(array_filter($allTables, function($table) {
            return $table['status'] === 'available';
        }));

        $totalCapacity = array_sum(array_column($allTables, 'capacity'));

        $data = [
            'title' => 'Table Management',
            'tables' => $tables,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTables' => $totalTables,
            'availableTables' => $availableTables,
            'totalCapacity' => $totalCapacity,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('tables/index', $data);
    }

    public function createTable()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/superadmin/tables');
                return;
            }

            $tableData = [
                'table_number' => $this->sanitize($_POST['table_number']),
                'capacity' => (int)$_POST['capacity'],
                'status' => 'available',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Validate required fields
            if (empty($tableData['table_number']) || $tableData['capacity'] < 1) {
                $this->setFlash('error', 'Please fill in all required fields correctly.');
                $this->redirect('/superadmin/tables/create');
                return;
            }

            // Check if table number already exists
            if ($this->tableModel->findByField('table_number', $tableData['table_number'])) {
                $this->setFlash('error', 'Table number already exists.');
                $this->redirect('/superadmin/tables/create');
                return;
            }

            if ($this->tableModel->createTable($tableData)) {
                $this->setFlash('success', 'Table created successfully.');
            } else {
                $this->setFlash('error', 'Failed to create table.');
            }

            $this->redirect('/superadmin/tables');
            return;
        }

        $data = [
            'title' => 'Create Table',
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadSuperAdminView('tables/create', $data);    }

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

            $restaurantData = [
                'restaurant_name' => $this->sanitize($_POST['restaurant_name']),
                'address' => $this->sanitize($_POST['address']),
                'phone' => $this->sanitize($_POST['phone']),
                'email' => $this->sanitize($_POST['email']),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'opening_hours' => $this->sanitize($_POST['opening_hours'] ?? '09:00 - 22:00'),
                'capacity' => (int)($_POST['capacity'] ?? 100)
            ];

            // Update or create restaurant info
            $db = Database::getInstance()->getConnection();

            $stmt = $db->prepare("SELECT COUNT(*) FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $exists = $stmt->fetchColumn() > 0;

            if ($exists) {
                $stmt = $db->prepare("
                    UPDATE restaurant_info
                    SET restaurant_name = ?, address = ?, phone = ?, email = ?, description = ?, opening_hours = ?, capacity = ?, updated_at = NOW()
                    WHERE id = 1
                ");
            } else {
                $stmt = $db->prepare("
                    INSERT INTO restaurant_info (id, restaurant_name, address, phone, email, description, opening_hours, capacity)
                    VALUES (1, ?, ?, ?, ?, ?, ?, ?)
                ");
            }

            if ($stmt->execute(array_values($restaurantData))) {
                $this->setFlash('success', 'Restaurant information updated successfully.');
            } else {
                $this->setFlash('error', 'Failed to update restaurant information.');
            }

            $this->redirect('/superadmin/restaurant');
            return;
        }

        // Get current restaurant info
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
        $stmt->execute();
        $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$restaurantInfo) {
            $restaurantInfo = [
                'restaurant_name' => 'Buffet Restaurant',
                'address' => '',
                'phone' => '',
                'email' => '',
                'description' => '',
                'opening_hours' => '09:00 - 22:00',
                'capacity' => 100
            ];
        }

        // Get statistics for restaurant info page
        $stats = [
            'total_orders' => $this->orderModel->count(),
            'total_customers' => $this->userModel->count(['role' => 'customer']),
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

    public function updateRestaurantInfo()
    {
        // This method is called by the routing for POST requests to /superadmin/restaurant/update
        $this->restaurantInfo();
    }

    // ==========================================
    // PROMOTION MANAGEMENT
    // ==========================================    public function promotions()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get promotions with filters
        $db = Database::getInstance()->getConnection();

        // Build filter conditions
        $whereConditions = [];
        $params = [];

        if (!empty($_GET['status'])) {
            switch ($_GET['status']) {
                case 'active':
                    $whereConditions[] = "is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()";
                    break;
                case 'inactive':
                    $whereConditions[] = "is_active = 0";
                    break;
                case 'expired':
                    $whereConditions[] = "end_date < CURDATE()";
                    break;
            }
        }

        if (!empty($_GET['type'])) {
            $whereConditions[] = "type = ?";
            $params[] = $_GET['type'];
        }

        if (!empty($_GET['search'])) {
            $whereConditions[] = "(name LIKE ? OR code LIKE ?)";
            $searchTerm = '%' . $_GET['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get promotions
        $sql = "SELECT * FROM promotions $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count
        array_pop($params); // Remove offset
        array_pop($params); // Remove limit
        $countSql = "SELECT COUNT(*) FROM promotions $whereClause";
        $stmt = $db->prepare($countSql);
        $stmt->execute($params);
        $totalPromotions = $stmt->fetchColumn();
        $totalPages = ceil($totalPromotions / $limit);

        // Calculate statistics
        $stats = $this->getPromotionStats();

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

            $db = Database::getInstance()->getConnection();

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
            $stmt = $db->prepare("SELECT COUNT(*) FROM promotions WHERE code = ?");
            $stmt->execute([$promotionData['code']]);
            if ($stmt->fetchColumn() > 0) {
                $this->jsonResponse(['success' => false, 'message' => 'Promotion code already exists.'], 400);
                return;
            }

            // Insert promotion
            $stmt = $db->prepare("
                INSERT INTO promotions (name, code, description, type, discount_value, start_date, end_date, usage_limit, minimum_amount)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if ($stmt->execute([
                $promotionData['name'],
                $promotionData['code'],
                $promotionData['description'],
                $promotionData['type'],
                $promotionData['discount_value'],
                $promotionData['start_date'],
                $promotionData['end_date'],
                $promotionData['usage_limit'],
                $promotionData['minimum_amount']
            ])) {
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

            $db = Database::getInstance()->getConnection();

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
            $stmt = $db->prepare("SELECT COUNT(*) FROM promotions WHERE code = ? AND id != ?");
            $stmt->execute([$promotionData['code'], $id]);
            if ($stmt->fetchColumn() > 0) {
                $this->jsonResponse(['success' => false, 'message' => 'Promotion code already exists.'], 400);
                return;
            }

            // Update promotion
            $stmt = $db->prepare("
                UPDATE promotions
                SET name = ?, code = ?, description = ?, type = ?, discount_value = ?,
                    start_date = ?, end_date = ?, usage_limit = ?, minimum_amount = ?, updated_at = NOW()
                WHERE id = ?
            ");

            if ($stmt->execute([
                $promotionData['name'],
                $promotionData['code'],
                $promotionData['description'],
                $promotionData['type'],
                $promotionData['discount_value'],
                $promotionData['start_date'],
                $promotionData['end_date'],
                $promotionData['usage_limit'],
                $promotionData['minimum_amount'],
                $id
            ])) {
                $this->jsonResponse(['success' => true, 'message' => 'Promotion updated successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update promotion.'], 500);
            }
        } else {
            // GET request - return promotion data for editing
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM promotions WHERE id = ?");
            $stmt->execute([$id]);
            $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

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

            $db = Database::getInstance()->getConnection();

            $stmt = $db->prepare("DELETE FROM promotions WHERE id = ?");
            if ($stmt->execute([$id])) {
                $this->jsonResponse(['success' => true, 'message' => 'Promotion deleted successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete promotion.'], 500);
            }
        }
    }

    public function togglePromotionStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance()->getConnection();

            // Get current status
            $stmt = $db->prepare("SELECT is_active FROM promotions WHERE id = ?");
            $stmt->execute([$id]);
            $currentStatus = $stmt->fetchColumn();

            if ($currentStatus !== false) {
                $newStatus = $currentStatus ? 0 : 1;
                $stmt = $db->prepare("UPDATE promotions SET is_active = ? WHERE id = ?");

                if ($stmt->execute([$newStatus, $id])) {
                    $this->jsonResponse(['success' => true, 'is_active' => $newStatus]);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Failed to update status.'], 500);
                }
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Promotion not found.'], 404);
            }
        }
    }

    private function getPromotionStats()
    {
        $db = Database::getInstance()->getConnection();

        $stats = [];

        // Total promotions
        $stats['total_promotions'] = $db->query("SELECT COUNT(*) FROM promotions")->fetchColumn();

        // Active promotions
        $stats['active_promotions'] = $db->query("
            SELECT COUNT(*) FROM promotions
            WHERE is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()
        ")->fetchColumn();

        // Expired promotions
        $stats['expired_promotions'] = $db->query("
            SELECT COUNT(*) FROM promotions WHERE end_date < CURDATE()
        ")->fetchColumn();

        // Total usage (if you track promotion usage)
        $stats['used_promotions'] = $db->query("
            SELECT COALESCE(SUM(used_count), 0) FROM promotions
        ")->fetchColumn();

        return $stats;
    }

    // ==========================================
    // STATISTICS & REPORTS
    // ==========================================

    public function statistics()
    {
        // Get comprehensive statistics
        $stats = $this->getDashboardStats();

        // Additional statistics for super admin
        $stats['monthly_revenue'] = $this->orderModel->getMonthlyRevenue();
        $stats['popular_foods'] = $this->foodModel->getPopularFoods(10);
        $stats['user_growth'] = $this->userModel->getUserGrowthStats();
        $stats['booking_trends'] = $this->bookingModel->getBookingTrends();

        $data = [
            'title' => 'Advanced Statistics',
            'stats' => $stats
        ];

        $this->loadSuperAdminView('statistics/index', $data);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

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
        $recentUsers = $this->userModel->getRecentUsers(5);

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
            'active_users' => $this->userModel->countActive(),
            'monthly_revenue' => $monthlyRevenue,
            'recent_orders' => $recentOrders,
            'recent_bookings' => $recentBookings,
            'recent_users' => $recentUsers
        ];
    }
}
?>
