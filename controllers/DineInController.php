<?php
/**
 * Dine-in Controller
 * Quản lý đặt món tại bàn
 */

require_once 'controllers/BaseController.php';
require_once 'models/DineInOrder.php';
require_once 'models/Food.php';
require_once 'models/Category.php';
require_once 'models/Table.php';

class DineInController extends BaseController {
    private $tableModel;
    private $foodModel;
    private $categoryModel;
    private $dineInOrderModel;

    public function __construct() {
        $this->tableModel = new Table();
        $this->foodModel = new Food();
        $this->categoryModel = new Category();
        $this->dineInOrderModel = new DineInOrder();
    }

    public function index() {
        // Bắt buộc đăng nhập
        if (!isLoggedIn()) {
            $table_id = $_GET['table'] ?? '';
            $redirect_url = '/dine-in' . ($table_id ? ('?table=' . urlencode($table_id)) : '');
            $_SESSION['redirect_after_login'] = $redirect_url;
            redirect('/auth/login');
        }

        $table_id = $_GET['table'] ?? null;
        $data = [];

        if ($table_id) {
            // Kiểm tra trạng thái bàn
            $table = $this->tableModel->getTableByNumber($table_id);
            if (!$table) {
                $_SESSION['error'] = 'Bàn không tồn tại!';
                redirect('/dine-in');
            }

            // Lấy danh sách món ăn và danh mục
            $data['categories'] = $this->categoryModel->getAllCategories();
            $data['foods'] = $this->foodModel->getAvailableFoods();

            // Lấy đơn hàng hiện tại của bàn (nếu có)
            $data['current_order'] = $this->dineInOrderModel->getCurrentOrderByTable($table['id'], $_SESSION['user']['id']);
        }

        $this->loadView('customer/dine_in/index', $data);
    }

    public function addToCart() {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $food_id = $_POST['food_id'] ?? null;
        $table_id = $_POST['table_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if (!$food_id || !$table_id) {
            jsonResponse(['success' => false, 'message' => 'Missing required data']);
        }

        // Kiểm tra bàn
        $table = $this->tableModel->getTableByNumber($table_id);
        if (!$table) {
            jsonResponse(['success' => false, 'message' => 'Invalid table']);
        }

        // Kiểm tra món ăn
        $food = $this->foodModel->getFoodById($food_id);
        if (!$food || !$food['is_available']) {
            jsonResponse(['success' => false, 'message' => 'Food not available']);
        }

        // Thêm vào session cart
        if (!isset($_SESSION['dine_in_cart'][$table_id])) {
            $_SESSION['dine_in_cart'][$table_id] = [];
        }

        if (isset($_SESSION['dine_in_cart'][$table_id][$food_id])) {
            $_SESSION['dine_in_cart'][$table_id][$food_id] += $quantity;
        } else {
            $_SESSION['dine_in_cart'][$table_id][$food_id] = $quantity;
        }

        $cart_count = array_sum($_SESSION['dine_in_cart'][$table_id]);

        // Debug log
        error_log("Cart debug - Table: $table_id, Food: $food_id, Quantity: $quantity, Cart count: $cart_count");
        error_log("Cart contents: " . print_r($_SESSION['dine_in_cart'][$table_id], true));

        jsonResponse([
            'success' => true,
            'message' => 'Added to cart',
            'cart_count' => $cart_count
        ]);
    }

    public function submitOrder() {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $table_number = $_POST['table_id'] ?? null;
        $notes = $_POST['notes'] ?? '';

        if (!$table_number || !isset($_SESSION['dine_in_cart'][$table_number])) {
            jsonResponse(['success' => false, 'message' => 'Invalid table or empty cart']);
        }

        // Lấy thông tin bàn từ table_number
        $table = $this->tableModel->getTableByNumber($table_number);
        if (!$table) {
            jsonResponse(['success' => false, 'message' => 'Table not found']);
        }

        try {
            // Tính tổng tiền
            $total_amount = 0;
            foreach ($_SESSION['dine_in_cart'][$table_number] as $food_id => $quantity) {
                $food = $this->foodModel->getFoodById($food_id);
                if ($food) {
                    $total_amount += $food['price'] * $quantity;
                }
            }

            // Tạo đơn hàng mới
            $order_data = [
                'table_id' => $table['id'], // Sử dụng table_id thực từ database
                'user_id' => $_SESSION['user']['id'],
                'status' => 'pending',
                'notes' => $notes,
                'total_amount' => $total_amount,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $order_id = $this->dineInOrderModel->createOrder($order_data);

            // Thêm chi tiết đơn hàng
            foreach ($_SESSION['dine_in_cart'][$table_number] as $food_id => $quantity) {
                $food = $this->foodModel->getFoodById($food_id);
                $item_data = [
                    'order_id' => $order_id,
                    'food_id' => $food_id,
                    'quantity' => $quantity,
                    'price' => $food['price'],
                    'total' => $food['price'] * $quantity
                ];
                $this->dineInOrderModel->addOrderItem($item_data);
            }

            // Cập nhật trạng thái bàn
            $this->tableModel->updateTableStatus($table['id'], 'occupied');

            // Xóa giỏ hàng
            unset($_SESSION['dine_in_cart'][$table_number]);

            jsonResponse([
                'success' => true,
                'message' => 'Order submitted successfully',
                'order_id' => $order_id
            ]);

        } catch (Exception $e) {
            jsonResponse([
                'success' => false,
                'message' => 'Error creating order: ' . $e->getMessage()
            ]);
        }
    }

    public function getCart() {
        $table_id = $_GET['table_id'] ?? null;

        if (!$table_id) {
            jsonResponse(['success' => false, 'message' => 'Invalid table']);
            return;
        }

        $cart_items = [];
        $total = 0;

        if (isset($_SESSION['dine_in_cart'][$table_id])) {
            foreach ($_SESSION['dine_in_cart'][$table_id] as $food_id => $quantity) {
                $food = $this->foodModel->getFoodById($food_id);
                if ($food) {
                    $item_total = $food['price'] * $quantity;
                    $cart_items[] = [
                        'id' => $food_id,
                        'name' => $food['name'],
                        'price' => $food['price'],
                        'quantity' => $quantity,
                        'total' => $item_total,
                        'image' => $food['image']
                    ];
                    $total += $item_total;
                }
            }
        }

        jsonResponse([
            'success' => true,
            'cart_items' => $cart_items,
            'total' => $total
        ]);
    }

    public function updateCartItem() {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $table_id = $_POST['table_id'] ?? null;
        $food_id = $_POST['food_id'] ?? null;
        $quantity = (int)$_POST['quantity'] ?? 0;

        if (!$table_id || !$food_id) {
            jsonResponse(['success' => false, 'message' => 'Invalid data']);
        }

        // Initialize cart if not exists
        if (!isset($_SESSION['dine_in_cart'][$table_id])) {
            $_SESSION['dine_in_cart'][$table_id] = [];
        }

        if ($quantity > 0) {
            $_SESSION['dine_in_cart'][$table_id][$food_id] = $quantity;
        } else {
            unset($_SESSION['dine_in_cart'][$table_id][$food_id]);
        }

        $cart_count = isset($_SESSION['dine_in_cart'][$table_id]) ? array_sum($_SESSION['dine_in_cart'][$table_id]) : 0;

        jsonResponse([
            'success' => true,
            'message' => 'Cart updated',
            'cart_count' => $cart_count
        ]);
    }

    public function clearCart() {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $table_id = $_POST['table_id'] ?? null;

        if (!$table_id) {
            jsonResponse(['success' => false, 'message' => 'Invalid table']);
        }

        // Clear cart for this table
        if (isset($_SESSION['dine_in_cart'][$table_id])) {
            unset($_SESSION['dine_in_cart'][$table_id]);
        }

        jsonResponse([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function getOrderStatus() {
        $table_number = $_GET['table_id'] ?? null;

        if (!$table_number) {
            jsonResponse(['success' => false, 'message' => 'Invalid table']);
            return;
        }

        // Lấy thông tin bàn từ table_number
        $table = $this->tableModel->getTableByNumber($table_number);
        if (!$table) {
            jsonResponse(['success' => false, 'message' => 'Table not found']);
            return;
        }

        // Lấy 5 order gần nhất của bàn này
        $orders = $this->dineInOrderModel->getOrdersByTableId($table['id'], 5, $_SESSION['user']['id']);

        $orderStatuses = [];
        foreach ($orders as $order) {
            $items = $this->dineInOrderModel->getOrderItemsByOrderId($order['id']);
            $orderStatuses[] = [
                'id' => $order['id'],
                'status' => $order['status'],
                'created_at' => $order['created_at'],
                'total_amount' => $order['total_amount'] ?? 0,
                'notes' => $order['notes'] ?? '',
                'items' => $items
            ];
        }

        jsonResponse([
            'success' => true,
            'orders' => $orderStatuses
        ]);
    }
}
?>
