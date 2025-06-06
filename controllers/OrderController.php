<?php
/**
 * Order Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/User.php';

class OrderController extends BaseController {
    private $orderModel;
    private $foodModel;
    private $userModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->foodModel = new Food();
        $this->userModel = new User();
    }

    public function index() {
        $this->myOrders();
    }

    public function checkout() {
        // Check if cart is not empty
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $_SESSION['error'] = 'Giỏ hàng trống';
            redirect('/index.php?page=cart');
        }

        // Calculate cart totals
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $foodId => $quantity) {
            $food = $this->foodModel->findById($foodId);
            if ($food && $food['is_available']) {
                $itemTotal = $food['price'] * $quantity;
                $cartItems[] = [
                    'food' => $food,
                    'quantity' => $quantity,
                    'total' => $itemTotal
                ];
                $subtotal += $itemTotal;
            }
        }

        if (empty($cartItems)) {
            $_SESSION['error'] = 'Không có món ăn hợp lệ trong giỏ hàng';
            redirect('/index.php?page=cart');
        }

        // Calculate fees and total
        $deliveryFee = 30000; // 30k delivery fee
        $serviceFee = $subtotal * 0.05; // 5% service fee
        $total = $subtotal + $deliveryFee + $serviceFee;

        $data = [
            'title' => 'Thanh Toán - ' . SITE_NAME,
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'serviceFee' => $serviceFee,
            'total' => $total
        ];

        $this->loadView('customer/order/checkout', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=order&action=checkout');
        }

        $this->validateCSRF();

        // Check if cart is not empty
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $_SESSION['error'] = 'Giỏ hàng trống';
            redirect('/index.php?page=cart');
        }

        // Get form data
        $customerName = sanitizeInput($_POST['customer_name'] ?? '');
        $customerEmail = sanitizeInput($_POST['customer_email'] ?? '');
        $customerPhone = sanitizeInput($_POST['customer_phone'] ?? '');
        $deliveryAddress = sanitizeInput($_POST['delivery_address'] ?? '');
        $orderNotes = sanitizeInput($_POST['order_notes'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'cod';

        // Validation
        $errors = [];

        if (empty($customerName)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }

        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (empty($customerPhone)) {
            $errors[] = 'Vui lòng nhập số điện thoại';
        }

        if (empty($deliveryAddress)) {
            $errors[] = 'Vui lòng nhập địa chỉ giao hàng';
        }

        if (!in_array($paymentMethod, ['cod', 'bank_transfer', 'credit_card'])) {
            $errors[] = 'Phương thức thanh toán không hợp lệ';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            redirect('/index.php?page=order&action=checkout');
        }

        // Prepare cart items and calculate totals
        $orderItems = [];
        $subtotal = 0;

        foreach ($cart as $foodId => $quantity) {
            $food = $this->foodModel->findById($foodId);
            if ($food && $food['is_available']) {
                $itemTotal = $food['price'] * $quantity;
                $orderItems[] = [
                    'food_id' => $foodId,
                    'food_name' => $food['name'],
                    'price' => $food['price'],
                    'quantity' => $quantity,
                    'total' => $itemTotal
                ];
                $subtotal += $itemTotal;
            }
        }

        if (empty($orderItems)) {
            $_SESSION['error'] = 'Không có món ăn hợp lệ trong giỏ hàng';
            redirect('/index.php?page=cart');
        }

        // Calculate fees and total
        $deliveryFee = 30000;
        $serviceFee = $subtotal * 0.05;
        $totalAmount = $subtotal + $deliveryFee + $serviceFee;

        // Create order data
        $orderData = [
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'delivery_address' => $deliveryAddress,
            'order_notes' => $orderNotes,
            'payment_method' => $paymentMethod,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount,
            'status' => 'pending'
        ];

        // Add user ID if logged in
        if (isLoggedIn()) {
            $orderData['user_id'] = $_SESSION['user_id'];
        }

        // Create order
        $orderId = $this->orderModel->createOrder($orderData, $orderItems);

        if ($orderId) {
            // Clear cart
            unset($_SESSION['cart']);

            $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng: ' . $orderId;

            // TODO: Send order confirmation email

            redirect('/index.php?page=order&action=detail&id=' . $orderId);
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.';
            $_SESSION['form_data'] = $_POST;
            redirect('/index.php?page=order&action=checkout');
        }
    }

    public function myOrders() {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $orders = $this->orderModel->getUserOrders($userId);

        $data = [
            'title' => 'Đơn Hàng Của Tôi - ' . SITE_NAME,
            'orders' => $orders
        ];

        $this->loadView('customer/order/my_orders', $data);
    }

    public function detail() {
        $orderId = intval($_GET['id'] ?? 0);

        if ($orderId <= 0) {
            redirect('/index.php');
        }

        $order = $this->orderModel->getOrderWithItems($orderId);

        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng';
            redirect('/index.php');
        }

        // Check if user has permission to view this order
        if (isLoggedIn()) {
            if ($order['user_id'] != $_SESSION['user_id'] && !isAdmin() && !isManager()) {
                $_SESSION['error'] = 'Bạn không có quyền xem đơn hàng này';
                redirect('/index.php');
            }
        } else {
            // For guests, they can only view immediately after placing order
            if (!isset($_SESSION['success'])) {
                redirect('/index.php?page=auth&action=login');
            }
        }

        $data = [
            'title' => 'Chi Tiết Đơn Hàng #' . $orderId . ' - ' . SITE_NAME,
            'order' => $order
        ];

        $this->loadView('customer/order/detail', $data);
    }

    public function cancel() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=order&action=myOrders');
        }

        $this->validateCSRF();

        $orderId = intval($_POST['order_id'] ?? 0);

        if ($orderId <= 0) {
            $_SESSION['error'] = 'ID đơn hàng không hợp lệ';
            redirect('/index.php?page=order&action=myOrders');
        }

        $order = $this->orderModel->findById($orderId);

        // Check if order belongs to current user
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng';
            redirect('/index.php?page=order&action=myOrders');
        }

        // Check if order can be cancelled
        if (!in_array($order['status'], ['pending', 'confirmed'])) {
            $_SESSION['error'] = 'Không thể hủy đơn hàng này';
            redirect('/index.php?page=order&action=detail&id=' . $orderId);
        }

        // Update order status
        if ($this->orderModel->updateOrderStatus($orderId, 'cancelled')) {
            $_SESSION['success'] = 'Đã hủy đơn hàng thành công';

            // TODO: Send cancellation email

        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi hủy đơn hàng';
        }

        redirect('/index.php?page=order&action=myOrders');
    }

    public function track() {
        $orderId = intval($_GET['id'] ?? 0);

        if ($orderId <= 0) {
            redirect('/index.php');
        }

        $order = $this->orderModel->findById($orderId);

        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng';
            redirect('/index.php');
        }

        // Check permission
        if (isLoggedIn()) {
            if ($order['user_id'] != $_SESSION['user_id'] && !isAdmin() && !isManager()) {
                $_SESSION['error'] = 'Bạn không có quyền xem đơn hàng này';
                redirect('/index.php');
            }
        }

        // Get order timeline/history
        $orderHistory = $this->orderModel->getOrderHistory($orderId);

        $data = [
            'title' => 'Theo Dõi Đơn Hàng #' . $orderId . ' - ' . SITE_NAME,
            'order' => $order,
            'orderHistory' => $orderHistory
        ];

        $this->loadView('customer/order/track', $data);
    }

    // AJAX endpoint for order status
    public function getStatus() {
        $orderId = intval($_GET['id'] ?? 0);

        if ($orderId <= 0) {
            $this->jsonResponse(['error' => 'Invalid order ID'], 400);
        }

        $order = $this->orderModel->findById($orderId);

        if (!$order) {
            $this->jsonResponse(['error' => 'Order not found'], 404);
        }

        $this->jsonResponse([
            'status' => $order['status'],
            'status_text' => $this->getStatusText($order['status']),
            'updated_at' => $order['updated_at']
        ]);
    }

    private function getStatusText($status) {
        $statusTexts = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'preparing' => 'Đang chuẩn bị',
            'ready' => 'Sẵn sàng giao hàng',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy'
        ];

        return $statusTexts[$status] ?? 'Không xác định';
    }
}
?>
