<?php
/**
 * Test SSE Endpoint
 * Kiểm tra SSE endpoint có hoạt động không
 */

echo "<h1>🧪 Test SSE Endpoint</h1>";

// Test URLs
$urls = [
    'Admin SSE' => 'http://localhost/buffet_booking_mvc/admin/internal-messages/sse',
    'Super Admin SSE' => 'http://localhost/buffet_booking_mvc/superadmin/internal-messages/sse'
];

foreach ($urls as $name => $url) {
    echo "<h2>{$name}</h2>";
    echo "<p><strong>URL:</strong> <code>{$url}</code></p>";

    // Test với cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    echo "<p><strong>HTTP Code:</strong> {$httpCode}</p>";
    echo "<p><strong>Content-Type:</strong> {$contentType}</p>";

    if ($error) {
        echo "<p style='color: red;'>❌ cURL Error: {$error}</p>";
    } else {
        if ($httpCode == 200) {
            echo "<p style='color: green;'>✅ Endpoint hoạt động</p>";

            // Kiểm tra response có phải SSE không
            if (strpos($contentType, 'text/event-stream') !== false) {
                echo "<p style='color: green;'>✅ Content-Type đúng (text/event-stream)</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Content-Type không đúng: {$contentType}</p>";
            }

            // Hiển thị một phần response
            $responseLines = explode("\n", $response);
            $firstLines = array_slice($responseLines, 0, 10);
            echo "<p><strong>Response (first 10 lines):</strong></p>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
            foreach ($firstLines as $line) {
                echo htmlspecialchars($line) . "\n";
            }
            echo "</pre>";

        } elseif ($httpCode == 401) {
            echo "<p style='color: orange;'>⚠️ Cần đăng nhập (điều này là bình thường)</p>";
        } elseif ($httpCode == 404) {
            echo "<p style='color: red;'>❌ Endpoint không tồn tại (404)</p>";
        } else {
            echo "<p style='color: red;'>❌ Lỗi HTTP: {$httpCode}</p>";
        }
    }

    echo "<hr>";
}

// Test với session (nếu có)
echo "<h2>Test với Session</h2>";
session_start();

if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ Có session user_id: {$_SESSION['user_id']}</p>";
    echo "<p style='color: green;'>✅ Có session user_role: {$_SESSION['user_role']}</p>";

    // Test SSE với session
    $testUrl = $_SESSION['user_role'] === 'super_admin'
        ? 'http://localhost/buffet_booking_mvc/superadmin/internal-messages/sse'
        : 'http://localhost/buffet_booking_mvc/admin/internal-messages/sse';

    echo "<p><strong>Test URL với session:</strong> <code>{$testUrl}</code></p>";

    // Tạo cookie session
    $cookie = session_name() . '=' . session_id();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "<p><strong>HTTP Code với session:</strong> {$httpCode}</p>";

    if ($httpCode == 200) {
        echo "<p style='color: green;'>✅ SSE hoạt động với session</p>";
    } else {
        echo "<p style='color: red;'>❌ SSE không hoạt động với session</p>";
    }

} else {
    echo "<p style='color: orange;'>⚠️ Không có session - cần đăng nhập trước</p>";
    echo "<p><a href='http://localhost/buffet_booking_mvc/admin' target='_blank'>Đăng nhập Admin</a></p>";
    echo "<p><a href='http://localhost/buffet_booking_mvc/superadmin' target='_blank'>Đăng nhập Super Admin</a></p>";
}

echo "<h2>Hướng dẫn test</h2>";
echo "<ol>";
echo "<li>Đăng nhập vào Admin hoặc Super Admin</li>";
echo "<li>Mở Developer Tools (F12) → Console</li>";
echo "<li>Kiểm tra có log 'SSE Connected' không</li>";
echo "<li>Gửi thông báo từ tab khác và xem có popup không</li>";
echo "</ol>";
?>
