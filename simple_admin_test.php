<?php
/**
 * Simple Admin Test
 */

session_start();

// Include config
require_once 'config/config.php';
require_once 'models/User.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Simple Admin Test</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body>";

echo "<div class='container mt-4'>";
echo "<h1>🧪 Simple Admin Test</h1>";

// Create/get admin user
$userModel = new User();
$admin = $userModel->findByEmail('admin@buffet.com');

if (!$admin) {
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
        $admin = $userModel->findByEmail('admin@buffet.com');
        echo "<div class='alert alert-success'>✅ Admin user created!</div>";
    }
}

// Set session
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

echo "<div class='alert alert-info'>";
echo "<h4>🔑 Admin Session Set</h4>";
echo "<p><strong>User ID:</strong> {$_SESSION['user_id']}</p>";
echo "<p><strong>Role:</strong> {$_SESSION['user_role']}</p>";
echo "<p><strong>Name:</strong> {$_SESSION['user_name']}</p>";
echo "</div>";

echo "<div class='card'>";
echo "<div class='card-header'><h3>🎯 Test Admin Access</h3></div>";
echo "<div class='card-body'>";

echo "<div class='row'>";
echo "<div class='col-md-4'>";
echo "<h5>Direct Directory:</h5>";
echo "<a href='/buffet_booking_mvc/admin/' target='_blank' class='btn btn-primary'>Test /admin/</a>";
echo "<p class='small text-muted'>Should show admin dashboard</p>";
echo "</div>";

echo "<div class='col-md-4'>";
echo "<h5>Routing System:</h5>";
echo "<a href='/buffet_booking_mvc/admin' target='_blank' class='btn btn-success'>Test /admin</a>";
echo "<p class='small text-muted'>Through main routing</p>";
echo "</div>";

echo "<div class='col-md-4'>";
echo "<h5>Login Test:</h5>";
echo "<a href='/buffet_booking_mvc/auth/login' target='_blank' class='btn btn-info'>Test Login</a>";
echo "<p class='small text-muted'>Manual login process</p>";
echo "</div>";
echo "</div>";

echo "<hr>";
echo "<h5>📝 Login Credentials:</h5>";
echo "<ul>";
echo "<li><strong>Email:</strong> admin@buffet.com</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";

echo "</div></div>";

echo "</div></body></html>";
?>
