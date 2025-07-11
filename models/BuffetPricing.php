<?php

require_once __DIR__ . '/BaseModel.php';

class BuffetPricing extends BaseModel
{
    protected $table = 'buffet_pricing';

    public function getAllActive()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY type, age_from");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPriceByAge($age)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1
            AND age_from <= ?
            AND age_to >= ?
            ORDER BY price DESC
            LIMIT 1
        ");
        $stmt->execute([$age, $age]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdultPrice()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE type = 'adult' AND is_active = 1 LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getChildPrices()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE type = 'child' AND is_active = 1 ORDER BY age_from");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePrice($id, $price)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET price = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$price, $id]);
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $data['id'] = $id;

        $stmt = $this->db->prepare("UPDATE {$this->table} SET $setClause, updated_at = NOW() WHERE id = :id");
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleActive($id)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_active = NOT is_active, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
