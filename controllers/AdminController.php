<?php
require_once __DIR__ . '/../helpers/booking_trend_helper.php';

require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/InternalMessage.php';
require_once __DIR__ . '/../helpers/mail_helper.php';
require_once __DIR__ . '/../helpers/pdf_helper.php';

class AdminController extends BaseController
{
    private $categoryModel;
    private $userModel;
    private $foodModel;
    private $orderModel;
    private $bookingModel;
    private $tableModel;
    private $internalMessageModel;

    public function __construct()
    {
        $this->requireRole(['manager', 'super_admin']);
        $this->categoryModel = new Category();
        $this->userModel = new User();
        $this->foodModel = new Food();
        $this->orderModel = new Order();
        $this->bookingModel = new Booking();
        $this->tableModel = new Table();
        $this->internalMessageModel = new InternalMessage();
    }
    public function dashboard()
    {
        $data = [
            'title' => 'Admin Dashboard',
            'stats' => $this->getDashboardStats()
        ];

        $this->loadAdminView('dashboard', $data);
    }

    public function dashboardStats()
    {
        try {
            $pdo = $this->getDatabase();

            // Lấy thống kê cơ bản
            $stats = [
                'total_orders' => $this->getTotalOrders($pdo),
                'monthly_revenue' => $this->getMonthlyRevenue($pdo),
                'active_bookings' => $this->getActiveBookings($pdo),
                'total_users' => $this->getTotalUsers($pdo),
                'confirmed_bookings' => $this->getBookingCountByStatus($pdo, 'confirmed'),
                'pending_bookings' => $this->getBookingCountByStatus($pdo, 'pending'),
                'cancelled_bookings' => $this->getBookingCountByStatus($pdo, 'cancelled'),
                'recent_orders' => $this->getRecentOrders($pdo),
                'recent_bookings' => $this->getRecentBookings($pdo),
                'booking_trend_data' => BookingTrendHelper::getBookingTrendData($pdo)
            ];

            header('Content-Type: application/json');
            echo json_encode($stats);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function users()
    {
        $userModel = new User();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';

        // Use getAllForAdmin() to get properly formatted user data
        $allUsers = $userModel->getAllForAdmin();

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
        $totalPages = ceil($totalUsers / $limit);

        // Apply pagination to the filtered data
        $users = array_slice($allUsers, $offset, $limit);        // Calculate statistics for the dashboard cards (based on all users before filters)
        $allUsersForStats = $userModel->getAllForAdmin();
        $activeUsers = count(array_filter($allUsersForStats, function ($user) {
            return $user['is_active'] == 1;
        }));

        $adminUsers = count(array_filter($allUsersForStats, function ($user) {
            return in_array($user['role'], ['manager', 'super_admin']);
        }));

        $newToday = count(array_filter($allUsersForStats, function ($user) {
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
            'newToday' => $newToday
        ];

        $this->loadAdminView('users/index', $data);
    }
    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/users');
                return;
            }

            $userModel = new User();
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

        $this->loadAdminView('users/create', $data);
    }

    public function editUser($id)
    {
        $userModel = new User();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/users');
                return;
            }
            $userData = [
                'first_name' => $this->sanitize($_POST['first_name']),
                'last_name' => $this->sanitize($_POST['last_name']),
                'email' => $this->sanitize($_POST['email']),
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'address' => $this->sanitize($_POST['address'] ?? ''),
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Only super admin can change roles and status for other users
            if ($this->hasRole(['super_admin']) && $id != $_SESSION['user']['id']) {
                $userData['role'] = $_POST['role'] ?? 'customer';
                $userData['is_active'] = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
            }            // Handle password update
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $this->setFlash('error', 'Password must be at least 6 characters long.');
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

        $this->loadAdminView('users/edit', $data);
    }

    public function deleteUser($id)
    {
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
    public function foods()
    {
        $foodModel = new Food();
        $categoryModel = new Category();

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';

        // Get all foods for filtering
        $allFoods = $foodModel->getAllForAdmin();

        // Apply filters
        if ($search) {
            $allFoods = array_filter($allFoods, function ($food) use ($search) {
                return stripos($food['name'], $search) !== false ||
                    stripos($food['description'], $search) !== false ||
                    stripos($food['category_name'], $search) !== false;
            });
        }

        if ($category) {
            $allFoods = array_filter($allFoods, function ($food) use ($category) {
                return $food['category_id'] == $category;
            });
        }

        if ($status !== '') {
            $allFoods = array_filter($allFoods, function ($food) use ($status) {
                return ($status === 'available' && $food['is_available'] == 1) ||
                    ($status === 'unavailable' && $food['is_available'] == 0);
            });
        }

        $totalFoods = count($allFoods);
        $totalPages = ceil($totalFoods / $limit);

        // Apply pagination to filtered data
        $foods = array_slice($allFoods, $offset, $limit);

        // Get all foods and categories for statistics (unfiltered)
        $allFoodsForStats = $foodModel->getAllForAdmin();
        $categories = $categoryModel->findAll();        // Calculate statistics for the dashboard cards
        $availableFoods = count(array_filter($allFoodsForStats, function ($food) {
            return $food['is_available'] == 1;
        }));

        $totalFoodsCount = count($allFoodsForStats);
        $totalCategories = count($categories);

        $popularToday = count(array_filter($allFoodsForStats, function ($food) {
            // This is a simplified calculation - you can make it more sophisticated
            return $food['is_available'] == 1 && ($food['price'] ?? 0) > 10;
        }));

        $data = [
            'title' => 'Food Management',
            'foods' => $foods,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalFoods' => $totalFoodsCount,
            'availableFoods' => $availableFoods,
            'totalCategories' => $totalCategories,
            'popularToday' => $popularToday
        ];

        $this->loadAdminView('foods/index', $data);
    }

    public function createFood()
    {
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
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'price' => (float)$_POST['price'],
                'category_id' => (int)$_POST['category_id'],
                'subcategory_id' => !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null,
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
                'is_new' => isset($_POST['is_new']) ? 1 : 0,
                'is_seasonal' => isset($_POST['is_seasonal']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ]; // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/food_images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $foodData['image'] = $fileName;
                    error_log("Image uploaded successfully: " . $fileName);
                } else {
                    error_log("Failed to move uploaded file from " . $_FILES['image']['tmp_name'] . " to " . $uploadPath);
                }
            } else {
                error_log("Image upload failed or no image provided. Error: " . ($_FILES['image']['error'] ?? 'No file'));
            }

            error_log("Food data to be inserted: " . print_r($foodData, true));

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

        $this->loadAdminView('foods/create', $data);
    }

    public function editFood($id)
    {
        $foodModel = new Food();
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
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
                'is_new' => isset($_POST['is_new']) ? 1 : 0,
                'is_seasonal' => isset($_POST['is_seasonal']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]; // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/food_images/';
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

        $this->loadAdminView('foods/edit', $data);
    }

    public function deleteFood($id)
    {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/foods');
            return;
        }

        $foodModel = new Food();
        $food = $foodModel->findById($id);
        if ($food && $foodModel->delete($id)) {
            // Delete image file
            if ($food['image'] && file_exists('uploads/food_images/' . $food['image'])) {
                unlink('uploads/food_images/' . $food['image']);
            }
            $this->setFlash('success', 'Food item deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete food item.');
        }

        $this->redirect('/admin/foods');
    }
    public function orders()
    {
        $orderModel = new Order();

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        // Build filters array
        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($search) $filters['search'] = $search;
        if ($dateFrom) $filters['date_from'] = $dateFrom;
        if ($dateTo) $filters['date_to'] = $dateTo;

        $orders = $orderModel->getFilteredOrders($filters, $limit, $offset);
        $totalOrders = $orderModel->countFilteredOrders($filters);
        $totalPages = ceil($totalOrders / $limit);

        // Calculate statistics for the dashboard cards (unfiltered)
        $allOrders = $orderModel->getAllOrders();

        $completedOrders = count(array_filter($allOrders, function ($order) {
            return $order['status'] === 'completed';
        }));

        $pendingOrders = count(array_filter($allOrders, function ($order) {
            return $order['status'] === 'pending';
        }));

        $todayRevenue = array_sum(array_map(function ($order) {
            if (
                date('Y-m-d', strtotime($order['created_at'])) === date('Y-m-d')
                && $order['status'] === 'completed'
            ) {
                return $order['total_amount'] ?? 0;
            }
            return 0;
        }, $allOrders));
        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => count($allOrders), // Total from unfiltered data
            'filteredCount' => $totalOrders, // Count from filtered data
            'filters' => $filters,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
            'todayRevenue' => $todayRevenue,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadAdminView('orders/index', $data);
    }
    public function updateOrderStatus($id)
    {
        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
            return;
        }

        // Check if status is provided
        if (!isset($_POST['status']) || empty($_POST['status'])) {
            echo json_encode(['success' => false, 'message' => 'Status is required.']);
            return;
        }

        $orderModel = new Order();
        $status = $this->sanitize($_POST['status']);

        // Validate status
        $allowedStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status provided.']);
            return;
        }

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

    public function updatePaymentStatus($id)
    {
        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
            return;
        }

        $orderModel = new Order();
        $paymentStatus = $this->sanitize($_POST['payment_status']);

        // Validate payment status
        $allowedStatuses = ['pending', 'paid', 'failed', 'refunded'];
        if (!in_array($paymentStatus, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid payment status.']);
            return;
        }

        $updateData = [
            'payment_status' => $paymentStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($orderModel->update($id, $updateData)) {
            echo json_encode(['success' => true, 'message' => 'Payment status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update payment status.']);
        }
    }
    public function bookings()
    {
        $bookingModel = new Booking();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? null;
        $date = $_GET['date'] ?? null;

        $bookings = $bookingModel->getAllForAdmin($limit, $offset, $status, $search, $date);
        $totalBookings = $bookingModel->count($status ? 'status' : null, $status);
        $totalPages = ceil($totalBookings / $limit);

        // Export functionality
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $allBookings = $bookingModel->getAllForAdmin(null, 0, $status, $search, $date);
            $this->exportBookingsCSV($allBookings);
            return;
        }

        // Calculate statistics (unfiltered)
        $confirmedBookings = $bookingModel->count('status', 'confirmed');
        $pendingBookings = $bookingModel->count('status', 'pending');
        $todayBookings = $bookingModel->getTodayCount();

        // Generate CSRF token and ensure it's set in the session
        $csrfToken = $this->generateCSRF();

        // Log the CSRF token (for debugging only)
        error_log("Generated CSRF token for bookings page: " . $csrfToken);

        $data = [
            'title' => 'Booking Management',
            'bookings' => $bookings,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalBookings' => $bookingModel->count(), // Total unfiltered
            'filteredCount' => $totalBookings, // Filtered count
            'confirmedBookings' => $confirmedBookings,
            'pendingBookings' => $pendingBookings,
            'todayBookings' => $todayBookings,
            'upcomingBookings' => count($bookingModel->getUpcomingBookings()),
            'csrf_token' => $csrfToken
        ];

        $this->loadAdminView('bookings/index', $data);
    }
    public function updateBookingStatus($bookingIdFromUrl = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }        // Handle both JSON and form-encoded input
        $input = null;
        $rawInput = file_get_contents('php://input');

        if (!empty($rawInput)) {
            $input = json_decode($rawInput, true);
        }

        // If JSON decode failed or input is empty, try POST data
        if ($input === null) {
            $input = $_POST;
        }

        // Get booking ID from URL parameter or request body
        $bookingId = null;
        if ($bookingIdFromUrl !== null) {
            $bookingId = (int)$bookingIdFromUrl;
        } elseif (isset($input['booking_id'])) {
            $bookingId = (int)$input['booking_id'];
        }

        // Validate required fields
        if (!$bookingId || !isset($input['status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields (booking ID or status)']);
            return;
        }

        $status = $this->sanitize($input['status']);

        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'];
        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            return;
        }
        $bookingModel = new Booking();
        if ($bookingModel->updateBookingStatus($bookingId, $status)) {
            // Gửi email xác nhận nếu trạng thái là confirmed
            if ($status === 'confirmed') {
                $booking = $bookingModel->getBookingDetails($bookingId);

                // Lấy email từ booking hoặc user
                $email = $booking['email'] ?? '';
                if (empty($email) && !empty($booking['user_id'])) {
                    $userModel = new User();
                    $user = $userModel->findById($booking['user_id']);
                    $email = $user['email'] ?? '';
                }

                if (!empty($email)) {
                    $subject = "Đặt bàn của bạn đã được xác nhận!";
                    $message = "
                    <h2>Xin chào {$booking['customer_name']},</h2>
                    <p>Đơn đặt bàn của bạn tại <b>Buffet Booking</b> đã được xác nhận!</p>
                    <ul>
                        <li><b>Ngày:</b> " . date('d/m/Y', strtotime($booking['reservation_time'])) . "</li>
                        <li><b>Giờ:</b> " . date('H:i', strtotime($booking['reservation_time'])) . "</li>
                        <li><b>Số lượng khách:</b> {$booking['number_of_guests']}</li>
                        <li><b>Số điện thoại:</b> {$booking['phone_number']}</li>
                    </ul>
                    <p>Chúng tôi rất mong được đón tiếp bạn!</p>
                    <p>Trạng thái: <b>Đã xác nhận</b></p>
                ";
                    // Gửi email xác nhận đã nhận phiếu đặt bàn
                    sendResetMail($email, $subject, $message);

                    // Lấy lại thông tin booking vừa tạo
                    $booking = $this->bookingModel->getBookingDetails($bookingId);

                    // Truyền biến $booking vào view PDF
                    ob_start();
                    include __DIR__ . '/../views/customer/booking/pdf_detail.php';
                    $htmlContent = ob_get_clean();

                    sendBookingPDFMail($email, $subject, $message, $htmlContent);
                }
            }
            echo json_encode(['success' => true, 'message' => 'Booking status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
        }
    }

    public function assignTable()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Check CSRF token from multiple possible sources
        $token = null;

        // Check for CSRF token in headers
        $headers = getallheaders();
        if (isset($headers['X-CSRF-Token'])) {
            $token = $headers['X-CSRF-Token'];
        } elseif (isset($headers['x-csrf-token'])) {
            $token = $headers['x-csrf-token'];
        }

        // Log headers for debugging
        error_log("Request headers: " . json_encode($headers));

        // Also check for token in request body
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$token && isset($input['csrf_token'])) {
            $token = $input['csrf_token'];
        }

        // Validate the token
        if (!$this->validateCSRFToken($token)) {
            error_log("CSRF validation failed in assignTable");
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }

        // Parse JSON input if not already parsed
        if (!$input) {
            $input = json_decode(file_get_contents('php://input'), true);
        }

        // Get and validate booking ID and table ID
        $bookingId = (int)($input['booking_id'] ?? 0);
        $tableId = (int)($input['table_id'] ?? 0);
        $notes = $input['notes'] ?? '';

        if (!$bookingId || !$tableId) {
            echo json_encode(['success' => false, 'message' => 'Invalid booking or table ID']);
            return;
        }

        // Log for debugging
        error_log("Assigning table ID $tableId to booking ID $bookingId");

        $bookingModel = new Booking();
        $tableModel = new Table();

        // Get database connection from model
        $db = $bookingModel->getDb();

        // Use a transaction if the database connection supports it
        $useTransaction = ($db && method_exists($db, 'beginTransaction'));

        if ($useTransaction) {
            $db->beginTransaction();
        }

        try {
            // Get booking details to check location compatibility
            $booking = $bookingModel->getBookingDetails($bookingId);
            if (!$booking) {
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }

            // Get table details to check location
            $table = $tableModel->findById($tableId);
            if (!$table) {
                echo json_encode(['success' => false, 'message' => 'Table not found']);
                return;
            }

            // Check if table location matches booking location (if booking has location)
            if (!empty($booking['booking_location']) && !empty($table['location'])) {
                if ($booking['booking_location'] !== $table['location']) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Table location does not match booking location. Booking: ' . $booking['booking_location'] . ', Table: ' . $table['location']
                    ]);
                    return;
                }
            }

            // Assign table to booking
            if ($bookingModel->assignTable($bookingId, $tableId)) {
                // Update table availability to 0 (unavailable)
                if ($tableModel->updateTableStatus($tableId, 'unavailable')) {
                    // If there are notes, update the booking with admin_notes
                    if (!empty($notes)) {
                        $bookingModel->updateBooking($bookingId, ['admin_notes' => $notes]);
                    }

                    // Update booking status to confirmed if it's pending
                    $booking = $bookingModel->getBookingDetails($bookingId);
                    if ($booking && $booking['status'] === 'pending') {
                        $bookingModel->updateStatus($bookingId, 'confirmed');
                    }

                    // Commit the transaction if using one
                    if ($useTransaction) {
                        $db->commit();
                    }

                    echo json_encode(['success' => true, 'message' => 'Table assigned successfully']);
                } else {
                    // Failed to update table status
                    if ($useTransaction) {
                        $db->rollBack();
                    }
                    error_log("Failed to update table status for table ID $tableId");
                    echo json_encode(['success' => false, 'message' => 'Failed to update table status']);
                }
            } else {
                // Failed to assign table
                if ($useTransaction) {
                    $db->rollBack();
                }
                error_log("Failed to assign table ID $tableId to booking ID $bookingId");
                echo json_encode(['success' => false, 'message' => 'Failed to assign table to booking']);
            }
        } catch (Exception $e) {
            // Log any exceptions and roll back
            if ($useTransaction) {
                $db->rollBack();
            }
            error_log("Exception in assignTable: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred while processing your request']);
        }
    }

    // Helper method to validate CSRF token from header
    private function validateCSRFToken($token)
    {
        // For debugging
        error_log("Validating CSRF token: " . ($token ?? 'null') . " against session token: " . ($_SESSION['csrf_token'] ?? 'null'));

        if (empty($token)) {
            return false;
        }

        // Use hash_equals for secure comparison
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function getBookingDetails($id)
    {
        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingDetails($id);

        if ($booking) {
            echo json_encode(['success' => true, 'booking' => $booking]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
        }
    }
    public function getCategory($id)
    {
        header('Content-Type: application/json');

        $categoryModel = new Category();
        $foodModel = new Food();

        $category = $categoryModel->findById((int)$id);
        if ($category) {
            // Get additional statistics
            $category['food_count'] = $foodModel->countByCategory($id);

            // Transform data for frontend compatibility
            $category['status'] = $category['is_active'] == 1 ? 'active' : 'inactive';

            // Format dates for display
            if (isset($category['created_at'])) {
                $category['created_at'] = date('Y-m-d H:i:s', strtotime($category['created_at']));
            }
            if (isset($category['updated_at'])) {
                $category['updated_at'] = date('Y-m-d H:i:s', strtotime($category['updated_at']));
            }

            echo json_encode(['success' => true, 'category' => $category]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Category not found']);
        }
        exit; // Ensure no further output
    }

    public function getAvailableTables()
    {
        $bookingModel = new Booking();
        $bookingId = $_GET['booking_id'] ?? null;
        $reservationTime = $_GET['reservation_time'] ?? '';
        $numberOfGuests = (int)($_GET['number_of_guests'] ?? 1);
        $bookingLocation = $_GET['booking_location'] ?? '';

        // If we have a booking ID but no reservation details,
        // retrieve the booking details to get those values
        if (!empty($bookingId) && (empty($reservationTime) || $numberOfGuests <= 1 || empty($bookingLocation))) {
            // Passing null as userId since this is an admin request
            $booking = $bookingModel->getBookingDetails($bookingId, null);
            if ($booking) {
                $reservationTime = $booking['reservation_time'] ?? '';
                $numberOfGuests = (int)($booking['number_of_guests'] ?? 1);
                $bookingLocation = $booking['booking_location'] ?? '';

                // Log for debugging
                error_log("Using data from booking #$bookingId: time=$reservationTime, guests=$numberOfGuests, location=$bookingLocation");
            } else {
                error_log("No booking found with ID: $bookingId");
            }
        }

        if (empty($reservationTime)) {
            echo json_encode(['success' => false, 'message' => 'Reservation time is required']);
            return;
        }

        $availableTables = $bookingModel->getAvailableTables($reservationTime, $numberOfGuests, $bookingLocation);
        error_log("Found " . count($availableTables) . " available tables for time: $reservationTime, guests: $numberOfGuests, location: $bookingLocation");

        echo json_encode(['success' => true, 'tables' => $availableTables]);
    }

    public function createBooking()
    {
        $data = [
            'title' => 'Create New Booking',
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadAdminView('bookings/create', $data);
    }

    public function storeBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            header('Location: /admin/bookings');
            exit;
        }

        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            header('Location: /admin/bookings/create');
            exit;
        }

        // Validate required fields
        $requiredFields = ['customer_name', 'phone_number', 'number_of_guests', 'reservation_date', 'reservation_time'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = 'Please fill in all required fields';
                header('Location: /admin/bookings/create');
                exit;
            }
        }

        // Combine date and time
        $reservationDateTime = $_POST['reservation_date'] . ' ' . $_POST['reservation_time'];

        // Prepare booking data
        $bookingData = [
            'customer_name' => trim($_POST['customer_name']),
            'phone_number' => trim($_POST['phone_number']),
            'email' => trim($_POST['customer_email']) ?: null,
            'number_of_guests' => (int)$_POST['number_of_guests'],
            'reservation_time' => $reservationDateTime,
            'special_requests' => trim($_POST['special_requests']) ?: null,
            'status' => 'pending'
        ];

        // Validate phone number format
        if (!preg_match('/^[0-9\-\+\(\)\s]+$/', $bookingData['phone_number'])) {
            $_SESSION['error'] = 'Invalid phone number format';
            header('Location: /admin/bookings/create');
            exit;
        }

        // Validate email if provided
        if ($bookingData['email'] && !filter_var($bookingData['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header('Location: /admin/bookings/create');
            exit;
        }

        // Validate number of guests
        if ($bookingData['number_of_guests'] < 1 || $bookingData['number_of_guests'] > 20) {
            $_SESSION['error'] = 'Number of guests must be between 1 and 20';
            header('Location: /admin/bookings/create');
            exit;
        }

        // Validate reservation time is in the future
        if (strtotime($reservationDateTime) < time()) {
            $_SESSION['error'] = 'Reservation time must be in the future';
            header('Location: /admin/bookings/create');
            exit;
        }

        try {
            $bookingId = $this->bookingModel->createBooking($bookingData);
            if ($bookingId) {
                $_SESSION['success'] = 'Booking created successfully';
                header('Location: /admin/bookings');
            } else {
                $_SESSION['error'] = 'Failed to create booking. Please try again.';
                header('Location: /admin/bookings/create');
            }
        } catch (Exception $e) {
            error_log("Error creating booking: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while creating the booking';
            header('Location: /admin/bookings/create');
        }
        exit;
    }

    // API Endpoints for Dashboard
    public function apiRecentOrders()
    {
        header('Content-Type: application/json');

        $orderModel = new Order();
        $recentOrders = $orderModel->getRecentOrders(10);

        echo json_encode(['success' => true, 'orders' => $recentOrders]);
    }
    public function editBooking($id)
    {
        if (!$id) {
            $_SESSION['error'] = 'Invalid booking ID';
            header('Location: /admin/bookings');
            exit;
        }

        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingDetails($id);

        if (!$booking) {
            $_SESSION['error'] = 'Booking not found';
            header('Location: /admin/bookings');
            exit;
        }

        // Ensure booking data is an array (debug step)
        if (is_object($booking)) {
            error_log("Warning: getBookingDetails returned object instead of array for booking ID: $id");
            // Convert object to array if needed
            $booking = (array) $booking;
        }

        $data = [
            'title' => 'Edit Booking',
            'booking' => $booking,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadAdminView('bookings/edit', $data);
    }

    public function updateBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            header('Location: /admin/bookings');
            exit;
        }

        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            header('Location: /admin/bookings');
            exit;
        }

        $bookingId = $_POST['booking_id'] ?? null;
        if (!$bookingId) {
            $_SESSION['error'] = 'Invalid booking ID';
            header('Location: /admin/bookings');
            exit;
        }

        // Validate required fields
        $requiredFields = ['customer_name', 'phone_number', 'number_of_guests', 'reservation_date', 'reservation_time'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = 'Please fill in all required fields';
                header("Location: /admin/bookings/edit/$bookingId");
                exit;
            }
        }

        // Combine date and time
        $reservationDateTime = $_POST['reservation_date'] . ' ' . $_POST['reservation_time'];

        // Prepare booking data
        $bookingData = [
            'customer_name' => trim($_POST['customer_name']),
            'phone_number' => trim($_POST['phone_number']),
            'email' => trim($_POST['customer_email']) ?: null,
            'number_of_guests' => (int)$_POST['number_of_guests'],
            'reservation_time' => $reservationDateTime,
            'special_requests' => trim($_POST['special_requests']) ?: null
        ];

        // Validate phone number format
        if (!preg_match('/^[0-9\-\+\(\)\s]+$/', $bookingData['phone_number'])) {
            $_SESSION['error'] = 'Invalid phone number format';
            header("Location: /admin/bookings/edit/$bookingId");
            exit;
        }

        // Validate email if provided
        if ($bookingData['email'] && !filter_var($bookingData['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header("Location: /admin/bookings/edit/$bookingId");
            exit;
        }

        // Validate number of guests
        if ($bookingData['number_of_guests'] < 1 || $bookingData['number_of_guests'] > 20) {
            $_SESSION['error'] = 'Number of guests must be between 1 and 20';
            header("Location: /admin/bookings/edit/$bookingId");
            exit;
        }

        // Validate reservation time is in the future (for future bookings only)
        $currentBooking = $this->bookingModel->getBookingDetails($bookingId);
        if (strtotime($reservationDateTime) < time() && strtotime($currentBooking['reservation_time']) >= time()) {
            $_SESSION['error'] = 'Reservation time cannot be in the past';
            header("Location: /admin/bookings/edit/$bookingId");
            exit;
        }

        try {
            if ($this->bookingModel->updateBooking($bookingId, $bookingData)) {
                $_SESSION['success'] = 'Booking updated successfully';
                header('Location: /admin/bookings');
            } else {
                $_SESSION['error'] = 'Failed to update booking. Please try again.';
                header("Location: /admin/bookings/edit/$bookingId");
            }
        } catch (Exception $e) {
            error_log("Error updating booking: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while updating the booking';
            header("Location: /admin/bookings/edit/$bookingId");
        }
        exit;
    }

    public function apiUpcomingBookings()
    {
        header('Content-Type: application/json');

        $bookingModel = new Booking();
        $upcomingBookings = $bookingModel->getUpcomingBookings(10);

        echo json_encode(['success' => true, 'bookings' => $upcomingBookings]);
    }

    public function apiOrderStats()
    {
        header('Content-Type: application/json');

        $orderModel = new Order();
        $orderStats = $orderModel->getOrderStats();

        echo json_encode(['success' => true, 'stats' => $orderStats]);
    }

    public function apiBookingStats()
    {
        header('Content-Type: application/json');

        $bookingModel = new Booking();
        $bookingStats = $bookingModel->getBookingStats();

        echo json_encode(['success' => true, 'stats' => $bookingStats]);
    }
    public function categories()
    {
        $categoryModel = new Category();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        // Get all categories for filtering
        $allCategories = $categoryModel->getAllWithStats();

        // Apply filters
        if ($search) {
            $allCategories = array_filter($allCategories, function ($category) use ($search) {
                return stripos($category['name'], $search) !== false ||
                    stripos($category['description'], $search) !== false;
            });
        }

        if ($status !== '') {
            $allCategories = array_filter($allCategories, function ($category) use ($status) {
                return ($status === 'active' && $category['is_active'] == 1) ||
                    ($status === 'inactive' && $category['is_active'] == 0);
            });
        }

        $totalCategories = count($allCategories);
        $totalPages = ceil($totalCategories / $limit);

        // Apply pagination to filtered data
        $categories = array_slice($allCategories, $offset, $limit);

        // Get unfiltered data for statistics
        $allCategoriesForStats = $categoryModel->getAllWithStats();
        $stats = [
            'active_categories' => $categoryModel->count('active'),
            'total_foods' => $this->getTotalFoodsInCategories($allCategoriesForStats),
            'empty_categories' => $this->getEmptyCategories($allCategoriesForStats)
        ];

        $popularCategories = $categoryModel->getPopularCategories(5);

        // Calculate statistics for the dashboard cards (based on all categories)
        $activeCategories = count(array_filter($allCategoriesForStats, function ($category) {
            return $category['is_active'] == 1;
        }));

        $foodItems = $this->foodModel->count();
        $popularToday = count(array_filter($allCategoriesForStats, function ($category) {
            // This is a simplified calculation - you can make it more sophisticated
            return isset($category['food_count']) && $category['food_count'] > 0;
        })); // Ensure CSRF token exists
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $data = [
            'title' => 'Category Management',
            'categories' => $categories,
            'stats' => $stats,
            'popularCategories' => $popularCategories,
            'totalCategories' => count($allCategoriesForStats), // Total unfiltered
            'filteredCount' => $totalCategories, // Total filtered
            'activeCategories' => $activeCategories,
            'foodItems' => $foodItems,
            'popularToday' => $popularToday,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'csrf_token' => $_SESSION['csrf_token']
        ];

        $this->loadAdminView('categories/index', $data);
    }
    protected function transformCategoryData($data)
    {
        return [
            'name' => htmlspecialchars(trim($data['name'] ?? '')),
            'description' => htmlspecialchars(trim($data['description'] ?? '')),
            // Removed 'icon' field - column doesn't exist in database
            'is_active' => ($data['status'] ?? 'inactive') === 'active' ? 1 : 0,
            'sort_order' => isset($data['sort_order']) && is_numeric($data['sort_order']) ? (int)$data['sort_order'] : 0,
            // 'parent_id' => isset($data['parent_id']) && is_numeric($data['parent_id']) ? (int)$data['parent_id'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    public function createCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/categories');
                return;
            }

            $categoryModel = new Category();
            $categoryData = $this->transformCategoryData($_POST);

            if (empty($categoryData['name'])) {
                $this->setFlash('error', 'Category name is required.');
                $this->redirect('/admin/categories');
                return;
            }

            if ($categoryModel->create($categoryData)) {
                $this->setFlash('success', 'Category created successfully.');
            } else {
                $this->setFlash('error', 'Failed to create category.');
            }

            $this->redirect('/admin/categories');
            return;
        }

        // If GET request, redirect to categories page
        $this->redirect('/admin/categories');
    }
    public function updateCategory($id = null)
    {
        try {
            error_log("=== updateCategory() method started ===");
            error_log("Request method: " . ($_SERVER['REQUEST_METHOD'] ?? 'unknown'));
            error_log("POST data: " . print_r($_POST, true));
            error_log("URL parameter ID: " . ($id ?? 'null'));

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            if (!$this->validateCSRF()) {
                throw new Exception('Invalid security token');
            }

            // Get category ID from URL parameter, fallback to POST data
            $categoryId = (int)($id ?? $_POST['category_id'] ?? 0);
            error_log("Category ID: " . $categoryId);

            if ($categoryId <= 0) {
                throw new Exception('Invalid category ID');
            }

            $categoryModel = new Category();

            // Check if category exists before updating
            $existingCategory = $categoryModel->findById($categoryId);
            if (!$existingCategory) {
                error_log("Category not found with ID: " . $categoryId);
                throw new Exception('Category not found');
            }
            error_log("Existing category: " . print_r($existingCategory, true));

            $categoryData = $this->transformCategoryData($_POST);
            error_log("Transformed category data: " . print_r($categoryData, true));

            // Do not update created_at field
            unset($categoryData['created_at']);
            error_log("Final category data for update: " . print_r($categoryData, true));

            if (empty($categoryData['name'])) {
                throw new Exception('Category name is required');
            }
            $updateResult = $categoryModel->update($categoryId, $categoryData);
            error_log("Update result: " . ($updateResult ? 'true' : 'false'));
            if ($updateResult) {
                // Verify the update by fetching the updated record
                $updatedCategory = $categoryModel->findById($categoryId);
                error_log("Updated category after save: " . print_r($updatedCategory, true));

                $this->setFlash('success', 'Category updated successfully.');
                $this->redirect('/admin/categories');
                return;
            }

            throw new Exception('Failed to update category - Database operation returned false');
        } catch (Exception $e) {
            error_log("Exception in updateCategory: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());

            $this->setFlash('error', $e->getMessage());
            $this->redirect('/admin/categories/edit/' . $categoryId);
            return;
        }
    }

    public function editCategory($id)
    {
        $categoryModel = new Category();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/categories');
                return;
            }

            $categoryData = [
                'name' => $this->sanitize($_POST['name']),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'sort_order' => !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (empty($categoryData['name'])) {
                $this->setFlash('error', 'Category name is required.');
                $this->redirect('/admin/categories/edit/' . $id);
                return;
            }

            if ($categoryModel->update($id, $categoryData)) {
                $this->setFlash('success', 'Category updated successfully.');
            } else {
                $this->setFlash('error', 'Failed to update category.');
            }

            $this->redirect('/admin/categories');
            return;
        }

        $category = $categoryModel->findById($id);
        if (!$category) {
            $this->setFlash('error', 'Category not found.');
            $this->redirect('/admin/categories');
            return;
        }

        // Get category statistics
        $foodModel = new Food();
        $category['food_count'] = $foodModel->countByCategory($id);

        $data = [
            'title' => 'Edit Category',
            'category' => $category,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadAdminView('categories/edit', $data);
    }
    public function deleteCategory($id = null)
    {
        // Support both POST (form submission) and DELETE (AJAX) methods
        $isAjax = $_SERVER['REQUEST_METHOD'] === 'DELETE' ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
                return;
            }
            $this->setFlash('error', 'Method not allowed.');
            $this->redirect('/admin/categories');
            return;
        }

        if (!$this->validateCSRF()) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
                return;
            }
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/categories');
            return;
        }

        // Get category ID from URL parameter or form data
        $categoryId = (int)($id ?? $_POST['category_id'] ?? 0);

        if ($categoryId <= 0) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid category ID'], 400);
                return;
            }
            $this->setFlash('error', 'Invalid category ID.');
            $this->redirect('/admin/categories');
            return;
        }

        $categoryModel = new Category();
        $foodModel = new Food();

        // Check if category exists
        $category = $categoryModel->findById($categoryId);
        if (!$category) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Category not found'], 404);
                return;
            }
            $this->setFlash('error', 'Category not found.');
            $this->redirect('/admin/categories');
            return;
        }

        // Check if category has foods
        $foodCount = $foodModel->countByCategory($categoryId);
        if ($foodCount > 0) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Cannot delete category that contains food items'], 400);
                return;
            }
            $this->setFlash('error', 'Cannot delete category that contains food items.');
            $this->redirect('/admin/categories');
            return;
        }
        if ($categoryModel->delete($categoryId)) {
            if ($isAjax) {
                $this->jsonResponse(['success' => true, 'message' => 'Category deleted successfully']);
                return;
            }
            $this->setFlash('success', 'Category deleted successfully.');
        } else {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete category'], 500);
                return;
            }
            $this->setFlash('error', 'Failed to delete category.');
        }

        $this->redirect('/admin/categories');
    }

    public function tables()
    {
        $tableModel = new Table();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
    $offset = ($page - 1) * $limit;

    // Get filter parameters
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $location = $_GET['location'] ?? '';

    // Get all tables for filtering
    $allTables = $tableModel->getAllTables();

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

    // Get unfiltered statistics
    $stats = $tableModel->getTableStats();
    $locationStats = $tableModel->getTablesByLocation();

    // Lấy danh sách địa chỉ (bao gồm status)
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT address, status FROM addresses");
    $stmt->execute();
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($addresses as $addr) {
    $normalizedAddr = mb_strtolower(trim(preg_replace('/\s+/', ' ', $addr['address'])), 'UTF-8');
    if ((string)$addr['status'] === '0') {
        // Khóa bàn nếu địa chỉ bị khóa
        $stmt = $db->prepare("UPDATE tables SET is_available = 0 WHERE LOWER(TRIM(REPLACE(location, '  ', ' '))) = :location");
        $stmt->execute([':location' => $normalizedAddr]);
    }
    // } else {
    //     // Mở bàn nếu địa chỉ mở lại
    //     $stmt = $db->prepare("UPDATE tables SET is_available = 1 WHERE LOWER(TRIM(REPLACE(location, '  ', ' '))) = :location");
    //     $stmt->execute([':location' => $normalizedAddr]);
    // }
}
    // echo json_encode(['addresses' => $addresses]); // Debugging output
    $data = [
        'title' => 'Table Management',
        'tables' => $tables,
        'stats' => $stats,
        'locationStats' => $locationStats,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalTables' => $stats['total_tables'] ?? 0,
        'filteredCount' => $totalTables,
        'addresses' => $addresses
    ];
    // echo json_encode($data);
    // print_r($data); // Debugging output


    $this->loadAdminView('tables/index', $data);
}

    public function createTable()
    {
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
                'is_available' => (int)($_POST['is_available'] ?? 1)
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
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, address FROM addresses WHERE status = 1 ORDER BY id DESC");
        $stmt->execute();
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['addresses'] = $addresses;

        $this->loadAdminView('tables/create', $data);
    }

    public function editTable($id)
    {
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
                'is_available' => (int)($_POST['is_available'] ?? 1)
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

          // Lấy danh sách địa chỉ (address) cho dropdown
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id, address FROM addresses WHERE status = 1 ORDER BY id DESC");
    $stmt->execute();
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Edit Table',
            'table' => $table,
            'bookingHistory' => $bookingHistory,
            'csrf_token' => $this->generateCSRF(),
             'addresses' => $addresses
        ];

        $this->loadAdminView('tables/edit', $data);
    }
    public function deleteTable($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        if (!$this->validateCSRF()) {
            // Handle both AJAX and form requests
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
                return;
            } else {
                $this->setFlash('error', 'Invalid security token.');
                $this->redirect('/admin/tables');
                return;
            }
        }

        $tableModel = new Table();        // Check if table exists
        $table = $tableModel->findByField('id', $id);
        if (!$table) {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $this->jsonResponse(['success' => false, 'message' => 'Table not found'], 404);
                return;
            } else {
                $this->setFlash('error', 'Table not found.');
                $this->redirect('/admin/tables');
                return;
            }
        }

        try {
            $result = $tableModel->deleteTable($id);

            if ($result) {
                if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $this->jsonResponse(['success' => true, 'message' => 'Table deleted successfully']);
                } else {
                    $this->setFlash('success', 'Table deleted successfully.');
                    $this->redirect('/admin/tables');
                }
            } else {
                if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $this->jsonResponse(['success' => false, 'message' => 'Cannot delete table. It may have existing bookings.'], 400);
                } else {
                    $this->setFlash('error', 'Cannot delete table. It may have existing bookings.');
                    $this->redirect('/admin/tables');
                }
            }
        } catch (Exception $e) {
            error_log("Table deletion error: " . $e->getMessage());
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete table'], 500);
            } else {
                $this->setFlash('error', 'Failed to delete table.');
                $this->redirect('/admin/tables');
            }
        }
    }

    public function tableUtilization()
    {
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

    public function getTableHistory($tableId)
    {
        header('Content-Type: application/json');

        $tableModel = new Table();
        $limit = (int)($_GET['limit'] ?? 20);
        $history = $tableModel->getTableBookingHistory($tableId, $limit);

        // Format the history data for display
        $formattedHistory = array_map(function ($booking) {
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

    public function checkTableAvailability($tableId)
    {
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

    public function toggleTableStatus()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            return;
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON data'
            ]);
            return;
        }

        $tableId = $input['table_id'] ?? null;
        $isAvailable = $input['is_available'] ?? null;

        if (!$tableId || $isAvailable === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Table ID and availability status are required'
            ]);
            return;
        }

        $tableModel = new Table();

        // Update table status
        $success = $tableModel->updateTable($tableId, [
            'is_available' => (int)$isAvailable
        ]);

        if ($success) {
            $statusText = $isAvailable ? 'available' : 'unavailable';
            echo json_encode([
                'success' => true,
                'message' => "Table status updated to {$statusText} successfully"
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update table status'
            ]);
        }
    }

    private function exportBookingsCSV($bookings)
    {
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

    public function exportCategories()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        $categoryModel = new Category();
        $foodModel = new Food();

        // Get all categories with stats
        $categories = $categoryModel->getAllWithStats();

        // Add food count for each category
        foreach ($categories as &$category) {
            $category['food_count'] = $foodModel->countByCategory($category['id']);
        }

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="categories_export_' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create file pointer for output
        $output = fopen('php://output', 'w');

        // Add CSV header
        fputcsv($output, [
            'ID',
            'Name',
            'Description',
            'Status',
            'Sort Order',
            'Food Items',
            'Created Date',
            'Updated Date'
        ]);

        // Add data rows
        foreach ($categories as $category) {
            fputcsv($output, [
                $category['id'],
                $category['name'],
                $category['description'] ?? '',
                ($category['is_active'] ?? 0) == 1 ? 'Active' : 'Inactive',
                $category['sort_order'] ?? 0,
                $category['food_count'] ?? 0,
                isset($category['created_at']) ? date('Y-m-d H:i:s', strtotime($category['created_at'])) : '',
                isset($category['updated_at']) ? date('Y-m-d H:i:s', strtotime($category['updated_at'])) : ''
            ]);
        }

        fclose($output);
        exit;
    }

    // PAYMENT MANAGEMENT
    public function payments()
    {
        require_once __DIR__ . '/../models/Payment.php';
        $paymentModel = new Payment();

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get filters
        $filters = [
            'status' => $_GET['status'] ?? '',
            'payment_method' => $_GET['payment_method'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Get payments with pagination
        $payments = $paymentModel->getAllPaymentsWithOrders($limit, $offset, $filters);
        $totalPayments = $paymentModel->countPayments($filters);
        $totalPages = ceil($totalPayments / $limit);

        // Get payment statistics
        $stats = $paymentModel->getPaymentStats();

        $data = [
            'title' => 'Payment Management',
            'payments' => $payments,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPayments' => $totalPayments,
            'stats' => $stats,
            'filters' => $filters,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadAdminView('payments/index', $data);
    }

    public function paymentDetails($id)
    {
        require_once __DIR__ . '/../models/Payment.php';
        $paymentModel = new Payment();

        $payment = $paymentModel->findById($id);
        if (!$payment) {
            $this->jsonResponse(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }

        // Get order details
        $orderModel = new Order();
        $order = $orderModel->findById($payment['order_id']);

        $html = $this->renderPaymentDetailsHtml($payment, $order);

        $this->jsonResponse(['success' => true, 'html' => $html]);
    }

    private function renderPaymentDetailsHtml($payment, $order)
    {
        ob_start();
?>
        <div class="row">
            <div class="col-md-6">
                <h6>Thông Tin Thanh Toán</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID Thanh Toán:</strong></td>
                        <td><?= $payment['id'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Mã Giao Dịch VNPay:</strong></td>
                        <td><?= htmlspecialchars($payment['vnp_txn_ref']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Mã Giao Dịch Ngân Hàng:</strong></td>
                        <td><?= htmlspecialchars($payment['vnp_transaction_no'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Số Tiền:</strong></td>
                        <td><strong><?= number_format(($payment['vnp_amount'] ?? 0) / 100, 0, ',', '.') ?>đ</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Ngân Hàng:</strong></td>
                        <td><?= htmlspecialchars($payment['vnp_bank_code'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Trạng Thái:</strong></td>
                        <td>
                            <?php
                            require_once __DIR__ . '/../helpers/booking_trend_helper.php';

                            $statusClass = [
                                'pending' => 'warning',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'cancelled' => 'secondary'
                            ];
                            $status = $payment['payment_status'] ?? 'pending';
                            ?>
                            <span class="badge bg-<?= $statusClass[$status] ?? 'secondary' ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Thông Tin Đơn Hàng</h6>
                <?php
                require_once __DIR__ . '/../helpers/booking_trend_helper.php';
                if ($order): ?>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Mã Đơn Hàng:</strong></td>
                            <td>#<?= $order['order_number'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Khách Hàng:</strong></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?= htmlspecialchars($order['customer_email']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Điện Thoại:</strong></td>
                            <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tổng Tiền Đơn Hàng:</strong></td>
                            <td><strong><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Trạng Thái Đơn Hàng:</strong></td>
                            <td><?= ucfirst($order['status']) ?></td>
                        </tr>
                    </table>
                <?php
                    require_once __DIR__ . '/../helpers/booking_trend_helper.php';
                else: ?>
                    <p class="text-muted">Không tìm thấy thông tin đơn hàng</p>
                <?php
                    require_once __DIR__ . '/../helpers/booking_trend_helper.php';
                endif; ?>
            </div>
        </div>

        <?php
        require_once __DIR__ . '/../helpers/booking_trend_helper.php';
        if (!empty($payment['payment_data'])): ?>
            <div class="mt-3">
                <h6>Dữ Liệu Raw VNPay</h6>
                <pre class="bg-light p-3 rounded small"><?= htmlspecialchars(json_encode(json_decode($payment['payment_data']), JSON_PRETTY_PRINT)) ?></pre>
            </div>
        <?php
            require_once __DIR__ . '/../helpers/booking_trend_helper.php';
        endif; ?>
<?php
        require_once __DIR__ . '/../helpers/booking_trend_helper.php';

        return ob_get_clean();
    }

    public function cancelPayment($id)
    {
        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        require_once __DIR__ . '/../models/Payment.php';
        $paymentModel = new Payment();

        $payment = $paymentModel->findById($id);
        if (!$payment) {
            $this->jsonResponse(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }

        if (!in_array($payment['payment_status'], ['pending', 'failed'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Cannot cancel this payment'], 400);
            return;
        }

        if ($paymentModel->update($id, ['payment_status' => 'cancelled'])) {
            $this->jsonResponse(['success' => true, 'message' => 'Payment cancelled successfully']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to cancel payment'], 500);
        }
    }

    public function exportPayments()
    {
        require_once __DIR__ . '/../models/Payment.php';
        $paymentModel = new Payment();

        // Get filters from request
        $filters = [
            'status' => $_GET['status'] ?? '',
            'payment_method' => $_GET['payment_method'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Get all payments with filters (no limit for export)
        $payments = $paymentModel->getAllPaymentsWithOrders(999999, 0, $filters);

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=payments_export_' . date('Y-m-d_H-i-s') . '.csv');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // Add CSV headers
        fputcsv($output, [
            'ID',
            'Mã Đơn Hàng',
            'Khách Hàng',
            'Email',
            'Điện Thoại',
            'Mã GD VNPay',
            'Mã GD Ngân Hàng',
            'Số Tiền (VND)',
            'Phương Thức',
            'Trạng Thái',
            'Ngân Hàng',
            'Ngày Tạo',
            'Ngày Hoàn Thành'
        ]);

        // Add data rows
        foreach ($payments as $payment) {
            fputcsv($output, [
                $payment['id'],
                $payment['order_number'] ?? 'N/A',
                $payment['customer_name'] ?? 'N/A',
                $payment['customer_email'] ?? 'N/A',
                $payment['customer_phone'] ?? 'N/A',
                $payment['vnp_txn_ref'],
                $payment['vnp_transaction_no'] ?? 'N/A',
                number_format(($payment['vnp_amount'] ?? 0) / 100, 0, ',', '.'),
                strtoupper($payment['payment_method']),
                ucfirst($payment['payment_status']),
                $payment['vnp_bank_code'] ?? 'N/A',
                date('d/m/Y H:i:s', strtotime($payment['created_at'])),
                $payment['completed_at'] ? date('d/m/Y H:i:s', strtotime($payment['completed_at'])) : 'N/A'
            ]);
        }

        fclose($output);
    }

    private function getTotalFoodsInCategories($categories)
    {
        return array_sum(array_column($categories, 'food_count'));
    }

    private function getEmptyCategories($categories)
    {
        return count(array_filter($categories, function ($cat) {
            return ($cat['food_count'] ?? 0) == 0;
        }));
    }
    private function getDashboardStats()
    {
        $userModel = new User();
        $foodModel = new Food();
        $orderModel = new Order();
        $bookingModel = new Booking();

        // Get basic counts
        $totalOrders = $orderModel->count();
        $totalUsers = $userModel->count();
        $totalBookings = $bookingModel->count();

        // Get booking status counts
        $confirmedBookings = $bookingModel->count('confirmed');
        $pendingBookings = $bookingModel->count('pending');
        $cancelledBookings = $bookingModel->count('cancelled');
        $activeBookings = $confirmedBookings + $pendingBookings;        // Get revenue data
        $orderStats = $orderModel->getOrderStats();
        $monthlyRevenue = $orderModel->getMonthlyRevenue();
        $monthlyRevenueData = $orderModel->getMonthlyRevenueData();
        // Get recent data
        $recentOrders = $orderModel->getRecentOrdersWithCustomer(5);
        $recentBookings = $bookingModel->getRecentBookingsWithCustomer(5);

        // Lấy kết nối PDO trực tiếp
        $pdoBooking = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

        return [
            'total_users' => $totalUsers,
            'total_foods' => $foodModel->count(),
            'total_orders' => $totalOrders,
            'total_bookings' => $totalBookings,
            'active_bookings' => $activeBookings,
            'confirmed_bookings' => $confirmedBookings,
            'pending_bookings' => $pendingBookings,
            'cancelled_bookings' => $cancelledBookings,
            'pending_orders' => $orderModel->count('pending'),
            'today_orders' => $orderStats['today_orders'],
            'today_revenue' => $orderStats['today_revenue'],
            'monthly_revenue' => $monthlyRevenue,
            'monthly_revenue_data' => $monthlyRevenueData,
            'booking_trend_data' => BookingTrendHelper::getBookingTrendData($pdoBooking),
            'recent_orders' => $recentOrders,
            'recent_bookings' => $recentBookings
        ];
    }

    public function getSubcategories($categoryId)
    {
        $categoryModel = new Category();
        $subcategories = $categoryModel->getSubcategories($categoryId);

        header('Content-Type: application/json');
        echo json_encode($subcategories);
    }

    // Order Details for Modal
    public function orderDetails($id)
    {
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
    public function printOrder($id)
    {
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
    public function exportOrdersCSV()
    {
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
    public function ordersFiltered()
    {
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
        $this->loadAdminView('orders/index', $data);
    }

    // Get Order for Editing
    public function getOrder($id)
    {
        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            $this->jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
            return;
        }

        $this->jsonResponse(['success' => true, 'order' => $order]);
    }

    // Update Order
    public function updateOrder($id)
    {
        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        $orderModel = new Order();

        // Build update data from POST
        $updateData = [];

        if (isset($_POST['customer_name'])) {
            $updateData['customer_name'] = $this->sanitize($_POST['customer_name']);
        }
        if (isset($_POST['customer_email'])) {
            $updateData['customer_email'] = $this->sanitize($_POST['customer_email']);
        }
        if (isset($_POST['customer_phone'])) {
            $updateData['customer_phone'] = $this->sanitize($_POST['customer_phone']);
        }
        if (isset($_POST['status'])) {
            $updateData['status'] = $this->sanitize($_POST['status']);
        }
        if (isset($_POST['payment_method'])) {
            $updateData['payment_method'] = $this->sanitize($_POST['payment_method']);
        }
        if (isset($_POST['total_amount'])) {
            $updateData['total_amount'] = floatval($_POST['total_amount']);
        }
        if (isset($_POST['delivery_address'])) {
            $updateData['delivery_address'] = $this->sanitize($_POST['delivery_address']);
        }
        if (isset($_POST['order_notes'])) {
            $updateData['order_notes'] = $this->sanitize($_POST['order_notes']);
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');

        try {
            // Update order in database
            $sql = "UPDATE orders SET ";
            $setParts = [];
            $params = [];

            foreach ($updateData as $key => $value) {
                $setParts[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            $sql .= implode(', ', $setParts) . " WHERE id = :id";
            $params[':id'] = $id;

            $result = $orderModel->update($id, $updateData);

            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Order updated successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update order'], 500);
            }
        } catch (Exception $e) {
            error_log("Order update error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Database error occurred'], 500);
        }
    }

    // Duplicate Order
    public function duplicateOrder($id)
    {
        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        $orderModel = new Order();
        $originalOrder = $orderModel->getOrderWithItems($id);

        if (!$originalOrder) {
            $this->jsonResponse(['success' => false, 'message' => 'Original order not found'], 404);
            return;
        }

        try {
            // Create new order data based on original
            $newOrderData = [
                'user_id' => $originalOrder['user_id'],
                'customer_name' => $originalOrder['customer_name'],
                'customer_email' => $originalOrder['customer_email'],
                'customer_phone' => $originalOrder['customer_phone'],
                'total_amount' => $originalOrder['total_amount'],
                'status' => 'pending', // Reset status for new order
                'payment_method' => $originalOrder['payment_method'],
                'delivery_address' => $originalOrder['delivery_address'],
                'order_notes' => 'Duplicate of Order #' . $id . ($originalOrder['order_notes'] ? "\n\n" . $originalOrder['order_notes'] : ''),
                'subtotal' => $originalOrder['subtotal'],
                'delivery_fee' => $originalOrder['delivery_fee'],
                'service_fee' => $originalOrder['service_fee'],
                'order_type' => $originalOrder['order_type'] ?? 'delivery'
            ];

            // Duplicate order items if they exist
            $orderItems = [];
            if (!empty($originalOrder['items'])) {
                foreach ($originalOrder['items'] as $item) {
                    $orderItems[] = [
                        'food_item_id' => $item['food_item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['unit_price'] ?? 0
                    ];
                }
                $newOrderData['items'] = $orderItems;
            }

            $newOrderId = $orderModel->createOrder($newOrderData);

            if ($newOrderId) {
                $this->jsonResponse(['success' => true, 'message' => 'Order duplicated successfully', 'new_order_id' => $newOrderId]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to duplicate order'], 500);
            }
        } catch (Exception $e) {
            error_log("Order duplication error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to duplicate order'], 500);
        }
    }

    // Send Order Email
    public function sendOrderEmail($id)
    {
        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            $this->jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
            return;
        }

        if (empty($order['customer_email'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No email address found for this order'], 400);
            return;
        }

        try {
            // Prepare email content
            $subject = "Order Confirmation #" . str_pad($id, 6, '0', STR_PAD_LEFT) . " - " . SITE_NAME;

            $message = "Dear " . htmlspecialchars($order['customer_name']) . ",\n\n";
            $message .= "Thank you for your order! Here are your order details:\n\n";
            $message .= "Order ID: #" . str_pad($id, 6, '0', STR_PAD_LEFT) . "\n";
            $message .= "Order Date: " . date('M j, Y \a\t g:i A', strtotime($order['created_at'])) . "\n";
            $message .= "Status: " . ucfirst($order['status']) . "\n";
            $message .= "Total Amount: $" . number_format($order['total_amount'], 2) . "\n\n";

            if (!empty($order['items'])) {
                $message .= "Order Items:\n";
                foreach ($order['items'] as $item) {
                    $message .= "- " . $item['food_name'] . " x" . $item['quantity'] . " = $" . number_format(($item['unit_price'] ?? 0) * $item['quantity'], 2) . "\n";
                }
                $message .= "\n";
            }

            if (!empty($order['delivery_address'])) {
                $message .= "Delivery Address: " . $order['delivery_address'] . "\n\n";
            }

            if (!empty($order['order_notes'])) {
                $message .= "Order Notes: " . $order['order_notes'] . "\n\n";
            }

            $message .= "Thank you for choosing " . SITE_NAME . "!\n\n";
            $message .= "Best regards,\n";
            $message .= "The " . SITE_NAME . " Team";

            $headers = [
                'From: ' . SITE_NAME . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
                'Reply-To: noreply@' . $_SERVER['HTTP_HOST'],
                'Content-Type: text/plain; charset=UTF-8'
            ];

            // Send email
            if (mail($order['customer_email'], $subject, $message, implode("\r\n", $headers))) {
                $this->jsonResponse(['success' => true, 'message' => 'Email sent successfully to ' . $order['customer_email']]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to send email'], 500);
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to send email'], 500);
        }
    }

    // Delete Order
    public function deleteOrder($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        $orderModel = new Order();

        // Check if order exists
        $order = $orderModel->findById($id);
        if (!$order) {
            $this->jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
            return;
        }

        // Check if order can be deleted (only pending or cancelled orders)
        if ($order['status'] === 'delivered' || $order['status'] === 'completed') {
            $this->jsonResponse(['success' => false, 'message' => 'Cannot delete completed or delivered orders'], 400);
            return;
        }
        try {
            $result = $orderModel->deleteOrder($id);

            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Order deleted successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete order'], 500);
            }
        } catch (Exception $e) {
            error_log("Order deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete order'], 500);
        }
    }

    // Send Booking Confirmation Email
    public function sendConfirmationEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        // Handle both JSON and form-encoded data
        $bookingId = 0;

        if ($_SERVER['CONTENT_TYPE'] === 'application/json' || strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            // Handle JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = (int)($input['booking_id'] ?? 0);
        } else {
            // Handle form-encoded input or URL parameter
            $bookingId = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);
        }

        if (!$bookingId) {
            $this->jsonResponse(['success' => false, 'message' => 'Booking ID is required'], 400);
            return;
        }

        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingDetails($bookingId);

        if (!$booking) {
            $this->jsonResponse(['success' => false, 'message' => 'Booking not found'], 404);
            return;
        }

        if (empty($booking['email'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No email address found for this booking'], 400);
            return;
        }

        try {
            // Prepare email content
            $subject = "Booking Confirmation #" . str_pad($bookingId, 6, '0', STR_PAD_LEFT) . " - " . SITE_NAME;

            $message = "Dear " . htmlspecialchars($booking['name']) . ",\n\n";
            $message .= "Thank you for your booking! Here are your booking details:\n\n";
            $message .= "Booking ID: #" . str_pad($bookingId, 6, '0', STR_PAD_LEFT) . "\n";
            $message .= "Booking Date: " . date('M j, Y \a\t g:i A', strtotime($booking['reservation_time'])) . "\n";
            $message .= "Number of Guests: " . $booking['number_of_guests'] . "\n";
            $message .= "Status: " . ucfirst($booking['status']) . "\n";

            if (!empty($booking['table_number'])) {
                $message .= "Table Number: " . $booking['table_number'] . "\n";
            }

            if (!empty($booking['special_requests'])) {
                $message .= "Special Requests: " . $booking['special_requests'] . "\n";
            }

            $message .= "\nPhone: " . $booking['phone'] . "\n\n";
            $message .= "Please arrive on time for your reservation. If you need to cancel or modify your booking, please contact us as soon as possible.\n\n";
            $message .= "Thank you for choosing " . SITE_NAME . "!\n\n";
            $message .= "Best regards,\n";
            $message .= "The " . SITE_NAME . " Team";

            $headers = [
                'From: ' . SITE_NAME . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
                'Reply-To: noreply@' . $_SERVER['HTTP_HOST'],
                'Content-Type: text/plain; charset=UTF-8'
            ];

            // Send email
            if (mail($booking['email'], $subject, $message, implode("\r\n", $headers))) {
                $this->jsonResponse(['success' => true, 'message' => 'Confirmation email sent successfully to ' . $booking['email']]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to send confirmation email'], 500);
            }
        } catch (Exception $e) {
            error_log("Booking email sending error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to send confirmation email'], 500);
        }
    }

    // Bulk Update Booking Status
    public function bulkUpdateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        // Handle both JSON and form-encoded data
        $bookingIds = [];
        $status = '';

        if ($_SERVER['CONTENT_TYPE'] === 'application/json' || strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            // Handle JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingIds = $input['booking_ids'] ?? [];
            $status = $this->sanitize($input['status'] ?? '');
        } else {
            // Handle form-encoded input
            $bookingIdsString = $_POST['booking_ids'] ?? '';
            $bookingIds = !empty($bookingIdsString) ? explode(',', $bookingIdsString) : [];
            $status = $this->sanitize($_POST['status'] ?? '');
        }

        if (empty($bookingIds) || !is_array($bookingIds)) {
            $this->jsonResponse(['success' => false, 'message' => 'Booking IDs are required'], 400);
            return;
        }

        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'];
        if (!in_array($status, $allowedStatuses)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
            return;
        }

        $bookingModel = new Booking();
        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($bookingIds as $bookingId) {
            $bookingId = (int)$bookingId;
            if ($bookingId <= 0) {
                $failedCount++;
                $errors[] = "Invalid booking ID: $bookingId";
                continue;
            }
            try {
                if ($bookingModel->updateBookingStatus($bookingId, $status)) {
                    $successCount++;
                } else {
                    $failedCount++;
                    $errors[] = "Failed to update booking ID: $bookingId";
                }
            } catch (Exception $e) {
                $failedCount++;
                $errors[] = "Error updating booking ID $bookingId: " . $e->getMessage();
                error_log("Bulk update error for booking $bookingId: " . $e->getMessage());
            }
        }

        $message = "Bulk update completed: $successCount updated";
        if ($failedCount > 0) {
            $message .= ", $failedCount failed";
        }

        $this->jsonResponse([
            'success' => $successCount > 0,
            'message' => $message,
            'details' => [
                'total' => count($bookingIds),
                'success' => $successCount,
                'failed' => $failedCount,
                'errors' => $errors
            ]
        ]);
    }

    // =============================================================================
    // LOGS MANAGEMENT FUNCTIONS
    // =============================================================================

    public function logs()
    {
        $logFiles = $this->getLogFiles();
        $recentLogs = $this->getRecentLogEntries(100);

        $data = [
            'title' => 'System Logs',
            'logFiles' => $logFiles,
            'recentLogs' => $recentLogs,
            'csrf_token' => $this->generateCSRF()
        ];

        $this->loadAdminView('logs/index', $data);
    }

    public function viewLog($logFile)
    {
        $logPath = $this->getLogPath($logFile);

        if (!$logPath || !file_exists($logPath)) {
            $this->setFlash('error', 'Log file not found.');
            $this->redirect('/admin/logs');
            return;
        }

        $page = (int)($_GET['page'] ?? 1);
        $linesPerPage = 100;
        $searchTerm = $_GET['search'] ?? '';
        $level = $_GET['level'] ?? '';

        $logData = $this->readLogFile($logPath, $page, $linesPerPage, $searchTerm, $level);

        $data = [
            'title' => 'View Log - ' . basename($logFile),
            'logFile' => $logFile,
            'logData' => $logData,
            'currentPage' => $page,
            'searchTerm' => $searchTerm,
            'level' => $level
        ];

        $this->loadAdminView('logs/view', $data);
    }

    public function downloadLog($logFile)
    {
        $logPath = $this->getLogPath($logFile);

        if (!$logPath || !file_exists($logPath)) {
            $this->setFlash('error', 'Log file not found.');
            $this->redirect('/admin/logs');
            return;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($logFile) . '"');
        header('Content-Length: ' . filesize($logPath));
        readfile($logPath);
        exit;
    }

    public function clearLog($logFile)
    {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/logs');
            return;
        }

        $logPath = $this->getLogPath($logFile);

        if (!$logPath || !file_exists($logPath)) {
            $this->setFlash('error', 'Log file not found.');
            $this->redirect('/admin/logs');
            return;
        }

        if (file_put_contents($logPath, '')) {
            $this->setFlash('success', 'Log file cleared successfully.');
            error_log("Log file {$logFile} cleared by admin user {$_SESSION['user_id']}");
        } else {
            $this->setFlash('error', 'Failed to clear log file.');
        }

        $this->redirect('/admin/logs');
    }

    private function getLogFiles()
    {
        $logFiles = [];
        $logDirectory = __DIR__ . '/../';

        // Common log file patterns
        $patterns = [
            '*.log',
            'logs/*.log',
            'storage/logs/*.log',
            'var/log/*.log'
        ];

        foreach ($patterns as $pattern) {
            $files = glob($logDirectory . $pattern);
            foreach ($files as $file) {
                if (is_file($file) && is_readable($file)) {
                    $relativePath = str_replace($logDirectory, '', $file);
                    $logFiles[] = [
                        'name' => basename($file),
                        'path' => $relativePath,
                        'size' => filesize($file),
                        'modified' => filemtime($file),
                        'readable' => is_readable($file),
                        'writable' => is_writable($file)
                    ];
                }
            }
        }

        // Sort by modification date (newest first)
        usort($logFiles, function ($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $logFiles;
    }

    private function getLogPath($logFile)
    {
        $basePath = __DIR__ . '/../';
        $sanitizedFile = basename($logFile);

        // Security check - only allow .log files
        if (!preg_match('/\.log$/', $sanitizedFile)) {
            return null;
        }

        $possiblePaths = [
            $basePath . $sanitizedFile,
            $basePath . 'logs/' . $sanitizedFile,
            $basePath . 'storage/logs/' . $sanitizedFile,
            $basePath . 'var/log/' . $sanitizedFile
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                // Security check - ensure file is within allowed directories
                $realPath = realpath($path);
                $realBasePath = realpath($basePath);

                if ($realPath && $realBasePath && strpos($realPath, $realBasePath) === 0) {
                    return $realPath;
                }
            }
        }

        return null;
    }

    private function readLogFile($logPath, $page = 1, $linesPerPage = 100, $searchTerm = '', $level = '')
    {
        $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $totalLines = count($lines);

        // Filter lines if search term or level specified
        if ($searchTerm || $level) {
            $filteredLines = [];
            foreach ($lines as $line) {
                $matchesSearch = empty($searchTerm) || stripos($line, $searchTerm) !== false;
                $matchesLevel = empty($level) || $this->logLineMatchesLevel($line, $level);

                if ($matchesSearch && $matchesLevel) {
                    $filteredLines[] = $line;
                }
            }
            $lines = $filteredLines;
            $totalLines = count($lines);
        }

        // Reverse array to show newest entries first
        $lines = array_reverse($lines);

        $totalPages = ceil($totalLines / $linesPerPage);
        $offset = ($page - 1) * $linesPerPage;
        $pageLines = array_slice($lines, $offset, $linesPerPage);

        // Parse log lines for better display
        $parsedLines = [];
        foreach ($pageLines as $index => $line) {
            $parsedLines[] = $this->parseLogLine($line, $offset + $index + 1);
        }

        return [
            'lines' => $parsedLines,
            'totalLines' => $totalLines,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'linesPerPage' => $linesPerPage
        ];
    }

    private function logLineMatchesLevel($line, $level)
    {
        $levelPatterns = [
            'ERROR' => '/\[ERROR\]|\bERROR\b|Fatal error|Parse error/i',
            'WARNING' => '/\[WARNING\]|\[WARN\]|\bWARNING\b|\bWARN\b/i',
            'INFO' => '/\[INFO\]|\bINFO\b/i',
            'DEBUG' => '/\[DEBUG\]|\bDEBUG\b/i'
        ];

        return isset($levelPatterns[$level]) && preg_match($levelPatterns[$level], $line);
    }

    private function parseLogLine($line, $lineNumber)
    {
        // Try to parse common log formats
        $parsed = [
            'number' => $lineNumber,
            'raw' => $line,
            'timestamp' => null,
            'level' => 'INFO',
            'message' => $line,
            'context' => null
        ];

        // Parse timestamp (various formats)
        if (preg_match('/\[([\d\-\s:]+)\]/', $line, $matches)) {
            $parsed['timestamp'] = $matches[1];
        } elseif (preg_match('/^(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})/', $line, $matches)) {
            $parsed['timestamp'] = $matches[1];
        }

        // Parse log level
        if (preg_match('/\[(ERROR|WARNING|WARN|INFO|DEBUG)\]/i', $line, $matches)) {
            $parsed['level'] = strtoupper($matches[1]);
        } elseif (preg_match('/\b(ERROR|WARNING|WARN|INFO|DEBUG)\b/i', $line, $matches)) {
            $parsed['level'] = strtoupper($matches[1]);
        } elseif (strpos($line, 'Fatal error') !== false || strpos($line, 'Parse error') !== false) {
            $parsed['level'] = 'ERROR';
        }

        // Extract main message (remove timestamp and level)
        $message = $line;
        $message = preg_replace('/^\[[\d\-\s:]+\]\s*/', '', $message);
        $message = preg_replace('/^\[(ERROR|WARNING|WARN|INFO|DEBUG)\]\s*/i', '', $message);
        $parsed['message'] = trim($message);

        return $parsed;
    }

    private function getRecentLogEntries($limit = 50)
    {
        $allEntries = [];
        $logFiles = $this->getLogFiles();

        foreach ($logFiles as $logFile) {
            $logPath = $this->getLogPath($logFile['path']);
            if ($logPath && file_exists($logPath)) {
                $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $recentLines = array_slice($lines, -$limit);

                foreach ($recentLines as $line) {
                    $parsed = $this->parseLogLine($line, 0);
                    $parsed['file'] = $logFile['name'];
                    $allEntries[] = $parsed;
                }
            }
        }

        // Sort by timestamp (newest first)
        usort($allEntries, function ($a, $b) {
            if ($a['timestamp'] && $b['timestamp']) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            }
            return 0;
        });

        return array_slice($allEntries, 0, $limit);
    }

    public function bulkUpdateCategoryStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$this->validateCSRF()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            return;
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        $categoryIds = $input['category_ids'] ?? [];
        $status = $input['status'] ?? '';

        if (empty($categoryIds) || !is_array($categoryIds)) {
            $this->jsonResponse(['success' => false, 'message' => 'No categories selected'], 400);
            return;
        }

        if (!in_array($status, ['active', 'inactive'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
            return;
        }

        $categoryModel = new Category();
        $isActive = $status === 'active' ? 1 : 0;
        $updatedCount = 0;

        foreach ($categoryIds as $categoryId) {
            $categoryId = (int)$categoryId;
            if ($categoryId > 0) {
                $updateData = [
                    'is_active' => $isActive,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($categoryModel->update($categoryId, $updateData)) {
                    $updatedCount++;
                }
            }
        }

        if ($updatedCount > 0) {
            $statusText = $status === 'active' ? 'activated' : 'deactivated';
            $this->jsonResponse([
                'success' => true,
                'message' => "$updatedCount categories $statusText successfully"
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'No categories were updated'], 500);
        }
    }

    // ==========================================
    // INTERNAL MESSAGES
    // ==========================================

    /**
     * Hiển thị danh sách thông báo nội bộ đã nhận
     */
    public function internalMessages()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $messages = $this->internalMessageModel->getReceivedMessages($_SESSION['user_id'], $limit, $offset);
        $unreadCount = $this->internalMessageModel->getUnreadCount($_SESSION['user_id']);

        $data = [
            'title' => 'Thông Báo Nội Bộ',
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'page' => $page,
            'limit' => $limit
        ];

        $this->loadAdminView('internal_messages/received', $data);
    }

    /**
     * Xem chi tiết thông báo nội bộ
     */
    public function viewInternalMessage($messageId)
    {
        $message = $this->internalMessageModel->getMessageDetail($messageId, $_SESSION['user_id']);

        if (!$message) {
            $this->setFlash('error', 'Thông báo không tồn tại');
            $this->redirect('/admin/internal-messages');
        }

        // Đánh dấu đã đọc
        $this->internalMessageModel->markAsRead($messageId, $_SESSION['user_id']);

        $data = [
            'title' => 'Chi Tiết Thông Báo',
            'message' => $message
        ];

        $this->loadAdminView('internal_messages/view', $data);
    }

    /**
     * API: Lấy số thông báo chưa đọc
     */
    public function getUnreadInternalMessageCount()
    {
        $count = $this->internalMessageModel->getUnreadCount($_SESSION['user_id']);
        $this->jsonResponse(['count' => $count]);
    }

    /**
     * Download file đính kèm
     */
    public function downloadInternalMessageAttachment($messageId)
    {
        $message = $this->internalMessageModel->getMessageDetail($messageId, $_SESSION['user_id']);

        if (!$message || !$message['attachment_path']) {
            $this->setFlash('error', 'File không tồn tại');
            $this->redirect('/admin/internal-messages');
        }

        $filepath = $message['attachment_path'];
        $filename = $message['attachment_name'];

        if (!file_exists($filepath)) {
            $this->setFlash('error', 'File không tồn tại');
            $this->redirect('/admin/internal-messages');
        }

        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        readfile($filepath);
        exit;
    }
}
