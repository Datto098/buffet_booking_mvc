<?php
/**
 * Test Admin Login System Fix
 */

// Start session
session_start();

// Include necessary files
require_once 'config/config.php';
require_once 'models/User.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Admin Login Test</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body>";

echo "<div class='container mt-4'>";
echo "<h1>Admin Login System Test</h1>";

// Get admin user
$userModel = new User();
$admins = $userModel->findByCondition(['role' => 'manager']);

if (empty($admins)) {
    echo "<div class='alert alert-warning'>No admin users found. Creating test admin...</div>";

    $adminData = [
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
        echo "<div class='alert alert-success'>Test admin created! ID: $adminId</div>";
        $admins = $userModel->findByCondition(['role' => 'manager']);
    }
}

if (!empty($admins)) {
    $admin = $admins[0];

    echo "<div class='card mb-3'>";
    echo "<div class='card-header'><h3>Available Admin User</h3></div>";
    echo "<div class='card-body'>";
    echo "<p><strong>Email:</strong> {$admin['email']}</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Role:</strong> {$admin['role']}</p>";
    echo "</div></div>";

    // Simulate login session
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['is_logged_in'] = true;
    $_SESSION['user'] = [
        'id' => $admin['id'],
        'name' => $admin['name'],
        'email' => $admin['email'],
        'role' => $admin['role']
    ];

    echo "<div class='alert alert-success'>Session set for admin user!</div>";

    echo "<div class='card'>";
    echo "<div class='card-header'><h3>Test Links</h3></div>";
    echo "<div class='card-body'>";
    echo "<div class='d-grid gap-2'>";
    echo "<a href='/buffet_booking_mvc/admin/' class='btn btn-primary'>Test Admin Directory Access</a>";
    echo "<a href='/buffet_booking_mvc/admin' class='btn btn-success'>Test Admin Dashboard (Proper Route)</a>";
    echo "<a href='/buffet_booking_mvc/admin/users' class='btn btn-info'>Test User Management</a>";
    echo "<a href='/buffet_booking_mvc/admin/orders' class='btn btn-warning'>Test Order Management</a>";
    echo "</div>";
    echo "</div></div>";

    echo "<div class='card mt-3'>";
    echo "<div class='card-header'><h3>Session Debug Info</h3></div>";
    echo "<div class='card-body'>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    echo "</div></div>";
}

echo "</div></body></html>";
?>
