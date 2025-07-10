<?php
/**
 * Debug Session Trace
 * Theo dõi thay đổi session khi truy cập internal-messages
 */

session_start();

echo "<h1>🔍 Debug Session Trace</h1>";

// Lưu session ban đầu
$initialSession = $_SESSION;

echo "<h2>Session ban đầu:</h2>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
print_r($initialSession);
echo "</pre>";

echo "<h2>Test URLs:</h2>";
echo "<ul>";
echo "<li><a href='http://localhost/buffet_booking_mvc/superadmin/internal-messages' target='_blank'>Super Admin Internal Messages</a></li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/superadmin/internal-messages/send' target='_blank'>Super Admin Send Message</a></li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/superadmin/internal-messages/sent' target='_blank'>Super Admin Sent Messages</a></li>";
echo "</ul>";

echo "<h2>Hướng dẫn test:</h2>";
echo "<ol>";
echo "<li>Click vào link 'Super Admin Internal Messages'</li>";
echo "<li>Nếu bị redirect về trang chủ, quay lại đây</li>";
echo "<li>Click 'Check Session After Redirect' để xem session có thay đổi không</li>";
echo "</ol>";

// Form để check session sau khi redirect
echo "<form method='post' style='margin: 20px 0; padding: 20px; background: #e9ecef; border-radius: 8px;'>";
echo "<h3>Check Session After Redirect:</h3>";
echo "<button type='submit' name='check_session' style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>Check Session After Redirect</button>";
echo "</form>";

if (isset($_POST['check_session'])) {
    echo "<h2>Session sau khi redirect:</h2>";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    print_r($_SESSION);
    echo "</pre>";

    echo "<h3>So sánh:</h3>";

    // So sánh user_role
    $initialRole = $initialSession['user_role'] ?? 'not set';
    $currentRole = $_SESSION['user_role'] ?? 'not set';

    echo "<p><strong>user_role ban đầu:</strong> {$initialRole}</p>";
    echo "<p><strong>user_role hiện tại:</strong> {$currentRole}</p>";

    if ($initialRole === $currentRole) {
        echo "<p style='color: green;'>✅ user_role không thay đổi</p>";
    } else {
        echo "<p style='color: red;'>❌ user_role đã thay đổi từ '{$initialRole}' thành '{$currentRole}'</p>";
    }

    // So sánh user_id
    $initialId = $initialSession['user_id'] ?? 'not set';
    $currentId = $_SESSION['user_id'] ?? 'not set';

    echo "<p><strong>user_id ban đầu:</strong> {$initialId}</p>";
    echo "<p><strong>user_id hiện tại:</strong> {$currentId}</p>";

    if ($initialId === $currentId) {
        echo "<p style='color: green;'>✅ user_id không thay đổi</p>";
    } else {
        echo "<p style='color: red;'>❌ user_id đã thay đổi từ '{$initialId}' thành '{$currentId}'</p>";
    }
}

echo "<h2>Debug Info:</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";
echo "<p><strong>Current URL:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";

// Test requireSuperAdmin logic
echo "<h2>Test requireSuperAdmin Logic:</h2>";

$hasLogin = isset($_SESSION['user_id']);
$hasValidRole = isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'manager', 'super_admin']);
$isSuperAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin';

echo "<p><strong>Has Login:</strong> " . ($hasLogin ? '✅ Yes' : '❌ No') . "</p>";
echo "<p><strong>Has Valid Role:</strong> " . ($hasValidRole ? '✅ Yes' : '❌ No') . "</p>";
echo "<p><strong>Is Super Admin:</strong> " . ($isSuperAdmin ? '✅ Yes' : '❌ No') . "</p>";

if ($hasLogin && $hasValidRole && $isSuperAdmin) {
    echo "<p style='color: green;'>✅ requireSuperAdmin() sẽ cho phép truy cập</p>";
} else {
    echo "<p style='color: red;'>❌ requireSuperAdmin() sẽ redirect</p>";

    if (!$hasLogin) {
        echo "<p style='color: red;'>- Lý do: Không có login</p>";
    }
    if (!$hasValidRole) {
        echo "<p style='color: red;'>- Lý do: Role không hợp lệ</p>";
    }
    if (!$isSuperAdmin) {
        echo "<p style='color: red;'>- Lý do: Không phải super_admin</p>";
    }
}
?>
