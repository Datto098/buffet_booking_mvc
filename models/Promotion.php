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
    }    /**
     * Create a new promotion
     */
    public function createPromotion($data)
    {
        $sql = "INSERT INTO {$this->table} (name, code, description, type, application_type, discount_value, start_date, end_date, usage_limit, minimum_amount)
                VALUES (:name, :code, :description, :type, :application_type, :discount_value, :start_date, :end_date, :usage_limit, :minimum_amount)";

        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':name' => $data['name'],
            ':code' => $data['code'],
            ':description' => $data['description'] ?? '',
            ':type' => $data['type'],
            ':application_type' => $data['application_type'] ?? 'all',
            ':discount_value' => $data['discount_value'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':usage_limit' => $data['usage_limit'],
            ':minimum_amount' => $data['minimum_amount']
        ]);

        if ($success) {
            $promotionId = $this->db->lastInsertId();

            // Lưu food items nếu có
            if (!empty($data['food_items']) && $data['application_type'] === 'specific_items') {
                $this->saveFoodItems($promotionId, $data['food_items']);
            }

            // Lưu categories nếu có
            if (!empty($data['categories']) && $data['application_type'] === 'categories') {
                $this->saveCategories($promotionId, $data['categories']);
            }
        }        return $success;
    }    /**
     * Update a promotion
     */
    public function updatePromotion($id, $data)
    {
        try {
            error_log("updatePromotion called for ID: $id with data: " . print_r($data, true));

            $sql = "UPDATE {$this->table}
                    SET name = :name, code = :code, description = :description, type = :type, application_type = :application_type,
                        discount_value = :discount_value, start_date = :start_date, end_date = :end_date,
                        usage_limit = :usage_limit, minimum_amount = :minimum_amount, updated_at = NOW()
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':code' => $data['code'],
                ':description' => $data['description'] ?? '',
                ':type' => $data['type'],
                ':application_type' => $data['application_type'] ?? 'all',
                ':discount_value' => $data['discount_value'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':usage_limit' => $data['usage_limit'],
                ':minimum_amount' => $data['minimum_amount']
            ]);

            if ($success) {
                error_log("Basic promotion update successful, now updating relationships");

                try {
                    // Cập nhật food items nếu có
                    if ($data['application_type'] === 'specific_items') {
                        error_log("Updating food items: " . print_r($data['food_items'], true));
                        $this->saveFoodItems($id, $data['food_items'] ?? []);
                    } else {
                        error_log("Deleting food items for non-specific promotion");
                        $this->deleteFoodItems($id);
                    }

                    // Cập nhật categories nếu có
                    if ($data['application_type'] === 'categories') {
                        error_log("Updating categories: " . print_r($data['categories'], true));
                        $this->saveCategories($id, $data['categories'] ?? []);
                    } else {
                        error_log("Deleting categories for non-category promotion");
                        $this->deleteCategories($id);
                    }

                    error_log("Relationship updates completed successfully");
                } catch (Exception $e) {
                    error_log("Error updating relationships: " . $e->getMessage());
                    // Don't fail the entire operation if only relationships fail
                    // The basic promotion data is already saved
                }
            } else {
                error_log("Failed to update basic promotion data");
            }

            return $success;
        } catch (Exception $e) {
            error_log("Error in updatePromotion: " . $e->getMessage());
            throw $e;
        }
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
    }    /**
     * Save food items for a promotion
     */
    public function saveFoodItems($promotionId, $foodItemIds)
    {
        try {
            error_log("saveFoodItems called for promotion $promotionId with items: " . print_r($foodItemIds, true));

            // Xóa các food items cũ
            $this->deleteFoodItems($promotionId);

            // Thêm food items mới
            if (!empty($foodItemIds)) {
                $sql = "INSERT INTO promotion_food_items (promotion_id, food_item_id) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);

                foreach ($foodItemIds as $foodItemId) {
                    if (!empty($foodItemId)) {
                        $stmt->execute([$promotionId, $foodItemId]);
                        error_log("Added food item $foodItemId to promotion $promotionId");
                    }
                }
            }
            error_log("saveFoodItems completed successfully");
        } catch (Exception $e) {
            error_log("Error in saveFoodItems: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Save categories for a promotion
     */
    public function saveCategories($promotionId, $categoryIds)
    {
        try {
            error_log("saveCategories called for promotion $promotionId with categories: " . print_r($categoryIds, true));

            // Xóa các categories cũ
            $this->deleteCategories($promotionId);

            // Thêm categories mới
            if (!empty($categoryIds)) {
                $sql = "INSERT INTO promotion_categories (promotion_id, category_id) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);

                foreach ($categoryIds as $categoryId) {
                    if (!empty($categoryId)) {
                        $stmt->execute([$promotionId, $categoryId]);
                        error_log("Added category $categoryId to promotion $promotionId");
                    }
                }
            }
            error_log("saveCategories completed successfully");
        } catch (Exception $e) {
            error_log("Error in saveCategories: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete food items for a promotion
     */
    public function deleteFoodItems($promotionId)
    {
        $sql = "DELETE FROM promotion_food_items WHERE promotion_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$promotionId]);
    }

    /**
     * Delete categories for a promotion
     */
    public function deleteCategories($promotionId)
    {
        $sql = "DELETE FROM promotion_categories WHERE promotion_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$promotionId]);
    }

    /**
     * Get food item IDs for a promotion
     */
    public function getFoodItemIds($promotionId)
    {
        $sql = "SELECT food_item_id FROM promotion_food_items WHERE promotion_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$promotionId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get category IDs for a promotion
     */
    public function getCategoryIds($promotionId)
    {
        $sql = "SELECT category_id FROM promotion_categories WHERE promotion_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$promotionId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get all food items for selection
     */
    public function getAllFoodItems()
    {
        $sql = "SELECT id, name, price, is_available FROM food_items WHERE is_available = 1 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all categories for selection
     */
    public function getAllCategories()
    {
        $sql = "SELECT id, name FROM categories ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get promotions applicable to a specific food item
     */
    public function getPromotionsForFoodItem($foodItemId)
    {
        $sql = "SELECT p.* FROM {$this->table} p
                LEFT JOIN promotion_food_items pfi ON p.id = pfi.promotion_id
                LEFT JOIN promotion_categories pc ON p.id = pc.promotion_id
                LEFT JOIN food_items fi ON fi.category_id = pc.category_id
                WHERE p.is_active = 1
                AND p.start_date <= CURDATE()
                AND p.end_date >= CURDATE()
                AND (
                    p.application_type = 'all'
                    OR (p.application_type = 'specific_items' AND pfi.food_item_id = ?)
                    OR (p.application_type = 'categories' AND fi.id = ?)
                )
                ORDER BY p.discount_value DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$foodItemId, $foodItemId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active promotions with their associated food items for customer display
     */
    public function getActivePromotionsWithFoodItems()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()
                ORDER BY discount_value DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // For each promotion, get its associated food items
        foreach ($promotions as &$promotion) {
            $promotion['food_items'] = [];

            if ($promotion['application_type'] === 'specific_items') {
                // Get specific food items for this promotion
                $sql = "SELECT f.*, c.name as category_name
                        FROM food_items f
                        JOIN promotion_food_items pfi ON f.id = pfi.food_item_id
                        LEFT JOIN categories c ON f.category_id = c.id
                        WHERE pfi.promotion_id = ? AND f.is_available = 1
                        ORDER BY f.name";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([$promotion['id']]);
                $promotion['food_items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } elseif ($promotion['application_type'] === 'categories') {
                // Get all food items from selected categories
                $sql = "SELECT f.*, c.name as category_name
                        FROM food_items f
                        JOIN categories c ON f.category_id = c.id
                        JOIN promotion_categories pc ON c.id = pc.category_id
                        WHERE pc.promotion_id = ? AND f.is_available = 1
                        ORDER BY f.name";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([$promotion['id']]);
                $promotion['food_items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // For 'all' type promotions, get a sample of featured foods to display
                $sql = "SELECT f.*, c.name as category_name
                        FROM food_items f
                        LEFT JOIN categories c ON f.category_id = c.id
                        WHERE f.is_available = 1
                        AND (f.is_popular = 1 OR f.is_new = 1 OR f.is_seasonal = 1)
                        ORDER BY f.is_popular DESC, f.is_new DESC, f.created_at DESC
                        LIMIT 8";

                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $promotion['food_items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $promotions;
    }

    // ...existing code...
}
?>
