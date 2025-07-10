<?php
/**
 * Debug 404 Detailed
 * Tìm nguyên nhân 404 cho internal-messages
 */

// Include config để có functions
require_once 'config/config.php';

session_start();

echo "<h1>🔍 Debug 404 Detailed</h1>";

// Test URL parsing
echo "<h2>1. Test URL Parsing:</h2>";

$testUrls = [
    'http://localhost/buffet_booking_mvc/admin/internal-messages/sse',
    'http://localhost/buffet_booking_mvc/admin/internal-messages/get-unread-count'
];

foreach ($testUrls as $url) {
    echo "<h3>URL: {$url}</h3>";

    // Parse URL như trong index.php
    $uri = parse_url($url, PHP_URL_PATH);
    $uri = rtrim($uri, '/');

    // Remove base path
    $basePath = '/buffet_booking_mvc';
    if ($basePath !== '/') {
        $uri = substr($uri, strlen($basePath));
    }

    // Split into segments
    $segments = array_filter(explode('/', $uri));
    $segments = array_values($segments);

    echo "<p><strong>Parsed segments:</strong> " . implode(', ', $segments) . "</p>";

    // Simulate routing logic
    $page = $segments[0] ?? 'home';
    $action = $segments[1] ?? 'index';
    $param = $segments[2] ?? null;

    echo "<p><strong>Routing:</strong> page='{$page}', action='{$action}', param='{$param}'</p>";

    if ($page === 'admin') {
        echo "<p style='color: green;'>✅ Sẽ được route đến handleAdminRoute</p>";

        $section = $segments[1] ?? 'dashboard';
        $action2 = $segments[2] ?? 'index';
        $param2 = $segments[3] ?? null;

        echo "<p><strong>Admin routing:</strong> section='{$section}', action='{$action2}', param='{$param2}'</p>";

        if ($section === 'internal-messages') {
            echo "<p style='color: green;'>✅ Sẽ được route đến handleAdminInternalMessagesRoute</p>";

            $validActions = ['view', 'download-attachment', 'sse', 'get-unread-count', 'mark-as-read'];
            if (in_array($action2, $validActions)) {
                echo "<p style='color: green;'>✅ Action '{$action2}' hợp lệ</p>";
            } else {
                echo "<p style='color: red;'>❌ Action '{$action2}' không hợp lệ</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Section '{$section}' không match</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Không phải admin route</p>";
    }

    echo "<hr>";
}

// Test functions
echo "<h2>2. Test Functions:</h2>";

$functions = ['isLoggedIn', 'isAdmin', 'isManager', 'getUserRole'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green;'>✅ Function {$func}() tồn tại</p>";
    } else {
        echo "<p style='color: red;'>❌ Function {$func}() không tồn tại</p>";
    }
}

// Test session
echo "<h2>3. Session Info:</h2>";
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

// Test authentication
echo "<h2>4. Test Authentication:</h2>";
if (function_exists('isLoggedIn')) {
    $isLoggedIn = isLoggedIn();
    echo "<p>isLoggedIn(): " . ($isLoggedIn ? 'true' : 'false') . "</p>";
}

if (function_exists('isAdmin')) {
    $isAdmin = isAdmin();
    echo "<p>isAdmin(): " . ($isAdmin ? 'true' : 'false') . "</p>";
}

// Test direct controller
echo "<h2>5. Test Direct Controller:</h2>";

try {
    require_once 'controllers/InternalMessageController.php';
    echo "<p style='color: green;'>✅ InternalMessageController loaded</p>";

    $controller = new InternalMessageController();
    echo "<p style='color: green;'>✅ InternalMessageController instantiated</p>";

    // Test methods exist
    if (method_exists($controller, 'sse')) {
        echo "<p style='color: green;'>✅ Method sse() tồn tại</p>";
    } else {
        echo "<p style='color: red;'>❌ Method sse() không tồn tại</p>";
    }

    if (method_exists($controller, 'getUnreadCount')) {
        echo "<p style='color: green;'>✅ Method getUnreadCount() tồn tại</p>";
    } else {
        echo "<p style='color: red;'>❌ Method getUnreadCount() không tồn tại</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

// Test cURL with detailed error
echo "<h2>6. Test cURL với Error Details:</h2>";

foreach ($testUrls as $url) {
    echo "<h3>URL: {$url}</h3>";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    // Capture verbose output
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);

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
        // Get verbose output
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        fclose($verbose);

        echo "<p><strong>Verbose Log:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px; max-height: 200px; overflow-y: auto;'>";
        echo htmlspecialchars($verboseLog);
        echo "</pre>";

        // Show response headers
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        echo "<p><strong>Response Headers:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>";
        echo htmlspecialchars($headers);
        echo "</pre>";

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

echo "<h2>7. Hướng dẫn Debug:</h2>";
echo "<ol>";
echo "<li>Kiểm tra error log của web server</li>";
echo "<li>Kiểm tra PHP error log</li>";
echo "<li>Thêm error_reporting(E_ALL) vào đầu file</li>";
echo "<li>Kiểm tra .htaccess có rewrite đúng không</li>";
echo "</ol>";
?>
