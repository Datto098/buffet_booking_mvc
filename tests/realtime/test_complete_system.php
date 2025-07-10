<?php
/**
 * Test Complete Realtime System
 * Kiểm tra toàn bộ hệ thống thông báo realtime
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/InternalMessage.php';

class CompleteSystemTest {
    private $db;
    private $internalMessageModel;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->internalMessageModel = new InternalMessage();
    }

    public function runCompleteTest() {
        echo "<h1>🧪 Test Complete Realtime System</h1>";

        // 1. Kiểm tra cơ sở dữ liệu
        $this->testDatabase();

        // 2. Kiểm tra users
        $this->testUsers();

        // 3. Tạo thông báo test
        $this->createTestMessage();

        // 4. Kiểm tra SSE endpoint
        $this->testSSEEndpoint();

        // 5. Hướng dẫn test thủ công
        $this->showManualTestInstructions();

        // 6. Debug info
        $this->showDebugInfo();
    }

    private function testDatabase() {
        echo "<h2>1. Kiểm tra Database</h2>";

        try {
            // Kiểm tra bảng internal_messages
            $stmt = $this->db->query("SELECT COUNT(*) FROM internal_messages");
            $count = $stmt->fetchColumn();
            echo "<p style='color: green;'>✅ Bảng internal_messages: {$count} thông báo</p>";

            // Kiểm tra bảng internal_message_recipients
            $stmt = $this->db->query("SELECT COUNT(*) FROM internal_message_recipients");
            $count = $stmt->fetchColumn();
            echo "<p style='color: green;'>✅ Bảng internal_message_recipients: {$count} bản ghi</p>";

            // Kiểm tra cấu trúc bảng
            $this->checkTableStructure();

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Lỗi database: " . $e->getMessage() . "</p>";
        }
    }

    private function checkTableStructure() {
        echo "<h3>Cấu trúc bảng:</h3>";

        // Kiểm tra internal_messages
        $stmt = $this->db->query("DESCRIBE internal_messages");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p><strong>internal_messages columns:</strong></p><ul>";
        foreach ($columns as $column) {
            echo "<li>{$column['Field']} - {$column['Type']}</li>";
        }
        echo "</ul>";

        // Kiểm tra internal_message_recipients
        $stmt = $this->db->query("DESCRIBE internal_message_recipients");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p><strong>internal_message_recipients columns:</strong></p><ul>";
        foreach ($columns as $column) {
            echo "<li>{$column['Field']} - {$column['Type']}</li>";
        }
        echo "</ul>";
    }

    private function testUsers() {
        echo "<h2>2. Kiểm tra Users</h2>";

        try {
            // Super Admin
            $stmt = $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name) as name, role FROM users WHERE role = 'super_admin' LIMIT 3");
            $superAdmins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p><strong>Super Admins:</strong></p><ul>";
            foreach ($superAdmins as $admin) {
                echo "<li>ID: {$admin['id']} - {$admin['name']} ({$admin['role']})</li>";
            }
            echo "</ul>";

            // Admin/Manager
            $stmt = $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name) as name, role FROM users WHERE role IN ('manager', 'admin') LIMIT 5");
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p><strong>Admins/Managers:</strong></p><ul>";
            foreach ($admins as $admin) {
                echo "<li>ID: {$admin['id']} - {$admin['name']} ({$admin['role']})</li>";
            }
            echo "</ul>";

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
        }
    }

    private function createTestMessage() {
        echo "<h2>3. Tạo Thông Báo Test</h2>";

        try {
            // Lấy Super Admin đầu tiên
            $stmt = $this->db->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role = 'super_admin' LIMIT 1");
            $stmt->execute();
            $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$superAdmin) {
                echo "<p style='color: red;'>❌ Không tìm thấy Super Admin</p>";
                return;
            }

            // Lấy Admin đầu tiên
            $stmt = $this->db->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role = 'manager' LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                echo "<p style='color: red;'>❌ Không tìm thấy Admin</p>";
                return;
            }

            // Tạo thông báo test
            $messageData = [
                'title' => 'Test Realtime System - ' . date('Y-m-d H:i:s'),
                'content' => 'Đây là thông báo test để kiểm tra hệ thống realtime. Thời gian tạo: ' . date('Y-m-d H:i:s') . '. Nội dung này sẽ được hiển thị trong popup notification.',
                'message_type' => 'notification',
                'priority' => 'high',
                'sender_id' => $superAdmin['id'],
                'recipients' => [$admin['id']]
            ];

            $result = $this->internalMessageModel->createMessage($messageData);

            if ($result) {
                echo "<p style='color: green;'>✅ Tạo thông báo test thành công</p>";
                echo "<p><strong>Message ID:</strong> {$result}</p>";
                echo "<p><strong>Tiêu đề:</strong> {$messageData['title']}</p>";
                echo "<p><strong>Người gửi:</strong> {$superAdmin['name']} (ID: {$superAdmin['id']})</p>";
                echo "<p><strong>Người nhận:</strong> {$admin['name']} (ID: {$admin['id']})</p>";
                echo "<p><strong>Loại:</strong> {$messageData['message_type']}</p>";
                echo "<p><strong>Ưu tiên:</strong> {$messageData['priority']}</p>";

                // Kiểm tra thông báo đã được tạo
                $this->verifyMessageCreated($result, $admin['id']);
            } else {
                echo "<p style='color: red;'>❌ Lỗi tạo thông báo test</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
        }
    }

    private function verifyMessageCreated($messageId, $recipientId) {
        echo "<h3>Kiểm tra thông báo đã tạo:</h3>";

        try {
            // Kiểm tra trong bảng internal_messages
            $stmt = $this->db->prepare("SELECT * FROM internal_messages WHERE id = ?");
            $stmt->execute([$messageId]);
            $message = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($message) {
                echo "<p style='color: green;'>✅ Thông báo tồn tại trong internal_messages</p>";
            } else {
                echo "<p style='color: red;'>❌ Thông báo không tồn tại trong internal_messages</p>";
            }

            // Kiểm tra trong bảng internal_message_recipients
            $stmt = $this->db->prepare("SELECT * FROM internal_message_recipients WHERE message_id = ? AND recipient_id = ?");
            $stmt->execute([$messageId, $recipientId]);
            $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($recipient) {
                echo "<p style='color: green;'>✅ Người nhận đã được thêm vào internal_message_recipients</p>";
                echo "<p><strong>Is Read:</strong> " . ($recipient['is_read'] ? 'Yes' : 'No') . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Người nhận không tồn tại trong internal_message_recipients</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Lỗi kiểm tra: " . $e->getMessage() . "</p>";
        }
    }

    private function testSSEEndpoint() {
        echo "<h2>4. Kiểm tra SSE Endpoint</h2>";

        $endpoints = [
            'Admin SSE' => '/admin/internal-messages/sse',
            'Super Admin SSE' => '/superadmin/internal-messages/sse'
        ];

        foreach ($endpoints as $name => $endpoint) {
            echo "<h3>{$name}</h3>";
            echo "<p><strong>URL:</strong> <code>{$endpoint}</code></p>";

            $fullUrl = SITE_URL . $endpoint;
            echo "<p><strong>Full URL:</strong> <code>{$fullUrl}</code></p>";

            // Test với curl
            $this->testEndpointWithCurl($fullUrl, $name);
        }
    }

    private function testEndpointWithCurl($url, $name) {
        echo "<h4>Test với cURL:</h4>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo "<p style='color: red;'>❌ cURL Error: {$error}</p>";
        } else {
            echo "<p style='color: green;'>✅ HTTP Code: {$httpCode}</p>";

            if ($httpCode == 200) {
                echo "<p style='color: green;'>✅ Endpoint hoạt động</p>";
            } elseif ($httpCode == 401) {
                echo "<p style='color: orange;'>⚠️ Cần đăng nhập (điều này là bình thường)</p>";
            } else {
                echo "<p style='color: red;'>❌ Endpoint không hoạt động</p>";
            }
        }
    }

    private function showManualTestInstructions() {
        echo "<h2>5. Hướng Dẫn Test Thủ Công</h2>";

        echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px;'>";
        echo "<h3>🔧 Cách Test Realtime:</h3>";
        echo "<ol>";
        echo "<li><strong>Mở 2 tab trình duyệt</strong></li>";
        echo "<li><strong>Tab 1 - Super Admin:</strong>";
        echo "<ul>";
        echo "<li>Đăng nhập Super Admin: <code>" . SITE_URL . "/superadmin</code></li>";
        echo "<li>Truy cập: <code>" . SITE_URL . "/superadmin/internal-messages/send</code></li>";
        echo "<li>Gửi thông báo mới</li>";
        echo "</ul></li>";
        echo "<li><strong>Tab 2 - Admin:</strong>";
        echo "<ul>";
        echo "<li>Đăng nhập Admin: <code>" . SITE_URL . "/admin</code></li>";
        echo "<li>Mở Developer Tools (F12) → Console</li>";
        echo "<li>Xem có log 'SSE Connected' không</li>";
        echo "<li>Gửi thông báo từ Tab 1 và xem có popup không</li>";
        echo "</ul></li>";
        echo "</ol>";

        echo "<h3>🐛 Debug SSE:</h3>";
        echo "<p>Mở Console trong Developer Tools và xem các log:</p>";
        echo "<ul>";
        echo "<li><code>SSE Connected</code> - Kết nối thành công</li>";
        echo "<li><code>SSE Error</code> - Lỗi kết nối</li>";
        echo "<li><code>Error parsing SSE message</code> - Lỗi parse dữ liệu</li>";
        echo "<li><code>Error updating unread count</code> - Lỗi cập nhật số thông báo</li>";
        echo "</ul>";

        echo "<h3>🔍 Kiểm Tra Network:</h3>";
        echo "<p>Trong Developer Tools → Network tab:</p>";
        echo "<ul>";
        echo "<li>Tìm request đến <code>/admin/internal-messages/sse</code></li>";
        echo "<li>Kiểm tra Response có dạng <code>data: {...}</code> không</li>";
        echo "<li>Kiểm tra Status Code có phải 200 không</li>";
        echo "</ul>";

        echo "<h3>📝 Test File:</h3>";
        echo "<p>Sử dụng file test: <code>" . SITE_URL . "/tests/realtime/simple_sse_test.html</code></p>";
        echo "</div>";
    }

    private function showDebugInfo() {
        echo "<h2>6. Debug Info</h2>";

        echo "<div style='background: #e9ecef; padding: 15px; border-radius: 8px;'>";
        echo "<h3>📊 Thông tin hệ thống:</h3>";
        echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
        echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "</p>";
        echo "<p><strong>Site URL:</strong> " . SITE_URL . "</p>";
        echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

        // Kiểm tra logs
        $logFile = '../logs/application.log';
        if (file_exists($logFile)) {
            $logSize = filesize($logFile);
            echo "<p><strong>Log File Size:</strong> " . number_format($logSize) . " bytes</p>";
        }

        echo "<h3>🔧 Các file quan trọng:</h3>";
        $files = [
            'realtime-notifications.js' => '../assets/js/realtime-notifications.js',
            'InternalMessageController.php' => '../controllers/InternalMessageController.php',
            'InternalMessage.php' => '../models/InternalMessage.php',
            'Admin Header' => '../views/admin/layouts/header.php',
            'SuperAdmin Header' => '../views/layouts/superadmin_header.php'
        ];

        foreach ($files as $name => $path) {
            if (file_exists($path)) {
                $size = filesize($path);
                echo "<p style='color: green;'>✅ {$name}: " . number_format($size) . " bytes</p>";
            } else {
                echo "<p style='color: red;'>❌ {$name}: Không tồn tại</p>";
            }
        }
        echo "</div>";
    }
}

// Chạy test
$test = new CompleteSystemTest();
$test->runCompleteTest();
?>
