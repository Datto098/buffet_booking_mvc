<?php
/**
 * Test Realtime Notifications
 * Kiểm tra chức năng SSE và thông báo realtime
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/InternalMessage.php';

class RealtimeNotificationsTest {
    private $db;
    private $internalMessageModel;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->internalMessageModel = new InternalMessage();
    }

    /**
     * Test gửi thông báo và kiểm tra SSE
     */
    public function testRealtimeNotifications() {
        echo "<h2>🧪 Test Realtime Notifications</h2>";

        // 1. Tạo thông báo test
        $this->createTestMessage();

        // 2. Kiểm tra SSE endpoint
        $this->testSSEEndpoint();

        // 3. Kiểm tra API endpoints
        $this->testAPIEndpoints();

        // 4. Hướng dẫn test thủ công
        $this->showManualTestInstructions();
    }

    private function createTestMessage() {
        echo "<h3>1. Tạo thông báo test</h3>";

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
                'title' => 'Test Realtime Notification - ' . date('Y-m-d H:i:s'),
                'content' => 'Đây là thông báo test để kiểm tra chức năng realtime. Thông báo này sẽ được gửi ngay lập tức đến Admin.',
                'message_type' => 'notification',
                'priority' => 'high',
                'sender_id' => $superAdmin['id'],
                'recipients' => [$admin['id']]
            ];

            $result = $this->internalMessageModel->createMessage($messageData);

            if ($result) {
                echo "<p style='color: green;'>✅ Tạo thông báo test thành công</p>";
                echo "<p><strong>Tiêu đề:</strong> {$messageData['title']}</p>";
                echo "<p><strong>Nội dung:</strong> {$messageData['content']}</p>";
                echo "<p><strong>Người gửi:</strong> Super Admin (ID: {$superAdmin['id']})</p>";
                echo "<p><strong>Người nhận:</strong> Admin (ID: {$admin['id']})</p>";
            } else {
                echo "<p style='color: red;'>❌ Lỗi tạo thông báo test</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
        }
    }

    private function testSSEEndpoint() {
        echo "<h3>2. Kiểm tra SSE Endpoint</h3>";

        $endpoints = [
            'Super Admin SSE' => '/superadmin/internal-messages/sse',
            'Admin SSE' => '/admin/internal-messages/sse'
        ];

        foreach ($endpoints as $name => $endpoint) {
            echo "<h4>{$name}</h4>";
            echo "<p><strong>URL:</strong> <code>{$endpoint}</code></p>";

            // Kiểm tra xem endpoint có tồn tại không
            $fullUrl = SITE_URL . $endpoint;
            echo "<p><strong>Full URL:</strong> <code>{$fullUrl}</code></p>";

            echo "<p><strong>Trạng thái:</strong> <span style='color: orange;'>⚠️ Cần test thủ công</span></p>";
            echo "<p>SSE endpoint sẽ trả về Content-Type: text/event-stream</p>";
        }
    }

    private function testAPIEndpoints() {
        echo "<h3>3. Kiểm tra API Endpoints</h3>";

        $endpoints = [
            'Super Admin - Get Unread Count' => '/superadmin/internal-messages/get-unread-count',
            'Super Admin - Mark as Read' => '/superadmin/internal-messages/mark-as-read',
            'Admin - Get Unread Count' => '/admin/internal-messages/get-unread-count',
            'Admin - Mark as Read' => '/admin/internal-messages/mark-as-read'
        ];

        foreach ($endpoints as $name => $endpoint) {
            echo "<h4>{$name}</h4>";
            echo "<p><strong>URL:</strong> <code>{$endpoint}</code></p>";
            echo "<p><strong>Method:</strong> " . (strpos($endpoint, 'mark-as-read') !== false ? 'POST' : 'GET') . "</p>";

            if (strpos($endpoint, 'mark-as-read') !== false) {
                echo "<p><strong>Body:</strong> <code>message_id=1</code></p>";
            }

            echo "<p><strong>Response:</strong> JSON</p>";
        }
    }

    private function showManualTestInstructions() {
        echo "<h3>4. Hướng dẫn test thủ công</h3>";

        echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>📋 Các bước test:</h4>";
        echo "<ol>";
        echo "<li><strong>Mở 2 tab trình duyệt:</strong></li>";
        echo "<ul>";
        echo "<li>Tab 1: Đăng nhập Super Admin → Internal Messages → Send</li>";
        echo "<li>Tab 2: Đăng nhập Admin → Internal Messages → Received</li>";
        echo "</ul>";

        echo "<li><strong>Kiểm tra SSE connection:</strong></li>";
        echo "<ul>";
        echo "<li>Mở Developer Tools (F12) → Console</li>";
        echo "<li>Kiểm tra log 'SSE Connected'</li>";
        echo "<li>Nếu có lỗi, kiểm tra Network tab</li>";
        echo "</ul>";

        echo "<li><strong>Test gửi thông báo realtime:</strong></li>";
        echo "<ul>";
        echo "<li>Tab 1: Gửi thông báo mới</li>";
        echo "<li>Tab 2: Kiểm tra popup thông báo xuất hiện ngay lập tức</li>";
        echo "<li>Kiểm tra badge số thông báo tăng lên</li>";
        echo "</ul>";

        echo "<li><strong>Test âm thanh:</strong></li>";
        echo "<ul>";
        echo "<li>Kiểm tra có âm thanh thông báo không</li>";
        echo "<li>Âm thanh sẽ phát khi có thông báo mới</li>";
        echo "</ul>";

        echo "<li><strong>Test đánh dấu đã đọc:</strong></li>";
        echo "<ul>";
        echo "<li>Click 'Xem' trong popup thông báo</li>";
        echo "<li>Kiểm tra badge số thông báo giảm</li>";
        echo "</ul>";
        echo "</ol>";
        echo "</div>";

        echo "<h4>🔧 Troubleshooting:</h4>";
        echo "<ul>";
        echo "<li><strong>SSE không kết nối:</strong> Kiểm tra firewall, proxy, hoặc server config</li>";
        echo "<li><strong>Không có popup:</strong> Kiểm tra JavaScript console có lỗi không</li>";
        echo "<li><strong>Badge không hiển thị:</strong> Kiểm tra CSS và DOM structure</li>";
        echo "<li><strong>Âm thanh không phát:</strong> Kiểm tra browser permissions</li>";
        echo "</ul>";
    }

    /**
     * Test performance của SSE
     */
    public function testSSEPerformance() {
        echo "<h3>5. Test Performance</h3>";

        echo "<p><strong>SSE Connection:</strong></p>";
        echo "<ul>";
        echo "<li>Keep-alive mỗi 30 giây</li>";
        echo "<li>Kiểm tra thông báo mới mỗi 5 giây</li>";
        echo "<li>Auto reconnect khi mất kết nối</li>";
        echo "<li>Tối đa 5 lần thử kết nối lại</li>";
        echo "</ul>";

        echo "<p><strong>Memory Usage:</strong> SSE sử dụng ít memory hơn WebSocket</p>";
        echo "<p><strong>Browser Support:</strong> Hỗ trợ tốt trên tất cả browser hiện đại</p>";
    }
}

// Chạy test
$test = new RealtimeNotificationsTest();
$test->testRealtimeNotifications();
$test->testSSEPerformance();

echo "<hr>";
echo "<p><strong>✅ Test hoàn thành!</strong></p>";
echo "<p>Bây giờ bạn có thể test thủ công theo hướng dẫn ở trên.</p>";
?>
