<?php
require_once 'BaseModel.php';

class Address extends BaseModel
{
    protected $table = 'addresses';

    public function getAll()
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->query("SELECT * FROM " . $this->table . " ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAddresses($limit = 20, $offset = 0, $filters = [])
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $where = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = 'user_id = ?';
            $params[] = $filters['user_id'];
        }
        if (!empty($filters['type'])) {
            $where[] = 'type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(address LIKE ? OR city LIKE ? OR district LIKE ? OR ward LIKE ?)';

            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search, $search]);
        }
        if (!empty($filters['city'])) {
            $where[] = 'address LIKE ?';
            $params[] = '%' . $filters['city'] . '%';
        }
        $sql = "SELECT * FROM {$this->table}";
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY id DESC LIMIT ? OFFSET ?';
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAddresses($filters = [])
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $where = [];
        $params = [];
        if (!empty($filters['user_id'])) {
            $where[] = 'user_id = ?';
            $params[] = $filters['user_id'];
        }
        if (!empty($filters['type'])) {
            $where[] = 'type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(address LIKE ? OR city LIKE ? OR district LIKE ? OR ward LIKE ?)';

            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search, $search]);
        }
        if (!empty($filters['city'])) {
            $where[] = 'address LIKE ?';
            $params[] = '%' . $filters['city'] . '%';
        }
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function addAddress($data)
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $sql = "INSERT INTO {$this->table} (address, status) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['address'],
            $data['status']
        ]);
    }
}