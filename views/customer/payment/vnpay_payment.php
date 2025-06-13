<?php
/**
 * VNPay Payment Processing View
 */

// Get order data from session
$vnpayOrder = $_SESSION['vnpay_order'] ?? null;
if (!$vnpayOrder) {
    $_SESSION['error'] = 'Không tìm thấy thông tin đơn hàng';
    redirect('/index.php?page=order&action=checkout');
}

$orderId = $vnpayOrder['order_id'];
$amount = $vnpayOrder['amount'];
$bankCode = $vnpayOrder['bank_code'];
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Thanh Toán VNPay
                    </h4>
                </div>

                <div class="card-body p-5">
                    <!-- Order Information -->
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng</h6>
                        <div class="row">
                            <div class="col-6">
                                <strong>Mã đơn hàng:</strong><br>
                                <span class="text-primary">#<?= str_pad($orderId, 6, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <div class="col-6 text-end">
                                <strong>Tổng tiền:</strong><br>
                                <span class="text-danger h5"><?= number_format($amount, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- VNPay Information -->
                    <div class="text-center mb-4">
                        <img src="<?= SITE_URL ?>/assets/images/vnpay-logo.png" alt="VNPay" class="img-fluid mb-3" style="max-height: 60px;">
                        <p class="text-muted">
                            Bạn sẽ được chuyển đến cổng thanh toán VNPay để hoàn tất giao dịch
                        </p>
                    </div>

                    <!-- Payment Form -->
                    <form method="POST" action="<?= SITE_URL ?>/payment/confirm_vnpay" id="vnpayForm">
                        <?= csrf_token_field() ?>
                        <input type="hidden" name="order_id" value="<?= $orderId ?>">
                        <input type="hidden" name="amount" value="<?= $amount ?>">
                        <input type="hidden" name="bank_code" value="<?= htmlspecialchars($bankCode) ?>">

                        <!-- Bank Selection (if not already selected) -->
                        <?php if (empty($bankCode)): ?>
                        <div class="mb-4">
                            <label for="bank_code" class="form-label">
                                <i class="fas fa-university me-2"></i>Chọn ngân hàng thanh toán
                            </label>
                            <select class="form-select form-select-lg" id="bank_code" name="bank_code">
                                <option value="">Chọn cổng thanh toán</option>
                                <option value="NCB">Ngân hàng NCB</option>
                                <option value="AGRIBANK">Ngân hàng Agribank</option>
                                <option value="SCB">Ngân hàng SCB</option>
                                <option value="SACOMBANK">Ngân hàng SacomBank</option>
                                <option value="EXIMBANK">Ngân hàng EximBank</option>
                                <option value="VIETINBANK">Ngân hàng Vietinbank</option>
                                <option value="VIETCOMBANK">Ngân hàng VCB</option>
                                <option value="HDBANK">Ngân hàng HDBank</option>
                                <option value="TPBANK">Ngân hàng TPBank</option>
                                <option value="BIDV">Ngân hàng BIDV</option>
                                <option value="TECHCOMBANK">Ngân hàng Techcombank</option>
                                <option value="VPBANK">Ngân hàng VPBank</option>
                                <option value="MBBANK">Ngân hàng MBBank</option>
                                <option value="ACB">Ngân hàng ACB</option>
                                <option value="OCB">Ngân hàng OCB</option>
                                <option value="VISA">Thanh toán qua VISA/MASTER</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Payment Security Notice -->
                        <div class="bg-light p-3 rounded mb-4">
                            <h6 class="text-success mb-2">
                                <i class="fas fa-shield-alt me-2"></i>Bảo mật thanh toán
                            </h6>
                            <ul class="mb-0 small">
                                <li>✓ Giao dịch được bảo mật bằng công nghệ SSL 256-bit</li>
                                <li>✓ Thông tin thẻ không được lưu trữ trên hệ thống</li>
                                <li>✓ Tuân thủ tiêu chuẩn bảo mật PCI DSS</li>
                                <li>✓ Hỗ trợ 24/7 từ VNPay</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-success btn-lg" id="payButton">
                                <i class="fas fa-credit-card me-2"></i>
                                Thanh Toán Ngay
                                <span class="fw-bold"><?= number_format($amount, 0, ',', '.') ?>đ</span>
                            </button>

                            <a href="<?= SITE_URL ?>/order/detail/<?= $orderId ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại đơn hàng
                            </a>
                        </div>
                    </form>

                    <!-- Payment Instructions -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="mb-3">
                            <i class="fas fa-question-circle me-2"></i>Hướng dẫn thanh toán
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="small">
                                    <strong>Thẻ ATM nội địa:</strong>
                                    <ul class="mt-1">
                                        <li>Chọn ngân hàng của bạn</li>
                                        <li>Nhập thông tin thẻ ATM</li>
                                        <li>Xác thực OTP</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small">
                                    <strong>Thẻ Visa/Mastercard:</strong>
                                    <ul class="mt-1">
                                        <li>Chọn "VISA/MASTER"</li>
                                        <li>Nhập thông tin thẻ</li>
                                        <li>Xác thực 3D Secure</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('payButton');
    const vnpayForm = document.getElementById('vnpayForm');

    vnpayForm.addEventListener('submit', function() {
        // Show loading state
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang chuyển hướng...';

        // Add a timeout to re-enable button if something goes wrong
        setTimeout(function() {
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-credit-card me-2"></i>Thanh Toán Ngay <span class="fw-bold"><?= number_format($amount, 0, ",", ".") ?>đ</span>';
        }, 10000);
    });

    // Auto-submit form if all required data is available
    <?php if (!empty($bankCode)): ?>
    // If bank code is already selected, show countdown and auto-submit
    let countdown = 5;
    const countdownElement = document.createElement('div');
    countdownElement.className = 'alert alert-warning mt-3 text-center';
    countdownElement.innerHTML = `<i class="fas fa-clock me-2"></i>Tự động chuyển hướng sau <strong id="countdown">${countdown}</strong> giây...`;

    vnpayForm.appendChild(countdownElement);

    const countdownTimer = setInterval(function() {
        countdown--;
        document.getElementById('countdown').textContent = countdown;

        if (countdown <= 0) {
            clearInterval(countdownTimer);
            vnpayForm.submit();
        }
    }, 1000);

    // Allow user to stop auto-submit
    payButton.addEventListener('click', function(e) {
        e.preventDefault();
        clearInterval(countdownTimer);
        countdownElement.remove();
        vnpayForm.submit();
    });
    <?php endif; ?>
});
</script>

<style>
.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 1.1rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.border-top {
    border-top: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }

    .card-body {
        padding: 2rem !important;
    }
}
</style>
