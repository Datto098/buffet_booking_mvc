<?php
/**
 * Test SSE Connection
 * Kiểm tra kết nối SSE và debug vấn đề realtime
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/InternalMessage.php';

class SSETest {
    private $db;
    private $internalMessageModel;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->internalMessageModel = new InternalMessage();
    }

    public function runTests() {
        echo "<h1>🧪 Test SSE Connection</h1>";

        // 1. Kiểm tra session
        $this->testSession();

        // 2. Kiểm tra database connection
        $this->testDatabase();

        // 3. Kiểm tra SSE endpoint
        $this->testSSEEndpoint();

        // 4. Tạo thông báo test
        $this->createTestMessage();

        // 5. Hướng dẫn test thủ công
        $this->showManualTestInstructions();
    }

    private function testSession() {
        echo "<h2>1. Kiểm tra Session</h2>";

        session_start();

        if (isset($_SESSION['user_id'])) {
            echo "<p style='color: green;'>✅ Session user_id: {$_SESSION['user_id']}</p>";
        } else {
            echo "<p style='color: red;'>❌ Không có session user_id</p>";
        }

        if (isset($_SESSION['user_role'])) {
            echo "<p style='color: green;'>✅ Session user_role: {$_SESSION['user_role']}</p>";
        } else {
            echo "<p style='color: red;'>❌ Không có session user_role</p>";
        }
    }

    private function testDatabase() {
        echo "<h2>2. Kiểm tra Database</h2>";

        try {
            // Kiểm tra bảng internal_messages
            $stmt = $this->db->query("SELECT COUNT(*) FROM internal_messages");
            $count = $stmt->fetchColumn();
            echo "<p style='color: green;'>✅ Bảng internal_messages: {$count} thông báo</p>";

            // Kiểm tra bảng internal_message_recipients
            $stmt = $this->db->query("SELECT COUNT(*) FROM internal_message_recipients");
            $count = $stmt->fetchColumn();
            echo "<p style='color: green;'>✅ Bảng internal_message_recipients: {$count} bản ghi</p>";

            // Kiểm tra users có role admin
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role IN ('manager', 'admin')");
            $count = $stmt->fetchColumn();
            echo "<p style='color: green;'>✅ Users admin: {$count} người</p>";

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Lỗi database: " . $e->getMessage() . "</p>";
        }
    }

    private function testSSEEndpoint() {
        echo "<h2>3. Kiểm tra SSE Endpoint</h2>";

        $endpoints = [
            'Admin SSE' => '/admin/internal-messages/sse',
            'Super Admin SSE' => '/superadmin/internal-messages/sse'
        ];

        foreach ($endpoints as $name => $endpoint) {
            echo "<h3>{$name}</h3>";
            echo "<p><strong>URL:</strong> <code>{$endpoint}</code></p>";

            // Kiểm tra xem endpoint có tồn tại không
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
                echo "<p style='color: orange;'>⚠️ Cần đăng nhập</p>";
            } else {
                echo "<p style='color: red;'>❌ Endpoint không hoạt động</p>";
            }
        }
    }

    private function createTestMessage() {
        echo "<h2>4. Tạo Thông Báo Test</h2>";

        try {
            // Lấy Super Admin đầu tiên
            $stmt = $this->db->prepare("SELECT id FROM users WHERE role = 'super_admin' LIMIT 1");
            $stmt->execute();
            $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$superAdmin) {
                echo "<p style='color: red;'>❌ Không tìm thấy Super Admin</p>";
                return;
            }

            // Lấy Admin đầu tiên
            $stmt = $this->db->prepare("SELECT id FROM users WHERE role = 'manager' LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                echo "<p style='color: red;'>❌ Không tìm thấy Admin</p>";
                return;
            }

            // Tạo thông báo test
            $messageData = [
                'title' => 'Test SSE - ' . date('Y-m-d H:i:s'),
                'content' => 'Đây là thông báo test để kiểm tra SSE. Thời gian: ' . date('Y-m-d H:i:s'),
                'message_type' => 'notification',
                'priority' => 'high',
                'sender_id' => $superAdmin['id'],
                'recipients' => [$admin['id']]
            ];

            $result = $this->internalMessageModel->createMessage($messageData);

            if ($result) {
                echo "<p style='color: green;'>✅ Tạo thông báo test thành công (ID: {$result})</p>";
                echo "<p><strong>Tiêu đề:</strong> {$messageData['title']}</p>";
                echo "<p><strong>Người gửi:</strong> Super Admin (ID: {$superAdmin['id']})</p>";
                echo "<p><strong>Người nhận:</strong> Admin (ID: {$admin['id']})</p>";
            } else {
                echo "<p style='color: red;'>❌ Lỗi tạo thông báo test</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
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
        echo "</div>";
    }
}

// Chạy test
$test = new SSETest();
$test->runTests();
?>
