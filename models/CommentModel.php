<?php
require_once 'BaseModel.php';

class CommentModel extends BaseModel {
    protected $table = 'comments'; 

    // Thêm comment mới
    public function add($data) {
        $fields = [];
        $placeholders = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = $key;
            $placeholders[] = '?';
            $values[] = $value;
        }
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    // Lấy danh sách comment theo food_items_id
    public function getByFoodId($foodId) {
        $sql = "SELECT * FROM {$this->table} WHERE food_items_id = ? ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$foodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>