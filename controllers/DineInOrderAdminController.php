<?php
require_once 'models/DineInOrder.php';
require_once 'models/Table.php';

class DineInOrderAdminController {
    private $orderModel;
    private $tableModel;

    public function __construct() {
        $this->orderModel = new DineInOrder();
        $this->tableModel = new Table();
    }

    // Danh sách đơn gọi món
    public function index() {
        $status = $_GET['status'] ?? '';
        $table = $_GET['table'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $orders = $this->orderModel->getDineInOrdersAdmin($status, $table, $search, $limit, $offset);
        $totalOrders = $this->orderModel->countDineInOrdersAdmin($status, $table, $search);
        $tables = $this->tableModel->getAllTables();

        require 'views/admin/dine_in_orders/index.php';
    }

    // Xem chi tiết đơn
    public function view() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng!';
            redirect('/admin/dine-in-orders');
        }
        $order = $this->orderModel->getOrderById($id);
        require 'views/admin/dine_in_orders/view.php';
    }

    // Cập nhật trạng thái đơn
    public function updateStatus() {
        // Kiểm tra method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method không hợp lệ!']);
            return;
        }

        // Kiểm tra CSRF token
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            jsonResponse(['success' => false, 'message' => 'Token không hợp lệ!']);
            return;
        }

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            jsonResponse(['success' => false, 'message' => 'Thiếu thông tin bắt buộc!']);
            return;
        }

        // Kiểm tra trạng thái hợp lệ
        $validStatuses = ['pending', 'preparing', 'served', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            jsonResponse(['success' => false, 'message' => 'Trạng thái không hợp lệ!']);
            return;
        }

        // Kiểm tra đơn hàng tồn tại
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn hàng!']);
            return;
        }

        // Cập nhật trạng thái
        $success = $this->orderModel->updateOrderStatus($id, $status);

        if ($success) {
            jsonResponse([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'data' => [
                    'order_id' => $id,
                    'new_status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Cập nhật thất bại! Vui lòng thử lại.']);
        }
    }
}
