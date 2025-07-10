<?php
/**
 * Debug Session và Role
 * Kiểm tra session và role của user hiện tại
 */

session_start();

echo "<h1>🔍 Debug Session và Role</h1>";

echo "<h2>Session Data:</h2>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Role Check:</h2>";

// Kiểm tra các function
if (function_exists('isAdmin')) {
    $isAdmin = isAdmin();
    echo "<p><strong>isAdmin():</strong> " . ($isAdmin ? '✅ True' : '❌ False') . "</p>";
} else {
    echo "<p style='color: red;'>❌ Function isAdmin() không tồn tại</p>";
}

if (function_exists('isAuthenticated')) {
    $isAuth = isAuthenticated();
    echo "<p><strong>isAuthenticated():</strong> " . ($isAuth ? '✅ True' : '❌ False') . "</p>";
} else {
    echo "<p style='color: red;'>❌ Function isAuthenticated() không tồn tại</p>";
}

// Kiểm tra role trực tiếp
if (isset($_SESSION['user_role'])) {
    $role = $_SESSION['user_role'];
    echo "<p><strong>Session user_role:</strong> {$role}</p>";

    $validRoles = ['admin', 'manager', 'super_admin'];
    if (in_array($role, $validRoles)) {
        echo "<p style='color: green;'>✅ Role hợp lệ</p>";
    } else {
        echo "<p style='color: red;'>❌ Role không hợp lệ</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Không có session user_role</p>";
}

// Kiểm tra user_id
if (isset($_SESSION['user_id'])) {
    echo "<p><strong>Session user_id:</strong> {$_SESSION['user_id']}</p>";
} else {
    echo "<p style='color: red;'>❌ Không có session user_id</p>";
}

echo "<h2>Test URLs:</h2>";
echo "<ul>";
echo "<li><a href='http://localhost/buffet_booking_mvc/superadmin/internal-messages' target='_blank'>Super Admin Internal Messages</a></li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/admin/internal-messages' target='_blank'>Admin Internal Messages</a></li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/superadmin' target='_blank'>Super Admin Dashboard</a></li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/admin' target='_blank'>Admin Dashboard</a></li>";
echo "</ul>";

echo "<h2>Debug Info:</h2>";
echo "<p><strong>Current URL:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";

// Test requireSuperAdmin logic
echo "<h2>Test requireSuperAdmin Logic:</h2>";

// Simulate the logic
$hasLogin = isset($_SESSION['user_id']);
$hasValidRole = isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'manager', 'super_admin']);

echo "<p><strong>Has Login:</strong> " . ($hasLogin ? '✅ Yes' : '❌ No') . "</p>";
echo "<p><strong>Has Valid Role:</strong> " . ($hasValidRole ? '✅ Yes' : '❌ No') . "</p>";

if ($hasLogin && $hasValidRole) {
    echo "<p style='color: green;'>✅ requireSuperAdmin() sẽ cho phép truy cập</p>";
} else {
    echo "<p style='color: red;'>❌ requireSuperAdmin() sẽ redirect</p>";
}

// Test specific role check
if (isset($_SESSION['user_role'])) {
    $role = $_SESSION['user_role'];
    if ($role === 'super_admin') {
        echo "<p style='color: green;'>✅ User có role super_admin</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ User có role: {$role} (không phải super_admin)</p>";
    }
}
?>
