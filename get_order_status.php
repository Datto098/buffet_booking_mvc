<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/DineInOrder.php';

header('Content-Type: application/json');

try {
    $table_number = $_GET['table'] ?? null;

    if (!$table_number) {
        echo json_encode(['success' => false, 'message' => 'Table number is required']);
        exit;
    }

    // Check if user is logged in (required for dine-in)
    if (!isset($_SESSION['user']['id'])) {
        echo json_encode(['success' => false, 'message' => 'User must be logged in']);
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    $dineInOrderModel = new DineInOrder();

    // Get orders by both table number and user ID for security
    $orders = $dineInOrderModel->getOrdersByTableAndUser($table_number, $user_id);

    // Format orders for display
    $formattedOrders = [];
    foreach ($orders as $order) {
        $formattedOrder = [
            'id' => $order['id'],
            'status' => $order['status'],
            'total' => $order['total_amount'],
            'notes' => $order['notes'],
            'created_at' => date('d/m/Y H:i', strtotime($order['created_at'])),
            'items' => []
        ];

        // Get order items
        $items = $dineInOrderModel->getOrderItems($order['id']);
        foreach ($items as $item) {
            $formattedOrder['items'][] = [
                'name' => $item['food_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }

        $formattedOrders[] = $formattedOrder;
    }

    echo json_encode([
        'success' => true,
        'orders' => $formattedOrders,
        'debug_info' => [
            'table_number' => $table_number,
            'user_id' => $user_id,
            'total_orders' => count($formattedOrders),
            'query_executed' => "SELECT o.* FROM dine_in_orders o INNER JOIN tables t ON o.table_id = t.id WHERE t.table_number = '$table_number' AND o.user_id = $user_id",
            'raw_orders_count' => count($orders)
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_order_status.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error'
    ]);
}
?>
