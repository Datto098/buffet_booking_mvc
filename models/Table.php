<?php
/**
 * Table Model
 * Handles restaurant table management operations
 */

require_once 'BaseModel.php';

class Table extends BaseModel {
    protected $table = 'tables';

    public function getAllTables($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} ORDER BY table_number";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAvailableTables($capacity = null) {
        $sql = "SELECT * FROM {$this->table} WHERE is_available = 1";

        if ($capacity) {
            $sql .= " AND capacity >= :capacity";
        }

        $sql .= " ORDER BY capacity, table_number";

        $stmt = $this->db->prepare($sql);

        if ($capacity) {
            $stmt->bindValue(':capacity', $capacity, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createTable($tableData) {
        $sql = "INSERT INTO {$this->table} (table_number, capacity, location, description, is_available)
                VALUES (:table_number, :capacity, :location, :description, :is_available)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':table_number' => $tableData['table_number'],
            ':capacity' => (int)$tableData['capacity'],
            ':location' => $tableData['location'] ?? '',
            ':description' => $tableData['description'] ?? '',
            ':is_available' => $tableData['is_available'] ?? 1
        ]);
    }    public function updateTable($id, $tableData) {
        // Build dynamic SQL for partial updates
        $setParts = [];
        $params = [':id' => $id];

        $allowedFields = ['table_number', 'capacity', 'location', 'description', 'is_available'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $tableData)) {
                $setParts[] = "{$field} = :{$field}";
                if ($field === 'capacity') {
                    $params[":{$field}"] = (int)$tableData[$field];
                } else {
                    $params[":{$field}"] = $tableData[$field];
                }
            }
        }

        // Always update the timestamp
        $setParts[] = "updated_at = CURRENT_TIMESTAMP";

        if (empty($setParts)) {
            return false; // No fields to update
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteTable($id) {
        // Check if table has any bookings
        $sql = "SELECT COUNT(*) as booking_count FROM bookings WHERE table_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result['booking_count'] > 0) {
            return false; // Cannot delete table with existing bookings
        }

        return $this->delete($id);
    }

    public function getTableStats() {
        $sql = "SELECT
                    COUNT(*) as total_tables,
                    SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_tables,
                    SUM(capacity) as total_capacity,
                    AVG(capacity) as average_capacity
                FROM {$this->table}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getTablesByLocation() {
        $sql = "SELECT location, COUNT(*) as table_count, SUM(capacity) as total_capacity
                FROM {$this->table}
                GROUP BY location
                ORDER BY location";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTableBookingHistory($tableId, $limit = 10) {
        $sql = "SELECT b.*, u.first_name, u.last_name, u.email
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.table_id = :table_id
                ORDER BY b.booking_date DESC, b.booking_time DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':table_id', $tableId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function isTableAvailable($tableId, $date, $time) {
        $sql = "SELECT COUNT(*) as booking_count
                FROM bookings
                WHERE table_id = :table_id
                AND booking_date = :date
                AND booking_time = :time
                AND status IN ('confirmed', 'pending')";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':table_id', $tableId, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':time', $time);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result['booking_count'] == 0;
    }

    public function getTableUtilization($days = 30) {
        $sql = "SELECT
                    t.table_number,
                    t.capacity,
                    COUNT(b.id) as total_bookings,
                    COUNT(CASE WHEN b.status = 'completed' THEN 1 END) as completed_bookings,
                    (COUNT(b.id) / :days) as avg_bookings_per_day
                FROM {$this->table} t
                LEFT JOIN bookings b ON t.id = b.table_id
                    AND b.booking_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY t.id, t.table_number, t.capacity
                ORDER BY t.table_number";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find table by specific field value
     * @param string $field Field name to search by
     * @param mixed $value Value to search for
     * @return array|null Table record or null if not found
     */
    public function findByField($field, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /**
     * Get table by ID
     * @param int $id Table ID
     * @return array|null Table record or null if not found
     */
    public function getTableById($id) {
        return $this->findByField('id', $id);
    }

    /**
     * Get table by table number
     * @param string|int $tableNumber Table number
     * @return array|null Table record or null if not found
     */
    public function getTableByNumber($tableNumber) {
        return $this->findByField('table_number', $tableNumber);
    }

    /**
     * Update table status
     * @param int $tableId Table ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateTableStatus($tableId, $status) {
        return $this->updateTable($tableId, ['is_available' => $status === 'available' ? 1 : 0]);
    }
}
?>
