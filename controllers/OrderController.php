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
        // Lấy dữ liệu giỏ hàng từ session
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $subtotal = 0;
        $serviceFee = 0;
        $deliveryFee = 0;
        $total = 0;

        // Lấy thông tin món ăn và tính toán
        foreach ($cart as $foodId => $quantity) {
            $food = $this->foodModel->getFoodDetails($foodId);
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
        $serviceFee = $subtotal * 0.05;
        $deliveryFee = 0;
        $total = $subtotal + $serviceFee + $deliveryFee;

        // Lấy thông tin user từ DB
        $userInfo = null;
        if (isset($_SESSION['user_id'])) {
            $userInfo = $this->userModel->findById($_SESSION['user_id']);
        }

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'deliveryFee' => $deliveryFee,
            'total' => $total,
            'userInfo' => $userInfo,
            'info' => $restaurantInfo // Thêm dòng này
        ];
        $this->loadView('customer/order/checkout', $data);
    }

    public function create() {

        $this->requireLogin();
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
        }        if (!in_array($paymentMethod, ['cod', 'bank_transfer', 'credit_card', 'vnpay'])) {
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
            $food = $this->foodModel->getFoodDetails($foodId);
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
        }        // Create order
        $orderId = $this->orderModel->createOrder($orderData, $orderItems);

        if ($orderId) {
            unset($_SESSION['cart']);
            // If VNPay payment method, redirect to payment
            if ($paymentMethod === 'vnpay') {
                // Store order data in session for payment processing
                $_SESSION['vnpay_order'] = [
                    'order_id' => $orderId,
                    'amount' => $totalAmount,
                    'bank_code' => $_POST['bank_code'] ?? ''
                ];

                // Don't clear cart yet - will clear after successful payment
                redirect('/index.php?page=payment&action=confirm_vnpay');
            } else {
                // For other payment methods, proceed normally
                unset($_SESSION['cart']);
                $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng: ' . $orderId;
                redirect('/index.php?page=order&action=detail&id=' . $orderId);
            }
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.';
            $_SESSION['form_data'] = $_POST;
            redirect('/index.php?page=order&action=checkout');
        }
    }

    public function myOrders() {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        // Lấy trang hiện tại
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = 10;
        $offset = ($current_page - 1) * $per_page;

        // Lấy tổng số đơn
        $total_orders = $this->orderModel->countOrdersByUser($userId);
        $total_pages = ceil($total_orders / $per_page);

        // Lấy đơn hàng cho trang hiện tại
        $orders = $this->orderModel->getOrdersByUser($userId, $per_page, $offset);

        foreach ($orders as &$order) {
            $order['order_items'] = $this->orderModel->getOrderItems($order['id']);
        }

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Đơn Hàng Của Tôi - ' . SITE_NAME,
            'orders' => $orders,
            'total_pages' => $total_pages,
            'current_page_num' => $current_page, // Fixed variable name to match view expectation
            'filter_params' => '',
            'info' => $restaurantInfo // Thêm dòng này
        ];

        $this->loadView('customer/order/history', $data);
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


        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Chi Tiết Đơn Hàng #' . $orderId . ' - ' . SITE_NAME,
            'order' => $order,
            'info' => $restaurantInfo // Thêm dòng này
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

    // public function history() {
    //     $orderId = intval($_GET['id'] ?? 0);

    //     // if ($orderId <= 0) {
    //     //     redirect('/index.php');
    //     // }

    //     $order = $this->orderModel->findById($orderId);

    //     // if (!$order) {
    //     //     $_SESSION['error'] = 'Không tìm thấy đơn hàng';
    //     //     redirect('/index.php');
    //     // }

    //     // Check permission
    //     // if (isLoggedIn()) {
    //     //     if ($order['user_id'] != $_SESSION['user_id'] && !isAdmin() && !isManager()) {
    //     //         $_SESSION['error'] = 'Bạn không có quyền xem đơn hàng này';
    //     //         redirect('/index.php');
    //     //     }
    //     // }

    //     // Get order timeline/history
    //     $orderHistory = $this->orderModel->getOrderHistory($orderId);

    //     $data = [
    //         'title' => 'Theo Dõi Đơn Hàng #' . $orderId . ' - ' . SITE_NAME,
    //         'order' => $order,
    //         'orderHistory' => $orderHistory
    //     ];


    //     //  $this->loadView('/index.php?page=order&action=history', $data);
    //     $this->loadView('/customer/order/history', $data);
    // }

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

    public function history() {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        // Pagination setup - use 'p' parameter to avoid conflict with route 'page' parameter
        $page = max(1, (int)($_GET['p'] ?? 1)); // Ensure page is at least 1
        $limit = 10; // Orders per page
        $offset = ($page - 1) * $limit;

        // Get total count for pagination
        $totalOrders = $this->orderModel->countOrdersByUser($userId);
        $totalPages = ceil($totalOrders / $limit);

        // Get orders with pagination
        $orders = $this->orderModel->getOrdersByUser($userId, $limit, $offset);

        // Lấy thêm các món ăn cho từng đơn
        foreach ($orders as &$order) {
            $order['order_items'] = $this->orderModel->getOrderItems($order['id']);
        }

        // Build filter parameters for pagination links
        $filterParams = '';
        foreach ($_GET as $key => $value) {
            if ($key !== 'p') { // Exclude pagination parameter 'p'
                $filterParams .= '&' . urlencode($key) . '=' . urlencode($value);
            }
        }

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Lịch Sử Đơn Hàng - ' . SITE_NAME,
            'orders' => $orders,
            'current_page_num' => $page, // Use different variable name to avoid conflict
            'total_pages' => $totalPages,
            'total_orders' => $totalOrders,
            'filter_params' => $filterParams,
            'info' => $restaurantInfo
        ];

        $this->loadView('customer/order/history', $data);
    }
}
?>
