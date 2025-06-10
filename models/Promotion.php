<?php
/**
 * Promotion Model
 * Handles promotion management operations for Super Admin
 */

require_once 'BaseModel.php';

class Promotion extends BaseModel {
    protected $table = 'promotions';

    /**
     * Get all promotions with optional filters
     */
    public function getAllPromotions($limit = null, $offset = 0, $filters = [])
    {
        $whereConditions = [];
        $params = [];

        // Build filter conditions
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $whereConditions[] = "is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()";
                    break;
                case 'inactive':
                    $whereConditions[] = "is_active = 0";
                    break;
                case 'expired':
                    $whereConditions[] = "end_date < CURDATE()";
                    break;
            }
        }

        if (!empty($filters['type'])) {
            $whereConditions[] = "type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $whereConditions[] = "(name LIKE ? OR code LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        $sql = "SELECT * FROM {$this->table} $whereClause ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new promotion
     */
    public function createPromotion($data)
    {
        $sql = "INSERT INTO {$this->table} (name, code, description, type, discount_value, start_date, end_date, usage_limit, minimum_amount)
                VALUES (:name, :code, :description, :type, :discount_value, :start_date, :end_date, :usage_limit, :minimum_amount)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':code' => $data['code'],
            ':description' => $data['description'] ?? '',
            ':type' => $data['type'],
            ':discount_value' => $data['discount_value'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':usage_limit' => $data['usage_limit'],
            ':minimum_amount' => $data['minimum_amount']
        ]);
    }

    /**
     * Update a promotion
     */
    public function updatePromotion($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET name = :name, code = :code, description = :description, type = :type,
                    discount_value = :discount_value, start_date = :start_date, end_date = :end_date,
                    usage_limit = :usage_limit, minimum_amount = :minimum_amount, updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':code' => $data['code'],
            ':description' => $data['description'] ?? '',
            ':type' => $data['type'],
            ':discount_value' => $data['discount_value'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':usage_limit' => $data['usage_limit'],
            ':minimum_amount' => $data['minimum_amount']
        ]);
    }

    /**
     * Delete a promotion
     */
    public function deletePromotion($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Toggle promotion status
     */
    public function toggleStatus($id)
    {
        // Get current status
        $current = $this->findById($id);
        if (!$current) {
            return false;
        }

        $newStatus = $current['is_active'] ? 0 : 1;

        $sql = "UPDATE {$this->table} SET is_active = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute([$newStatus, $id])) {
            return $newStatus;
        }

        return false;
    }

    /**
     * Check if promotion code exists
     */
    public function codeExists($code, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = ?";
        $params = [$code];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get promotion statistics
     */
    public function getStats()
    {
        $stats = [];

        // Total promotions
        $stats['total_promotions'] = $this->count();

        // Active promotions
        $sql = "SELECT COUNT(*) FROM {$this->table}
                WHERE is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['active_promotions'] = $stmt->fetchColumn();

        // Expired promotions
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE end_date < CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['expired_promotions'] = $stmt->fetchColumn();

        // Total usage
        $sql = "SELECT COALESCE(SUM(used_count), 0) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['used_promotions'] = $stmt->fetchColumn();

        return $stats;
    }

    /**
     * Get active promotions
     */
    public function getActivePromotions()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()
                ORDER BY discount_value DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Validate promotion code for order
     */
    public function validateCode($code, $orderAmount = 0)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE code = ? AND is_active = 1
                AND start_date <= CURDATE() AND end_date >= CURDATE()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$promotion) {
            return ['valid' => false, 'message' => 'Invalid or expired promotion code'];
        }

        // Check minimum amount
        if ($promotion['minimum_amount'] && $orderAmount < $promotion['minimum_amount']) {
            return [
                'valid' => false,
                'message' => "Minimum order amount of $" . number_format($promotion['minimum_amount'], 2) . " required"
            ];
        }

        // Check usage limit
        if ($promotion['usage_limit'] && $promotion['used_count'] >= $promotion['usage_limit']) {
            return ['valid' => false, 'message' => 'Promotion usage limit reached'];
        }

        return ['valid' => true, 'promotion' => $promotion];
    }

    /**
     * Use promotion (increment used_count)
     */
    public function usePromotion($id)
    {
        $sql = "UPDATE {$this->table} SET used_count = used_count + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
