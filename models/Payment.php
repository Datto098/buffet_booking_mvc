<?php
/**
 * Payment Model
 */

require_once 'BaseModel.php';

class Payment extends BaseModel {
    protected $table = 'payments';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Create a new payment record
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} (
                order_id, payment_method, vnp_txn_ref, vnp_amount, vnp_order_info,
                vnp_response_code, vnp_transaction_no, vnp_bank_code, vnp_pay_date,
                vnp_secure_hash, payment_status, payment_data, created_at, updated_at
            ) VALUES (
                :order_id, :payment_method, :vnp_txn_ref, :vnp_amount, :vnp_order_info,
                :vnp_response_code, :vnp_transaction_no, :vnp_bank_code, :vnp_pay_date,
                :vnp_secure_hash, :payment_status, :payment_data, :created_at, :updated_at
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':order_id' => $data['order_id'],
                ':payment_method' => $data['payment_method'] ?? 'vnpay',
                ':vnp_txn_ref' => $data['vnp_txn_ref'],
                ':vnp_amount' => $data['vnp_amount'],
                ':vnp_order_info' => $data['vnp_order_info'] ?? null,
                ':vnp_response_code' => $data['vnp_response_code'] ?? null,
                ':vnp_transaction_no' => $data['vnp_transaction_no'] ?? null,
                ':vnp_bank_code' => $data['vnp_bank_code'] ?? null,
                ':vnp_pay_date' => $data['vnp_pay_date'] ?? null,
                ':vnp_secure_hash' => $data['vnp_secure_hash'] ?? null,
                ':payment_status' => $data['payment_status'] ?? 'pending',
                ':payment_data' => $data['payment_data'] ?? null,
                ':created_at' => date('Y-m-d H:i:s'),
                ':updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("Payment creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update payment record
     */
    public function update($id, $data) {
        try {
            $setParts = [];
            $params = [':id' => $id];

            foreach ($data as $key => $value) {
                $setParts[] = "$key = :$key";
                $params[":$key"] = $value;
            }

            $data['updated_at'] = date('Y-m-d H:i:s');
            $setParts[] = "updated_at = :updated_at";
            $params[':updated_at'] = $data['updated_at'];

            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Payment update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find payment by VNPay transaction reference
     */
    public function findByTxnRef($vnpTxnRef) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE vnp_txn_ref = :vnp_txn_ref LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':vnp_txn_ref' => $vnpTxnRef]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Payment findByTxnRef error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find payment by order ID
     */
    public function findByOrderId($orderId) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE order_id = :order_id ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Payment findByOrderId error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get payments by order ID (can have multiple attempts)
     */
    public function getPaymentsByOrderId($orderId) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE order_id = :order_id ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Payment getPaymentsByOrderId error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all payments with order information (for admin)
     */
    public function getAllPaymentsWithOrders($limit = 50, $offset = 0, $filters = []) {
        try {
            $whereConditions = [];
            $params = [];

            // Apply filters
            if (!empty($filters['status'])) {
                $whereConditions[] = "p.payment_status = :status";
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['payment_method'])) {
                $whereConditions[] = "p.payment_method = :payment_method";
                $params[':payment_method'] = $filters['payment_method'];
            }

            if (!empty($filters['date_from'])) {
                $whereConditions[] = "DATE(p.created_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $whereConditions[] = "DATE(p.created_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }

            if (!empty($filters['search'])) {
                $whereConditions[] = "(o.order_number LIKE :search OR o.customer_name LIKE :search OR o.customer_email LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $sql = "SELECT p.*,
                           o.order_number, o.customer_name, o.customer_email, o.customer_phone,
                           o.total_amount as order_total, o.status as order_status,
                           o.created_at as order_created_at
                    FROM {$this->table} p
                    LEFT JOIN orders o ON p.order_id = o.id
                    {$whereClause}
                    ORDER BY p.created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            // Bind limit and offset
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

            // Bind other parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Payment getAllPaymentsWithOrders error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count payments with filters
     */
    public function countPayments($filters = []) {
        try {
            $whereConditions = [];
            $params = [];

            // Apply same filters as getAllPaymentsWithOrders
            if (!empty($filters['status'])) {
                $whereConditions[] = "p.payment_status = :status";
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['payment_method'])) {
                $whereConditions[] = "p.payment_method = :payment_method";
                $params[':payment_method'] = $filters['payment_method'];
            }

            if (!empty($filters['date_from'])) {
                $whereConditions[] = "DATE(p.created_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $whereConditions[] = "DATE(p.created_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }

            if (!empty($filters['search'])) {
                $whereConditions[] = "(o.order_number LIKE :search OR o.customer_name LIKE :search OR o.customer_email LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $sql = "SELECT COUNT(*) as total
                    FROM {$this->table} p
                    LEFT JOIN orders o ON p.order_id = o.id
                    {$whereClause}";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (Exception $e) {
            error_log("Payment countPayments error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats() {
        try {
            $sql = "SELECT
                        COUNT(*) as total_payments,
                        COUNT(CASE WHEN payment_status = 'completed' THEN 1 END) as completed_payments,
                        COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as failed_payments,
                        COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_payments,
                        COALESCE(SUM(CASE WHEN payment_status = 'completed' THEN vnp_amount/100 ELSE 0 END), 0) as total_completed_amount,
                        COALESCE(SUM(CASE WHEN payment_status = 'completed' AND DATE(created_at) = CURDATE() THEN vnp_amount/100 ELSE 0 END), 0) as today_completed_amount
                    FROM {$this->table}";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Payment getPaymentStats error: " . $e->getMessage());
            return [
                'total_payments' => 0,
                'completed_payments' => 0,
                'failed_payments' => 0,
                'pending_payments' => 0,
                'total_completed_amount' => 0,
                'today_completed_amount' => 0
            ];
        }
    }

    /**
     * Delete payment record (for admin)
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Payment deletion error: " . $e->getMessage());
            return false;
        }
    }
}
?>
