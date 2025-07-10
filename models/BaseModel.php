<?php
/**
 * Base Model Class
 */

class BaseModel {
    protected $db;
    protected $table;    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function findAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table}";

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

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }
    public function update($id, $data) {
        try {
            $setClause = [];
            foreach ($data as $key => $value) {
                $setClause[] = "$key = :$key";
            }
            $setClause = implode(', ', $setClause);

            $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
            error_log("SQL Query: " . $sql);
            error_log("Data for update: " . print_r($data, true));
            error_log("ID: " . $id);

            $stmt = $this->db->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $result = $stmt->execute();
            $rowCount = $stmt->rowCount();

            error_log("Execute result: " . ($result ? 'true' : 'false'));
            error_log("Rows affected: " . $rowCount);

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error: " . print_r($errorInfo, true));
            }

            return $result;
        } catch (PDOException $e) {
            error_log("PDO Exception in update: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }    public function count($condition = null, $value = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        if ($condition && $value) {
            $sql .= " WHERE $condition = :value";
        }

        $stmt = $this->db->prepare($sql);

        if ($condition && $value) {
            $stmt->bindValue(':value', $value);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getDb() {
        return $this->db;
    }
}
?>
