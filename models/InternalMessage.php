<?php
/**
 * Internal Message Model
 * Quản lý thông báo nội bộ từ Super Admin đến Admin
 */

class InternalMessage extends BaseModel {
    protected $table = 'internal_messages';

    /**
     * Tạo thông báo mới
     */
    public function createMessage($data) {
        try {
            // Ensure tables exist before proceeding
            $this->ensureTables();

            error_log("Creating internal message with data: " . print_r($data, true));

            $this->db->beginTransaction();

            // Tạo thông báo chính
            $messageData = [
                'sender_id' => $data['sender_id'],
                'title' => $data['title'],
                'content' => $data['content'],
                'attachment_path' => $data['attachment_path'] ?? null,
                'attachment_name' => $data['attachment_name'] ?? null,
                'message_type' => $data['message_type'] ?? 'general',
                'priority' => $data['priority'] ?? 'normal',
                'is_broadcast' => $data['is_broadcast'] ?? 0
            ];

            error_log("Prepared message data: " . print_r($messageData, true));

            $sql = "INSERT INTO {$this->table} (sender_id, title, content, attachment_path, attachment_name, message_type, priority, is_broadcast)
                    VALUES (:sender_id, :title, :content, :attachment_path, :attachment_name, :message_type, :priority, :is_broadcast)";

            $stmt = $this->db->prepare($sql);
            error_log("Executing SQL: " . $sql);

            $result = $stmt->execute($messageData);
            error_log("SQL execution result: " . ($result ? "success" : "failed"));

            $messageId = $this->db->lastInsertId();
            error_log("Generated message ID: " . $messageId);

            // Thêm người nhận
            if (!empty($data['recipients'])) {
                error_log("Adding recipients: " . print_r($data['recipients'], true));
                $this->addRecipients($messageId, $data['recipients']);
            } else {
                error_log("No recipients to add");
            }

            $this->db->commit();
            error_log("Transaction committed successfully");
            return $messageId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error creating internal message: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            // Log any database-specific errors
            if ($stmt && $stmt->errorInfo()[2]) {
                error_log("Database error: " . print_r($stmt->errorInfo(), true));
            }

            return false;
        }
    }

    /**
     * Thêm người nhận cho thông báo
     */
    public function addRecipients($messageId, $recipientIds) {
        $sql = "INSERT INTO internal_message_recipients (message_id, recipient_id) VALUES (:message_id, :recipient_id)";
        $stmt = $this->db->prepare($sql);

        foreach ($recipientIds as $recipientId) {
            $stmt->execute([
                'message_id' => $messageId,
                'recipient_id' => $recipientId
            ]);
        }
    }

    /**
     * Lấy danh sách thông báo đã gửi
     */
    public function getSentMessages($senderId, $limit = 20, $offset = 0) {
        $sql = "SELECT im.*,
                       COUNT(imr.id) as recipient_count,
                       COUNT(CASE WHEN imr.is_read = 1 THEN 1 END) as read_count
                FROM {$this->table} im
                LEFT JOIN internal_message_recipients imr ON im.id = imr.message_id
                WHERE im.sender_id = :sender_id
                GROUP BY im.id
                ORDER BY im.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sender_id', $senderId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách thông báo đã nhận
     */
    public function getReceivedMessages($recipientId, $limit = 20, $offset = 0) {
        $sql = "SELECT im.*, imr.is_read, imr.read_at,
                       CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.email as sender_email
                FROM {$this->table} im
                INNER JOIN internal_message_recipients imr ON im.id = imr.message_id
                INNER JOIN users u ON im.sender_id = u.id
                WHERE imr.recipient_id = :recipient_id
                ORDER BY im.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':recipient_id', $recipientId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách thông báo đã nhận theo recipient ID - Phiên bản tối ưu
     */
    public function getReceivedMessagesOptimized($recipientId, $limit = 10, $offset = 0) {
        // Chỉ lấy các trường cần thiết để hiển thị danh sách
        // Bỏ qua nội dung thông báo đầy đủ và các thông tin không cần thiết để tăng tốc
        $sql = "SELECT im.id, im.title, LEFT(im.content, 150) as content, im.message_type, im.priority,
                       im.attachment_path, im.attachment_name, im.created_at,
                       imr.is_read, imr.read_at,
                       CONCAT(u.first_name, ' ', u.last_name) as sender_name
                FROM {$this->table} im
                INNER JOIN internal_message_recipients imr ON im.id = imr.message_id
                INNER JOIN users u ON im.sender_id = u.id
                WHERE imr.recipient_id = :recipient_id
                ORDER BY im.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':recipient_id', $recipientId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết thông báo
     */
    public function getMessageDetail($messageId, $recipientId = null) {
        $sql = "SELECT im.*,
                       CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.email as sender_email,
                       imr.is_read, imr.read_at
                FROM {$this->table} im
                INNER JOIN users u ON im.sender_id = u.id";

        if ($recipientId) {
            $sql .= " LEFT JOIN internal_message_recipients imr ON im.id = imr.message_id AND imr.recipient_id = :recipient_id";
        }

        $sql .= " WHERE im.id = :message_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message_id', $messageId, PDO::PARAM_INT);

        if ($recipientId) {
            $stmt->bindValue(':recipient_id', $recipientId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead($messageId, $recipientId) {
        $sql = "UPDATE internal_message_recipients
                SET is_read = 1, read_at = CURRENT_TIMESTAMP
                WHERE message_id = :message_id AND recipient_id = :recipient_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'message_id' => $messageId,
            'recipient_id' => $recipientId
        ]);
    }

    /**
     * Lấy danh sách người nhận của thông báo
     */
    public function getMessageRecipients($messageId) {
        $sql = "SELECT imr.*, CONCAT(u.first_name, ' ', u.last_name) as full_name, u.email, u.role
                FROM internal_message_recipients imr
                INNER JOIN users u ON imr.recipient_id = u.id
                WHERE imr.message_id = :message_id
                ORDER BY imr.created_at";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message_id', $messageId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy số thông báo chưa đọc
     */
    public function getUnreadCount($recipientId) {
        $sql = "SELECT COUNT(*)
                FROM internal_message_recipients imr
                WHERE imr.recipient_id = :recipient_id AND imr.is_read = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':recipient_id', $recipientId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Lấy danh sách admin có thể nhận thông báo
     */
    public function getAvailableRecipients() {
        $sql = "SELECT id, CONCAT(first_name, ' ', last_name) as full_name, email, role
                FROM users
                WHERE role IN ('manager', 'super_admin')
                ORDER BY first_name, last_name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Xóa thông báo (chỉ người gửi mới có thể xóa)
     */
    public function deleteMessage($messageId, $senderId) {
        try {
            error_log("Attempting to delete message ID: {$messageId} by sender ID: {$senderId}");

            $this->db->beginTransaction();

            // Kiểm tra người gửi
            $message = $this->findById($messageId);
            error_log("Found message: " . ($message ? "YES" : "NO"));
            if ($message) {
                error_log("Message sender_id: {$message['sender_id']}, requesting user: {$senderId}");
            }

            if (!$message || $message['sender_id'] != $senderId) {
                error_log("Delete validation failed: " . (!$message ? "Message not found" : "Sender mismatch"));
                return false;
            }

            // Xóa người nhận trước
            $sql = "DELETE FROM internal_message_recipients WHERE message_id = :message_id";
            error_log("Deleting recipients with SQL: " . $sql);
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['message_id' => $messageId]);
            error_log("Delete recipients result: " . ($result ? "success" : "failed"));

            if (!$result) {
                error_log("Recipients delete error: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to delete message recipients");
            }

            // Xóa thông báo
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            error_log("Deleting message with SQL: " . $sql);
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['id' => $messageId]);
            error_log("Delete message result: " . ($result ? "success" : "failed"));

            if (!$result) {
                error_log("Message delete error: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to delete message");
            }

            $this->db->commit();
            error_log("Message deletion transaction committed successfully");
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error deleting internal message: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Lấy thống kê thông báo
     */
    public function getMessageStats($userId) {
        $sql = "SELECT
                    COUNT(*) as total_sent,
                    COUNT(CASE WHEN im.message_type = 'urgent' THEN 1 END) as urgent_sent,
                    COUNT(CASE WHEN im.is_broadcast = 1 THEN 1 END) as broadcast_sent
                FROM {$this->table} im
                WHERE im.sender_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Ensure required database tables exist
     */
    private function ensureTables() {
        try {
            // Check if internal_messages table exists
            $stmt = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            if ($stmt->rowCount() == 0) {
                error_log("Creating internal_messages table");
                $sql = "CREATE TABLE {$this->table} (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    sender_id INT NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    content TEXT NOT NULL,
                    attachment_path VARCHAR(255),
                    attachment_name VARCHAR(255),
                    message_type ENUM('general', 'system_update', 'policy_change', 'maintenance', 'personal') DEFAULT 'general',
                    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
                    is_broadcast TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX (sender_id),
                    INDEX (message_type),
                    INDEX (priority),
                    INDEX (created_at)
                )";
                $this->db->exec($sql);
            }

            // Check if internal_message_recipients table exists
            $stmt = $this->db->query("SHOW TABLES LIKE 'internal_message_recipients'");
            if ($stmt->rowCount() == 0) {
                error_log("Creating internal_message_recipients table");
                $sql = "CREATE TABLE internal_message_recipients (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    message_id INT NOT NULL,
                    recipient_id INT NOT NULL,
                    is_read TINYINT(1) DEFAULT 0,
                    read_at TIMESTAMP NULL DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (message_id),
                    INDEX (recipient_id),
                    INDEX (is_read)
                )";
                $this->db->exec($sql);
            }
        } catch (Exception $e) {
            error_log("Error ensuring tables exist: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
