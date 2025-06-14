<?php
/**
 * Cart Index View - Modern Clean Design
 */
?>

<!-- Cart Header -->
<section class="cart-header-modern" style="margin-top: 80px;">
    <div class="container">
        <div class="cart-header-content">
            <div class="cart-title-area">
                <h1 class="cart-title">
                    <i class="fas fa-shopping-bag"></i>
                    Giỏ hàng
                    <?php if (!empty($cartItems) && $itemCount > 0): ?>
                        <span class="cart-badge"><?= $itemCount ?></span>
                    <?php endif; ?>
                </h1>
                <p class="cart-subtitle">Kiểm tra đơn hàng trước khi thanh toán</p>
            </div>
            <div class="cart-actions">
                <a href="<?= SITE_URL ?>/menu" class="btn-add-more">
                    <i class="fas fa-plus"></i>
                    Thêm món
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Cart Content -->
<section class="cart-content-modern">
    <div class="container">
        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="empty-cart-modern">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h2>Giỏ hàng đang trống</h2>
                <p>Khám phá thực đơn phong phú và thêm những món ăn yêu thích của bạn!</p>
                <a href="<?= SITE_URL ?>/menu" class="btn-primary-modern">
                    <i class="fas fa-utensils"></i>
                    Khám phá thực đơn
                </a>
            </div>
        <?php else: ?>
            <!-- Cart Layout -->
            <div class="cart-layout-modern">
                <!-- Cart Items -->
                <div class="cart-items-area">
                    <div class="cart-items-header">
                        <h3>Món đã chọn</h3>
                        <button class="btn-clear-cart" id="clearCartBtn">
                            <i class="fas fa-trash-alt"></i>
                            Xóa tất cả
                        </button>
                    </div>

                    <div class="cart-items-list">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item" data-food-id="<?= $item['food']['id'] ?>">
                                <div class="item-image">
                                    <img src="<?= !empty($item['food']['image'])
                                        ? SITE_URL . '/uploads/food_images/' . htmlspecialchars($item['food']['image'])
                                        : SITE_URL . '/assets/images/food-placeholder.svg' ?>"
                                        alt="<?= htmlspecialchars($item['food']['name']) ?>">
                                </div>

                                <div class="item-info">
                                    <h4 class="item-name"><?= htmlspecialchars($item['food']['name']) ?></h4>
                                    <p class="item-category"><?= htmlspecialchars($item['food']['category_name']) ?></p>
                                    <div class="item-price"><?= number_format($item['food']['price'], 0, ',', '.') ?>đ</div>
                                </div>

                                <div class="item-controls">
                                    <div class="quantity-controls">
                                        <button class="qty-btn qty-minus quantity-btn" data-action="decrease" data-food-id="<?= $item['food']['id'] ?>">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="qty-display"><?= $item['quantity'] ?></span>
                                        <button class="qty-btn qty-plus quantity-btn" data-action="increase" data-food-id="<?= $item['food']['id'] ?>">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <div class="item-total">
                                        <span class="total-amount"><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</span>
                                        <button class="btn-remove remove-item-btn" data-food-id="<?= $item['food']['id'] ?>" title="Xóa món">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary-area">
                    <div class="summary-card">
                        <h3>Tóm tắt đơn hàng</h3>

                        <div class="summary-details">
                            <div class="summary-row">
                                <span>Tạm tính</span>
                                <span id="subtotal"><?= number_format($totalAmount, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="summary-row">
                                <span>Phí dịch vụ (5%)</span>
                                <span id="serviceFee"><?= number_format($totalAmount * 0.05, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="summary-row delivery-fee">
                                <span>Phí giao hàng</span>
                                <span class="free-delivery">Miễn phí</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row total-row">
                                <span>Tổng cộng</span>
                                <span id="cartTotal"><?= number_format($totalAmount * 1.05, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>

                        <div class="checkout-section">
                            <?php if (isLoggedIn()): ?>
                                <a href="<?= SITE_URL ?>/index.php?page=order&action=checkout" class="btn-checkout">
                                    <i class="fas fa-credit-card"></i>
                                    Thanh toán
                                </a>
                            <?php else: ?>
                                <p class="login-notice">Vui lòng đăng nhập để thanh toán</p>
                                <a href="<?= SITE_URL ?>/index.php?page=auth&action=login" class="btn-login">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Đăng nhập
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="delivery-info">
                            <div class="delivery-time">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <span class="info-label">Thời gian giao hàng</span>
                                    <span class="info-value">30-45 phút</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modern Cart Styles -->
<style>
/* Cart Header */
.cart-header-modern {
    background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-navy-dark) 100%);
    padding: 3rem 0 2rem;
    position: relative;
}

.cart-header-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23D4AF37" fill-opacity="0.05"><circle cx="30" cy="30" r="1"/></g></svg>') repeat;
}

.cart-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.cart-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--text-white);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.cart-title i {
    color: var(--primary-gold);
}

.cart-badge {
    background: var(--primary-gold);
    color: var(--text-white);
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    margin-left: 0.5rem;
}

.cart-subtitle {
    color: var(--neutral-pearl);
    font-family: 'Inter', sans-serif;
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
}

.btn-add-more {
    background: var(--primary-gold);
    color: var(--text-white);
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: 2px solid var(--primary-gold);
}

.btn-add-more:hover {
    background: transparent;
    color: var(--primary-gold);
    transform: translateY(-2px);
    text-decoration: none;
}

/* Cart Content */
.cart-content-modern {
    padding: 3rem 0;
    background: var(--bg-primary);
}

/* Empty Cart */
.empty-cart-modern {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-cart-icon {
    font-size: 4rem;
    color: var(--primary-gold);
    margin-bottom: 2rem;
}

.empty-cart-modern h2 {
    font-family: 'Playfair Display', serif;
    color: var(--text-primary);
    font-size: 2rem;
    margin-bottom: 1rem;
}

.empty-cart-modern p {
    color: var(--text-secondary);
    font-size: 1.1rem;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.btn-primary-modern {
    background: linear-gradient(135deg, var(--primary-gold), var(--primary-gold-dark));
    color: var(--text-white);
    padding: 1rem 2rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
    color: var(--text-white);
    text-decoration: none;
}

/* Cart Layout */
.cart-layout-modern {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 3rem;
}

@media (max-width: 968px) {
    .cart-layout-modern {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}

/* Cart Items */
.cart-items-area {
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    padding: 2rem;
    border: 1px solid var(--neutral-pearl);
}

.cart-items-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--neutral-pearl);
}

.cart-items-header h3 {
    font-family: 'Playfair Display', serif;
    color: var(--text-primary);
    margin: 0;
    font-size: 1.5rem;
}

.btn-clear-cart {
    background: transparent;
    color: #dc3545;
    border: 1px solid #dc3545;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-clear-cart:hover {
    background: #dc3545;
    color: white;
}

/* Cart Item */
.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: 1.5rem;
    padding: 1.5rem 0;
    border-bottom: 1px solid var(--neutral-pearl);
    align-items: center;
}

.cart-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-md);
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-info h4 {
    font-family: 'Playfair Display', serif;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
}

.item-category {
    color: var(--text-light);
    font-size: 0.9rem;
    margin: 0 0 0.5rem 0;
}

.item-price {
    color: var(--primary-gold);
    font-weight: 600;
    font-size: 1rem;
}

.item-controls {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
    background: var(--bg-primary);
    border-radius: var(--radius-sm);
    border: 1px solid var(--neutral-pearl);
}

.qty-btn {
    background: transparent;
    border: none;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.3s ease;
}

.qty-btn:hover {
    background: var(--primary-gold);
    color: white;
}

.qty-display {
    padding: 0 1rem;
    font-weight: 500;
    color: var(--text-primary);
    min-width: 40px;
    text-align: center;
}

.item-total {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.total-amount {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 1.1rem;
}

.btn-remove {
    background: transparent;
    border: none;
    color: #dc3545;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-remove:hover {
    background: #dc3545;
    color: white;
}

/* Order Summary */
.order-summary-area {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.summary-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    padding: 2rem;
    border: 1px solid var(--neutral-pearl);
}

.summary-card h3 {
    font-family: 'Playfair Display', serif;
    color: var(--text-primary);
    margin: 0 0 2rem 0;
    font-size: 1.5rem;
}

.summary-details {
    margin-bottom: 2rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    color: var(--text-secondary);
}

.summary-row span:last-child {
    font-weight: 500;
    color: var(--text-primary);
}

.free-delivery {
    color: #28a745 !important;
    font-weight: 600;
}

.summary-divider {
    height: 1px;
    background: var(--neutral-pearl);
    margin: 1rem 0;
}

.total-row {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
}

.total-row span:last-child {
    color: var(--primary-gold);
    font-size: 1.3rem;
}

.btn-checkout {
    width: 100%;
    background: linear-gradient(135deg, var(--primary-gold), var(--primary-gold-dark));
    color: var(--text-white);
    padding: 1rem 2rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    color: var(--text-white);
    text-decoration: none;
}

.login-notice {
    text-align: center;
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin: 0 0 1rem 0;
}

.btn-login {
    width: 100%;
    background: var(--primary-navy);
    color: var(--text-white);
    padding: 1rem 2rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.btn-login:hover {
    background: var(--primary-navy-dark);
    color: var(--text-white);
    text-decoration: none;
}

.delivery-info {
    padding-top: 1rem;
    border-top: 1px solid var(--neutral-pearl);
}

.delivery-time {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.delivery-time i {
    color: var(--primary-gold);
    font-size: 1.2rem;
}

.info-label {
    display: block;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.info-value {
    display: block;
    color: var(--text-primary);
    font-weight: 600;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .cart-header-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .cart-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 0.5rem;
    }

    .cart-item {
        grid-template-columns: 1fr;
        gap: 1rem;
        text-align: center;
    }

    .item-controls {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }

    .cart-items-area,
    .summary-card {
        padding: 1.5rem;
    }
}
</style>

<!-- Cart JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity control buttons
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            const foodId = this.dataset.foodId;

            fetch('<?= SITE_URL ?>/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `food_id=${foodId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật giỏ hàng');
            });
        });
    });

    // Remove item buttons
    document.querySelectorAll('.remove-item-btn').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.dataset.foodId;

            if (confirm('Bạn có chắc muốn xóa món này khỏi giỏ hàng?')) {
                fetch('<?= SITE_URL ?>/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `food_id=${foodId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa món');
                });
            }
        });
    });

    // Clear cart button
    document.getElementById('clearCartBtn')?.addEventListener('click', function() {
        if (confirm('Bạn có chắc muốn xóa tất cả món trong giỏ hàng?')) {
            fetch('<?= SITE_URL ?>/cart/clear', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa giỏ hàng');
            });
        }
    });
});
</script>
