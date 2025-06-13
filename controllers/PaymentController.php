<?php
/**
 * Payment Controller - VNPay Integration
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../config/vnpay.php';

class PaymentController extends BaseController {
    private $orderModel;
    private $paymentModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->paymentModel = new Payment();
    }/**
     * Initiate VNPay payment
     */
    public function confirm_vnpay() {
        // Check if we have order data in session (from order creation)
        if (!isset($_SESSION['vnpay_order'])) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đơn hàng';
            redirect('/index.php?page=order&action=checkout');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Show payment form
            $data = [
                'title' => 'Thanh Toán VNPay - ' . SITE_NAME
            ];
            $this->loadView('customer/payment/vnpay_payment', $data);
            return;
        }

        // Handle POST request for payment processing
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();

            // Use data from session and form
            $orderId = $_SESSION['vnpay_order']['order_id'];
            $amount = $_SESSION['vnpay_order']['amount'];
            $bankCode = $_POST['bank_code'] ?? $_SESSION['vnpay_order']['bank_code'];

            if ($orderId <= 0 || $amount <= 0) {
                $_SESSION['error'] = 'Dữ liệu đơn hàng không hợp lệ';
                redirect('/index.php?page=order&action=checkout');
            }

            // Get order details
            $order = $this->orderModel->findById($orderId);
            if (!$order) {
                $_SESSION['error'] = 'Không tìm thấy đơn hàng';
                redirect('/index.php?page=order&action=checkout');
            }

            // Check if user has permission to pay this order
            if (isLoggedIn() && $order['user_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = 'Bạn không có quyền thanh toán đơn hàng này';
                redirect('/index.php?page=order&action=myOrders');
            }

        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_TxnRef = time() . "_" . $orderId; // Mã đơn hàng unique
        $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order['order_number'] . ' tại ' . SITE_NAME;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100; // VNPay yêu cầu số tiền * 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = $bankCode ?: 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // Thời gian hết hạn thanh toán (15 phút)
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        // Return URL
        $vnp_Returnurl = SITE_URL . "/payment/vnpay_return";
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => VNP_TMNCODE,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // Sắp xếp dữ liệu
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }        $vnp_Url = VNP_URL . "?" . $query;
        if (defined('VNP_HASHSECRET')) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, VNP_HASHSECRET);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Lưu thông tin payment vào database
        try {
            $paymentData = [
                'order_id' => $orderId,
                'payment_method' => 'vnpay',
                'vnp_txn_ref' => $vnp_TxnRef,
                'vnp_amount' => $vnp_Amount,
                'vnp_order_info' => $vnp_OrderInfo,
                'payment_status' => 'pending',
                'payment_data' => json_encode($inputData)
            ];

            $paymentId = $this->paymentModel->create($paymentData);

            if ($paymentId) {
                // Update order payment method and status
                $this->orderModel->update($orderId, [
                    'payment_method' => 'vnpay',
                    'payment_id' => $paymentId,
                    'payment_status' => 'pending'
                ]);

                // Store payment info in session for verification
                $_SESSION['vnpay_payment'] = [
                    'payment_id' => $paymentId,
                    'vnp_txn_ref' => $vnp_TxnRef,
                    'order_id' => $orderId
                ];

                // Redirect to VNPay
                header('Location: ' . $vnp_Url);
                exit();
            } else {
                $_SESSION['error'] = 'Không thể tạo giao dịch thanh toán';
                redirect('/index.php?page=order&action=detail&id=' . $orderId);
            }
        } catch (Exception $e) {
            error_log("VNPay payment creation error: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo giao dịch thanh toán';
            redirect('/index.php?page=order&action=detail&id=' . $orderId);
        }
    }

    /**
     * Handle VNPay return URL
     */    public function vnpay_return() {
        $vnp_HashSecret = VNP_HASHSECRET;
        $inputData = array();
        $returnData = array();

        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        try {
            $vnp_TxnRef = $_GET['vnp_TxnRef'];
            $vnp_Amount = $_GET['vnp_Amount'];
            $vnp_OrderInfo = $_GET['vnp_OrderInfo'];
            $vnp_ResponseCode = $_GET['vnp_ResponseCode'];
            $vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? null;
            $vnp_BankCode = $_GET['vnp_BankCode'] ?? null;
            $vnp_PayDate = $_GET['vnp_PayDate'] ?? null;

            // Tìm payment record
            $payment = $this->paymentModel->findByTxnRef($vnp_TxnRef);
            if (!$payment) {
                throw new Exception('Không tìm thấy giao dịch');
            }

            $orderId = $payment['order_id'];
            $order = $this->orderModel->findById($orderId);

            if ($secureHash == $vnp_SecureHash) {
                if ($vnp_ResponseCode == "00") {
                    // Thanh toán thành công
                    $this->paymentModel->update($payment['id'], [
                        'vnp_response_code' => $vnp_ResponseCode,
                        'vnp_transaction_no' => $vnp_TransactionNo,
                        'vnp_bank_code' => $vnp_BankCode,
                        'vnp_pay_date' => $vnp_PayDate,
                        'vnp_secure_hash' => $vnp_SecureHash,
                        'payment_status' => 'completed',
                        'completed_at' => date('Y-m-d H:i:s'),
                        'payment_data' => json_encode($_GET)
                    ]);

                    // Update order status
                    $this->orderModel->update($orderId, [
                        'payment_status' => 'paid',
                        'status' => 'confirmed' // hoặc status khác tùy logic business
                    ]);

                    $_SESSION['success'] = 'Thanh toán thành công! Đơn hàng của bạn đã được xác nhận.';
                      // Clear cart and session data if exists
                    unset($_SESSION['cart']);
                    unset($_SESSION['vnpay_payment']);
                    unset($_SESSION['vnpay_order']);

                } else {
                    // Thanh toán thất bại
                    $this->paymentModel->update($payment['id'], [
                        'vnp_response_code' => $vnp_ResponseCode,
                        'vnp_transaction_no' => $vnp_TransactionNo,
                        'vnp_bank_code' => $vnp_BankCode,
                        'vnp_pay_date' => $vnp_PayDate,
                        'vnp_secure_hash' => $vnp_SecureHash,
                        'payment_status' => 'failed',
                        'payment_data' => json_encode($_GET)
                    ]);

                    $this->orderModel->update($orderId, [
                        'payment_status' => 'failed'
                    ]);

                    $_SESSION['error'] = 'Thanh toán thất bại! Mã lỗi: ' . $vnp_ResponseCode;
                }
            } else {
                // Sai chữ ký
                $this->paymentModel->update($payment['id'], [
                    'payment_status' => 'failed',
                    'payment_data' => json_encode(array_merge($_GET, ['error' => 'Invalid signature']))
                ]);

                $_SESSION['error'] = 'Chữ ký không hợp lệ!';
            }

            // Redirect to order detail page
            redirect('/index.php?page=order&action=detail&id=' . $orderId);

        } catch (Exception $e) {
            error_log("VNPay return processing error: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi xử lý kết quả thanh toán';
            redirect('/index.php');
        }
    }

    /**
     * IPN (Instant Payment Notification) - Webhook for VNPay
     */    public function vnpay_ipn() {
        $vnp_HashSecret = VNP_HASHSECRET;
        $inputData = array();
        $returnData = array();

        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_TxnRef = $inputData['vnp_TxnRef'];
        $vnp_Amount = $inputData['vnp_Amount'];
        $vnp_OrderInfo = $inputData['vnp_OrderInfo'];
        $vnp_ResponseCode = $inputData['vnp_ResponseCode'];
        $vnp_TransactionNo = $inputData['vnp_TransactionNo'];
        $vnp_BankCode = $inputData['vnp_BankCode'];
        $vnp_PayDate = $inputData['vnp_PayDate'];

        $Status = 0; // Giả sử lỗi trong quá trình xử lý

        try {
            // Kiểm tra checksum
            if ($secureHash == $vnp_SecureHash) {
                $payment = $this->paymentModel->findByTxnRef($vnp_TxnRef);
                if ($payment) {
                    if ($payment['vnp_amount'] == $vnp_Amount) {
                        if ($payment['payment_status'] == "pending") {
                            if ($vnp_ResponseCode == "00") {
                                $Status = 1; // Thành công
                                // Cập nhật trạng thái trong DB
                                $this->paymentModel->update($payment['id'], [
                                    'payment_status' => 'completed',
                                    'vnp_response_code' => $vnp_ResponseCode,
                                    'vnp_transaction_no' => $vnp_TransactionNo,
                                    'completed_at' => date('Y-m-d H:i:s')
                                ]);

                                $this->orderModel->update($payment['order_id'], [
                                    'payment_status' => 'paid'
                                ]);
                            } else {
                                $Status = 2; // Thất bại
                                $this->paymentModel->update($payment['id'], [
                                    'payment_status' => 'failed',
                                    'vnp_response_code' => $vnp_ResponseCode
                                ]);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $Status = 0;
            error_log("VNPay IPN error: " . $e->getMessage());
        }

        $returnData['RspCode'] = $Status == 1 ? '00' : '97';
        $returnData['Message'] = $Status == 1 ? 'Confirm Success' : 'Confirm Fail';

        echo json_encode($returnData);
    }

    /**
     * Query payment status from VNPay
     */
    public function query_payment() {
        // Implementation for querying payment status if needed
        $this->jsonResponse(['message' => 'Query payment functionality not implemented yet']);
    }

    /**
     * Refund payment (for admin)
     */
    public function refund_payment() {
        // Implementation for refund functionality if needed
        $this->jsonResponse(['message' => 'Refund functionality not implemented yet']);
    }

    /**
     * Show VNPay payment page
     */
    public function index() {
        // Check if we have order data for payment
        if (!isset($_SESSION['vnpay_order'])) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đơn hàng cần thanh toán';
            redirect('/index.php?page=cart');
        }

        $data = [
            'title' => 'Thanh Toán VNPay - ' . SITE_NAME
        ];

        $this->loadView('customer/payment/vnpay_payment', $data);
    }
}
?>
