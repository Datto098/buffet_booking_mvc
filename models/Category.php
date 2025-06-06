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
    }

    public function getAllWithStats() {
        $sql = "SELECT c.*,
                (SELECT COUNT(*) FROM food_items f WHERE f.category_id = c.id) as food_count
                FROM {$this->table} c
                ORDER BY c.sort_order ASC, c.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
    }

    /**
     * Count categories, optionally by status
     * @param string $status Optional status to filter by
     * @return int Number of categories
     */
    public function count($status = null) {
        if ($status) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = :status";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } else {
            // Call parent count method for standard counting
            return parent::count();
        }
    }

    public function getMainCategories() {
        $sql = "SELECT * FROM {$this->table} WHERE parent_id IS NULL OR parent_id = 0 ORDER BY sort_order ASC, name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
