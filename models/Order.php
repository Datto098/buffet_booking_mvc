<?php

/**
 * Order Model
 */

require_once 'BaseModel.php';

class Order extends BaseModel
{
    protected $table = 'orders';

    public function createOrder($orderData, $orderItems = null)
    {
        try {
            $this->db->beginTransaction();

            // If order items are passed separately, merge them into orderData
            if ($orderItems !== null && !isset($orderData['items'])) {
                $orderData['items'] = $orderItems;
            }

            // Create main order
            $sql = "INSERT INTO {$this->table} (
                user_id, booking_id, order_number, customer_name, customer_email, customer_phone, order_type, subtotal, tax_amount, total_amount, payment_method, payment_status, status, special_instructions, delivery_address, order_notes, estimated_ready_time, completed_at, created_at, updated_at
            ) VALUES (
                :user_id, :booking_id, :order_number, :customer_name, :customer_email, :customer_phone, :order_type, :subtotal, :tax_amount, :total_amount, :payment_method, :payment_status, :status, :special_instructions, :delivery_address, :order_notes, :estimated_ready_time, :completed_at, :created_at, :updated_at
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $orderData['user_id'] ?? null,
                ':booking_id' => $orderData['booking_id'] ?? null,
                ':order_number' => $orderData['order_number'] ?? uniqid('ORD'),
                ':customer_name' => $orderData['customer_name'] ?? '',
                ':customer_email' => $orderData['customer_email'] ?? '',
                ':customer_phone' => $orderData['customer_phone'] ?? '',
                ':order_type' => $orderData['order_type'] ?? 'delivery',
                ':subtotal' => $orderData['subtotal'] ?? 0,
                ':tax_amount' => $orderData['tax_amount'] ?? 0,
                ':total_amount' => $orderData['total_amount'] ?? 0,
                ':payment_method' => $orderData['payment_method'] ?? 'cash',
                ':payment_status' => $orderData['payment_status'] ?? 'pending',
                ':status' => $orderData['status'] ?? 'pending',
                ':special_instructions' => $orderData['special_instructions'] ?? null,
                ':delivery_address' => $orderData['delivery_address'] ?? null,
                ':order_notes' => $orderData['order_notes'] ?? null,
                ':estimated_ready_time' => $orderData['estimated_ready_time'] ?? null,
                ':completed_at' => $orderData['completed_at'] ?? null,
                ':created_at' => date('Y-m-d H:i:s'),
                ':updated_at' => date('Y-m-d H:i:s')
            ]);

            $orderId = $this->db->lastInsertId();

            // Create order items
            if (!empty($orderData['items'])) {
                $sql = "INSERT INTO order_items (order_id, food_item_id, quantity, unit_price, total_price, special_instructions, created_at)
                        VALUES (:order_id, :food_item_id, :quantity, :unit_price, :total_price, :special_instructions, :created_at)";
                $stmt = $this->db->prepare($sql);

                foreach ($orderData['items'] as $item) {
                    $stmt->execute([
                        ':order_id' => $orderId,
                        ':food_item_id' => $item['food_id'], // đổi từ food_item_id thành food_id
                        ':quantity' => $item['quantity'],
                        ':unit_price' => $item['price'],     // đổi từ unit_price thành price
                        ':total_price' => $item['total'],    // đổi từ total_price thành total
                        ':special_instructions' => $item['special_instructions'] ?? null,
                        ':created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getOrdersByUser($userId, $limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countOrdersByUser($userId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getOrderDetails($orderId, $userId = null)
    {
        $sql = "SELECT o.*, ua.address_line, CONCAT(u.first_name, ' ', u.last_name) as full_name, u.phone_number
                FROM {$this->table} o
                JOIN user_addresses ua ON o.address_id = ua.id
                JOIN users u ON o.user_id = u.id
                WHERE o.id = :order_id";

        if ($userId) {
            $sql .= " AND o.user_id = :user_id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);

        if ($userId) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetch();
    }
    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, f.name AS food_name, f.image, f.description
            FROM order_items oi
            LEFT JOIN food_items f ON oi.food_item_id = f.id
            WHERE oi.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($orderId, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOrderStatistics($startDate = null, $endDate = null)
    {
        $whereClause = "";
        $params = [];

        if ($startDate && $endDate) {
            $whereClause = "WHERE created_at BETWEEN :start_date AND :end_date";
            $params = [
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ];
        }

        $sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_order_value,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
                FROM {$this->table} {$whereClause}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getRecentOrders($limit = 10)
    {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getOrdersByStatus($status, $limit = null)
    {
        $sql = "SELECT o.*, u.full_name, ua.address_line
                FROM {$this->table} o
                JOIN users u ON o.user_id = u.id
                JOIN user_addresses ua ON o.address_id = ua.id
                WHERE o.status = :status
                ORDER BY o.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTodayCount()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getTodayRevenue()
    {
        $sql = "SELECT SUM(total_amount) as revenue FROM {$this->table}
                WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['revenue'] ?? 0;
    }
    public function getAllOrders($limit = null, $offset = 0, $status = '')
    {
        $sql = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email as customer_email,
                       COUNT(oi.id) as total_items
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id";

        if ($status) {
            $sql .= " WHERE o.status = :status";
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($status) {
            $stmt->bindValue(':status', $status);
        }

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count($status = '')
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        if ($status) {
            $sql .= " WHERE status = :status";
        }

        $stmt = $this->db->prepare($sql);

        if ($status) {
            $stmt->bindValue(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Count orders for a specific user
     * @param int $userId User ID
     * @return int Number of orders
     */
    public function countUserOrders($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get total amount spent by a user
     * @param int $userId User ID
     * @return float Total spent amount
     */
    public function getUserTotalSpent($userId)
    {
        $sql = "SELECT SUM(total_amount) as total FROM {$this->table}
                WHERE user_id = :user_id AND status NOT IN ('cancelled')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Count user orders by status
     * @param int $userId User ID
     * @param string $status Order status
     * @return int Number of orders with specific status
     */
    public function countUserOrdersByStatus($userId, $status)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE user_id = :user_id AND status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get user's monthly spending data for charts
     * @param int $userId User ID
     * @param int $months Number of months to get data for
     * @return array Monthly spending data
     */
    public function getUserMonthlySpending($userId, $months = 6)
    {
        $sql = "SELECT
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(total_amount) as total_spent,
                    COUNT(*) as order_count
                FROM {$this->table}
                WHERE user_id = :user_id
                AND status NOT IN ('cancelled')
                AND created_at >= DATE_SUB(NOW(), INTERVAL :months MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get order with items for detailed view
     * @param int $orderId Order ID
     * @return array Order details with items

     */    public function getOrderWithItems($orderId)
    {
        // Get order details
        $sql = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email as customer_email

                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch();

        if (!$order) {
            return []; // Return an empty array instead of null
        }

        // Get order items with special instructions
        $sql = "SELECT oi.*, f.name as food_name, f.image, f.description
                FROM order_items oi
                LEFT JOIN food_items f ON oi.food_item_id = f.id
                WHERE oi.order_id = :order_id
                ORDER BY f.name";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $order['items'] = $stmt->fetchAll();

        return $order;

    }    // Get orders for CSV export with filtering
    public function getOrdersForExport($filters = [])
    {

        $sql = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email as customer_email,
                       COUNT(oi.id) as total_items
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (CONCAT(u.first_name, ' ', u.last_name) LIKE :search OR u.email LIKE :search OR o.id LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Get filtered orders for enhanced admin interface
    public function getFilteredOrders($filters = [], $limit = null, $offset = 0)
    {
        $sql = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email as customer_email,
                       COUNT(oi.id) as total_items
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (CONCAT(u.first_name, ' ', u.last_name) LIKE :search OR u.email LIKE :search OR o.id LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Count filtered orders
    public function countFilteredOrders($filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT o.id) as count
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE :search OR u.email LIKE :search OR o.id LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get order status history/timeline for tracking
     * @param int $orderId Order ID
     * @return array Array of status history records
     */
    public function getOrderHistory($orderId)
    {
        // For now, return a timeline based on the current order status
        // In a full implementation, this would come from a separate order_history table
        $order = $this->findById($orderId);

        if (!$order) {
            return [];
        }

        $timeline = [];
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
        $currentStatusIndex = array_search($order['status'], $statuses);

        foreach ($statuses as $index => $status) {
            $timeline[] = [
                'status' => $status,
                'status_text' => $this->getStatusText($status),
                'completed' => $index <= $currentStatusIndex,
                'timestamp' => $index <= $currentStatusIndex ? $order['created_at'] : null,
                'description' => $this->getStatusDescription($status)
            ];

            // Stop at cancelled status if order is cancelled
            if ($order['status'] === 'cancelled' && $index === 0) {
                $timeline[] = [
                    'status' => 'cancelled',
                    'status_text' => 'Order Cancelled',
                    'completed' => true,
                    'timestamp' => $order['updated_at'] ?? $order['created_at'],
                    'description' => 'Order was cancelled'
                ];
                break;
            }
        }

        return $timeline;
    }

    /**
     * Get human-readable status text
     * @param string $status Status code
     * @return string Human-readable status
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'pending' => 'Order Placed',
            'confirmed' => 'Order Confirmed',
            'preparing' => 'Preparing Food',
            'ready' => 'Ready for Pickup/Delivery',
            'completed' => 'Order Completed',
            'cancelled' => 'Order Cancelled'
        ];

        return $statusTexts[$status] ?? ucfirst($status);
    }

    /**
     * Get status description
     * @param string $status Status code
     * @return string Status description
     */
    private function getStatusDescription($status)
    {
        $descriptions = [
            'pending' => 'Your order has been received and is awaiting confirmation',
            'confirmed' => 'Your order has been confirmed and is being processed',
            'preparing' => 'Our kitchen is preparing your delicious meal',
            'ready' => 'Your order is ready for pickup or delivery',
            'completed' => 'Your order has been completed successfully',
            'cancelled' => 'Your order has been cancelled'
        ];

        return $descriptions[$status] ?? '';
    }

    /**
     * Get orders for a specific user (alias for getOrdersByUser for controller compatibility)
     * @param int $userId User ID
     * @param int|null $limit Optional limit
     * @param int $offset Optional offset
     * @return array Array of user orders
     */
    public function getUserOrders($userId, $limit = null, $offset = 0)
    {
        return $this->getOrdersByUser($userId, $limit, $offset);
    }

    /**
     * Count orders by user (alias for countUserOrders)
     * @param int $userId User ID
     * @return int Number of orders

     */
    public function countByUser($userId)
    {
        return $this->countUserOrders($userId);
    }

    /**
     * Get total amount spent by user (alias for getUserTotalSpent)
     * @param int $userId User ID
     * @return float Total spent amount
     */
    public function getTotalSpentByUser($userId)
    {
        return $this->getUserTotalSpent($userId);
    }

    /**

     * Get comprehensive order statistics for admin dashboard
     * @return array Order statistics
     */
    public function getOrderStats()
    {
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_order_value,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders,
                    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_orders,
                    COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() AND status != 'cancelled' THEN total_amount END), 0) as today_revenue,
                    COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_orders,
                    COALESCE(SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND status != 'cancelled' THEN total_amount END), 0) as week_revenue,
                    COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as month_orders,
                    COALESCE(SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND status != 'cancelled' THEN total_amount END), 0) as month_revenue
                FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get current month revenue
     */
    public function getMonthlyRevenue()
    {
        $sql = "SELECT SUM(total_amount) as revenue FROM {$this->table}
                WHERE MONTH(created_at) = MONTH(CURDATE())
                AND YEAR(created_at) = YEAR(CURDATE())
                AND status != 'cancelled'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['revenue'] ?? 0;
    }

    /**
     * Get monthly revenue data for chart (last 12 months)
     */
    public function getMonthlyRevenueData()
    {
        $sql = "SELECT
                    MONTH(created_at) as month,
                    YEAR(created_at) as year,
                    SUM(total_amount) as revenue
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                AND status != 'cancelled'
                GROUP BY YEAR(created_at), MONTH(created_at)
                ORDER BY year, month";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        // Initialize array for 12 months with 0 values
        $monthlyData = array_fill(0, 12, 0);

        foreach ($results as $result) {
            $monthIndex = $result['month'] - 1; // Convert to 0-based index
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $monthlyData[$monthIndex] = (float)$result['revenue'];
            }
        }

        return $monthlyData;
    }

    /**
     * Get recent orders with customer information
     */
    public function getRecentOrdersWithCustomer($limit = 5)
    {
        $sql = "SELECT o.*,
                       COALESCE(CONCAT(u.first_name, ' ', u.last_name), o.customer_name) as customer_name
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find order by ID
     * @param int $id Order ID
     * @return array|false Order data or false if not found
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Update order data
     * @param int $id Order ID
     * @param array $data Update data
     * @return bool Success status
     */
    public function update($id, $data) {
        $setParts = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $setParts[] = "`$key` = :$key";
            $params[":$key"] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get total spent by user (for AdminController usage)
     * @param int $userId User ID
     * @return float Total amount spent
     */
    public function getTotalSpentByUser($userId) {
        $sql = "SELECT SUM(total_amount) as total FROM {$this->table}
                WHERE user_id = :user_id AND status IN ('completed', 'delivered')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Count orders by user
     * @param int $userId User ID
     * @return int Order count
     */
    public function countByUser($userId) {        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Delete an order and its items
     */
    public function deleteOrder($orderId) {
        try {
            $this->db->beginTransaction();

            // Delete order items first
            $stmt = $this->db->prepare("DELETE FROM order_items WHERE order_id = :order_id");
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();

            // Delete the order
            $stmt = $this->db->prepare("DELETE FROM orders WHERE id = :id");
            $stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error deleting order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        // Get today's stats
        $sql = "SELECT
                    COUNT(*) as today_orders,
                    COALESCE(SUM(total_amount), 0) as today_revenue
                FROM {$this->table}
                WHERE DATE(created_at) = CURDATE()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $todayStats = $stmt->fetch();

        // Get total stats
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_revenue
                FROM {$this->table}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $totalStats = $stmt->fetch();

        return [
            'today_orders' => $todayStats['today_orders'] ?? 0,
            'today_revenue' => $todayStats['today_revenue'] ?? 0,
            'total_orders' => $totalStats['total_orders'] ?? 0,
            'total_revenue' => $totalStats['total_revenue'] ?? 0
        ];
    }
}
