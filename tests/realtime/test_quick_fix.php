<?php
/**
 * Test Quick Fix
 * Kiểm tra sau khi sửa helpers/functions.php
 */

session_start();

echo "<h1>🔧 Test Quick Fix</h1>";

// Test functions
echo "<h2>Test Functions:</h2>";

if (function_exists('isAdmin')) {
    echo "<p style='color: green;'>✅ Function isAdmin() tồn tại</p>";
} else {
    echo "<p style='color: red;'>❌ Function isAdmin() không tồn tại</p>";
}

if (function_exists('isManager')) {
    echo "<p style='color: green;'>✅ Function isManager() tồn tại</p>";
} else {
    echo "<p style='color: red;'>❌ Function isManager() không tồn tại</p>";
}

// Test Admin URLs
echo "<h2>Test Admin URLs:</h2>";

$testUrls = [
    'Admin Internal Messages' => 'http://localhost/buffet_booking_mvc/admin/internal-messages',
    'Admin Get Unread Count' => 'http://localhost/buffet_booking_mvc/admin/internal-messages/get-unread-count'
];

foreach ($testUrls as $name => $url) {
    echo "<p><strong>{$name}:</strong> <a href='{$url}' target='_blank'>{$url}</a></p>";
}

// Test session
echo "<h2>Session Info:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ user_id: {$_SESSION['user_id']}</p>";
} else {
    echo "<p style='color: red;'>❌ Không có user_id</p>";
}

if (isset($_SESSION['user_role'])) {
    echo "<p style='color: green;'>✅ user_role: {$_SESSION['user_role']}</p>";
} else {
    echo "<p style='color: red;'>❌ Không có user_role</p>";
}

// Test role functions
if (isset($_SESSION['user_role'])) {
    $role = $_SESSION['user_role'];
    echo "<h2>Test Role Functions:</h2>";

    if (function_exists('isAdmin')) {
        $isAdmin = isAdmin();
        echo "<p>isAdmin(): " . ($isAdmin ? 'true' : 'false') . "</p>";
    }

    if (function_exists('isManager')) {
        $isManager = isManager();
        echo "<p>isManager(): " . ($isManager ? 'true' : 'false') . "</p>";
    }
}

echo "<h2>Hướng dẫn:</h2>";
echo "<ol>";
echo "<li>Đăng nhập Admin</li>";
echo "<li>Click vào các link test</li>";
echo "<li>Xem có còn 404 không</li>";
echo "</ol>";
?>
