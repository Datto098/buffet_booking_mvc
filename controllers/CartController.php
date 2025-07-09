<?php
/**
 * Cart Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Food.php';

class CartController extends BaseController {
    private $foodModel;

    public function __construct() {
        $this->foodModel = new Food();
    }

    public function index() {
        $cart = $this->getCart();
        $cartItems = [];
        $totalAmount = 0;
        if (!empty($cart)) {
            foreach ($cart as $foodId => $quantity) {
                $food = $this->foodModel->getFoodDetails($foodId);
                if ($food && $food['is_available']) {
                    $subtotal = $food['price'] * $quantity;
                    $cartItems[] = [
                        'food' => $food,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal
                    ];
                    $totalAmount += $subtotal;
                }
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
            'title' => 'Giỏ Hàng - ' . SITE_NAME,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'itemCount' => count($cartItems),
            'info' => $restaurantInfo // Thêm dòng này
        ];

        $this->loadView('customer/cart/index', $data);
    }    public function add() {
        error_log("CartController add() called");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("CartController add() - Not POST method");
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $foodId = intval($_POST['food_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);

        error_log("CartController add() - Food ID: $foodId, Quantity: $quantity");

        if ($foodId <= 0 || $quantity <= 0) {
            error_log("CartController add() - Invalid parameters");
            $this->jsonResponse(['error' => 'Invalid parameters'], 400);
        }        // Check if food exists and is available
        $food = $this->foodModel->getFoodDetails($foodId);
        error_log("CartController add() - Food details: " . json_encode($food));

        if (!$food || !$food['is_available']) {
            error_log("CartController add() - Food not available");
            $this->jsonResponse(['error' => 'Food not available', 'debug' => $foodId], 404);
        }

        // Check stock quantity if applicable
        if (isset($food['stock_quantity']) && $food['stock_quantity'] > 0) {
            $currentCart = $this->getCart();
            $currentQuantity = $currentCart[$foodId] ?? 0;

            if (($currentQuantity + $quantity) > $food['stock_quantity']) {
                $this->jsonResponse(['error' => 'Insufficient stock'], 400);
            }
        }

        // Add to cart
        $this->addToCart($foodId, $quantity);

        // Get updated cart info
        $cartInfo = $this->getCartInfo();

        $this->jsonResponse([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cartInfo' => $cartInfo
        ]);
    }    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $foodId = intval($_POST['food_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $quantity = intval($_POST['quantity'] ?? 0);

        error_log("CartController update - Food ID: $foodId, Action: $action, Quantity: $quantity");

        if ($foodId <= 0) {
            $this->jsonResponse(['error' => 'Invalid food ID'], 400);
        }

        // Handle action-based updates (increase/decrease)
        if (!empty($action) && in_array($action, ['increase', 'decrease'])) {
            $cart = $this->getCart();
            $currentQuantity = $cart[$foodId] ?? 0;

            error_log("CartController update - Current quantity: $currentQuantity");

            if ($action === 'increase') {
                $newQuantity = $currentQuantity + 1;
            } else { // decrease
                $newQuantity = $currentQuantity - 1;
            }

            error_log("CartController update - New quantity: $newQuantity");

            if ($newQuantity <= 0) {
                // Remove item from cart
                $this->removeFromCart($foodId);
                error_log("CartController update - Removed item from cart");
            } else {
                // Check stock quantity
                $food = $this->foodModel->getFoodDetails($foodId);
                if ($food && isset($food['stock_quantity']) && $food['stock_quantity'] > 0) {
                    if ($newQuantity > $food['stock_quantity']) {
                        $this->jsonResponse([
                            'success' => false,
                            'message' => 'Số lượng vượt quá tồn kho'
                        ]);
                        return;
                    }
                }

                // Update quantity
                $this->updateCartQuantity($foodId, $newQuantity);
                error_log("CartController update - Updated quantity to: $newQuantity");
            }
        }
        // Handle direct quantity update
        else if ($quantity > 0) {
            // Check stock quantity
            $food = $this->foodModel->getFoodDetails($foodId);
            if ($food && isset($food['stock_quantity']) && $food['stock_quantity'] > 0) {
                if ($quantity > $food['stock_quantity']) {
                    $this->jsonResponse(['error' => 'Insufficient stock'], 400);
                }
            }

            // Update quantity
            $this->updateCartQuantity($foodId, $quantity);
        }
        else {
            // Remove item from cart
            $this->removeFromCart($foodId);
        }

        // Get updated cart info
        $cartInfo = $this->getCartInfo();

        $this->jsonResponse([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'cartInfo' => $cartInfo
        ]);
    }

    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $foodId = intval($_POST['food_id'] ?? 0);

        if ($foodId <= 0) {
            $this->jsonResponse(['error' => 'Invalid food ID'], 400);
        }

        $this->removeFromCart($foodId);

        // Get updated cart info
        $cartInfo = $this->getCartInfo();

        $this->jsonResponse([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng',
            'cartInfo' => $cartInfo
        ]);
    }

    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $this->clearCart();

        $this->jsonResponse([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng'
        ]);
    }

    public function getCartInfo() {
        $cart = $this->getCart();
        $itemCount = 0;
        $totalAmount = 0;        if (!empty($cart)) {
            foreach ($cart as $foodId => $quantity) {
                $food = $this->foodModel->getFoodDetails($foodId);
                if ($food && $food['is_available']) {
                    $itemCount += $quantity;
                    $totalAmount += $food['price'] * $quantity;
                }
            }
        }

        return [
            'itemCount' => $itemCount,
            'totalAmount' => $totalAmount,
            'formattedTotal' => number_format($totalAmount, 0, ',', '.') . 'đ'
        ];
    }

    public function info() {
        $cartInfo = $this->getCartInfo();
        $this->jsonResponse($cartInfo);
    }

    // Cart session management methods
    private function getCart() {
        return $_SESSION['cart'] ?? [];
    }

    private function addToCart($foodId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$foodId])) {
            $_SESSION['cart'][$foodId] += $quantity;
        } else {
            $_SESSION['cart'][$foodId] = $quantity;
        }
        $this->updateCartCount();
    }

    private function updateCartQuantity($foodId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][$foodId] = $quantity;
        $this->updateCartCount();
    }

    private function removeFromCart($foodId) {
        if (isset($_SESSION['cart'][$foodId])) {
            unset($_SESSION['cart'][$foodId]);
        }
        $this->updateCartCount();
    }

    private function clearCart() {
        unset($_SESSION['cart']);
        $this->updateCartCount();
    }

    private function updateCartCount() {
        $_SESSION['cart_count'] = count($_SESSION['cart'] ?? []);
    }

    // Quick add from menu
    public function quickAdd() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=menu');
        }

        $foodId = intval($_POST['food_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        $redirectTo = $_POST['redirect_to'] ?? '/index.php?page=menu';        if ($foodId > 0 && $quantity > 0) {
            $food = $this->foodModel->getFoodDetails($foodId);
            if ($food && $food['is_available']) {
                $this->addToCart($foodId, $quantity);
                $_SESSION['success'] = 'Đã thêm ' . $food['name'] . ' vào giỏ hàng';
            } else {
                $_SESSION['error'] = 'Món ăn không khả dụng';
            }
        } else {
            $_SESSION['error'] = 'Thông tin không hợp lệ';
        }

        redirect($redirectTo);
    }
}
?>
