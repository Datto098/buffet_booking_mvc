<?php

require_once __DIR__ . '/BaseModel.php';

class Invoice extends BaseModel
{
    protected $table = 'invoices';

    public function findByOrderId($orderId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);

        return $this->db->lastInsertId();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePaymentStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET payment_status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getByOrderIds($orderIds)
    {
        if (empty($orderIds)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE order_id IN ($placeholders)");
        $stmt->execute($orderIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByDateRange($startDate, $endDate)
    {
        $stmt = $this->db->prepare("
            SELECT i.*, dio.table_number, u.full_name as customer_name
            FROM {$this->table} i
            LEFT JOIN dine_in_orders dio ON i.order_id = dio.id
            LEFT JOIN users u ON dio.user_id = u.id
            WHERE DATE(i.created_at) BETWEEN ? AND ?
            ORDER BY i.created_at DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRevenue($startDate = null, $endDate = null)
    {
        $sql = "SELECT SUM(total_amount) as total FROM {$this->table} WHERE payment_status = 'paid'";
        $params = [];

        if ($startDate && $endDate) {
            $sql .= " AND DATE(created_at) BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }

    public function getById($id)
    {
        return $this->findById($id);
    }

    public function getInvoiceDetails($invoiceId)
    {
        $stmt = $this->db->prepare("
            SELECT
                i.*,
                t.table_number,
                dio.created_at as order_created_at,
                CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                u.phone as customer_phone,
                u.email as customer_email
            FROM {$this->table} i
            LEFT JOIN dine_in_orders dio ON i.order_id = dio.id
            LEFT JOIN tables t ON dio.table_id = t.id
            LEFT JOIN users u ON dio.user_id = u.id
            WHERE i.id = ?
        ");
        $stmt->execute([$invoiceId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
