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
        $totalAmount = 0;        if (!empty($cart)) {
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

        $data = [
            'title' => 'Giỏ Hàng - ' . SITE_NAME,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'itemCount' => count($cartItems)
        ];

        $this->loadView('customer/cart/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $foodId = intval($_POST['food_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);

        if ($foodId <= 0 || $quantity <= 0) {
            $this->jsonResponse(['error' => 'Invalid parameters'], 400);
        }        // Check if food exists and is available
        $food = $this->foodModel->getFoodDetails($foodId);
        if (!$food || !$food['is_available']) {
            $this->jsonResponse(['error' => 'Food not available'], 404);
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
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $foodId = intval($_POST['food_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);

        if ($foodId <= 0) {
            $this->jsonResponse(['error' => 'Invalid food ID'], 400);
        }

        if ($quantity <= 0) {
            // Remove item from cart
            $this->removeFromCart($foodId);
        } else {            // Check stock quantity
            $food = $this->foodModel->getFoodDetails($foodId);
            if ($food && isset($food['stock_quantity']) && $food['stock_quantity'] > 0) {
                if ($quantity > $food['stock_quantity']) {
                    $this->jsonResponse(['error' => 'Insufficient stock'], 400);
                }
            }

            // Update quantity
            $this->updateCartQuantity($foodId, $quantity);
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
    }

    private function updateCartQuantity($foodId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $_SESSION['cart'][$foodId] = $quantity;
    }

    private function removeFromCart($foodId) {
        if (isset($_SESSION['cart'][$foodId])) {
            unset($_SESSION['cart'][$foodId]);
        }
    }

    private function clearCart() {
        unset($_SESSION['cart']);
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
