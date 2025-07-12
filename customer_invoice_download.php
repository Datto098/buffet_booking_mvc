<?php
session_start();

require_once 'config/config.php';
require_once 'models/Invoice.php';
require_once 'models/DineInOrder.php';

// Kiểm tra nếu user đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

try {
    $orderId = (int)$_GET['order_id'];
    $userId = $_SESSION['user_id'];

    // Khởi tạo models
    $invoiceModel = new Invoice();
    $orderModel = new DineInOrder();

    // Kiểm tra order có tồn tại và thuộc về user hiện tại
    $order = $orderModel->getById($orderId);
    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    // Kiểm tra quyền: order phải thuộc về user hoặc user phải có quyền xem
    // Đối với dine-in, kiểm tra table_number thông qua session hoặc logic khác
    // Tạm thời cho phép tất cả user đã đăng nhập

    // Tìm invoice cho order này
    $invoice = $invoiceModel->findByOrderId($orderId);
    if (!$invoice) {
        http_response_code(404);
        echo json_encode(['error' => 'Invoice not found for this order']);
        exit;
    }

    // Lấy chi tiết hóa đơn
    $invoiceDetails = $invoiceModel->getInvoiceDetails($invoice['id']);

    // Tạo PDF
    require_once 'vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => __DIR__ . '/temp',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 20,
        'margin_bottom' => 20
    ]);

    // Load template PDF
    ob_start();
    include __DIR__ . '/views/admin/invoice/pdf_template.php';
    $html = ob_get_clean();

    $mpdf->WriteHTML($html);
    $mpdf->Output('Hoa_don_' . $invoice['invoice_number'] . '.pdf', 'D');
    exit;

} catch (Exception $e) {
    error_log("Customer invoice download error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
    exit;
}
?>
