<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Table.php';

class AdminController extends BaseController {    public function __construct() {
        $this->requireRole(['manager', 'super_admin']);
    }

    public function dashboard() {
        $data = [
            'title' => 'Admin Dashboard',
            'stats' => $this->getDashboardStats()
        ];

        $this->view('admin/dashboard', $data);
    }

    public function users() {
        $userModel = new User();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $users = $userModel->findAll($limit, $offset);
        $totalUsers = $userModel->count();
        $totalPages = ceil($totalUsers / $limit);

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers
        ];

        $this->view('admin/users/index', $data);
    }

    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/users');
                return;
            }

            $userModel = new User();
            $userData = [
                'name' => $this->sanitize($_POST['name']),
                'email' => $this->sanitize($_POST['email']),
                'phone' => $this->sanitize($_POST['phone']),
                'role' => $this->sanitize($_POST['role']),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($userModel->create($userData)) {
                $this->setFlash('success', 'User created successfully.');
            } else {
                $this->setFlash('error', 'Failed to create user.');
            }

            $this->redirect('/admin/users');
            return;
        }

        $data = [
            'title' => 'Create User',
            'csrf_token' => $this->generateCSRF()
        ];

        $this->view('admin/users/create', $data);
    }

    public function editUser($id) {
        $userModel = new User();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/users');
                return;
            }

            $userData = [
                'name' => $this->sanitize($_POST['name']),
                'email' => $this->sanitize($_POST['email']),
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'address' => $this->sanitize($_POST['address'] ?? ''),
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Only super admin can change roles and status for other users
            if ($this->hasRole(['super_admin']) && $id != $_SESSION['user_id']) {
                $userData['role'] = $_POST['role'] ?? 'customer';
                $userData['status'] = $_POST['status'] ?? 'active';
            }

            // Handle password update
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $this->setFlash('error', 'Password must be at least 6 characters long.');
                    $this->redirect('/admin/users/edit/' . $id);
                    return;
                }

                if ($_POST['password'] !== $_POST['password_confirm']) {
                    $this->setFlash('error', 'Password confirmation does not match.');
                    $this->redirect('/admin/users/edit/' . $id);
                    return;
                }

                $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            if ($userModel->update($id, $userData)) {
                $this->setFlash('success', 'User updated successfully.');
            } else {
                $this->setFlash('error', 'Failed to update user.');
            }

            $this->redirect('/admin/users');
            return;
        }

        $user = $userModel->findById($id);
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }

        // Get user statistics
        $orderModel = new Order();
        $bookingModel = new Booking();
        $user['total_orders'] = $orderModel->countByUser($id);
        $user['total_spent'] = $orderModel->getTotalSpentByUser($id);
        $user['total_bookings'] = $bookingModel->countByUser($id);

        $data = [
            'title' => 'Edit User',
            'user' => $user
        ];

        $this->view('admin/users/edit', $data);
    }

    public function deleteUser($id) {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/users');
            return;
        }

        $userModel = new User();

        if ($userModel->delete($id)) {
            $this->setFlash('success', 'User deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete user.');
        }

        $this->redirect('/admin/users');
    }

    public function foods() {
        $foodModel = new Food();
        $categoryModel = new Category();

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $foods = $foodModel->getAllWithCategories($limit, $offset);
        $categories = $categoryModel->findAll();
        $totalFoods = $foodModel->count();
        $totalPages = ceil($totalFoods / $limit);

        $data = [
            'title' => 'Food Management',
            'foods' => $foods,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalFoods' => $totalFoods
        ];

        $this->view('admin/foods/index', $data);
    }

    public function createFood() {
        $categoryModel = new Category();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/foods');
                return;
            }

            $foodModel = new Food();
            $foodData = [
                'name' => $this->sanitize($_POST['name']),
                'description' => $this->sanitize($_POST['description']),
                'price' => (float)$_POST['price'],
                'category_id' => (int)$_POST['category_id'],
                'subcategory_id' => !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null,
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'assets/images/foods/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $foodData['image'] = $fileName;
                }
            }

            if ($foodModel->create($foodData)) {
                $this->setFlash('success', 'Food item created successfully.');
            } else {
                $this->setFlash('error', 'Failed to create food item.');
            }

            $this->redirect('/admin/foods');
            return;
        }

        $data = [
            'title' => 'Create Food Item',
            'categories' => $categoryModel->getMainCategories(),
            'csrf_token' => $this->generateCSRF()
        ];

        $this->view('admin/foods/create', $data);
    }

    public function editFood($id) {        $foodModel = new Food();
        $categoryModel = new Category();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/foods');
                return;
            }

            $foodData = [
                'name' => $this->sanitize($_POST['name']),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'price' => (float)$_POST['price'],
                'category_id' => (int)$_POST['category_id'],
                'availability' => $_POST['availability'] ?? 'available',
                'ingredients' => $this->sanitize($_POST['ingredients'] ?? ''),
                'spice_level' => $_POST['spice_level'] ?? 'mild',
                'prep_time' => !empty($_POST['prep_time']) ? (int)$_POST['prep_time'] : null,
                'calories' => !empty($_POST['calories']) ? (int)$_POST['calories'] : null,
                'is_vegetarian' => isset($_POST['is_vegetarian']) ? 1 : 0,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/foods/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['image']['type'];
                $fileSize = $_FILES['image']['size'];

                if (in_array($fileType, $allowedTypes) && $fileSize <= 2 * 1024 * 1024) {
                    $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $fileName = 'food_' . $id . '_' . time() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $foodData['image'] = $fileName;
                    }
                }
            }

            if ($foodModel->update($id, $foodData)) {
                $this->setFlash('success', 'Food item updated successfully.');
            } else {
                $this->setFlash('error', 'Failed to update food item.');
            }

            $this->redirect('/admin/foods');
            return;
        }

        $food = $foodModel->getFoodDetails($id);
        if (!$food) {
            $this->setFlash('error', 'Food item not found.');
            $this->redirect('/admin/foods');
            return;
        }

        $categories = $categoryModel->findAll();

        $data = [
            'title' => 'Edit Food Item',
            'food' => $food,
            'categories' => $categories
        ];

        $this->view('admin/foods/edit', $data);
    }

    public function deleteFood($id) {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/foods');
            return;
        }

        $foodModel = new Food();
        $food = $foodModel->findById($id);

        if ($food && $foodModel->delete($id)) {
            // Delete image file
            if ($food['image'] && file_exists('assets/images/foods/' . $food['image'])) {
                unlink('assets/images/foods/' . $food['image']);
            }
            $this->setFlash('success', 'Food item deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete food item.');
        }

        $this->redirect('/admin/foods');
    }

    public function orders() {
        $orderModel = new Order();

        $page = (int)($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $orders = $orderModel->getAllOrders($limit, $offset, $status);
        $totalOrders = $orderModel->count($status);
        $totalPages = ceil($totalOrders / $limit);

        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'statusFilter' => $status
        ];

        $this->view('admin/orders/index', $data);
    }

    public function updateOrderStatus($id) {
        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
            return;
        }

        $orderModel = new Order();
        $status = $this->sanitize($_POST['status']);

        $updateData = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($orderModel->update($id, $updateData)) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status.']);
        }
    }

    public function bookings() {
        $bookingModel = new Booking();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? null;

        $bookings = $bookingModel->getAllWithCustomers($limit, $offset, $status, $search);
        $totalBookings = $bookingModel->count($status);
        $totalPages = ceil($totalBookings / $limit);

        // Export functionality
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $allBookings = $bookingModel->getAllWithCustomers(null, 0, $status, $search);
            $this->exportBookingsCSV($allBookings);
            return;
        }

        $data = [
            'title' => 'Booking Management',
            'bookings' => $bookings,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalBookings' => $totalBookings,
            'currentStatus' => $status,
            'searchQuery' => $search,
            'todayBookings' => $bookingModel->getTodayCount(),
            'upcomingBookings' => count($bookingModel->getUpcomingBookings()),
            'csrf_token' => $this->generateCSRF()
        ];

        $this->view('admin/bookings/index', $data);
    }

    public function updateBookingStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $bookingId = (int)$input['booking_id'];
        $status = $this->sanitize($input['status']);

        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'];
        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            return;
        }

        $bookingModel = new Booking();
        if ($bookingModel->updateStatus($bookingId, $status)) {
            echo json_encode(['success' => true, 'message' => 'Booking status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
        }
    }

    public function assignTable() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $bookingId = (int)$input['booking_id'];
        $tableId = (int)$input['table_id'];

        $bookingModel = new Booking();
        if ($bookingModel->assignTable($bookingId, $tableId)) {
            echo json_encode(['success' => true, 'message' => 'Table assigned successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to assign table']);
        }
    }

    public function getBookingDetails($id) {
        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingDetails($id);

        if ($booking) {
            echo json_encode(['success' => true, 'booking' => $booking]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
        }
    }

    public function getAvailableTables() {
        $reservationTime = $_GET['reservation_time'] ?? '';
        $numberOfGuests = (int)($_GET['number_of_guests'] ?? 1);

        if (empty($reservationTime)) {
            echo json_encode(['success' => false, 'message' => 'Reservation time is required']);
            return;
        }

        $bookingModel = new Booking();
        $availableTables = $bookingModel->getAvailableTables($reservationTime, $numberOfGuests);

        echo json_encode(['success' => true, 'tables' => $availableTables]);
    }

    // API Endpoints for Dashboard
    public function apiRecentOrders() {
        header('Content-Type: application/json');

        $orderModel = new Order();
        $recentOrders = $orderModel->getRecentOrders(10);

        echo json_encode(['success' => true, 'orders' => $recentOrders]);
    }

    public function apiUpcomingBookings() {
        header('Content-Type: application/json');

        $bookingModel = new Booking();
        $upcomingBookings = $bookingModel->getUpcomingBookings(10);

        echo json_encode(['success' => true, 'bookings' => $upcomingBookings]);
    }

    public function apiOrderStats() {
        header('Content-Type: application/json');

        $orderModel = new Order();
        $orderStats = $orderModel->getOrderStats();

        echo json_encode(['success' => true, 'stats' => $orderStats]);
    }

    public function apiBookingStats() {
        header('Content-Type: application/json');

        $bookingModel = new Booking();
        $bookingStats = $bookingModel->getBookingStats();

        echo json_encode(['success' => true, 'stats' => $bookingStats]);
    }

    public function categories() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllWithStats();

        $stats = [
            'active_categories' => $categoryModel->count('active'),
            'total_foods' => $this->getTotalFoodsInCategories($categories),
            'empty_categories' => $this->getEmptyCategories($categories)
        ];

        $popularCategories = $categoryModel->getPopularCategories(5);

        $data = [
            'title' => 'Category Management',
            'categories' => $categories,
            'stats' => $stats,
            'popularCategories' => $popularCategories
        ];

        $this->view('admin/categories/index', $data);
    }

    public function createCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }

        $categoryModel = new Category();
        $categoryData = [
            'name' => $this->sanitize($_POST['name']),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'icon' => $this->sanitize($_POST['icon'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Validate required fields
        $errors = [];
        if (empty($categoryData['name'])) {
            $errors['name'] = ['Category name is required'];
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        if ($categoryModel->create($categoryData)) {
            echo json_encode(['success' => true, 'message' => 'Category created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category']);
        }
    }

    public function getCategory($id) {
        $categoryModel = new Category();
        $category = $categoryModel->findById($id);

        if ($category) {
            echo json_encode(['success' => true, 'category' => $category]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Category not found']);
        }
    }

    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }

        $categoryId = (int)$_POST['category_id'];
        $categoryModel = new Category();

        $categoryData = [
            'name' => $this->sanitize($_POST['name']),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'icon' => $this->sanitize($_POST['icon'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Validate required fields
        $errors = [];
        if (empty($categoryData['name'])) {
            $errors['name'] = ['Category name is required'];
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        if ($categoryModel->update($categoryId, $categoryData)) {
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update category']);
        }
    }

    public function deleteCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $categoryId = (int)$input['category_id'];

        $categoryModel = new Category();
        $foodModel = new Food();

        // Check if category has foods
        $foodCount = $foodModel->countByCategory($categoryId);
        if ($foodCount > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete category that contains food items']);
            return;
        }

        if ($categoryModel->delete($categoryId)) {
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
        }
    }

    public function tables() {
        $tableModel = new Table();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $tables = $tableModel->getAllTables($limit, $offset);
        $totalTables = $tableModel->count();
        $totalPages = ceil($totalTables / $limit);
        $stats = $tableModel->getTableStats();
        $locationStats = $tableModel->getTablesByLocation();

        $data = [
            'title' => 'Table Management',
            'tables' => $tables,
            'stats' => $stats,
            'locationStats' => $locationStats,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTables' => $totalTables
        ];

        $this->view('admin/tables/index', $data);
    }

    public function createTable() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/tables');
                return;
            }

            $tableModel = new Table();
            $tableData = [
                'table_number' => $this->sanitize($_POST['table_number']),
                'capacity' => (int)$_POST['capacity'],
                'location' => $this->sanitize($_POST['location'] ?? ''),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'is_available' => isset($_POST['is_available']) ? 1 : 0
            ];

            // Validate required fields
            $errors = [];
            if (empty($tableData['table_number'])) {
                $errors[] = 'Table number is required.';
            }
            if ($tableData['capacity'] < 1) {
                $errors[] = 'Table capacity must be at least 1.';
            }

            // Check if table number already exists
            $existingTable = $tableModel->findByField('table_number', $tableData['table_number']);
            if ($existingTable) {
                $errors[] = 'Table number already exists.';
            }

            if (!empty($errors)) {
                $this->setFlash('error', implode(' ', $errors));
                $this->redirect('/admin/tables/create');
                return;
            }

            if ($tableModel->createTable($tableData)) {
                $this->setFlash('success', 'Table created successfully.');
            } else {
                $this->setFlash('error', 'Failed to create table.');
            }

            $this->redirect('/admin/tables');
            return;
        }

        $data = [
            'title' => 'Create Table',
            'csrf_token' => $this->generateCSRF()
        ];

        $this->view('admin/tables/create', $data);
    }

    public function editTable($id) {
        $tableModel = new Table();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/tables');
                return;
            }

            $tableData = [
                'table_number' => $this->sanitize($_POST['table_number']),
                'capacity' => (int)$_POST['capacity'],
                'location' => $this->sanitize($_POST['location'] ?? ''),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'is_available' => isset($_POST['is_available']) ? 1 : 0
            ];

            // Validate required fields
            $errors = [];
            if (empty($tableData['table_number'])) {
                $errors[] = 'Table number is required.';
            }
            if ($tableData['capacity'] < 1) {
                $errors[] = 'Table capacity must be at least 1.';
            }

            // Check if table number already exists (excluding current table)
            $existingTable = $tableModel->findByField('table_number', $tableData['table_number']);
            if ($existingTable && $existingTable['id'] != $id) {
                $errors[] = 'Table number already exists.';
            }

            if (!empty($errors)) {
                $this->setFlash('error', implode(' ', $errors));
                $this->redirect('/admin/tables/edit/' . $id);
                return;
            }

            if ($tableModel->updateTable($id, $tableData)) {
                $this->setFlash('success', 'Table updated successfully.');
            } else {
                $this->setFlash('error', 'Failed to update table.');
            }

            $this->redirect('/admin/tables');
            return;
        }

        $table = $tableModel->findById($id);
        if (!$table) {
            $this->setFlash('error', 'Table not found.');
            $this->redirect('/admin/tables');
            return;
        }

        // Get table booking history
        $bookingHistory = $tableModel->getTableBookingHistory($id, 10);

        $data = [
            'title' => 'Edit Table',
            'table' => $table,
            'bookingHistory' => $bookingHistory,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->view('admin/tables/edit', $data);
    }

    public function deleteTable($id) {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/tables');
            return;
        }

        $tableModel = new Table();

        if ($tableModel->deleteTable($id)) {
            $this->setFlash('success', 'Table deleted successfully.');
        } else {
            $this->setFlash('error', 'Cannot delete table. It may have existing bookings.');
        }

        $this->redirect('/admin/tables');
    }

    public function tableUtilization() {
        $tableModel = new Table();
        $days = (int)($_GET['days'] ?? 30);

        $utilization = $tableModel->getTableUtilization($days);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'utilization' => $utilization,
            'period_days' => $days
        ]);
    }

    public function getTableHistory($tableId) {
        header('Content-Type: application/json');

        $tableModel = new Table();
        $limit = (int)($_GET['limit'] ?? 20);
        $history = $tableModel->getTableBookingHistory($tableId, $limit);

        // Format the history data for display
        $formattedHistory = array_map(function($booking) {
            return [
                'id' => $booking['id'],
                'booking_date' => $booking['booking_date'],
                'booking_time' => $booking['booking_time'],
                'customer_name' => $booking['first_name'] . ' ' . $booking['last_name'],
                'customer_email' => $booking['email'],
                'guest_count' => $booking['guest_count'],
                'status' => $booking['status'],
                'special_requests' => $booking['special_requests'] ?? '',
                'created_at' => $booking['created_at']
            ];
        }, $history);

        echo json_encode([
            'success' => true,
            'history' => $formattedHistory,
            'table_id' => $tableId
        ]);
    }

    public function checkTableAvailability($tableId) {
        header('Content-Type: application/json');

        $date = $_GET['date'] ?? '';
        $time = $_GET['time'] ?? '';

        if (empty($date) || empty($time)) {
            echo json_encode([
                'success' => false,
                'message' => 'Date and time are required'
            ]);
            return;
        }

        $tableModel = new Table();
        $isAvailable = $tableModel->isTableAvailable($tableId, $date, $time);

        echo json_encode([
            'success' => true,
            'available' => $isAvailable,
            'table_id' => $tableId,
            'date' => $date,
            'time' => $time,
            'message' => $isAvailable ? 'Table is available' : 'Table is not available at this time'
        ]);
    }

    private function exportBookingsCSV($bookings) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bookings_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, [
            'Booking ID',
            'Customer Name',
            'Customer Email',
            'Booking Date',
            'Booking Time',
            'Number of Guests',
            'Table Number',
            'Status',
            'Special Requests',
            'Created At'
        ]);

        // CSV data
        foreach ($bookings as $booking) {
            fputcsv($output, [
                $booking['id'],
                $booking['customer_name'],
                $booking['customer_email'],
                $booking['booking_date'],
                $booking['booking_time'],
                $booking['number_of_guests'],
                $booking['table_number'] ?? 'Not assigned',
                $booking['status'],
                $booking['special_requests'] ?? '',
                $booking['created_at']
            ]);
        }

        fclose($output);
    }

    private function getTotalFoodsInCategories($categories) {
        return array_sum(array_column($categories, 'food_count'));
    }

    private function getEmptyCategories($categories) {
        return count(array_filter($categories, function($cat) {
            return ($cat['food_count'] ?? 0) == 0;
        }));
    }

    private function getDashboardStats() {
        $userModel = new User();
        $foodModel = new Food();
        $orderModel = new Order();
        $bookingModel = new Booking();

        return [
            'total_users' => $userModel->count(),
            'total_foods' => $foodModel->count(),
            'total_orders' => $orderModel->count(),
            'total_bookings' => $bookingModel->count(),
            'pending_orders' => $orderModel->count('pending'),
            'confirmed_bookings' => $bookingModel->count('confirmed'),
            'today_orders' => $orderModel->getTodayCount(),
            'today_revenue' => $orderModel->getTodayRevenue()
        ];
    }

    public function getSubcategories($categoryId) {
        $categoryModel = new Category();
        $subcategories = $categoryModel->getSubcategories($categoryId);

        header('Content-Type: application/json');
        echo json_encode($subcategories);
    }

    // Order Details for Modal
    public function orderDetails($id) {
        $orderModel = new Order();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            http_response_code(404);
            echo '<div class="alert alert-danger">Order not found.</div>';
            return;
        }

        // Return HTML for modal content
        $this->view('admin/orders/details_modal', ['order' => $order], false);
    }

    // Print Order
    public function printOrder($id) {
        $orderModel = new Order();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }

        // Return printable HTML
        $this->view('admin/orders/print', ['order' => $order], false);
    }

    // Export Orders to CSV
    public function exportOrdersCSV() {
        $orderModel = new Order();

        // Get filter parameters
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        // Build filter conditions
        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($dateFrom) $filters['date_from'] = $dateFrom;
        if ($dateTo) $filters['date_to'] = $dateTo;

        $orders = $orderModel->getOrdersForExport($filters);

        // Set CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Output CSV
        $output = fopen('php://output', 'w');

        // CSV Headers
        fputcsv($output, [
            'Order ID',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Total Items',
            'Total Amount',
            'Status',
            'Payment Method',
            'Order Date',
            'Delivery Address',
            'Order Notes'
        ]);

        // CSV Data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['id'],
                $order['customer_name'] ?? 'Guest',
                $order['customer_email'],
                $order['customer_phone'],
                $order['total_items'],
                $order['total_amount'],
                ucfirst($order['status']),
                ucfirst($order['payment_method']),
                date('Y-m-d H:i:s', strtotime($order['created_at'])),
                $order['delivery_address'],
                $order['order_notes']
            ]);
        }

        fclose($output);
    }

    // Enhanced Order Filtering
    public function ordersFiltered() {
        $orderModel = new Order();

        $page = (int)($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $search = $_GET['search'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Build filter conditions
        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($dateFrom) $filters['date_from'] = $dateFrom;
        if ($dateTo) $filters['date_to'] = $dateTo;
        if ($search) $filters['search'] = $search;

        $orders = $orderModel->getFilteredOrders($filters, $limit, $offset);
        $totalOrders = $orderModel->countFilteredOrders($filters);
        $totalPages = ceil($totalOrders / $limit);

        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'filters' => $filters
        ];

        $this->view('admin/orders/index', $data);
    }
}
