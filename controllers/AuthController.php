<?php
/**
 * Authentication Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->showLoginForm();
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $this->showRegisterForm();
        }
    }    public function logout() {
        session_destroy();
        redirect('/');
    }    private function showLoginForm() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            redirect('/');
        }

        $data = [
            'title' => 'Đăng Nhập - ' . SITE_NAME
        ];

        $this->loadView('customer/auth/login', $data);
    }    private function showRegisterForm() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            redirect('/');
        }

        $data = [
            'title' => 'Đăng Ký - ' . SITE_NAME
        ];

        $this->loadView('customer/auth/register', $data);
    }

    private function handleLogin() {
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
        $user = $this->userModel->findByEmail($email);        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'] ?? $user['name'] ?? 'User';
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['is_logged_in'] = true;

            // Set user array for admin panel compatibility
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['full_name'] ?? $user['name'] ?? 'User',
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

    private function handleRegister() {
        $this->validateCSRF();

        $fullName = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone_number']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validate input
        $errors = [];

        if (empty($fullName)) {
            $errors[] = 'Họ tên không được để trống';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
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
        // echo '</pre>';

        // Create new user
        $userData = [
            'full_name' => $fullName,
            'email' => $email,
            'phone_number' => $phone,
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

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleForgotPassword();
        } else {
            $data = [
                'title' => 'Quên Mật Khẩu - ' . SITE_NAME
            ];
            $this->loadView('customer/auth/forgot_password', $data);
        }
    }

    private function handleForgotPassword() {
        $this->validateCSRF();

        $email = sanitizeInput($_POST['email']);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ';
            $this->forgotPassword();
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user) {
            // In a real application, you would send an email with reset link
            // For now, just show a success message
            $_SESSION['success'] = 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.';
        } else {
            $_SESSION['error'] = 'Email không tồn tại trong hệ thống';
        }

        $this->forgotPassword();
    }
}
?>
