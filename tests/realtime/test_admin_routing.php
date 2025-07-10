<?php
/**
 * Test Admin Routing
 * Kiểm tra routing cho Admin internal-messages
 */

session_start();

echo "<h1>🧪 Test Admin Routing</h1>";

// Test URLs
$testUrls = [
    'Admin Internal Messages' => 'http://localhost/buffet_booking_mvc/admin/internal-messages',
    'Admin Get Unread Count' => 'http://localhost/buffet_booking_mvc/admin/internal-messages/get-unread-count',
    'Admin SSE' => 'http://localhost/buffet_booking_mvc/admin/internal-messages/sse',
    'Admin Mark as Read' => 'http://localhost/buffet_booking_mvc/admin/internal-messages/mark-as-read'
];

echo "<h2>Test URLs:</h2>";
foreach ($testUrls as $name => $url) {
    echo "<p><strong>{$name}:</strong> <a href='{$url}' target='_blank'>{$url}</a></p>";
}

echo "<h2>Session Info:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ Có session user_id: {$_SESSION['user_id']}</p>";
} else {
    echo "<p style='color: red;'>❌ Không có session user_id</p>";
}

if (isset($_SESSION['user_role'])) {
    echo "<p style='color: green;'>✅ Có session user_role: {$_SESSION['user_role']}</p>";
} else {
    echo "<p style='color: red;'>❌ Không có session user_role</p>";
}

// Test với cURL
echo "<h2>Test với cURL:</h2>";

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
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    echo "<p><strong>HTTP Code:</strong> {$httpCode}</p>";
    echo "<p><strong>Content-Type:</strong> {$contentType}</p>";

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

        // Hiển thị một phần response
        $responseLines = explode("\n", $response);
        $firstLines = array_slice($responseLines, 0, 5);
        echo "<p><strong>Response (first 5 lines):</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>";
        foreach ($firstLines as $line) {
            echo htmlspecialchars($line) . "\n";
        }
        echo "</pre>";
    }

    echo "<hr>";
}

// Kiểm tra debug.log
echo "<h2>Debug Log:</h2>";
$debugLog = 'debug.log';
if (file_exists($debugLog)) {
    $logContent = file_get_contents($debugLog);
    if ($logContent) {
        echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-size: 12px; max-height: 300px; overflow-y: auto;'>";
        echo htmlspecialchars($logContent);
        echo "</pre>";
    } else {
        echo "<p>Debug log trống</p>";
    }
} else {
    echo "<p>Không tìm thấy debug.log</p>";
}

// Test routing logic
echo "<h2>Test Routing Logic:</h2>";

// Simulate URL parsing
$testUrls2 = [
    '/admin/internal-messages' => ['admin', 'internal-messages'],
    '/admin/internal-messages/get-unread-count' => ['admin', 'internal-messages', 'get-unread-count'],
    '/admin/internal-messages/sse' => ['admin', 'internal-messages', 'sse']
];

foreach ($testUrls2 as $url => $segments) {
    echo "<h3>URL: {$url}</h3>";
    echo "<p><strong>Segments:</strong> " . implode(', ', $segments) . "</p>";

    if (count($segments) >= 2 && $segments[0] === 'admin' && $segments[1] === 'internal-messages') {
        echo "<p style='color: green;'>✅ Sẽ được route đến handleAdminInternalMessagesRoute</p>";

        if (count($segments) >= 3) {
            $action = $segments[2];
            echo "<p><strong>Action:</strong> {$action}</p>";

            $validActions = ['view', 'download-attachment', 'sse', 'get-unread-count', 'mark-as-read'];
            if (in_array($action, $validActions)) {
                echo "<p style='color: green;'>✅ Action hợp lệ</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Action không hợp lệ, sẽ dùng default</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Không có action, sẽ dùng default (internalMessages)</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Không match với admin routing</p>";
    }

    echo "<hr>";
}

echo "<h2>Hướng dẫn test:</h2>";
echo "<ol>";
echo "<li>Đăng nhập Admin: <a href='http://localhost/buffet_booking_mvc/admin' target='_blank'>Admin Dashboard</a></li>";
echo "<li>Click vào các link test ở trên</li>";
echo "<li>Xem có bị 404 không</li>";
echo "<li>Kiểm tra debug log để xem lỗi</li>";
echo "</ol>";
?>
