<?php
/**
 * Review Model
 * Handles review management operations
 */

require_once 'BaseModel.php';

class Review extends BaseModel {
    protected $table = 'reviews';

    /**
     * Get all reviews with user and food item details
     */
    public function getAllReviews($limit = null, $offset = 0, $filters = []) {
        $whereConditions = [];
        $params = [];

        $sql = "SELECT r.*,
                       u.first_name, u.last_name, u.email as user_email,
                       f.name as food_name, f.image as food_image,
                       o.order_number
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN food_items f ON r.food_item_id = f.id
                LEFT JOIN orders o ON r.order_id = o.id";

        // Build filter conditions
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'approved':
                    $whereConditions[] = "r.is_approved = 1";
                    break;
                case 'pending':
                    $whereConditions[] = "r.is_approved = 0";
                    break;
                case 'verified':
                    $whereConditions[] = "r.is_verified = 1";
                    break;
            }
        }

        if (!empty($filters['rating'])) {
            $whereConditions[] = "r.rating = ?";
            $params[] = $filters['rating'];
        }

        if (!empty($filters['food_item_id'])) {
            $whereConditions[] = "r.food_item_id = ?";
            $params[] = $filters['food_item_id'];
        }

        if (!empty($filters['search'])) {
            $whereConditions[] = "(r.title LIKE ? OR r.comment LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR f.name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }

        $sql .= " ORDER BY r.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $index => $param) {
                $stmt->bindValue($index + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total reviews with filters
     */
    public function countReviews($filters = []) {
        $whereConditions = [];
        $params = [];

        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN food_items f ON r.food_item_id = f.id";

        // Build same filter conditions as getAllReviews
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'approved':
                    $whereConditions[] = "r.is_approved = 1";
                    break;
                case 'pending':
                    $whereConditions[] = "r.is_approved = 0";
                    break;
                case 'verified':
                    $whereConditions[] = "r.is_verified = 1";
                    break;
            }
        }

        if (!empty($filters['rating'])) {
            $whereConditions[] = "r.rating = ?";
            $params[] = $filters['rating'];
        }

        if (!empty($filters['food_item_id'])) {
            $whereConditions[] = "r.food_item_id = ?";
            $params[] = $filters['food_item_id'];
        }

        if (!empty($filters['search'])) {
            $whereConditions[] = "(r.title LIKE ? OR r.comment LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR f.name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }

        $stmt = $this->db->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $index => $param) {
                $stmt->bindValue($index + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Get review by ID with related data
     */    public function getReviewById($id) {
        $sql = "SELECT r.*,
                       u.first_name, u.last_name, u.email as user_email, u.phone as user_phone, u.created_at as user_created_at,
                       CONCAT(u.first_name, ' ', u.last_name) as user_name,
                       f.name as food_name, f.image as food_image, f.price as food_price,
                       c.name as category_name,
                       o.order_number, o.created_at as order_date
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN food_items f ON r.food_item_id = f.id
                LEFT JOIN categories c ON f.category_id = c.id
                LEFT JOIN orders o ON r.order_id = o.id
                WHERE r.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update review approval status
     */
    public function updateApprovalStatus($id, $isApproved) {
        $sql = "UPDATE {$this->table}
                SET is_approved = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$isApproved, $id]);
    }

    /**
     * Update review verified status
     */
    public function updateVerifiedStatus($id, $isVerified) {
        $sql = "UPDATE {$this->table}
                SET is_verified = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$isVerified, $id]);
    }

    /**
     * Delete review
     */
    public function deleteReview($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Update helpful count
     */
    public function updateHelpfulCount($id, $count) {
        $sql = "UPDATE {$this->table}
                SET helpful_count = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$count, $id]);
    }

    /**
     * Get review statistics
     */
    public function getReviewStats() {
        $stats = [];

        // Total reviews
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_reviews'] = $stmt->fetchColumn();

        // Approved reviews
        $sql = "SELECT COUNT(*) as approved FROM {$this->table} WHERE is_approved = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['approved_reviews'] = $stmt->fetchColumn();

        // Pending reviews
        $sql = "SELECT COUNT(*) as pending FROM {$this->table} WHERE is_approved = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['pending_reviews'] = $stmt->fetchColumn();

        // Verified reviews
        $sql = "SELECT COUNT(*) as verified FROM {$this->table} WHERE is_verified = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['verified_reviews'] = $stmt->fetchColumn();

        // Average rating        $sql = "SELECT AVG(rating) as avg_rating FROM {$this->table} WHERE is_approved = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $avgRating = $stmt->fetchColumn();
        $stats['average_rating'] = $avgRating ? round($avgRating, 1) : 0;

        // Rating distribution
        $sql = "SELECT rating, COUNT(*) as count
                FROM {$this->table}
                WHERE is_approved = 1
                GROUP BY rating
                ORDER BY rating DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['rating_distribution'] = $stmt->fetchAll();

        // Recent reviews (last 7 days)
        $sql = "SELECT COUNT(*) as recent
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['recent_reviews'] = $stmt->fetchColumn();

        return $stats;
    }

    /**
     * Get reviews by food item
     */
    public function getReviewsByFoodItem($foodId, $limit = 10, $onlyApproved = true) {
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.food_item_id = ?";

        if ($onlyApproved) {
            $sql .= " AND r.is_approved = 1";
        }

        $sql .= " ORDER BY r.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $foodId, PDO::PARAM_INT);

        if ($limit) {
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get reviews by user
     */
    public function getReviewsByUser($userId, $limit = 10) {
        $sql = "SELECT r.*, f.name as food_name, f.image as food_image
                FROM {$this->table} r
                LEFT JOIN food_items f ON r.food_item_id = f.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);

        if ($limit) {
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove($reviewIds) {
        if (empty($reviewIds)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($reviewIds) - 1) . '?';
        $sql = "UPDATE {$this->table}
                SET is_approved = 1, updated_at = CURRENT_TIMESTAMP
                WHERE id IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($reviewIds);
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDelete($reviewIds) {
        if (empty($reviewIds)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($reviewIds) - 1) . '?';
        $sql = "DELETE FROM {$this->table} WHERE id IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($reviewIds);
    }
}
