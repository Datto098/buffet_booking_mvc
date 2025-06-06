<?php
/**
 * Cart Index View
 */
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-shopping-cart"></i> Giỏ Hàng
                <?php if ($itemCount > 0): ?>
                    <span class="badge bg-primary ms-2"><?= $itemCount ?></span>
                <?php endif; ?>
            </h2>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>                    <h3>Giỏ hàng trống</h3>
                    <p class="text-muted mb-4">Bạn chưa thêm món ăn nào vào giỏ hàng</p>
                    <a href="<?= SITE_URL ?>/menu" class="btn btn-primary btn-lg">
                        <i class="fas fa-utensils"></i> Xem Thực Đơn
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Cart Items -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Món đã chọn</h5>
                        <button class="btn btn-outline-danger btn-sm" id="clearCartBtn">
                            <i class="fas fa-trash"></i> Xóa tất cả
                        </button>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cartItems as $index => $item): ?>
                            <div class="cart-item" data-food-id="<?= $item['food']['id'] ?>">
                                <div class="row align-items-center py-3 <?= $index > 0 ? 'border-top' : '' ?>">                                    <!-- Food Image -->
                                    <div class="col-md-2 col-3">
                                        <img src="<?= !empty($item['food']['image_url']) ? htmlspecialchars($item['food']['image_url']) : SITE_URL . '/assets/images/no-image.svg' ?>"
                                             class="img-fluid rounded" alt="<?= htmlspecialchars($item['food']['name']) ?>"
                                             style="height: 80px; width: 80px; object-fit: cover;">
                                    </div>

                                    <!-- Food Info -->
                                    <div class="col-md-4 col-9">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['food']['name']) ?></h6>
                                        <p class="text-muted small mb-1"><?= htmlspecialchars($item['food']['category_name']) ?></p>
                                        <span class="text-primary fw-bold">
                                            <?= number_format($item['food']['price'], 0, ',', '.') ?>đ
                                        </span>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="col-md-3 col-6">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary quantity-btn"
                                                    type="button"
                                                    data-action="decrease"
                                                    data-food-id="<?= $item['food']['id'] ?>">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number"
                                                   class="form-control text-center quantity-input"
                                                   value="<?= $item['quantity'] ?>"
                                                   min="1"
                                                   data-food-id="<?= $item['food']['id'] ?>">
                                            <button class="btn btn-outline-secondary quantity-btn"
                                                    type="button"
                                                    data-action="increase"
                                                    data-food-id="<?= $item['food']['id'] ?>">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="col-md-2 col-4">
                                        <div class="text-end">
                                            <span class="fw-bold text-primary subtotal">
                                                <?= number_format($item['subtotal'], 0, ',', '.') ?>đ
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="col-md-1 col-2">
                                        <button class="btn btn-outline-danger btn-sm remove-item-btn"
                                                data-food-id="<?= $item['food']['id'] ?>"
                                                title="Xóa khỏi giỏ hàng">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="mt-3">
                    <a href="/index.php?page=menu" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span id="cartSubtotal"><?= number_format($totalAmount, 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí dịch vụ (5%):</span>
                            <span id="serviceFee"><?= number_format($totalAmount * 0.05, 0, ',', '.') ?>đ</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-primary" id="cartTotal">
                                <?= number_format($totalAmount * 1.05, 0, ',', '.') ?>đ
                            </strong>
                        </div>

                        <?php if (isLoggedIn()): ?>
                            <a href="/index.php?page=order&action=checkout" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-credit-card"></i> Thanh toán
                            </a>
                        <?php else: ?>
                            <p class="text-muted small mb-2">Vui lòng đăng nhập để thanh toán</p>
                            <a href="/index.php?page=auth&action=login" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                        <?php endif; ?>

                        <!-- Estimated delivery time -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-success me-2"></i>
                                <div>
                                    <small class="text-muted">Thời gian giao hàng ước tính</small>
                                    <div class="fw-bold">30-45 phút</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Promo Code -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Mã giảm giá</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nhập mã giảm giá" id="promoCode">
                            <button class="btn btn-outline-primary" type="button" id="applyPromoBtn">
                                Áp dụng
                            </button>
                        </div>
                        <small class="text-muted">Nhập mã để được giảm giá</small>
                    </div>
                </div>

                <!-- Recommended Items -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Có thể bạn quan tâm</h6>
                    </div>
                    <div class="card-body">
                        <div id="recommendedItems">
                            <!-- Recommended items will be loaded via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Remove Confirmation Modal -->
<div class="modal fade" id="removeItemModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn xóa món này khỏi giỏ hàng?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveItem">Xóa</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity change handlers
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            const foodId = this.dataset.foodId;
            const input = document.querySelector(`.quantity-input[data-food-id="${foodId}"]`);
            let quantity = parseInt(input.value);

            if (action === 'increase') {
                quantity++;
            } else if (action === 'decrease' && quantity > 1) {
                quantity--;
            }

            input.value = quantity;
            updateCartQuantity(foodId, quantity);
        });
    });

    // Direct quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const foodId = this.dataset.foodId;
            const quantity = Math.max(1, parseInt(this.value) || 1);
            this.value = quantity;
            updateCartQuantity(foodId, quantity);
        });
    });

    // Remove item handlers
    document.querySelectorAll('.remove-item-btn').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.dataset.foodId;
            showRemoveConfirmation(foodId);
        });
    });

    // Clear cart
    document.getElementById('clearCartBtn')?.addEventListener('click', function() {
        if (confirm('Bạn có chắc muốn xóa tất cả món ăn khỏi giỏ hàng?')) {
            clearCart();
        }
    });

    // Apply promo code
    document.getElementById('applyPromoBtn')?.addEventListener('click', function() {
        const promoCode = document.getElementById('promoCode').value.trim();
        if (promoCode) {
            applyPromoCode(promoCode);
        }
    });

    // Load recommended items
    loadRecommendedItems();
});

function updateCartQuantity(foodId, quantity) {
    fetch('/index.php?page=cart&action=update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `food_id=${foodId}&quantity=${quantity}&csrf_token=${getCSRFToken()}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay(data.cartInfo);
            updateItemSubtotal(foodId, quantity);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra khi cập nhật giỏ hàng', 'error');
    });
}

function showRemoveConfirmation(foodId) {
    const modal = new bootstrap.Modal(document.getElementById('removeItemModal'));
    modal.show();

    document.getElementById('confirmRemoveItem').onclick = function() {
        removeFromCart(foodId);
        modal.hide();
    };
}

function removeFromCart(foodId) {
    fetch('/index.php?page=cart&action=remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `food_id=${foodId}&csrf_token=${getCSRFToken()}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove item from DOM
            const cartItem = document.querySelector(`.cart-item[data-food-id="${foodId}"]`);
            if (cartItem) {
                cartItem.remove();
            }

            updateCartDisplay(data.cartInfo);

            // Check if cart is empty
            const remainingItems = document.querySelectorAll('.cart-item');
            if (remainingItems.length === 0) {
                location.reload();
            }

            showToast(data.message, 'success');
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra khi xóa món ăn', 'error');
    });
}

function clearCart() {
    fetch('/index.php?page=cart&action=clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `csrf_token=${getCSRFToken()}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra khi xóa giỏ hàng', 'error');
    });
}

function updateItemSubtotal(foodId, quantity) {
    const cartItem = document.querySelector(`.cart-item[data-food-id="${foodId}"]`);
    if (cartItem) {
        const priceText = cartItem.querySelector('.text-primary.fw-bold').textContent;
        const price = parseInt(priceText.replace(/[^\d]/g, ''));
        const subtotal = price * quantity;

        cartItem.querySelector('.subtotal').textContent = formatPrice(subtotal);
        updateCartTotals();
    }
}

function updateCartTotals() {
    let subtotal = 0;
    document.querySelectorAll('.subtotal').forEach(element => {
        const amount = parseInt(element.textContent.replace(/[^\d]/g, ''));
        subtotal += amount;
    });

    const serviceFee = subtotal * 0.05;
    const total = subtotal + serviceFee;

    document.getElementById('cartSubtotal').textContent = formatPrice(subtotal);
    document.getElementById('serviceFee').textContent = formatPrice(serviceFee);
    document.getElementById('cartTotal').textContent = formatPrice(total);
}

function applyPromoCode(code) {
    // TODO: Implement promo code functionality
    showToast('Tính năng mã giảm giá sẽ được triển khai sớm', 'info');
}

function loadRecommendedItems() {
    // TODO: Load recommended items based on cart contents
    const container = document.getElementById('recommendedItems');
    if (container) {
        container.innerHTML = '<p class="text-muted small">Đang tải...</p>';

        // Simulate loading
        setTimeout(() => {
            container.innerHTML = '<p class="text-muted small">Không có gợi ý nào</p>';
        }, 1000);
    }
}

function formatPrice(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}
</script>

<style>
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.quantity-input {
    width: 60px;
}

.quantity-btn {
    width: 35px;
}

@media (max-width: 768px) {
    .cart-item .row > div {
        margin-bottom: 10px;
    }

    .quantity-input {
        width: 50px;
    }
}
</style>
