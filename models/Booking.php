<?php
/**
 * Booking Model
 */

require_once 'BaseModel.php';

class Booking extends BaseModel {
    protected $table = 'bookings';    public function createBooking($bookingData) {
        $sql = "INSERT INTO reservations
        (customer_name, customer_email, customer_phone, booking_date, booking_time, guest_count, special_requests, status, created_at, updated_at)
        VALUES
        (:customer_name, :customer_email, :customer_phone, :booking_date, :booking_time, :guest_count, :special_requests, :status, NOW(), NOW())";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':customer_name', $bookingData['customer_name']);
    $stmt->bindValue(':customer_email', $bookingData['customer_email']);
    $stmt->bindValue(':customer_phone', $bookingData['customer_phone']);
    $stmt->bindValue(':booking_date', $bookingData['booking_date']);
    $stmt->bindValue(':booking_time', $bookingData['booking_time']);
    $stmt->bindValue(':guest_count', $bookingData['party_size']); // <-- lấy từ form
    $stmt->bindValue(':special_requests', $bookingData['special_requests'] ?? '');
    $stmt->bindValue(':status', 'pending');
    return $stmt->execute();
}

    public function getBookingsByUser($userId, $limit = null, $offset = 0) {
        $sql = "SELECT r.*, t.table_number, t.capacity,
                       CONCAT(r.booking_date, ' ', r.booking_time) as reservation_time
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.user_id = :user_id
                ORDER BY CONCAT(r.booking_date, ' ', r.booking_time) DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBookingDetails($bookingId, $userId = null) {
        $sql = "SELECT r.*, u.email as customer_email, t.table_number, t.capacity,
                       CONCAT(r.booking_date, ' ', r.booking_time) as reservation_time
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
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
        $booking = $stmt->fetch();

        // Đảm bảo mapping cho view
        return $this->transformBookingForView($booking);
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
        $sql = "SELECT r.*, t.table_number, t.capacity,
                       CONCAT(r.booking_date, ' ', r.booking_time) as reservation_time
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.booking_date = :date";

        if ($status) {
            $sql .= " AND r.status = :status";
        }

        $sql .= " ORDER BY CONCAT(r.booking_date, ' ', r.booking_time)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':date', $date);

        if ($status) {
            $stmt->bindValue(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBookingsByStatus($status, $limit = null) {
        $sql = "SELECT r.*, t.table_number, t.capacity,
                       CONCAT(r.booking_date, ' ', r.booking_time) as reservation_time
                FROM {$this->table} r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.status = :status
                ORDER BY CONCAT(r.booking_date, ' ', r.booking_time)";

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
    }    public function getAvailableTables($reservationTime, $numberOfGuests, $location = null) {
        try {
            // First check if tables exist
            $checkSql = "SELECT COUNT(*) as count FROM tables";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute();
            $result = $checkStmt->fetch();

            error_log("Booking Debug - Tables in database: " . $result['count']);

            if ($result['count'] <= 0) {
                error_log("Booking Debug - No tables found in database");
                return [];
            }

            // Simple query: only check capacity and availability
            $sql = "SELECT t.*
                    FROM tables t
                    WHERE t.capacity >= :number_of_guests
                    AND t.is_available = 1";

            // Add location filter if provided
            if ($location) {
                $sql .= " AND t.location = :location";
            }

            $sql .= " ORDER BY t.capacity, t.table_number";

            error_log("Booking Debug - SQL: " . $sql);
            error_log("Booking Debug - Params: guests=" . $numberOfGuests . ", location=" . ($location ?? 'null'));

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':number_of_guests', $numberOfGuests, PDO::PARAM_INT);

            if ($location) {
                $stmt->bindValue(':location', $location, PDO::PARAM_STR);
            }

            $stmt->execute();

            $tables = $stmt->fetchAll();
            error_log("Booking Debug - Available tables found: " . count($tables));

            if (count($tables) > 0) {
                error_log("Booking Debug - First available table: capacity=" . $tables[0]['capacity'] . ", number=" . $tables[0]['table_number']);
            }

            return $tables;

        } catch (PDOException $e) {
            error_log("Error in getAvailableTables: " . $e->getMessage());
            error_log("SQL Error: " . $e->getCode());
            return [];
        }
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

        $sql .= " ORDER BY CONCAT(r.booking_date, ' ', r.booking_time) DESC";

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
                    booking_date
                FROM {$this->table}
                WHERE CONCAT(booking_date, ' ', booking_time) >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY status, booking_date
                ORDER BY booking_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUpcomingBookings($limit = 10) {        $sql = "SELECT r.*, u.email as customer_email, u.email, t.table_number,
                       CONCAT(r.booking_date, ' ', r.booking_time) as reservation_time
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE CONCAT(r.booking_date, ' ', r.booking_time) > NOW()
                AND r.status IN ('confirmed', 'pending')
                ORDER BY r.booking_date ASC, r.booking_time ASC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTodayBookings() {        $sql = "SELECT r.*, u.email as customer_email, u.email, t.table_number,
                       CONCAT(r.booking_date, ' ', r.booking_time) as reservation_time
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.booking_date = CURDATE()
                ORDER BY r.booking_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }    /**
     * Check availability for a booking at specified date and time
     */    public function checkAvailability($date, $time, $partySize, $location = null) {
        $bookingDateTime = $date . ' ' . $time;

        error_log("Booking Debug - checkAvailability called");
        error_log("Booking Debug - Date: $date, Time: $time, Party Size: $partySize, Location: $location");
        error_log("Booking Debug - DateTime: $bookingDateTime");

        // Check if there are available tables for the party size
        try {
            $availableTables = $this->getAvailableTables($bookingDateTime, $partySize, $location);

            if (count($availableTables) > 0) {
                error_log("Booking Debug - Found " . count($availableTables) . " available tables");
                return [
                    'available' => true,
                    'message' => 'Có bàn trống cho thời gian này',
                    'suggestedTimes' => []
                ];
            } else {
                error_log("Booking Debug - No available tables found");

                return [
                    'available' => false,
                    'message' => 'Không có bàn trống cho thời gian này. Vui lòng chọn thời gian khác.',
                    'suggestedTimes' => []
                ];
            }
        } catch (Exception $e) {
            // Log the error and return a user-friendly message
            error_log("Error in checkAvailability: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            return [
                'available' => false, // Changed to false to be safe
                'message' => 'Hệ thống đang gặp sự cố. Vui lòng thử lại sau.',
                'suggestedTimes' => []
            ];
        }
    }

    /**
     * Get user bookings (alias for getBookingsByUser for compatibility)
     * Updated to handle multiple parameters for pagination
     */
    public function getUserBookings($userId, $limit = null, $offset = 0) {
        return $this->getBookingsByUser($userId, $limit, $offset);
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
        // Generate unique booking reference
        $bookingReference = strtoupper(substr(md5(uniqid()), 0, 10));

        // Map controller data to model data
        $mappedData = [
            'user_id' => $bookingData['user_id'] ?? null,
            'customer_name' => $bookingData['customer_name'],
            'customer_email' => $bookingData['customer_email'] ?? '',
            'customer_phone' => $bookingData['customer_phone'],
            'booking_date' => date('Y-m-d', strtotime($bookingData['booking_datetime'])),
            'booking_time' => date('H:i:s', strtotime($bookingData['booking_datetime'])),
            'guest_count' => $bookingData['party_size'],
            'booking_location' => $bookingData['booking_location'] ?? null,
            'special_requests' => $bookingData['special_requests'] ?? null,
            'status' => $bookingData['status'] ?? 'pending',
            'booking_reference' => $bookingReference
        ];

        $sql = "INSERT INTO {$this->table}
                (user_id, customer_name, customer_email, customer_phone, booking_date, booking_time,
                 guest_count, booking_location, special_requests, status, booking_reference, created_at, updated_at)
                VALUES
                (:user_id, :customer_name, :customer_email, :customer_phone, :booking_date, :booking_time,
                 :guest_count, :booking_location, :special_requests, :status, :booking_reference, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':user_id' => $mappedData['user_id'],
            ':customer_name' => $mappedData['customer_name'],
            ':customer_email' => $mappedData['customer_email'],
            ':customer_phone' => $mappedData['customer_phone'],
            ':booking_date' => $mappedData['booking_date'],
            ':booking_time' => $mappedData['booking_time'],
            ':guest_count' => $mappedData['guest_count'],
            ':booking_location' => $mappedData['booking_location'],
            ':special_requests' => $mappedData['special_requests'],
            ':status' => $mappedData['status'],
            ':booking_reference' => $mappedData['booking_reference']
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Get recent bookings with customer information for dashboard
     */    public function getRecentBookingsWithCustomer($limit = 5) {
        $sql = "SELECT r.*,
                       COALESCE(CONCAT(u.first_name, ' ', u.last_name), r.customer_name) as customer_name,
                       r.booking_date,
                       r.booking_time,
                       CONCAT(r.booking_date, ' ', r.booking_time) as reservation_time,
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
     */    public function updateBooking($bookingId, $data) {
        error_log("Booking Model updateBooking - ID: $bookingId, Data: " . json_encode($data));

        $sql = "UPDATE {$this->table} SET
                customer_name = :customer_name,
                phone_number = :phone_number,
                number_of_guests = :number_of_guests,
                reservation_time = :reservation_time,
                notes = :notes,
                status = :status,
                updated_at = NOW()
            WHERE id = :id";

        error_log("Booking Model updateBooking - SQL: " . $sql);

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':customer_name', $data['customer_name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $data['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':number_of_guests', $data['number_of_guests'], PDO::PARAM_INT);
        $stmt->bindValue(':reservation_time', $data['reservation_time'], PDO::PARAM_STR);
        $stmt->bindValue(':notes', $data['notes'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);

        $result = $stmt->execute();

        if (!$result) {
            error_log("Booking Model updateBooking - SQL Error: " . json_encode($stmt->errorInfo()));
        } else {
            error_log("Booking Model updateBooking - Rows affected: " . $stmt->rowCount());
        }

        return $result;
    }
    /**
     * Transform booking data for admin interface compatibility
     * Maps database fields to expected view fields
     */
    private function transformBookingData($booking) {
        if (!$booking) return $booking;

        // Map fields for admin interface compatibility
        if (isset($booking['booking_date']) && isset($booking['booking_time'])) {
            $booking['reservation_time'] = $booking['booking_date'] . ' ' . $booking['booking_time'];
        }

        // Map customer_phone to phone_number for admin interface
        if (isset($booking['customer_phone'])) {
            $booking['phone_number'] = $booking['customer_phone'];
        }

        // Map guest_count to number_of_guests for admin interface
        if (isset($booking['guest_count'])) {
            $booking['number_of_guests'] = $booking['guest_count'];
        }

        // Ensure customer_email field exists (from user join or direct)
        if (!isset($booking['customer_email']) && isset($booking['email'])) {
            $booking['customer_email'] = $booking['email'];
        }

        // Map special_requests to notes for admin interface
        if (isset($booking['special_requests']) && !isset($booking['notes'])) {
            $booking['notes'] = $booking['special_requests'];
        }

        return $booking;
    }

    /**
     * Get all bookings with admin-compatible field mapping
     */    public function getAllForAdmin($limit = null, $offset = 0, $status = null, $search = null, $date = null) {
        $sql = "SELECT r.*,
                       COALESCE(u.email, r.customer_email) as customer_email,
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
            $conditions[] = "(r.customer_name LIKE :search OR r.customer_email LIKE :search OR u.email LIKE :search OR r.customer_phone LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if ($date) {
            $conditions[] = "r.booking_date = :date";
            $params[':date'] = $date;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY r.booking_date DESC, r.booking_time DESC";

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

    public function transformBookingForView($booking) {
        if (!$booking) return $booking;

        // Mapping cho view
        $booking['customer_email'] = $booking['customer_email'] ?? ($booking['email'] ?? '');
        $booking['customer_phone'] = $booking['customer_phone'] ?? ($booking['phone_number'] ?? '');
        $booking['guest_count'] = $booking['guest_count'] ?? ($booking['number_of_guests'] ?? '');
        // Tách ngày và giờ từ reservation_time nếu có
        if (isset($booking['reservation_time'])) {
            $booking['booking_date'] = date('Y-m-d', strtotime($booking['reservation_time']));
            $booking['booking_time'] = date('H:i:s', strtotime($booking['reservation_time']));
        } else {
            $booking['booking_date'] = $booking['booking_date'] ?? '';
            $booking['booking_time'] = $booking['booking_time'] ?? '';
        }
        return $booking;
    }

    public function cancelBooking($bookingId, $userId = null) {
        // Nếu có userId, chỉ cho phép user đó hủy booking của mình
        $sql = "UPDATE {$this->table} SET status = 'cancelled', updated_at = NOW() WHERE id = :id";
if ($userId !== null) {
    $sql .= " AND user_id = :user_id";
}
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);
        if ($userId !== null) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }

public function getBookingById($bookingId, $userId) {
    $sql = "SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function hasTablesForAddress($address) {
    $sql = "SELECT COUNT(*) as count FROM tables WHERE location = :address";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':address', $address);
    $stmt->execute();
    $result = $stmt->fetch();
    return ($result['count'] ?? 0) > 0;
}
}
?>
