<?php

/**
 * Authentication Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once 'helpers/mail_helper.php';

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        // Default to login page
        $this->login();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->showLoginForm();
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $this->showRegisterForm();
        }
    }
    public function logout()
    {
        session_destroy();
        redirect('/');
    }
    private function showLoginForm()
    {
        // Redirect if already logged in
        if (isLoggedIn()) {
            redirect('/');
        }

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Đăng Nhập - ' . SITE_NAME,
            'info' => $restaurantInfo // Thêm dòng này
        ];

        $this->loadView('customer/auth/login', $data);
    }
    private function showRegisterForm()
    {
        // Redirect if already logged in
        if (isLoggedIn()) {
            redirect('/');
        }

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Đăng Ký - ' . SITE_NAME,
            'info' => $restaurantInfo // Thêm dòng này
        ];

        $this->loadView('customer/auth/register', $data);
    }

    private function handleLogin()
    {
        $this->validateCSRF();

        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];

        // Validate input
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->showLoginForm();
            return;
        }

        // Find user by email
        $user = $this->userModel->findByEmail($email);
        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'] ?? '';
            $_SESSION['last_name'] = $user['last_name'] ?? '';
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['is_logged_in'] = true;

            // Set user array for admin panel compatibility
            $_SESSION['user'] = [
                'id' => $user['id'],
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'full_name' => $user['full_name'] ?? (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                'email' => $user['email'],
                'role' => $user['role']
            ];
            $_SESSION['success'] = 'Đăng nhập thành công!';

            // Redirect based on role
            if ($user['role'] === 'super_admin') {
                redirect('/superadmin/');
            } elseif ($user['role'] === 'manager') {
                redirect('/admin/');
            } else {
                redirect('/');
            }
        } else {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng';
            $this->showLoginForm();
        }
    }

    private function handleRegister()
    {
        $this->validateCSRF();        $fullName = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone_number']);
        $address = sanitizeInput($_POST['address'] ?? '');
        $password = $_POST['password'];
        $confirmPassword = $_POST['password_confirmation'];

        // Validate input
        $errors = [];

        if (empty($fullName)) {
            $errors[] = 'Họ tên không được để trống';
        }        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (empty($phone) || strlen($phone) < 10) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }

        if (empty($password) || strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Mật khẩu phải có ít nhất ' . PASSWORD_MIN_LENGTH . ' ký tự';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }

        // Check if email already exists
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email đã được sử dụng';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->showRegisterForm();
            return;
        }
        //  echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';        // Create new user
        $userData = [
            'full_name' => $fullName,
            'email' => $email,
            'phone_number' => $phone,
            'address' => $address,
            'password' => $password,
            'role' => 'customer'
        ];
        // echo '<pre>';
        // print_r($userData);
        // echo '</pre>';

        if ($this->userModel->createUser($userData)) {
            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            redirect('/auth/login');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.';
            $this->showRegisterForm();
        }
    }

    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                // Lưu token vào DB
                $this->userModel->saveResetToken($email, $token);
                // Gửi email
                $resetLink = SITE_URL . "/auth/resetPassword?token=$token";
                $sent = sendResetMail($email, "Đặt lại mật khẩu", "Nhấn vào link sau để đặt lại mật khẩu: $resetLink");
                if ($sent) {
                    $_SESSION['success'] = "Đã gửi liên kết đặt lại mật khẩu tới email của bạn!";
                } else {
                    $_SESSION['error'] = "Không gửi được email. Vui lòng thử lại sau!";
                }
            } else {
                $_SESSION['error'] = "Email không tồn tại!";
            }
            $this->loadView('customer/auth/forgot_password');
        } else {
            $this->loadView('customer/auth/forgot_password');
        }
    }



    public function resetPassword()
    {
        $token = $_GET['token'] ?? null;
        if (!$token) {
            $_SESSION['error'] = 'Liên kết không hợp lệ!';
            return $this->loadView('customer/auth/reset_password');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            // Tìm user theo token
            $user = $this->userModel->findByResetToken($token);
            if ($user) {
                // Hash mật khẩu mới
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                // Cập nhật mật khẩu và xoá token
                $this->userModel->updatePasswordAndClearToken($user['id'], $hashed);
                $_SESSION['success'] = 'Đặt lại mật khẩu thành công!';
                header('Location: ' . SITE_URL . '/auth/login');
                exit;
            } else {
                $_SESSION['error'] = 'Token không hợp lệ hoặc đã hết hạn!';
            }
        }
        $this->loadView('customer/auth/reset_password');
    }
}
