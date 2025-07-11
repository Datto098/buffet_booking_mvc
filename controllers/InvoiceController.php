<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/BuffetPricing.php';
require_once __DIR__ . '/../models/AdditionalChargeTypes.php';
require_once __DIR__ . '/../models/DineInOrder.php';

class InvoiceController extends BaseController
{
    private $invoiceModel;
    private $buffetPricingModel;
    private $additionalChargeTypesModel;
    private $dineInOrderModel;

    public function __construct()
    {
        $this->invoiceModel = new Invoice();
        $this->buffetPricingModel = new BuffetPricing();
        $this->additionalChargeTypesModel = new AdditionalChargeTypes();
        $this->dineInOrderModel = new DineInOrder();

        // Chỉ admin mới có thể tạo hóa đơn
        $this->requireAdmin();
    }

    /**
     * Tạo hóa đơn cho dine-in order
     */    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $orderId = $this->sanitize($_POST['order_id'] ?? '');
                $adultCount = (int)$this->sanitize($_POST['adult_count'] ?? 0);
                $childCount = (int)$this->sanitize($_POST['child_count'] ?? 0);
                $additionalCharges = $_POST['additional_charges'] ?? [];
                $paymentMethod = $this->sanitize($_POST['payment_method'] ?? 'cash');
                $notes = $this->sanitize($_POST['notes'] ?? '');

                // Debug logging
                error_log("Invoice validation - OrderID: '$orderId', AdultCount: $adultCount, ChildCount: $childCount");

                // Validate input - allow 0 people but not negative
                if (empty($orderId)) {
                    throw new Exception('Order ID không được để trống');
                }

                if ($adultCount < 0 || $childCount < 0) {
                    throw new Exception('Số lượng người không được âm');
                }

                if ($adultCount == 0 && $childCount == 0) {
                    throw new Exception('Phải có ít nhất 1 người (người lớn hoặc trẻ em)');
                }

                // Kiểm tra order có tồn tại không
                $order = $this->dineInOrderModel->findById($orderId);
                if (!$order) {
                    throw new Exception('Không tìm thấy order');
                }

                // Kiểm tra đã có hóa đơn chưa
                $existingInvoice = $this->invoiceModel->findByOrderId($orderId);
                if ($existingInvoice) {
                    throw new Exception('Order này đã có hóa đơn');
                }

                // Tính toán giá buffet
                $buffetCalculation = $this->calculateBuffetPrice($adultCount, $childCount);

                // Tính toán phí phát sinh
                $additionalCalculation = $this->calculateAdditionalCharges($additionalCharges);

                // Tạo hóa đơn
                $invoiceData = [
                    'order_id' => $orderId,
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'adult_count' => $adultCount,
                    'child_count' => $childCount,
                    'adult_price' => $buffetCalculation['adult_price'],
                    'child_price' => $buffetCalculation['child_price'],
                    'buffet_total' => $buffetCalculation['total'],
                    'food_total' => $order['total_amount'] ?? 0,
                    'additional_charges' => json_encode($additionalCalculation['charges']),
                    'additional_total' => $additionalCalculation['total'],
                    'subtotal' => $buffetCalculation['total'] + ($order['total_amount'] ?? 0) + $additionalCalculation['total'],
                    'tax_rate' => 0.00, // Có thể config sau
                    'tax_amount' => 0.00,
                    'total_amount' => $buffetCalculation['total'] + ($order['total_amount'] ?? 0) + $additionalCalculation['total'],
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'pending',
                    'notes' => $notes,
                    'created_by' => $_SESSION['user']['id']
                ];

                $invoiceId = $this->invoiceModel->create($invoiceData);

                // Trả về JSON response
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo hóa đơn thành công',
                    'invoice_id' => $invoiceId,
                    'invoice_number' => $invoiceData['invoice_number']
                ]);
                return;
            }

            // GET request - hiển thị form tạo hóa đơn
            $orderId = $this->sanitize($_GET['order_id'] ?? '');
            if (empty($orderId)) {
                throw new Exception('Không tìm thấy order');
            }

            $order = $this->dineInOrderModel->findById($orderId);
            if (!$order) {
                throw new Exception('Không tìm thấy order');
            }

            // Lấy danh sách loại phí phát sinh
            $additionalChargeTypes = $this->additionalChargeTypesModel->getAllActive();

            // Lấy giá buffet
            $buffetPricing = $this->buffetPricingModel->getAllActive();

            $this->loadAdminView('invoice/create', [
                'order' => $order,
                'additionalChargeTypes' => $additionalChargeTypes,
                'buffetPricing' => $buffetPricing
            ]);

        } catch (Exception $e) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/admin/dine-in-orders');
            }
        }
    }

    /**
     * Xem chi tiết hóa đơn
     */
    public function viewInvoice($id)
    {
        try {
            error_log("viewInvoice called with ID: " . $id);

            $invoice = $this->invoiceModel->getById($id);
            error_log("Invoice found: " . ($invoice ? 'YES' : 'NO'));

            if (!$invoice) {
                throw new Exception('Không tìm thấy hóa đơn');
            }

            // Lấy thông tin order
            $order = $this->dineInOrderModel->getById($invoice['order_id']);
            error_log("Order found: " . ($order ? 'YES' : 'NO'));

            if (!$order) {
                throw new Exception('Không tìm thấy order');
            }

            // Lấy chi tiết hóa đơn
            $invoiceDetails = $this->invoiceModel->getInvoiceDetails($id);
            error_log("InvoiceDetails found: " . ($invoiceDetails ? 'YES' : 'NO'));

            error_log("About to load view");
            $this->loadAdminView('invoice/view', [
                'invoice' => $invoice,
                'order' => $order,
                'invoiceDetails' => $invoiceDetails
            ]);

        } catch (Exception $e) {
            error_log("viewInvoice error: " . $e->getMessage());
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/admin/dine-in-orders');
        }
    }

    /**
     * Xuất PDF hóa đơn
     */
    public function exportPdf($id)
    {
        try {
            $invoice = $this->invoiceModel->findById($id);
            if (!$invoice) {
                throw new Exception('Không tìm thấy hóa đơn');
            }

            $order = $this->dineInOrderModel->findById($invoice['order_id']);

            // Sử dụng helper để tạo PDF
            require_once __DIR__ . '/../helpers/pdf_helper.php';
            generateInvoicePdf($invoice, $order);

        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/admin/dine-in-orders');
        }
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method không được hỗ trợ');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $invoiceId = $input['invoice_id'] ?? '';
            $paymentStatus = $input['payment_status'] ?? '';

            if (empty($invoiceId) || empty($paymentStatus)) {
                throw new Exception('Dữ liệu không hợp lệ');
            }

            // Kiểm tra hóa đơn tồn tại
            $invoice = $this->invoiceModel->getById($invoiceId);
            if (!$invoice) {
                throw new Exception('Không tìm thấy hóa đơn');
            }

            // Cập nhật trạng thái
            $result = $this->invoiceModel->updatePaymentStatus($invoiceId, $paymentStatus);

            if (!$result) {
                throw new Exception('Không thể cập nhật trạng thái thanh toán');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật trạng thái thanh toán thành công'
            ]);
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * In hóa đơn (PDF)
     */
    public function print($invoiceId)
    {
        try {
            $invoice = $this->invoiceModel->getById($invoiceId);
            if (!$invoice) {
                throw new Exception('Không tìm thấy hóa đơn');
            }

            // Lấy thông tin order
            $order = $this->dineInOrderModel->getById($invoice['order_id']);
            if (!$order) {
                throw new Exception('Không tìm thấy order');
            }

            // Lấy chi tiết hóa đơn
            $invoiceDetails = $this->invoiceModel->getInvoiceDetails($invoiceId);

            // Tạo PDF
            require_once __DIR__ . '/../vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => __DIR__ . '/../temp',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20
            ]);

            // Load template PDF
            ob_start();
            include __DIR__ . '/../views/admin/invoice/pdf_template.php';
            $html = ob_get_clean();

            $mpdf->WriteHTML($html);
            $mpdf->Output('Hoa_don_' . $invoice['invoice_number'] . '.pdf', 'D');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . SITE_URL . '/admin/dine-in-orders');
            exit;
        }
    }

    /**
     * Tính toán giá buffet dựa trên số lượng người lớn và trẻ em
     */
    private function calculateBuffetPrice($adultCount, $childCount)
    {
        $buffetPrices = $this->buffetPricingModel->getAllActive();

        $adultPrice = 0;
        $childPrice = 0;

        foreach ($buffetPrices as $price) {
            if ($price['type'] === 'adult') {
                $adultPrice = $price['price'];
            } elseif ($price['type'] === 'child' && $price['age_from'] >= 11) {
                $childPrice = $price['price']; // Lấy giá trẻ em cao nhất (11-17 tuổi)
            }
        }

        return [
            'adult_price' => $adultPrice,
            'child_price' => $childPrice,
            'total' => ($adultPrice * $adultCount) + ($childPrice * $childCount)
        ];
    }

    /**
     * Tính toán phí phát sinh
     */
    private function calculateAdditionalCharges($charges)
    {
        $total = 0;
        $chargeDetails = [];

        foreach ($charges as $chargeId => $quantity) {
            if ($quantity > 0) {
                $chargeType = $this->additionalChargeTypesModel->findById($chargeId);
                if ($chargeType) {
                    $amount = $chargeType['price'] * $quantity;
                    $total += $amount;

                    $chargeDetails[] = [
                        'id' => $chargeId,
                        'name' => $chargeType['name'],
                        'price' => $chargeType['price'],
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'unit' => $chargeType['unit']
                    ];
                }
            }
        }

        return [
            'charges' => $chargeDetails,
            'total' => $total
        ];
    }

    /**
     * Tạo số hóa đơn tự động
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = date('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $date . $random;
    }
}
