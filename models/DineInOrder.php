<?php
/**
 * Dine-in Order Model
 * Quản lý đơn hàng tại bàn
 */

require_once 'models/BaseModel.php';

class DineInOrder extends BaseModel {
    protected $table = 'dine_in_orders';
    protected $itemsTable = 'dine_in_order_items';

    public function createOrder($data) {
        try {
            $this->db->beginTransaction();

            // Insert order
            $sql = "INSERT INTO {$this->table} (table_id, user_id, status, notes, total_amount, created_at)
                    VALUES (:table_id, :user_id, :status, :notes, :total_amount, :created_at)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':table_id' => $data['table_id'],
                ':user_id' => $data['user_id'],
                ':status' => $data['status'],
                ':notes' => $data['notes'],
                ':total_amount' => $data['total_amount'] ?? 0,
                ':created_at' => $data['created_at']
            ]);

            $order_id = $this->db->lastInsertId();
            $this->db->commit();

            return $order_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function addOrderItem($data) {
        $sql = "INSERT INTO {$this->itemsTable} (order_id, food_id, quantity, price, total)
                VALUES (:order_id, :food_id, :quantity, :price, :total)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':order_id' => $data['order_id'],
            ':food_id' => $data['food_id'],
            ':quantity' => $data['quantity'],
            ':price' => $data['price'],
            ':total' => $data['total']
        ]);
    }

    public function getCurrentOrderByTable($table_id, $user_id) {
        $sql = "SELECT o.*,
                       GROUP_CONCAT(CONCAT(f.name, ' x', oi.quantity)) as items,
                       SUM(oi.total) as total_amount
                FROM {$this->table} o
                LEFT JOIN {$this->itemsTable} oi ON o.id = oi.order_id
                LEFT JOIN food_items f ON oi.food_id = f.id
                WHERE o.table_id = :table_id
                AND o.user_id = :user_id
                AND o.status IN ('pending', 'processing')
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':table_id' => $table_id, ':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderById($order_id) {
        $sql = "SELECT o.*, t.table_number
                FROM {$this->table} o
                LEFT JOIN tables t ON o.table_id = t.id
                WHERE o.id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Get order items
            $sql = "SELECT oi.*, f.name as food_name, f.image
                    FROM {$this->itemsTable} oi
                    LEFT JOIN food_items f ON oi.food_id = f.id
                    WHERE oi.order_id = :order_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $order_id]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $order;
    }

    public function updateOrderStatus($order_id, $status) {
        $sql = "UPDATE {$this->table}
                SET status = :status,
                    updated_at = NOW()
                WHERE id = :order_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':order_id' => $order_id,
            ':status' => $status
        ]);
    }

    public function getOrdersByDateRange($start_date, $end_date) {
        $sql = "SELECT o.*, t.table_number,
                       COUNT(oi.id) as total_items,
                       SUM(oi.total) as total_amount
                FROM {$this->table} o
                LEFT JOIN tables t ON o.table_id = t.id
                LEFT JOIN {$this->itemsTable} oi ON o.id = oi.order_id
                WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
                GROUP BY o.id
                ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopularItems($limit = 10) {
        $sql = "SELECT f.id, f.name, f.image,
                       COUNT(oi.id) as order_count,
                       SUM(oi.quantity) as total_quantity
                FROM {$this->itemsTable} oi
                LEFT JOIN food_items f ON oi.food_id = f.id
                GROUP BY f.id
                ORDER BY total_quantity DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersByTableId($tableId, $limit = 5, $user_id = null)
    {
        $sql = "SELECT o.*, COALESCE(SUM(oi.total), 0) as calculated_total_amount
                FROM dine_in_orders o
                LEFT JOIN dine_in_order_items oi ON o.id = oi.order_id
                WHERE o.table_id = :table_id";
        if ($user_id !== null) {
            $sql .= " AND o.user_id = :user_id";
        }
        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':table_id', $tableId, PDO::PARAM_INT);
        if ($user_id !== null) {
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        }
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sử dụng calculated_total_amount thay vì total_amount từ DB
        foreach ($orders as &$order) {
            $order['total_amount'] = $order['calculated_total_amount'];
            unset($order['calculated_total_amount']);
        }

        return $orders;
    }

    public function getOrderItemsByOrderId($order_id) {
        $sql = "SELECT oi.*, f.name as food_name, f.image
                FROM {$this->itemsTable} oi
                LEFT JOIN food_items f ON oi.food_id = f.id
                WHERE oi.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDineInOrdersAdmin($status = '', $table = '', $search = '', $limit = 20, $offset = 0) {
        $where = [];
        $params = [];
        if ($status) {
            $where[] = 'o.status = :status';
            $params[':status'] = $status;
        }
        if ($table) {
            $where[] = 'o.table_id = :table_id';
            $params[':table_id'] = $table;
        }
        if ($search) {
            $where[] = '(t.table_number LIKE :search OR o.notes LIKE :search)';
            $params[':search'] = "%$search%";
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT o.*, t.table_number, COALESCE(SUM(oi.total),0) as total_amount
                FROM dine_in_orders o
                LEFT JOIN tables t ON o.table_id = t.id
                LEFT JOIN dine_in_order_items oi ON o.id = oi.order_id
                $whereSql
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";
        $db = $this->db;
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countDineInOrdersAdmin($status = '', $table = '', $search = '') {
        $where = [];
        $params = [];
        if ($status) {
            $where[] = 'status = :status';
            $params[':status'] = $status;
        }
        if ($table) {
            $where[] = 'table_id = :table_id';
            $params[':table_id'] = $table;
        }
        if ($search) {
            $where[] = '(notes LIKE :search)';
            $params[':search'] = "%$search%";
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT COUNT(*) FROM dine_in_orders $whereSql";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
?>
