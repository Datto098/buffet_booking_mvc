<?php
/**
 * Food Model
 */

require_once 'BaseModel.php';

class Food extends BaseModel {
    protected $table = 'food_items';

    /**
     * Transform food data from database format to view format
     * Maps is_available (1/0) to status ('available'/'unavailable')
     */
    public function transformFoodData($food) {
        // Transform is_available to status
        $status = ($food['is_available'] == 1) ? 'available' : 'unavailable';

        // Return food data with additional transformed fields
        return array_merge($food, [
            'status' => $status
        ]);
    }

    /**
     * Get all foods with transformed data format for admin view
     */
    public function getAllForAdmin($limit = null, $offset = 0) {
        $sql = "SELECT f.*, c.name as category_name, sc.name as subcategory_name
                FROM {$this->table} f
                LEFT JOIN categories c ON f.category_id = c.id
                LEFT JOIN sub_categories sc ON f.subcategory_id = sc.id
                ORDER BY f.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        $foods = $stmt->fetchAll();

        // Transform each food item
        return array_map(function($food) {
            return $this->transformFoodData($food);
        }, $foods);
    }

    public function getFoodWithCategory($limit = null, $offset = 0) {
        $sql = "SELECT f.*, c.name as category_name, sc.name as subcategory_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                LEFT JOIN sub_categories sc ON f.subcategory_id = sc.id
                WHERE f.is_available = 1
                ORDER BY f.id DESC";

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

    /**
     * Get featured foods for homepage - those marked as popular, new, or seasonal
     */
    public function getFeaturedFoods($limit = 6) {
        $sql = "SELECT f.*, c.name as category_name, sc.name as subcategory_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                LEFT JOIN sub_categories sc ON f.subcategory_id = sc.id
                WHERE f.is_available = 1
                AND (f.is_popular = 1 OR f.is_new = 1 OR f.is_seasonal = 1)
                ORDER BY f.is_popular DESC, f.is_new DESC, f.is_seasonal DESC, f.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFoodByCategory($categoryId, $limit = null, $excludeId = null) {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                WHERE f.category_id = :category_id AND f.is_available = 1";

        if ($excludeId) {
            $sql .= " AND f.id != :exclude_id";
        }

        $sql .= " ORDER BY f.name";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);

        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchFood($keyword, $limit = null) {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                WHERE (f.name LIKE :keyword OR f.description LIKE :keyword)
                AND f.is_available = 1
                ORDER BY f.name";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        $keyword = "%$keyword%";
        $stmt->bindValue(':keyword', $keyword);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }    public function getFoodDetails($id) {
        $sql = "SELECT f.*, c.name as category_name, sc.name as subcategory_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                LEFT JOIN sub_categories sc ON f.subcategory_id = sc.id
                WHERE f.id = :id AND f.is_available = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getFoodReviews($foodId, $limit = 5) {
        $sql = "SELECT r.*, u.full_name
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.food_item_id = :food_id
                ORDER BY r.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':food_id', $foodId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAverageRating($foodId) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
                FROM reviews
                WHERE food_item_id = :food_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':food_id', $foodId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllWithCategories($limit = null, $offset = 0) {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                LEFT JOIN categories c ON f.category_id = c.id
                ORDER BY f.created_at DESC";

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

    public function countByCategory($categoryId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Get foods with complex filters, sorting, and pagination
     */
    public function getFoodWithFilters($whereClause, $params, $orderBy = 'name ASC', $limit = null, $offset = 0) {
        $sql = "SELECT f.*, c.name as category_name, sc.name as subcategory_name
                FROM food_items f
                LEFT JOIN categories c ON f.category_id = c.id
                LEFT JOIN sub_categories sc ON f.subcategory_id = sc.id"; 

        if (!empty($whereClause)) {
            $sql .= " WHERE $whereClause";
        }
        $sql .= " ORDER BY $orderBy";
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countFoodWithFilters($whereClause, $params) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} f
                LEFT JOIN categories c ON f.category_id = c.id
                LEFT JOIN sub_categories sc ON f.subcategory_id = sc.id";

        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
?>
