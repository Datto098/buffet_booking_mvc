<?php

/**
 * Notification Model
 * Handles notification management operations
 */

require_once 'BaseModel.php';

class Notification extends BaseModel {
    protected $table = 'notifications';

    /**
     * Create a new notification
     */
    public function createNotification($data) {
        $sql = "INSERT INTO {$this->table} (user_id, type, title, message, data, is_read, created_at)
                VALUES (:user_id, :type, :title, :message, :data, 0, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':type' => $data['type'],
            ':title' => $data['title'],
            ':message' => $data['message'],
            ':data' => isset($data['data']) ? json_encode($data['data']) : null
        ]);
    }

    /**
     * Create notification for new order
     */
    public function createOrderNotification($orderId, $orderData) {
        // Get all super admin users
        $sql = "SELECT id FROM users WHERE role = 'super_admin' AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $superAdmins = $stmt->fetchAll();

        $success = true;
        foreach ($superAdmins as $admin) {
            $notificationData = [
                'user_id' => $admin['id'],
                'type' => 'new_order',
                'title' => 'New Order Received',
                'message' => "New order #{$orderId} from {$orderData['customer_name']} - " .
                           number_format($orderData['total_amount'], 2) . " VND",
                'data' => [
                    'order_id' => $orderId,
                    'customer_name' => $orderData['customer_name'],
                    'total_amount' => $orderData['total_amount'],
                    'order_type' => $orderData['order_type'] ?? 'delivery',
                    'url' => "/superadmin/orders/view/{$orderId}"
                ]
            ];

            if (!$this->createNotification($notificationData)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Get notifications for a user with pagination
     */
    public function getUserNotifications($userId, $limit = 20, $offset = 0, $unreadOnly = false) {
        $whereClause = "user_id = :user_id";
        if ($unreadOnly) {
            $whereClause .= " AND is_read = 0";
        }

        $sql = "SELECT * FROM {$this->table}
                WHERE {$whereClause}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Count total notifications for a user
     */
    public function countUserNotifications($userId, $unreadOnly = false) {
        $whereClause = "user_id = :user_id";
        if ($unreadOnly) {
            $whereClause .= " AND is_read = 0";
        }

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id";
        $params = [':id' => $notificationId];

        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notificationId, $userId = null) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $params = [':id' => $notificationId];

        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats($userId) {
        $sql = "SELECT
                    COUNT(*) as total_notifications,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count,
                    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read_count,
                    COUNT(CASE WHEN type = 'new_order' THEN 1 END) as order_notifications,
                    COUNT(CASE WHEN type = 'new_booking' THEN 1 END) as booking_notifications,
                    COUNT(CASE WHEN type = 'system' THEN 1 END) as system_notifications
                FROM {$this->table}
                WHERE user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Get recent notifications for dashboard (latest 5)
     */
    public function getRecentNotifications($userId, $limit = 5) {
        return $this->getUserNotifications($userId, $limit, 0);
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete($notificationIds, $userId) {
        if (empty($notificationIds)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($notificationIds) - 1) . '?';
        $sql = "DELETE FROM {$this->table} WHERE id IN ({$placeholders}) AND user_id = ?";

        $params = array_merge($notificationIds, [$userId]);
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Clean old notifications (older than specified days)
     */
    public function cleanOldNotifications($days = 30) {
        $sql = "DELETE FROM {$this->table} WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':days' => $days]);
    }

    /**
     * Get notifications by type
     */
    public function getNotificationsByType($userId, $type, $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id AND type = :type
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Create notification for new booking
     */
    public function createBookingNotification($bookingId)
    {
        try {
            // Get booking details
            $bookingModel = new Booking();
            $booking = $bookingModel->getBookingDetails($bookingId);

            if (!$booking) {
                return false;
            }

            // Get all super_admin users
            $userModel = new User();
            $superAdmins = $userModel->getUsersByRole('super_admin');

            if (empty($superAdmins)) {
                return false;
            }

            $title = 'New Booking Created';
            $message = "A new booking has been created by {$booking['customer_name']} for " .
                      date('M j, Y \a\t g:i A', strtotime($booking['booking_date'] . ' ' . $booking['booking_time'])) .
                      " ({$booking['guest_count']} guests)";

            $data = [
                'booking_id' => $bookingId,
                'customer_name' => $booking['customer_name'],
                'reservation_time' => $booking['booking_date'] . ' ' . $booking['booking_time'],
                'guest_count' => $booking['guest_count'],
                'url' => "/admin/bookings?id={$bookingId}"
            ];

            $success = true;
            foreach ($superAdmins as $admin) {
                $result = $this->create([
                    'user_id' => $admin['id'],
                    'type' => 'new_booking',
                    'title' => $title,
                    'message' => $message,
                    'data' => json_encode($data)
                ]);

                if (!$result) {
                    $success = false;
                    error_log("Failed to create booking notification for admin ID: " . $admin['id']);
                }
            }

            return $success;

        } catch (Exception $e) {
            error_log("Error creating booking notification: " . $e->getMessage());
            return false;
        }
    }
}
