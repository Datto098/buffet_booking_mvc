<?php
/**
 * Setup Internal Messages System
 * Chạy migration và test chức năng thông báo nội bộ
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "<h1>🚀 Setup Internal Messages System</h1>\n";
echo "<hr>\n";

try {
    // Kết nối database
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>1. Kiểm tra kết nối database</h2>\n";
    echo "✅ Kết nối database thành công\n";

    echo "<h2>2. Chạy migration tạo bảng</h2>\n";

    // Tạo bảng internal_messages
    $sql1 = "CREATE TABLE IF NOT EXISTS `internal_messages` (
        `id` int NOT NULL AUTO_INCREMENT,
        `sender_id` int NOT NULL,
        `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        `attachment_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `attachment_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `message_type` enum('system_update','policy_change','maintenance','personal','general') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
        `priority` enum('low','normal','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
        `is_broadcast` tinyint(1) DEFAULT '0',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `fk_internal_messages_sender` (`sender_id`),
        KEY `idx_message_type` (`message_type`),
        KEY `idx_priority` (`priority`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql1);
    echo "✅ Bảng internal_messages đã được tạo\n";

    // Tạo bảng internal_message_recipients
    $sql2 = "CREATE TABLE IF NOT EXISTS `internal_message_recipients` (
        `id` int NOT NULL AUTO_INCREMENT,
        `message_id` int NOT NULL,
        `recipient_id` int NOT NULL,
        `is_read` tinyint(1) DEFAULT '0',
        `read_at` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_message_recipient` (`message_id`,`recipient_id`),
        KEY `fk_message_recipients_message` (`message_id`),
        KEY `fk_message_recipients_recipient` (`recipient_id`),
        KEY `idx_is_read` (`is_read`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql2);
    echo "✅ Bảng internal_message_recipients đã được tạo\n";

    echo "<h2>3. Tạo thư mục upload</h2>\n";
    $uploadDir = 'uploads/internal_messages/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "✅ Thư mục upload đã được tạo: {$uploadDir}\n";
    } else {
        echo "✅ Thư mục upload đã tồn tại: {$uploadDir}\n";
    }

    echo "<h2>4. Tạo dữ liệu mẫu</h2>\n";

    // Kiểm tra xem đã có dữ liệu mẫu chưa
    $checkSql = "SELECT COUNT(*) FROM internal_messages WHERE title LIKE '%Chào mừng%'";
    $count = $db->query($checkSql)->fetchColumn();

    if ($count == 0) {
        // Tạo thông báo mẫu
        $sampleSql = "INSERT INTO internal_messages (sender_id, title, content, message_type, priority, is_broadcast, created_at) VALUES
        (4, 'Chào mừng sử dụng hệ thống thông báo nội bộ', 'Đây là thông báo đầu tiên để kiểm tra hệ thống thông báo nội bộ. Hệ thống này cho phép Super Admin gửi thông báo đến các Admin một cách nhanh chóng và hiệu quả.', 'general', 'normal', 1, NOW())";

        $db->exec($sampleSql);
        $messageId = $db->lastInsertId();

        // Gán người nhận (giả sử user ID 1 và 4 là admin)
        $recipientSql = "INSERT INTO internal_message_recipients (message_id, recipient_id, created_at) VALUES
        ({$messageId}, 1, NOW()),
        ({$messageId}, 4, NOW())";

        $db->exec($recipientSql);
        echo "✅ Dữ liệu mẫu đã được tạo\n";
    } else {
        echo "✅ Dữ liệu mẫu đã tồn tại\n";
    }

    echo "<h2>5. Kiểm tra danh sách admin</h2>\n";
    $adminSql = "SELECT id, CONCAT(first_name, ' ', last_name) as full_name, email, role FROM users WHERE role IN ('manager', 'super_admin') ORDER BY first_name, last_name";
    $admins = $db->query($adminSql)->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($admins)) {
        echo "✅ Tìm thấy " . count($admins) . " admin:\n";
        echo "<ul>\n";
        foreach ($admins as $admin) {
            echo "<li>{$admin['full_name']} ({$admin['email']}) - {$admin['role']}</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "⚠️ Chưa có admin nào trong hệ thống\n";
    }

    echo "<hr>\n";
    echo "<h2>🎉 Setup hoàn thành!</h2>\n";
    echo "<p><strong>Hệ thống thông báo nội bộ đã sẵn sàng sử dụng!</strong></p>\n";

    echo "<h3>📋 Hướng dẫn sử dụng:</h3>\n";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h4>🔴 Super Admin:</h4>\n";
    echo "<ul>\n";
    echo "<li>Truy cập: <a href='" . SITE_URL . "/superadmin/internal-messages' target='_blank'>" . SITE_URL . "/superadmin/internal-messages</a></li>\n";
    echo "<li>Click vào menu <strong>\"Internal Messages\"</strong> trong sidebar</li>\n";
    echo "<li>Chọn <strong>\"Gửi thông báo mới\"</strong> để tạo thông báo</li>\n";
    echo "<li>Xem <strong>\"Thông báo đã gửi\"</strong> để quản lý</li>\n";
    echo "</ul>\n";

    echo "<h4>🔵 Admin:</h4>\n";
    echo "<ul>\n";
    echo "<li>Truy cập: <a href='" . SITE_URL . "/admin/internal-messages' target='_blank'>" . SITE_URL . "/admin/internal-messages</a></li>\n";
    echo "<li>Click vào menu <strong>\"Internal Messages\"</strong> trong sidebar</li>\n";
    echo "<li>Xem danh sách thông báo đã nhận</li>\n";
    echo "<li>Click vào thông báo để xem chi tiết</li>\n";
    echo "</ul>\n";
    echo "</div>\n";

    echo "<h3>🧪 Test chức năng:</h3>\n";
    echo "<p>Chạy file test để kiểm tra: <a href='" . SITE_URL . "/tests/internal_messages/test_internal_messages.php' target='_blank'>Test Internal Messages</a></p>\n";

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
h1, h2, h3, h4 {
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
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
