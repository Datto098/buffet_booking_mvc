<?php
/**
 * Final Admin System Verification
 */

session_start();

// Include necessary files
require_once 'config/config.php';
require_once 'models/User.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Admin System Verification</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body>";

echo "<div class='container mt-4'>";
echo "<h1>🔧 Admin System Fix Verification</h1>";

echo "<div class='alert alert-info'>";
echo "<h4>📋 What was fixed:</h4>";
echo "<ol>";
echo "<li>Created <code>/admin/index.php</code> to handle direct admin directory access</li>";
echo "<li>Updated <code>AuthController</code> to set proper session structure</li>";
echo "<li>Added admin authentication check in admin directory</li>";
echo "</ol>";
echo "</div>";

// Ensure admin user exists
$userModel = new User();
$admin = $userModel->findByEmail('admin@buffet.com');

if (!$admin) {
    echo "<div class='alert alert-warning'>Creating admin user for testing...</div>";
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
        echo "<div class='alert alert-success'>✅ Admin user created successfully!</div>";
        $admin = $userModel->findByEmail('admin@buffet.com');
    }
}

if ($admin) {
    echo "<div class='card mb-4'>";
    echo "<div class='card-header bg-success text-white'>";
    echo "<h3><i class='fas fa-user-shield'></i> Admin Login Credentials</h3>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<div class='row'>";
    echo "<div class='col-md-6'>";
    echo "<p><strong>📧 Email:</strong> <code>admin@buffet.com</code></p>";
    echo "<p><strong>🔐 Password:</strong> <code>admin123</code></p>";
    echo "</div>";
    echo "<div class='col-md-6'>";
    echo "<p><strong>👤 Role:</strong> <span class='badge bg-primary'>{$admin['role']}</span></p>";
    echo "<p><strong>🆔 User ID:</strong> {$admin['id']}</p>";
    echo "</div>";
    echo "</div>";
    echo "</div></div>";
}

echo "<div class='card mb-4'>";
echo "<div class='card-header bg-primary text-white'>";
echo "<h3><i class='fas fa-route'></i> Testing Instructions</h3>";
echo "</div>";
echo "<div class='card-body'>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<h5>🔐 Login Process:</h5>";
echo "<ol>";
echo "<li>Go to <a href='/buffet_booking_mvc/auth/login' target='_blank'>Login Page</a></li>";
echo "<li>Enter credentials above</li>";
echo "<li>You should be redirected to admin dashboard</li>";
echo "</ol>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<h5>🎯 Direct Admin Access:</h5>";
echo "<ol>";
echo "<li>After login, try <a href='/buffet_booking_mvc/admin/' target='_blank'>/admin/</a></li>";
echo "<li>Should redirect to proper dashboard</li>";
echo "<li>No more Apache directory listing</li>";
echo "</ol>";
echo "</div>";
echo "</div>";
echo "</div></div>";

echo "<div class='card mb-4'>";
echo "<div class='card-header bg-warning text-dark'>";
echo "<h3><i class='fas fa-link'></i> Quick Test Links</h3>";
echo "</div>";
echo "<div class='card-body'>";
echo "<div class='d-grid gap-2 d-md-block'>";
echo "<a href='/buffet_booking_mvc/auth/login' class='btn btn-primary me-2' target='_blank'>";
echo "<i class='fas fa-sign-in-alt'></i> Login Page</a>";
echo "<a href='/buffet_booking_mvc/admin/' class='btn btn-success me-2' target='_blank'>";
echo "<i class='fas fa-folder'></i> Admin Directory</a>";
echo "<a href='/buffet_booking_mvc/admin' class='btn btn-info me-2' target='_blank'>";
echo "<i class='fas fa-tachometer-alt'></i> Admin Dashboard</a>";
echo "<a href='/buffet_booking_mvc/' class='btn btn-secondary me-2' target='_blank'>";
echo "<i class='fas fa-home'></i> Main Site</a>";
echo "</div>";
echo "</div></div>";

// Show current session status
echo "<div class='card'>";
echo "<div class='card-header bg-info text-white'>";
echo "<h3><i class='fas fa-info-circle'></i> Current Session Status</h3>";
echo "</div>";
echo "<div class='card-body'>";
if (isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-success'>";
    echo "<h5>✅ Currently Logged In</h5>";
    echo "<p><strong>User:</strong> {$_SESSION['user_name']} ({$_SESSION['user_email']})</p>";
    echo "<p><strong>Role:</strong> {$_SESSION['user_role']}</p>";
    echo "<a href='/buffet_booking_mvc/auth/logout' class='btn btn-warning'>Logout</a>";
    echo "</div>";
} else {
    echo "<div class='alert alert-warning'>";
    echo "<h5>⚠️ Not Currently Logged In</h5>";
    echo "<p>Please log in using the credentials above to test the admin system.</p>";
    echo "</div>";
}
echo "</div></div>";

echo "</div></body></html>";
?>
