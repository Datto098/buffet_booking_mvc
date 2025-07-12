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

    /**
     * Get food by ID (alias for getFoodDetails)
     */
    public function getFoodById($id) {
        return $this->getFoodDetails($id);
    }

    /**
     * Get popular foods
     */
    public function getPopularFoods($limit = 8) {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                WHERE f.is_available = 1 AND f.is_popular = 1
                ORDER BY f.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get new foods
     */
    public function getNewFoods($limit = 8) {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                WHERE f.is_available = 1 AND f.is_new = 1
                ORDER BY f.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get available foods
     */
    public function getAvailableFoods($limit = null) {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                JOIN categories c ON f.category_id = c.id
                WHERE f.is_available = 1
                ORDER BY f.name";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get foods by category (alias for getFoodByCategory)
     */
    public function getFoodsByCategory($categoryId, $limit = null) {
        return $this->getFoodByCategory($categoryId, $limit);
    }

    /**
     * Search foods (alias for searchFood)
     */
    public function searchFoods($keyword, $limit = null) {
        return $this->searchFood($keyword, $limit);
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
     */    public function getFoodWithFilters($whereClause, $params, $orderBy = 'name ASC', $limit = null, $offset = 0) {
        try {
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

            // Debug the final SQL
            error_log("Food Model Debug - SQL: " . $sql);
            error_log("Food Model Debug - Params: " . json_encode($params));

            $stmt = $this->db->prepare($sql);

            // Set UTF-8 charset for the connection
            $this->db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
                error_log("Food Model Debug - Binding $key = $value");
            }

            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetchAll();

            error_log("Food Model Debug - Found " . count($result) . " results");
            return $result;

        } catch (PDOException $e) {
            error_log("Food Model Error: " . $e->getMessage());
            error_log("Food Model Error SQL: " . $sql);
            error_log("Food Model Error Params: " . json_encode($params));
            throw $e;
        }
    }    public function countFoodWithFilters($whereClause, $params) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} f
                    LEFT JOIN categories c ON f.category_id = c.id
                    LEFT JOIN sub_categories sc ON f.subcategory_id = sc.id";

            if (!empty($whereClause)) {
                $sql .= " WHERE " . $whereClause;
            }

            $stmt = $this->db->prepare($sql);

            // Set UTF-8 charset for the connection
            $this->db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 0;

        } catch (PDOException $e) {
            error_log("Food Count Error: " . $e->getMessage());
            error_log("Food Count Error SQL: " . $sql);
            error_log("Food Count Error Params: " . json_encode($params));
            return 0;
        }
    }

    /**
     * Debug method to test search functionality
     */    public function debugSearch($searchTerm) {
        try {
            $sql = "SELECT f.name, f.description
                    FROM food_items f
                    WHERE f.is_available = 1
                    AND (f.name LIKE :search_name OR f.description LIKE :search_desc)
                    LIMIT 5";

            $stmt = $this->db->prepare($sql);
            $searchPattern = "%$searchTerm%";
            $stmt->bindValue(':search_name', $searchPattern, PDO::PARAM_STR);
            $stmt->bindValue(':search_desc', $searchPattern, PDO::PARAM_STR);
            $stmt->execute();

            $results = $stmt->fetchAll();
            error_log("Debug Search Results for '$searchTerm': " . count($results) . " found");

            foreach ($results as $result) {
                error_log("Found: " . $result['name']);
            }

            return $results;
        } catch (PDOException $e) {
            error_log("Debug Search Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all buffet items (free items included in buffet price)
     */
    public function getBuffetItems() {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                LEFT JOIN categories c ON f.category_id = c.id
                WHERE f.is_buffet_item = 1 AND f.is_available = 1
                ORDER BY f.sort_order ASC, f.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all regular menu items (additional paid items)
     */
    public function getRegularMenuItems() {
        $sql = "SELECT f.*, c.name as category_name
                FROM {$this->table} f
                LEFT JOIN categories c ON f.category_id = c.id
                WHERE f.is_buffet_item = 0 AND f.is_available = 1
                ORDER BY c.sort_order ASC, f.sort_order ASC, f.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get foods by type (buffet or regular)
     */
    public function getFoodsByType($isBuffet = false) {
        if ($isBuffet) {
            return $this->getBuffetItems();
        } else {
            return $this->getRegularMenuItems();
        }
    }

    /**
     * Check if a food item is buffet item
     */
    public function isBuffetItem($foodId) {
        $sql = "SELECT is_buffet_item FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $foodId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (bool)$result['is_buffet_item'] : false;
    }

    /**
     * Update buffet status of a food item
     */
    public function updateBuffetStatus($foodId, $isBuffet) {
        $sql = "UPDATE {$this->table} SET is_buffet_item = :is_buffet WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':is_buffet', $isBuffet ? 1 : 0);
        $stmt->bindValue(':id', $foodId);
        return $stmt->execute();
    }
}
?>
