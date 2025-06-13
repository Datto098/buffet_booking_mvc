<?php
/**
 * VNPay Configuration
 * Contains VNPay integration settings
 */

// VNPay Configuration
define('VNP_TMNCODE', 'NJJ0R8FS'); // Terminal ID provided by VNPay
define('VNP_HASHSECRET', 'BYKJBHPPZKQMKBIBGGXIYKWYFAYSJXCW'); // Secret key provided by VNPay
define('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'); // Sandbox URL
define('VNP_RETURN_URL', SITE_URL . '/payment/vnpay_return'); // Return URL after payment
define('VNP_IPN_URL', SITE_URL . '/payment/vnpay_ipn'); // IPN URL for webhook
define('TEST_VN_PAY', SITE_URL . 'https://sandbox.vnpayment.vn/apis/vnpay-demo/'); // URL to get develop account
// VNPay Test Environment Settings (replace with production when ready)
// For testing: Use sandbox.vnpayment.vn
// For production: Use pay.vnpay.vn

// Supported banks for VNPay
define('VNP_BANK_CODES', [
    'NCB' => 'Ngân hàng NCB',
    'AGRIBANK' => 'Ngân hàng Agribank',
    'SCB' => 'Ngân hàng SCB',
    'SACOMBANK' => 'Ngân hàng SacomBank',
    'EXIMBANK' => 'Ngân hàng EximBank',
    'MSBANK' => 'Ngân hàng MS',
    'NAMABANK' => 'Ngân hàng NamA',
    'VNMART' => 'Ví VnMart',
    'VIETINBANK' => 'Ngân hàng Vietinbank',
    'VIETCOMBANK' => 'Ngân hàng VCB',
    'HDBANK' => 'Ngân hàng HDBank',
    'DONGABANK' => 'Ngân hàng Dong A',
    'TPBANK' => 'Ngân hàng TPBank',
    'OJB' => 'Ngân hàng OceanBank',
    'BIDV' => 'Ngân hàng BIDV',
    'TECHCOMBANK' => 'Ngân hàng Techcombank',
    'VPBANK' => 'Ngân hàng VPBank',
    'MBBANK' => 'Ngân hàng MBBank',
    'ACB' => 'Ngân hàng ACB',
    'OCB' => 'Ngân hàng OCB',
    'IVB' => 'Ngân hàng IVB',
    'VISA' => 'Thanh toán qua VISA/MASTER'
]);

// VNPay order types
define('VNP_ORDER_TYPES', [
    'billpayment' => 'Bill Payment',
    'other' => 'Other'
]);

// Response codes from VNPay
define('VNP_RESPONSE_CODES', [
    '00' => 'Giao dịch thành công',
    '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
    '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
    '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
    '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
    '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
    '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
    '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
    '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
    '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
    '75' => 'Ngân hàng thanh toán đang bảo trì.',
    '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
    '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)'
]);

/**
 * Get VNPay response message by code
 */
function getVNPayResponseMessage($code) {
    $codes = VNP_RESPONSE_CODES;
    return $codes[$code] ?? 'Lỗi không xác định';
}

/**
 * Get bank name by code
 */
function getBankName($code) {
    $banks = VNP_BANK_CODES;
    return $banks[$code] ?? 'Không xác định';
}

/**
 * Sort parameters for VNPay hash calculation
 */
function sortVNPayParams($params) {
    ksort($params);
    return $params;
}

/**
 * Create VNPay secure hash
 */
function createVNPaySecureHash($params, $hashSecret) {
    $params = sortVNPayParams($params);
    $hashData = http_build_query($params, '', '&');
    return hash_hmac('sha512', $hashData, $hashSecret);
}

/**
 * Validate VNPay secure hash
 */
function validateVNPaySecureHash($params, $receivedHash, $hashSecret) {
    // Remove vnp_SecureHash from params before validation
    unset($params['vnp_SecureHash']);
    unset($params['vnp_SecureHashType']);

    $calculatedHash = createVNPaySecureHash($params, $hashSecret);
    return hash_equals(strtolower($calculatedHash), strtolower($receivedHash));
}
?>
