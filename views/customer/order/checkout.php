<?php
/**
 * Checkout View
 */
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/index.php?page=cart">Giỏ hàng</a></li>
                    <li class="breadcrumb-item active">Thanh toán</li>
                </ol>
            </nav>

            <h2 class="mb-4">
                <i class="fas fa-credit-card"></i> Thanh Toán Đơn Hàng
            </h2>
        </div>
    </div>    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <form method="POST" action="<?= SITE_URL ?>/order/create" id="checkoutForm">
                <?= csrf_token_field() ?>

                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user"></i> Thông tin khách hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="customer_name"
                                           name="customer_name"
                                           value="<?= htmlspecialchars($_SESSION['form_data']['customer_name'] ?? ($_SESSION['user_name'] ?? '')) ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control"
                                           id="customer_email"
                                           name="customer_email"
                                           value="<?= htmlspecialchars($_SESSION['form_data']['customer_email'] ?? ($_SESSION['user_email'] ?? '')) ?>"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_phone" class="form-label">
                                        Số điện thoại <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel"
                                           class="form-control"
                                           id="customer_phone"
                                           name="customer_phone"
                                           value="<?= htmlspecialchars($_SESSION['form_data']['customer_phone'] ?? '') ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_address" class="form-label">
                                        Địa chỉ giao hàng <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="delivery_address"
                                           name="delivery_address"
                                           value="<?= htmlspecialchars($_SESSION['form_data']['delivery_address'] ?? '') ?>"
                                           placeholder="Số nhà, đường, phường, quận, thành phố"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="order_notes" class="form-label">Ghi chú đơn hàng</label>
                            <textarea class="form-control"
                                      id="order_notes"
                                      name="order_notes"
                                      rows="3"
                                      placeholder="Ghi chú về thời gian giao hàng, yêu cầu đặc biệt..."><?= htmlspecialchars($_SESSION['form_data']['order_notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-payment"></i> Phương thức thanh toán
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check payment-option">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="payment_method"
                                           id="cod"
                                           value="cod"
                                           <?= ($_SESSION['form_data']['payment_method'] ?? 'cod') == 'cod' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="cod">
                                        <div class="payment-card">
                                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                            <h6>Thanh toán khi nhận hàng</h6>
                                            <small class="text-muted">Thanh toán bằng tiền mặt khi nhận hàng</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check payment-option">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="payment_method"
                                           id="bank_transfer"
                                           value="bank_transfer"
                                           <?= ($_SESSION['form_data']['payment_method'] ?? '') == 'bank_transfer' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="bank_transfer">
                                        <div class="payment-card">
                                            <i class="fas fa-university fa-2x text-primary mb-2"></i>
                                            <h6>Chuyển khoản ngân hàng</h6>
                                            <small class="text-muted">Chuyển khoản qua ngân hàng</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check payment-option">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="payment_method"
                                           id="credit_card"
                                           value="credit_card"
                                           <?= ($_SESSION['form_data']['payment_method'] ?? '') == 'credit_card' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="credit_card">
                                        <div class="payment-card">
                                            <i class="fas fa-credit-card fa-2x text-warning mb-2"></i>
                                            <h6>Thẻ tín dụng</h6>
                                            <small class="text-muted">Visa, MasterCard, JCB</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Details -->
                        <div id="bankTransferDetails" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Thông tin chuyển khoản</h6>
                                <p class="mb-2"><strong>Ngân hàng:</strong> Vietcombank</p>
                                <p class="mb-2"><strong>Số tài khoản:</strong> 123-456-789</p>
                                <p class="mb-2"><strong>Chủ tài khoản:</strong> Nhà hàng Buffet</p>
                                <p class="mb-0"><strong>Nội dung:</strong> Thanh toan don hang [Mã đơn hàng]</p>
                            </div>
                        </div>

                        <!-- Credit Card Details -->
                        <div id="creditCardDetails" class="mt-3" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="fas fa-construction"></i>
                                Tính năng thanh toán thẻ tín dụng đang được phát triển
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Confirmation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle"></i> Xác nhận đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                            <label class="form-check-label" for="agreeTerms">
                                Tôi đồng ý với
                                <a href="#" class="text-decoration-none">điều khoản và điều kiện</a>
                                của nhà hàng <span class="text-danger">*</span>
                            </label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="receivePromotions">
                            <label class="form-check-label" for="receivePromotions">
                                Tôi muốn nhận thông tin khuyến mãi qua email
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitOrder">
                        <i class="fas fa-shopping-cart"></i> Đặt Hàng Ngay
                        <span id="totalInButton">(<?= number_format($total, 0, ',', '.') ?>đ)</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt"></i> Tóm tắt đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Cart Items -->
                    <div class="order-items mb-3">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                <img src="<?= !empty($item['food']['image_url']) ? htmlspecialchars($item['food']['image_url']) : '/assets/images/no-image.jpg' ?>"
                                     class="rounded me-2"
                                     style="width: 40px; height: 40px; object-fit: cover;"
                                     alt="<?= htmlspecialchars($item['food']['name']) ?>">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold"><?= htmlspecialchars($item['food']['name']) ?></div>
                                    <div class="small text-muted">
                                        <?= number_format($item['food']['price'], 0, ',', '.') ?>đ × <?= $item['quantity'] ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="small fw-bold"><?= number_format($item['total'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pricing Breakdown -->
                    <div class="pricing-breakdown">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span><?= number_format($deliveryFee, 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí dịch vụ (5%):</span>
                            <span><?= number_format($serviceFee, 0, ',', '.') ?>đ</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-primary h5"><?= number_format($total, 0, ',', '.') ?>đ</strong>
                        </div>
                    </div>

                    <!-- Estimated Delivery -->
                    <div class="delivery-info bg-light p-3 rounded">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-truck text-primary me-2"></i>
                            <strong>Thông tin giao hàng</strong>
                        </div>
                        <div class="small text-muted mb-1">Thời gian ước tính: 30-45 phút</div>
                        <div class="small text-muted">Phí giao hàng: <?= number_format($deliveryFee, 0, ',', '.') ?>đ</div>
                    </div>
                </div>
            </div>

            <!-- Security Info -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                    <h6>Bảo mật thông tin</h6>
                    <small class="text-muted">
                        Thông tin của bạn được bảo mật bằng SSL 256-bit
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const bankTransferDetails = document.getElementById('bankTransferDetails');
    const creditCardDetails = document.getElementById('creditCardDetails');

    // Payment method change handler
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all payment details
            bankTransferDetails.style.display = 'none';
            creditCardDetails.style.display = 'none';

            // Show relevant details
            if (this.value === 'bank_transfer') {
                bankTransferDetails.style.display = 'block';
            } else if (this.value === 'credit_card') {
                creditCardDetails.style.display = 'block';
            }
        });
    });

    // Form validation
    checkoutForm.addEventListener('submit', function(e) {
        if (!validateCheckoutForm()) {
            e.preventDefault();
        } else {
            // Show loading state
            const submitBtn = document.getElementById('submitOrder');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        }
    });

    // Phone number formatting
    document.getElementById('customer_phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        e.target.value = value;
    });

    // Initialize payment method display
    const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (selectedPayment) {
        selectedPayment.dispatchEvent(new Event('change'));
    }
});

function validateCheckoutForm() {
    const requiredFields = document.querySelectorAll('#checkoutForm [required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim() && field.type !== 'checkbox') {
            field.classList.add('is-invalid');
            isValid = false;
        } else if (field.type === 'checkbox' && !field.checked) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validate email format
    const emailField = document.getElementById('customer_email');
    if (emailField.value && !isValidEmail(emailField.value)) {
        emailField.classList.add('is-invalid');
        isValid = false;
    }

    // Validate phone number
    const phoneField = document.getElementById('customer_phone');
    if (phoneField.value && !isValidPhone(phoneField.value)) {
        phoneField.classList.add('is-invalid');
        isValid = false;
    }

    if (!isValid) {
        showToast('Vui lòng điền đầy đủ và chính xác thông tin', 'error');
    }

    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[0-9]{10}$/;
    return phoneRegex.test(phone);
}

function showToast(message, type = 'info') {
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
    } else {
        alert(message);
    }
}
</script>

<style>
.payment-option {
    height: 100%;
}

.payment-option .form-check-label {
    cursor: pointer;
    width: 100%;
}

.payment-card {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.payment-option input:checked + label .payment-card {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.payment-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
}

.order-items {
    max-height: 300px;
    overflow-y: auto;
}

.is-invalid {
    border-color: #dc3545;
}

.form-check-input.is-invalid {
    border-color: #dc3545;
}

.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 1020;
}

@media (max-width: 768px) {
    .payment-card {
        height: auto;
        padding: 15px;
    }

    .sticky-top {
        position: static;
    }
}
</style>

<?php
// Clear form data after displaying
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>
