<?php
/**
 * Category Model
 */

require_once 'BaseModel.php';

class Category extends BaseModel {
    protected $table = 'categories';

    public function getCategoriesWithSubcategories() {
        $sql = "SELECT c.*,
                (SELECT COUNT(*) FROM sub_categories sc WHERE sc.category_id = c.id) as subcategory_count,
                (SELECT COUNT(*) FROM food_items f WHERE f.category_id = c.id AND f.is_available = 1) as food_count
                FROM {$this->table} c
                ORDER BY c.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSubcategories($categoryId) {
        $sql = "SELECT * FROM sub_categories WHERE category_id = :category_id ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createSubcategory($data) {
        $sql = "INSERT INTO sub_categories (category_id, name) VALUES (:category_id, :name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $data['name']);
        return $stmt->execute();
    }

    public function updateSubcategory($id, $data) {
        $sql = "UPDATE sub_categories SET name = :name WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteSubcategory($id) {
        $sql = "DELETE FROM sub_categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }    public function getAllWithStats() {
        try {
            $sql = "SELECT c.*,
                    (SELECT COUNT(*) FROM food_items f WHERE f.category_id = c.id) as food_count,
                    CASE WHEN c.is_active = 1 THEN 'active' ELSE 'inactive' END as status,
                    COALESCE(c.sort_order, 0) as sort_order
                    FROM {$this->table} c
                    ORDER BY COALESCE(c.sort_order, 0) ASC, c.name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getAllWithStats: " . $e->getMessage());
            return [];
        }
    }

    public function getPopularCategories($limit = 5) {
        $sql = "SELECT c.*, COUNT(oi.id) as order_count
                FROM {$this->table} c
                LEFT JOIN food_items f ON c.id = f.category_id
                LEFT JOIN order_items oi ON f.id = oi.food_item_id
                GROUP BY c.id
                ORDER BY order_count DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }    /**
     * Count categories, optionally by status
     * @param string $status Optional status to filter by ('active' or 'inactive')
     * @return int Number of categories
     */
    public function count($status = null) {
        if ($status) {
            $is_active = ($status === 'active') ? 1 : 0;
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = :is_active";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':is_active', $is_active, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } else {
            // Call parent count method for standard counting
            return parent::count();
        }
    }    public function getMainCategories() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function transformForStorage($data) {
        $result = $data;
        // Convert string status to boolean is_active
        if (isset($result['status'])) {
            $result['is_active'] = ($result['status'] === 'active') ? 1 : 0;
            unset($result['status']);
        }
        // Ensure sort_order is an integer
        if (isset($result['sort_order'])) {
            $result['sort_order'] = (int)$result['sort_order'];
        }
        return $result;
    }

    public function create($data) {
        try {
            $data = $this->transformForStorage($data);
            return parent::create($data);
        } catch (PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $data = $this->transformForStorage($data);
            return parent::update($id, $data);
        } catch (PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }
}
?>
