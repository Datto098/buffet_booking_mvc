<?php
/**
 * Complete Admin Login Flow Test
 */

session_start();

// Include necessary files
require_once 'config/config.php';
require_once 'controllers/AuthController.php';
require_once 'models/User.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Complete Admin Login Test</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body>";

echo "<div class='container mt-4'>";
echo "<h1>Complete Admin Login Flow Test</h1>";

// Step 1: Check if admin user exists
$userModel = new User();
$admin = $userModel->findByEmail('admin@buffet.com');

if (!$admin) {
    echo "<div class='alert alert-warning'>Creating test admin user...</div>";

    $adminData = [
        'full_name' => 'Test Admin',
        'name' => 'Test Admin',
        'email' => 'admin@buffet.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'manager',
        'phone_number' => '1234567890',
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $adminId = $userModel->create($adminData);
    if ($adminId) {
        echo "<div class='alert alert-success'>Admin user created with ID: $adminId</div>";
        $admin = $userModel->findByEmail('admin@buffet.com');
    } else {
        echo "<div class='alert alert-danger'>Failed to create admin user</div>";
        die();
    }
}

echo "<div class='card mb-3'>";
echo "<div class='card-header'><h3>Admin User Info</h3></div>";
echo "<div class='card-body'>";
echo "<p><strong>Email:</strong> admin@buffet.com</p>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><strong>Current Role:</strong> {$admin['role']}</p>";
echo "</div></div>";

// Step 2: Simulate the login process manually
if (isset($_GET['do_login'])) {
    echo "<div class='alert alert-info'>Simulating login process...</div>";

    // Verify password
    if (password_verify('admin123', $admin['password'])) {
        echo "<div class='alert alert-success'>Password verification successful</div>";

        // Set session exactly like AuthController does
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['full_name'] ?? $admin['name'] ?? 'User';
        $_SESSION['user_email'] = $admin['email'];
        $_SESSION['user_role'] = $admin['role'];
        $_SESSION['is_logged_in'] = true;

        // Set user array for admin panel compatibility
        $_SESSION['user'] = [
            'id' => $admin['id'],
            'name' => $admin['full_name'] ?? $admin['name'] ?? 'User',
            'email' => $admin['email'],
            'role' => $admin['role']
        ];

        echo "<div class='alert alert-success'>Session data set successfully</div>";

        // Test admin access
        echo "<div class='card mt-3'>";
        echo "<div class='card-header'><h3>Session Data</h3></div>";
        echo "<div class='card-body'>";
        echo "<pre>" . print_r($_SESSION, true) . "</pre>";
        echo "</div></div>";

        echo "<div class='card mt-3'>";
        echo "<div class='card-header'><h3>Admin Access Test</h3></div>";
        echo "<div class='card-body'>";

        // Check admin route authentication logic
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['manager', 'super_admin'])) {
            echo "<div class='alert alert-danger'>❌ Admin authentication would fail</div>";
        } else {
            echo "<div class='alert alert-success'>✅ Admin authentication would pass</div>";
        }

        echo "<div class='d-grid gap-2 mt-3'>";
        echo "<a href='/buffet_booking_mvc/admin/' class='btn btn-primary'>Test: /admin/ (directory)</a>";
        echo "<a href='/buffet_booking_mvc/admin' class='btn btn-success'>Test: /admin (route)</a>";
        echo "<a href='/buffet_booking_mvc/auth/login' class='btn btn-info'>Test: Login Page</a>";
        echo "</div>";
        echo "</div></div>";

    } else {
        echo "<div class='alert alert-danger'>Password verification failed</div>";
    }

} else {
    echo "<div class='card'>";
    echo "<div class='card-header'><h3>Ready to Test</h3></div>";
    echo "<div class='card-body'>";
    echo "<p>Click the button below to simulate the admin login process:</p>";
    echo "<a href='?do_login=1' class='btn btn-primary'>Simulate Admin Login</a>";
    echo "</div></div>";
}

echo "</div></body></html>";
?>
