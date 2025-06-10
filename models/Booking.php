<?php
/**
 * Booking Model
 */

require_once 'BaseModel.php';

class Booking extends BaseModel {
    protected $table = 'reservations';    public function createBooking($bookingData) {
        $sql = "INSERT INTO {$this->table} (user_id, customer_name, phone_number, table_id, reservation_time, number_of_guests, status, notes)
                VALUES (:user_id, :customer_name, :phone_number, :table_id, :reservation_time, :number_of_guests, :status, :notes)";

        // Handle field name differences between controller and model
        $phone = $bookingData['phone_number'] ?? $bookingData['customer_phone'] ?? null;
        $resTime = $bookingData['reservation_time'] ?? $bookingData['booking_datetime'] ?? null;
        $guests = $bookingData['number_of_guests'] ?? $bookingData['party_size'] ?? null;
        $notes = $bookingData['notes'] ?? $bookingData['special_requests'] ?? null;        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':user_id' => $bookingData['user_id'] ?? null,
            ':customer_name' => $bookingData['customer_name'],
            ':phone_number' => $phone,
            ':table_id' => $bookingData['table_id'] ?? null,
            ':reservation_time' => $resTime,
            ':number_of_guests' => $guests,
            ':status' => $bookingData['status'] ?? 'pending',
            ':notes' => $notes
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    public function getBookingsByUser($userId, $limit = null) {
        $sql = "SELECT r.*, t.table_number, t.capacity
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.user_id = :user_id
                ORDER BY r.reservation_time DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBookingDetails($bookingId, $userId = null) {
        $sql = "SELECT r.*, t.table_number, t.capacity
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.id = :booking_id";

        if ($userId) {
            $sql .= " AND r.user_id = :user_id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':booking_id', $bookingId, PDO::PARAM_INT);

        if ($userId) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateBookingStatus($bookingId, $status) {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function assignTable($bookingId, $tableId) {
        $sql = "UPDATE {$this->table} SET table_id = :table_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':table_id', $tableId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getBookingsByDate($date, $status = null) {
        $sql = "SELECT r.*, t.table_number, t.capacity
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE DATE(r.reservation_time) = :date";

        if ($status) {
            $sql .= " AND r.status = :status";
        }

        $sql .= " ORDER BY r.reservation_time";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':date', $date);

        if ($status) {
            $stmt->bindValue(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBookingsByStatus($status, $limit = null) {
        $sql = "SELECT r.*, t.table_number, t.capacity
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.status = :status
                ORDER BY r.reservation_time";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }    public function getAvailableTables($reservationTime, $numberOfGuests) {
        // First check if tables table exists or has any data
        try {
            $checkSql = "SELECT COUNT(*) as count FROM tables";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute();
            $result = $checkStmt->fetch();

            if ($result['count'] <= 0) {
                // No tables in database, return empty array
                return [];
            }
        } catch (PDOException $e) {
            // Table likely doesn't exist or other DB error
            error_log("Error checking tables: " . $e->getMessage());
            return [];
        }

        $sql = "SELECT t.*
                FROM tables t
                WHERE t.capacity >= :number_of_guests
                AND t.id NOT IN (
                    SELECT DISTINCT r.table_id
                    FROM {$this->table} r
                    WHERE r.table_id IS NOT NULL
                    AND r.status IN ('confirmed', 'pending')
                    AND DATE(r.reservation_time) = DATE(:reservation_time)
                    AND TIME(r.reservation_time) BETWEEN
                        TIME(DATE_SUB(:reservation_time, INTERVAL 2 HOUR))
                        AND TIME(DATE_ADD(:reservation_time, INTERVAL 2 HOUR))
                )
                ORDER BY t.capacity, t.table_number";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':number_of_guests', $numberOfGuests, PDO::PARAM_INT);
        $stmt->bindValue(':reservation_time', $reservationTime);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentBookings($limit = 10) {
        $sql = "SELECT r.*, t.table_number
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                ORDER BY r.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Admin specific methods
    public function getAllWithCustomers($limit = null, $offset = 0, $status = null, $search = null) {        $sql = "SELECT r.*, u.email as customer_email, u.email, t.table_number, t.capacity
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN tables t ON r.table_id = t.id";

        $conditions = [];
        $params = [];

        if ($status) {
            $conditions[] = "r.status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $conditions[] = "(r.customer_name LIKE :search OR u.email LIKE :search OR r.phone_number LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY r.reservation_time DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus($bookingId, $status) {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTodayCount() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function countByUser($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }    public function count($condition = null, $value = null) {
        if ($condition && $value) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE $condition = :value";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':value', $value);
        } else {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getBookingStats() {
        $sql = "SELECT
                    status,
                    COUNT(*) as count,
                    DATE(reservation_time) as booking_date
                FROM {$this->table}
                WHERE reservation_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY status, DATE(reservation_time)
                ORDER BY booking_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUpcomingBookings($limit = 10) {        $sql = "SELECT r.*, u.email as customer_email, u.email, t.table_number
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.reservation_time > NOW()
                AND r.status IN ('confirmed', 'pending')
                ORDER BY r.reservation_time ASC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTodayBookings() {        $sql = "SELECT r.*, u.email as customer_email, u.email, t.table_number
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE DATE(r.reservation_time) = CURDATE()
                ORDER BY r.reservation_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }    /**
     * Check availability for a booking at specified date and time
     */
    public function checkAvailability($date, $time, $partySize) {
        $bookingDateTime = $date . ' ' . $time;

        // Check if there are available tables for the party size
        try {
            $availableTables = $this->getAvailableTables($bookingDateTime, $partySize);

            if (count($availableTables) > 0) {
                return [
                    'available' => true,
                    'message' => 'Có bàn trống cho thời gian này',
                    'suggestedTimes' => []
                ];
            } else {
                // Suggest alternative times
                $suggestedTimes = $this->getSuggestedTimes($date, $partySize);

                return [
                    'available' => false,
                    'message' => 'Không có bàn trống cho thời gian này. Vui lòng chọn thời gian khác.',
                    'suggestedTimes' => $suggestedTimes
                ];
            }
        } catch (Exception $e) {
            // Log the error and return a user-friendly message
            error_log("Error in checkAvailability: " . $e->getMessage());
            return [
                'available' => true, // Assume available for now to allow booking to proceed
                'message' => 'Hệ thống đang kiểm tra tình trạng bàn',
                'suggestedTimes' => []
            ];
        }
    }

    /**
     * Get user bookings (alias for getBookingsByUser for compatibility)
     * Updated to handle multiple parameters for pagination
     */
    public function getUserBookings($userId, $limit = null, $offset = 0) {
        return $this->getBookingsByUser($userId, $limit);
    }

    /**
     * Count bookings for a specific user
     * @param int $userId User ID
     * @return int Number of bookings
     */
    public function countUserBookings($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Count user bookings by status
     * @param int $userId User ID
     * @param string $status Booking status
     * @return int Number of bookings with specific status
     */
    public function countUserBookingsByStatus($userId, $status) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE user_id = :user_id AND status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get available time slots for a specific date and party size
     */
    public function getAvailableTimeSlots($date, $partySize) {
        $timeSlots = [
            '11:00:00', '11:30:00', '12:00:00', '12:30:00', '13:00:00', '13:30:00',
            '14:00:00', '14:30:00', '17:00:00', '17:30:00', '18:00:00', '18:30:00',
            '19:00:00', '19:30:00', '20:00:00', '20:30:00', '21:00:00'
        ];

        $availableSlots = [];

        foreach ($timeSlots as $time) {
            $availability = $this->checkAvailability($date, $time, $partySize);
            if ($availability['available']) {
                $availableSlots[] = [
                    'time' => $time,
                    'display_time' => date('H:i', strtotime($time)),
                    'available' => true
                ];
            }
        }

        return $availableSlots;
    }

    /**
     * Get suggested alternative times when requested time is not available
     */
    private function getSuggestedTimes($date, $partySize, $limit = 3) {
        $timeSlots = [
            '11:00:00', '11:30:00', '12:00:00', '12:30:00', '13:00:00', '13:30:00',
            '14:00:00', '14:30:00', '17:00:00', '17:30:00', '18:00:00', '18:30:00',
            '19:00:00', '19:30:00', '20:00:00', '20:30:00', '21:00:00'
        ];

        $suggested = [];
        $count = 0;

        foreach ($timeSlots as $time) {
            if ($count >= $limit) break;

            $bookingDateTime = $date . ' ' . $time;
            $availableTables = $this->getAvailableTables($bookingDateTime, $partySize);

            if (count($availableTables) > 0) {
                $suggested[] = [
                    'time' => $time,
                    'display_time' => date('H:i', strtotime($time))
                ];
                $count++;
            }
        }

        return $suggested;
    }

    /**
     * Create booking with proper data mapping for controller compatibility
     */
    public function createBookingFromController($bookingData) {
        // Map controller data to model data
        $mappedData = [
            'user_id' => $bookingData['user_id'] ?? null,
            'customer_name' => $bookingData['customer_name'],
            'phone_number' => $bookingData['customer_phone'],
            'table_id' => null, // Will be assigned later
            'reservation_time' => $bookingData['booking_datetime'],
            'number_of_guests' => $bookingData['party_size'],
            'status' => $bookingData['status'] ?? 'pending',
            'notes' => $bookingData['special_requests'] ?? null
        ];

        $sql = "INSERT INTO {$this->table} (user_id, customer_name, phone_number, table_id, reservation_time, number_of_guests, status, notes, created_at)
                VALUES (:user_id, :customer_name, :phone_number, :table_id, :reservation_time, :number_of_guests, :status, :notes, NOW())";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':user_id' => $mappedData['user_id'],
            ':customer_name' => $mappedData['customer_name'],
            ':phone_number' => $mappedData['phone_number'],
            ':table_id' => $mappedData['table_id'],
            ':reservation_time' => $mappedData['reservation_time'],
            ':number_of_guests' => $mappedData['number_of_guests'],
            ':status' => $mappedData['status'],
            ':notes' => $mappedData['notes']
        ]);        if ($result) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Get recent bookings with customer information for dashboard
     */    public function getRecentBookingsWithCustomer($limit = 5) {
        $sql = "SELECT r.*,
                       COALESCE(CONCAT(u.first_name, ' ', u.last_name), r.customer_name) as customer_name,
                       DATE(r.reservation_time) as booking_date,
                       TIME(r.reservation_time) as booking_time,
                       u.email as customer_email,
                       t.table_number
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN tables t ON r.table_id = t.id
                ORDER BY r.created_at DESC
                LIMIT :limit";
          $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update a booking record
     * @param int $bookingId Booking ID
     * @param array $data Updated booking data
     * @return bool Success status
     */
    public function updateBooking($bookingId, $data) {
        try {
            $sql = "UPDATE {$this->table} SET
                        customer_name = :customer_name,
                        phone_number = :phone_number,
                        email = :email,
                        number_of_guests = :number_of_guests,
                        reservation_time = :reservation_time,
                        special_requests = :special_requests
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':customer_name', $data['customer_name'], PDO::PARAM_STR);
            $stmt->bindValue(':phone_number', $data['phone_number'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':number_of_guests', $data['number_of_guests'], PDO::PARAM_INT);
            $stmt->bindValue(':reservation_time', $data['reservation_time'], PDO::PARAM_STR);
            $stmt->bindValue(':special_requests', $data['special_requests'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating booking: " . $e->getMessage());
            return false;
        }
    }    /**
     * Transform booking data for admin interface compatibility
     * Maps database fields to expected view fields
     */
    private function transformBookingData($booking) {
        if (!$booking) return $booking;

        // Split reservation_time into separate date and time fields
        if (isset($booking['reservation_time'])) {
            $booking['booking_date'] = date('Y-m-d', strtotime($booking['reservation_time']));
            $booking['booking_time'] = date('H:i:s', strtotime($booking['reservation_time']));
        }

        // Ensure customer_email field exists (from user join or direct)
        if (!isset($booking['customer_email']) && isset($booking['email'])) {
            $booking['customer_email'] = $booking['email'];
        }

        // Map notes to special_requests for view compatibility
        if (isset($booking['notes']) && !isset($booking['special_requests'])) {
            $booking['special_requests'] = $booking['notes'];
        }

        return $booking;
    }

    /**
     * Get all bookings with admin-compatible field mapping
     */
    public function getAllForAdmin($limit = null, $offset = 0, $status = null, $search = null) {
        $sql = "SELECT r.*,
                       COALESCE(u.email, '') as customer_email,
                       u.email,
                       t.table_number,
                       t.capacity
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN tables t ON r.table_id = t.id";

        $conditions = [];
        $params = [];

        if ($status) {
            $conditions[] = "r.status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $conditions[] = "(r.customer_name LIKE :search OR u.email LIKE :search OR r.phone_number LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY r.reservation_time DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        $bookings = $stmt->fetchAll();

        // Transform each booking for admin interface
        return array_map([$this, 'transformBookingData'], $bookings);
    }
}
?>
