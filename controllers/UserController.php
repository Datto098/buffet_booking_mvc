<?php
/**
 * User Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Booking.php';

class UserController extends BaseController {
    private $userModel;
    private $orderModel;
    private $bookingModel;

    public function __construct() {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->bookingModel = new Booking();
    }

    public function index() {
        $this->profile();
    }

    public function profile() {
        $this->requireLogin();

        $user = $this->userModel->findById($_SESSION['user_id']);
        $active_tab = $_GET['tab'] ?? 'profile-info'; 

        $data = [
            'title' => 'Thông Tin Cá Nhân - ' . SITE_NAME,
            'user' => $user,
            'active_tab' => $active_tab
        ];
        $this->loadView('customer/user/profile', $data);
    }

    public function edit() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleProfileUpdate();
        } else {
            $user = $this->userModel->findById($_SESSION['user_id']);

            $data = [
                'title' => 'Chỉnh Sửa Thông Tin - ' . SITE_NAME,
                'user' => $user
            ];

            $this->loadView('customer/user/edit', $data);
        }
    }

    public function changePassword() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePasswordChange(); // đã redirect và exit
        } else {
            // Nếu cần, load view đổi mật khẩu, hoặc load profile với tab security
            $user = $this->userModel->findById($_SESSION['user_id']);
            $data = [
                'title' => 'Đổi Mật Khẩu - ' . SITE_NAME,
                'user' => $user,
                'active_tab' => 'security'
            ];
            $this->loadView('customer/user/profile', $data);
        }
    }

    public function dashboard() {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];

        // Get comprehensive user data
        $user = $this->userModel->findById($userId);

        // Get statistics
        $stats = [
            'totalOrders' => $this->orderModel->countUserOrders($userId),
            'totalBookings' => $this->bookingModel->countUserBookings($userId),
            'totalSpent' => $this->orderModel->getUserTotalSpent($userId),
            'pendingOrders' => $this->orderModel->countUserOrdersByStatus($userId, 'pending'),
            'confirmedBookings' => $this->bookingModel->countUserBookingsByStatus($userId, 'confirmed')
        ];

        // Get recent activities
        $recentOrders = $this->orderModel->getUserOrders($userId, 5);
        $recentBookings = $this->bookingModel->getUserBookings($userId, 5);

        // Get monthly spending data for chart
        $monthlySpending = $this->orderModel->getUserMonthlySpending($userId, 6);

        $data = [
            'title' => 'Bảng Điều Khiển - ' . SITE_NAME,
            'user' => $user,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentBookings' => $recentBookings,
            'monthlySpending' => $monthlySpending
        ];

        $this->loadView('customer/user/dashboard', $data);
    }

    public function orders() {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $page = intval($_GET['p'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $orders = $this->orderModel->getUserOrders($userId, $limit, $offset);
        $totalOrders = $this->orderModel->countUserOrders($userId);
        $totalPages = ceil($totalOrders / $limit);

        $data = [
            'title' => 'Đơn Hàng Của Tôi - ' . SITE_NAME,
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders
        ];

        $this->loadView('customer/user/orders', $data);
    }

    public function bookings() {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $page = intval($_GET['p'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $bookings = $this->bookingModel->getUserBookings($userId, $limit, $offset);
        $totalBookings = $this->bookingModel->countUserBookings($userId);
        $totalPages = ceil($totalBookings / $limit);

        $data = [
            'title' => 'Lịch Sử Đặt Bàn - ' . SITE_NAME,
            'bookings' => $bookings,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalBookings' => $totalBookings
        ];

        $this->loadView('customer/user/bookings', $data);
    }

    public function favorites() {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $favoriteFoods = $this->userModel->getFavoriteFoods($userId);

        $data = [
            'title' => 'Món Ăn Yêu Thích - ' . SITE_NAME,
            'favoriteFoods' => $favoriteFoods
        ];

        $this->loadView('customer/user/favorites', $data);
    }

    public function addFavorite() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $foodId = intval($_POST['food_id'] ?? 0);

        if ($foodId <= 0) {
            $this->jsonResponse(['error' => 'Invalid food ID'], 400);
        }

        $userId = $_SESSION['user_id'];

        if ($this->userModel->addFavoriteFood($userId, $foodId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Đã thêm vào danh sách yêu thích']);
        } else {
            $this->jsonResponse(['error' => 'Không thể thêm vào danh sách yêu thích'], 500);
        }
    }

    public function removeFavorite() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $foodId = intval($_POST['food_id'] ?? 0);

        if ($foodId <= 0) {
            $this->jsonResponse(['error' => 'Invalid food ID'], 400);
        }

        $userId = $_SESSION['user_id'];

        if ($this->userModel->removeFavoriteFood($userId, $foodId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
        } else {
            $this->jsonResponse(['error' => 'Không thể xóa khỏi danh sách yêu thích'], 500);
        }
    }

    public function uploadAvatar() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(['error' => 'Không có file được upload'], 400);
        }

        $file = $_FILES['avatar'];

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            $this->jsonResponse(['error' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF)'], 400);
        }

        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            $this->jsonResponse(['error' => 'File ảnh quá lớn (tối đa 2MB)'], 400);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $uploadDir = 'uploads/user_avatars/';
        $uploadPath = $uploadDir . $filename;

        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Update user avatar in database
            if ($this->userModel->updateUserAvatar($_SESSION['user_id'], $uploadPath)) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Cập nhật ảnh đại diện thành công',
                    'avatar_url' => '/' . $uploadPath
                ]);
            } else {
                // Delete uploaded file if database update failed
                unlink($uploadPath);
                $this->jsonResponse(['error' => 'Không thể cập nhật ảnh đại diện'], 500);
            }
        } else {
            $this->jsonResponse(['error' => 'Không thể upload file'], 500);
        }
    }

    public function updateProfile() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ';
            redirect('/index.php?page=user&action=profile');
        }

        $this->validateCSRF();

        $userId = $_SESSION['user_id'];
        $first_name = sanitizeInput($_POST['first_name'] ?? '');
        $last_name = sanitizeInput($_POST['last_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $date_of_birth = $_POST['date_of_birth'] ?? null;
        $address = sanitizeInput($_POST['address'] ?? '');
        $avatar = null;

        // Validate
        $errors = [];
        if (empty($first_name) || empty($last_name)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }
        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        if (!empty($date_of_birth) && !strtotime($date_of_birth)) {
            $errors[] = 'Ngày sinh không hợp lệ';
        }

        // Avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $avatar = uniqid() . '.' . $ext;
            $target = __DIR__ . '/../assets/images/' . $avatar;
            move_uploaded_file($_FILES['avatar']['tmp_name'], $target);
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            redirect('/index.php?page=user&action=profile');
        }

        $result = $this->userModel->updateProfile($userId, [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'date_of_birth' => $date_of_birth,
            'address' => $address,
            'avatar' => $avatar
        ]);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật thông tin thành công';
             redirect('/index.php');
        } else {
            $_SESSION['error'] = 'Cập nhật thất bại';
            redirect('/index.php?page=user&action=profile');
        }
    }

    private function handleProfileUpdate() {
        $this->validateCSRF();

        $first_name = sanitizeInput($_POST['first_name'] ?? '');
        $last_name = sanitizeInput($_POST['last_name'] ?? '');
        $phone = sanitizeInput($_POST['phone_number'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        $dateOfBirth = $_POST['date_of_birth'] ?? '';
        $gmail = sanitizeInput($_POST['email']);
        $avatar = $_FILES['avatar'] ?? null;

        // Validation
        $errors = [];

        if (empty($first_name) || empty($last_name)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }

        if (empty($first_name)) {
            $errors[] = 'Vui lòng nhập tên';
        }
        //gmail
        if (empty($gmail)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (!empty($dateOfBirth) && !strtotime($dateOfBirth)) {
            $errors[] = 'Ngày sinh không hợp lệ';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            redirect('/index.php?page=user&action=edit');
        }

        // Update user data
        $updateData = [
            'email' => $_SESSION['user_email'],
            'first_name' => $first_name,
            'last_name' => $last_name,
            'avatar' => $avatar ? 'uploads/avatars/' . $avatar['name'] : null,
            'phone_number' => $phone,
            'address' => $address ?: null,
            'date_of_birth' => $dateOfBirth ?: null
        ];

        if ($this->userModel->updateUser($_SESSION['user_id'], $updateData)) {
            $_SESSION['success'] = 'Cập nhật thông tin thành công';
            redirect('/index.php?page=user&action=profile');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thông tin';
            redirect('/index.php?page=user&action=edit');
        }
    }

    private function handlePasswordChange() {
        $this->validateCSRF();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (empty($currentPassword)) {
            $errors[] = 'Vui lòng nhập mật khẩu hiện tại';
        }

        if (empty($newPassword)) {
            $errors[] = 'Vui lòng nhập mật khẩu mới';
        } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Mật khẩu mới phải có ít nhất ' . PASSWORD_MIN_LENGTH . ' ký tự';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            redirect('/index.php?page=user&action=profile&tab=security');
        }

        // Verify current password
        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$this->userModel->verifyPassword($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'Mật khẩu hiện tại không đúng';
            redirect('/index.php?page=user&action=profile&tab=security');
        }

        // Update password
        if ($this->userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
            $_SESSION['success'] = 'Đổi mật khẩu thành công';
            redirect('/index.php');
            // KHÔNG load view ở đây!
        } else {
            $_SESSION['error'] = 'Đổi mật khẩu thất bại';
            redirect('/index.php?page=user&action=profile&tab=security');
        }
    }
}
?>

