<?php
/**
 * Test Fix Conflict
 * Kiểm tra sau khi sửa conflict function
 */

session_start();

echo "<h1>🔧 Test Fix Conflict</h1>";

// Test functions
echo "<h2>Test Functions:</h2>";

if (function_exists('isAdmin')) {
    echo "<p style='color: green;'>✅ Function isAdmin() tồn tại</p>";

    // Test logic
    if (isset($_SESSION['user_role'])) {
        $role = $_SESSION['user_role'];
        $isAdmin = isAdmin();
        echo "<p>user_role: {$role}</p>";
        echo "<p>isAdmin(): " . ($isAdmin ? 'true' : 'false') . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Function isAdmin() không tồn tại</p>";
}

if (function_exists('isManager')) {
    echo "<p style='color: green;'>✅ Function isManager() tồn tại</p>";

    if (isset($_SESSION['user_role'])) {
        $role = $_SESSION['user_role'];
        $isManager = isManager();
        echo "<p>isManager(): " . ($isManager ? 'true' : 'false') . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Function isManager() không tồn tại</p>";
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

// Test Admin URLs
echo "<h2>Test Admin URLs:</h2>";

$testUrls = [
    'Admin Internal Messages' => 'http://localhost/buffet_booking_mvc/admin/internal-messages',
    'Admin Get Unread Count' => 'http://localhost/buffet_booking_mvc/admin/internal-messages/get-unread-count'
];

foreach ($testUrls as $name => $url) {
    echo "<p><strong>{$name}:</strong> <a href='{$url}' target='_blank'>{$url}</a></p>";
}

// Test cURL
echo "<h2>Test cURL:</h2>";

foreach ($testUrls as $name => $url) {
    echo "<h3>{$name}</h3>";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Thêm cookie session nếu có
    if (isset($_COOKIE[session_name()])) {
        curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . $_COOKIE[session_name()]);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "<p><strong>HTTP Code:</strong> {$httpCode}</p>";

    if ($error) {
        echo "<p style='color: red;'>❌ cURL Error: {$error}</p>";
    } else {
        if ($httpCode == 200) {
            echo "<p style='color: green;'>✅ Endpoint hoạt động</p>";
        } elseif ($httpCode == 401) {
            echo "<p style='color: orange;'>⚠️ Cần đăng nhập</p>";
        } elseif ($httpCode == 404) {
            echo "<p style='color: red;'>❌ Endpoint không tồn tại (404)</p>";
        } else {
            echo "<p style='color: red;'>❌ Lỗi HTTP: {$httpCode}</p>";
        }
    }

    echo "<hr>";
}

echo "<h2>Hướng dẫn:</h2>";
echo "<ol>";
echo "<li>Đăng nhập Admin</li>";
echo "<li>Click vào các link test</li>";
echo "<li>Xem có còn 404 không</li>";
echo "</ol>";
?>
