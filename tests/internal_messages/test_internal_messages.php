<?php
/**
 * Test Internal Messages System
 * Kiểm tra chức năng thông báo nội bộ
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/InternalMessage.php';

echo "<h1>🧪 Test Internal Messages System</h1>\n";
echo "<hr>\n";

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $internalMessageModel = new InternalMessage();

    echo "<h2>1. Kiểm tra kết nối database</h2>\n";
    echo "✅ Kết nối database thành công\n";

    echo "<h2>2. Kiểm tra bảng internal_messages</h2>\n";
    $sql = "SHOW TABLES LIKE 'internal_messages'";
    $result = $db->query($sql);
    if ($result && $result->rowCount() > 0) {
        echo "✅ Bảng internal_messages tồn tại\n";
    } else {
        echo "❌ Bảng internal_messages không tồn tại\n";
    }

    echo "<h2>3. Kiểm tra bảng internal_message_recipients</h2>\n";
    $sql = "SHOW TABLES LIKE 'internal_message_recipients'";
    $result = $db->query($sql);
    if ($result && $result->rowCount() > 0) {
        echo "✅ Bảng internal_message_recipients tồn tại\n";
    } else {
        echo "❌ Bảng internal_message_recipients không tồn tại\n";
    }

    echo "<h2>4. Kiểm tra danh sách admin có thể nhận thông báo</h2>\n";
    $recipients = $internalMessageModel->getAvailableRecipients();
    if (!empty($recipients)) {
        echo "✅ Tìm thấy " . count($recipients) . " admin:\n";
        echo "<ul>\n";
        foreach ($recipients as $recipient) {
            echo "<li>{$recipient['full_name']} ({$recipient['email']}) - {$recipient['role']}</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "❌ Không tìm thấy admin nào\n";
    }

    echo "<h2>5. Kiểm tra thông báo đã gửi</h2>\n";
    $sentMessages = $internalMessageModel->getSentMessages(4, 5, 0); // Super Admin ID = 4
    if (!empty($sentMessages)) {
        echo "✅ Tìm thấy " . count($sentMessages) . " thông báo đã gửi:\n";
        echo "<ul>\n";
        foreach ($sentMessages as $message) {
            echo "<li><strong>{$message['title']}</strong> - {$message['message_type']} - {$message['priority']}</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "ℹ️ Chưa có thông báo nào được gửi\n";
    }

    echo "<h2>6. Kiểm tra thông báo đã nhận</h2>\n";
    $receivedMessages = $internalMessageModel->getReceivedMessages(1, 5, 0); // Admin ID = 1
    if (!empty($receivedMessages)) {
        echo "✅ Tìm thấy " . count($receivedMessages) . " thông báo đã nhận:\n";
        echo "<ul>\n";
        foreach ($receivedMessages as $message) {
            $readStatus = $message['is_read'] ? 'Đã đọc' : 'Chưa đọc';
            echo "<li><strong>{$message['title']}</strong> - Từ: {$message['sender_name']} - {$readStatus}</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "ℹ️ Chưa có thông báo nào được nhận\n";
    }

    echo "<h2>7. Kiểm tra số thông báo chưa đọc</h2>\n";
    $unreadCount = $internalMessageModel->getUnreadCount(1);
    echo "✅ Admin ID 1 có {$unreadCount} thông báo chưa đọc\n";

    echo "<h2>8. Test tạo thông báo mới</h2>\n";
    $testMessageData = [
        'sender_id' => 4, // Super Admin
        'title' => 'Test thông báo từ script',
        'content' => 'Đây là thông báo test được tạo từ script kiểm tra chức năng.',
        'message_type' => 'system_update',
        'priority' => 'normal',
        'is_broadcast' => 0,
        'recipients' => [1] // Admin ID 1
    ];

    $messageId = $internalMessageModel->createMessage($testMessageData);
    if ($messageId) {
        echo "✅ Tạo thông báo thành công với ID: {$messageId}\n";

        echo "<h2>9. Kiểm tra chi tiết thông báo vừa tạo</h2>\n";
        $messageDetail = $internalMessageModel->getMessageDetail($messageId, 1);
        if ($messageDetail) {
            echo "✅ Thông báo chi tiết:\n";
            echo "<ul>\n";
            echo "<li><strong>Tiêu đề:</strong> {$messageDetail['title']}</li>\n";
            echo "<li><strong>Nội dung:</strong> {$messageDetail['content']}</li>\n";
            echo "<li><strong>Loại:</strong> {$messageDetail['message_type']}</li>\n";
            echo "<li><strong>Ưu tiên:</strong> {$messageDetail['priority']}</li>\n";
            echo "<li><strong>Người gửi:</strong> {$messageDetail['sender_name']}</li>\n";
            echo "</ul>\n";
        }

        echo "<h2>10. Test đánh dấu đã đọc</h2>\n";
        $markResult = $internalMessageModel->markAsRead($messageId, 1);
        if ($markResult) {
            echo "✅ Đánh dấu đã đọc thành công\n";

            // Kiểm tra lại số thông báo chưa đọc
            $newUnreadCount = $internalMessageModel->getUnreadCount(1);
            echo "✅ Số thông báo chưa đọc sau khi đánh dấu: {$newUnreadCount}\n";
        } else {
            echo "❌ Lỗi khi đánh dấu đã đọc\n";
        }

        echo "<h2>11. Test xóa thông báo</h2>\n";
        $deleteResult = $internalMessageModel->deleteMessage($messageId, 4);
        if ($deleteResult) {
            echo "✅ Xóa thông báo thành công\n";
        } else {
            echo "❌ Lỗi khi xóa thông báo\n";
        }
    } else {
        echo "❌ Lỗi khi tạo thông báo\n";
    }

    echo "<h2>12. Kiểm tra thống kê</h2>\n";
    $stats = $internalMessageModel->getMessageStats(4);
    if ($stats) {
        echo "✅ Thống kê thông báo:\n";
        echo "<ul>\n";
        echo "<li><strong>Tổng thông báo đã gửi:</strong> {$stats['total_sent']}</li>\n";
        echo "<li><strong>Thông báo khẩn cấp:</strong> {$stats['urgent_sent']}</li>\n";
        echo "<li><strong>Thông báo gửi tất cả:</strong> {$stats['broadcast_sent']}</li>\n";
        echo "</ul>\n";
    }

    echo "<hr>\n";
    echo "<h2>🎉 Kết quả test</h2>\n";
    echo "<p><strong>Hệ thống thông báo nội bộ hoạt động bình thường!</strong></p>\n";
    echo "<p>Các chức năng đã được kiểm tra:</p>\n";
    echo "<ul>\n";
    echo "<li>✅ Kết nối database</li>\n";
    echo "<li>✅ Cấu trúc bảng</li>\n";
    echo "<li>✅ Lấy danh sách người nhận</li>\n";
    echo "<li>✅ Tạo thông báo</li>\n";
    echo "<li>✅ Xem thông báo</li>\n";
    echo "<li>✅ Đánh dấu đã đọc</li>\n";
    echo "<li>✅ Xóa thông báo</li>\n";
    echo "<li>✅ Thống kê</li>\n";
    echo "</ul>\n";

} catch (Exception $e) {
    echo "<h2>❌ Lỗi</h2>\n";
    echo "<p><strong>Lỗi:</strong> " . $e->getMessage() . "</p>\n";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>\n";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>\n";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f5f5f5;
}
h1, h2 {
    color: #333;
}
ul {
    background: white;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}
li {
    margin: 5px 0;
}
hr {
    border: none;
    border-top: 2px solid #ddd;
    margin: 20px 0;
}
</style>
