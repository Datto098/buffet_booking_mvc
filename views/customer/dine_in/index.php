<?php
$table_id = $_GET['table'] ?? null;
$is_table_active = false;

if ($table_id) {
    // TODO: Kiểm tra trạng thái bàn từ database
    $is_table_active = true;
}
?>

<div class="container-fluid">
    <div class="dine-in-container mt-5 pt-5">
        <?php if (!$table_id): ?>
        <!-- Màn hình chọn bàn -->
        <div class="container">
            <div class="table-selection-container text-center py-5">
                <h2 class="mb-4">Vui lòng chọn bàn của bạn</h2>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body p-4">
                                <form action="" method="GET" class="table-form">
                                    <div class="mb-4">
                                        <label for="table" class="form-label">Nhập số bàn của bạn</label>
                                        <input type="text" class="form-control form-control-lg text-center"
                                               id="table" name="table" placeholder="Ví dụ: A01">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        Xác nhận bàn
                                    </button>
                                </form>
                                <div class="mt-4">
                                    <p class="text-muted">hoặc</p>
                                    <button class="btn btn-outline-primary w-100" onclick="scanQRCode()">
                                        <i class="fas fa-qrcode me-2"></i>
                                        Quét mã QR trên bàn
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Màn hình gọi món -->
        <div class="container-fluid">
            <div class="table-info mb-4">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h3>Bàn <?php echo htmlspecialchars($table_id); ?></h3>
                            <p class="mb-0">Trạng thái:
                                <span class="badge bg-success">Đang phục vụ</span>
                            </p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4>Thời gian</h4>
                            <p class="mb-0" id="current-time">12:00:00</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4>Tổng đơn hàng</h4>
                            <p class="mb-0" id="total-amount">0 ₫</p>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-outline-primary position-relative" onclick="toggleOrderStatus()">
                                <i class="fas fa-list-alt me-2"></i>Tình trạng đơn hàng
                                <span id="order-status-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info" style="display: none;">
                                    <span id="active-orders-count">0</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Section -->
            <div class="order-status-section mb-4" id="orderStatusSection" style="display: none;">
                <div class="container">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-list-check me-2"></i>Tình trạng đơn hàng của bạn
                                <span id="orders-count-badge" class="badge bg-secondary ms-2" style="display: none;">0</span>
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="refreshOrderStatus()">
                                    <i class="fas fa-sync-alt me-1"></i>Làm mới
                                </button>
                                <small class="text-muted align-self-center">
                                    <i class="fas fa-info-circle me-1"></i>Tự động cập nhật mỗi 60s
                                </small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="orderStatusList" class="p-3">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Đang tải thông tin đơn hàng...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Categories -->
            <div class="container">
                <div class="menu-categories mb-4">
                    <div class="d-flex justify-content-center align-items-center flex-wrap">
                        <button class="btn btn-outline-success m-1 category-btn active" data-category="buffet">
                            <i class="fas fa-utensils me-2"></i>Buffet Miễn Phí
                        </button>
                        <button class="btn btn-outline-primary m-1 category-btn" data-category="all">
                            Tất cả món
                        </button>
                        <?php foreach ($categories as $category): ?>
                        <button class="btn btn-outline-primary m-1 category-btn"
                                data-category="<?php echo $category['id']; ?>">
                            <?php echo $category['name']; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="container">
                <!-- Buffet Items Section -->
                <div id="buffet-section" class="buffet-section mb-5">
                    <div class="section-header text-center mb-4">
                        <h3 class="text-success">
                            <i class="fas fa-utensils me-2"></i>BUFFET MIỄN PHÍ
                        </h3>
                        <p class="text-muted">Các món ăn buffet đã bao gồm trong giá vé vào cửa - Gọi thoải mái!</p>
                    </div>
                    <div class="food-grid" id="buffet-foods">
                        <?php if (isset($buffet_foods) && !empty($buffet_foods)): ?>
                            <?php foreach ($buffet_foods as $food): ?>
                            <div class="food-card buffet-card" data-category="buffet">
                                <div class="buffet-badge">
                                    <span class="badge bg-success">MIỄN PHÍ</span>
                                </div>
                                <img src="<?= SITE_URL ?>/uploads/food_images/<?= $food['image'] ?>"
                                     alt="<?php echo $food['name']; ?>" class="food-image">
                                <div class="food-info">
                                    <h3 class="food-name"><?php echo $food['name']; ?></h3>
                                    <p class="food-description"><?php echo $food['description']; ?></p>
                                    <div class="food-meta">
                                        <div class="food-price text-success">
                                            <i class="fas fa-gift me-1"></i>MIỄN PHÍ
                                        </div>
                                    </div>
                                    <button class="add-to-cart-btn buffet-btn" onclick="addToDineInCart(<?php echo $food['id']; ?>)">
                                        <i class="fas fa-plus me-2"></i>Gọi món buffet
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted p-4">
                                <p>Hiện tại chưa có món buffet nào.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Regular Menu Items Section -->
                <div id="regular-section" class="regular-section" style="display: none;">
                    <div class="section-header text-center mb-4">
                        <h3 class="text-primary">
                            <i class="fas fa-restaurant me-2"></i>THỰC ĐƠN THÊM
                        </h3>
                        <p class="text-muted">Các món ăn đặc biệt - Tính phí theo giá niêm yết</p>
                    </div>
                    <div class="food-grid" id="regular-foods">
                        <?php if (isset($regular_foods) && !empty($regular_foods)): ?>
                            <?php foreach ($regular_foods as $food): ?>
                            <div class="food-card regular-card" data-category="<?php echo $food['category_id']; ?>">
                                <img src="<?= SITE_URL ?>/uploads/food_images/<?= $food['image'] ?>"
                                     alt="<?php echo $food['name']; ?>" class="food-image">
                                <div class="food-info">
                                    <h3 class="food-name"><?php echo $food['name']; ?></h3>
                                    <p class="food-description"><?php echo $food['description']; ?></p>
                                    <div class="food-meta">
                                        <div class="food-price">
                                            <?php echo number_format($food['price'], 0, ',', '.'); ?> ₫
                                        </div>
                                        <div class="food-badges">
                                            <?php if ($food['is_popular']): ?>
                                            <span class="badge badge-popular">Phổ biến</span>
                                            <?php endif; ?>
                                            <?php if ($food['is_new']): ?>
                                            <span class="badge badge-new">Mới</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <button class="add-to-cart-btn" onclick="addToDineInCart(<?php echo $food['id']; ?>)">
                                        <i class="fas fa-plus me-2"></i>Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted p-4">
                                <p>Hiện tại chưa có món ăn nào.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Sidebar -->
        <div class="cart-sidebar" id="cartSidebar">
            <div class="cart-header">
                <h3>Giỏ hàng - Bàn <?php echo htmlspecialchars($table_id); ?></h3>
                <button class="btn-close btn-close-white" onclick="toggleCart()"></button>
            </div>
            <div class="cart-items" id="cartItems">
                <!-- Cart items will be dynamically added here -->
            </div>
            <div class="cart-footer p-3 bg-light">
                <div class="mb-3">
                    <label for="orderNotes" class="form-label">Ghi chú đơn hàng:</label>
                    <textarea id="orderNotes" class="form-control" rows="2"
                              placeholder="Ví dụ: Không cay, ít đường..."></textarea>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <h5>Tổng cộng:</h5>
                    <h5 id="cartTotal">0 ₫</h5>
                </div>
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-danger flex-fill" onclick="clearCart()">
                        <i class="fas fa-trash me-2"></i>Xóa tất cả
                    </button>
                    <button class="btn btn-primary flex-fill" onclick="submitOrder()">
                        <i class="fas fa-paper-plane me-2"></i>Gửi đơn
                    </button>
                </div>
            </div>
        </div>

        <!-- View Order Button -->
        <button class="floating-cart-btn" onclick="toggleCart()">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">0</span>
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Override main.js jQuery error -->
<script>
// Provide a fallback for jQuery if it's not loaded
if (typeof jQuery === 'undefined') {
    window.jQuery = window.$ = function() {
        console.warn('jQuery is not loaded. Creating dummy jQuery object.');
        return {
            ready: function() { return this; },
            on: function() { return this; },
            find: function() { return this; },
            text: function() { return this; },
            html: function() { return this; },
            val: function() { return this; },
            addClass: function() { return this; },
            removeClass: function() { return this; },
            fadeOut: function() { return this; },
            ajax: function() { console.warn('jQuery AJAX not available'); }
        };
    };
    // Add document ready fallback
    jQuery.fn = jQuery.prototype = jQuery();
}
</script>

<script>
// Cập nhật thời gian
function updateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent =
        now.toLocaleTimeString('vi-VN');
}
setInterval(updateTime, 1000);

// Cập nhật số lượng trong giỏ
function updateCartCount(count) {
    count = parseInt(count, 10) || 0;
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;

        // Add pulse animation if items added
        if (count > 0) {
            const floatingBtn = document.querySelector('.floating-cart-btn');
            floatingBtn.classList.add('pulse');
            setTimeout(() => {
                floatingBtn.classList.remove('pulse');
            }, 2000);
        }
    }
}

// Quét mã QR
function scanQRCode() {
    // TODO: Implement QR code scanning
    alert('Tính năng đang được phát triển');
}

// Thêm vào giỏ hàng
function addToDineInCart(foodId) {
    const tableId = '<?php echo $table_id; ?>';
    const button = event.target;

    // Add loading state
    button.classList.add('loading');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';

    fetch('<?php echo SITE_URL; ?>/dine-in/add-to-cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `food_id=${foodId}&table_id=${tableId}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            updateCart();

            // Show success feedback
            button.innerHTML = '<i class="fas fa-check me-2"></i>Đã thêm!';
            button.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';

            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-plus me-2"></i>Thêm vào giỏ';
                button.style.background = '';
                button.disabled = false;
                button.classList.remove('loading');
            }, 1500);
        } else {
            showNotification(data.message, 'error');
            resetButton(button);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi thêm món vào giỏ', 'error');
        resetButton(button);
    });
}

function resetButton(button) {
    // Determine if this is a buffet button
    const isBuffetBtn = button.classList.contains('buffet-btn');

    if (isBuffetBtn) {
        button.innerHTML = '<i class="fas fa-plus me-2"></i>Gọi món buffet';
    } else {
        button.innerHTML = '<i class="fas fa-plus me-2"></i>Thêm vào giỏ';
    }

    button.disabled = false;
    button.classList.remove('loading');
    button.style.background = '';
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} notification-toast`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
        ${message}
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Cập nhật giỏ hàng
function updateCart() {
    const tableId = '<?php echo $table_id; ?>';
    fetch(`<?php echo SITE_URL; ?>/dine-in/get-cart?table_id=${tableId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartItems = document.getElementById('cartItems');
            cartItems.innerHTML = '';

            if (data.cart_items.length === 0) {
                cartItems.innerHTML = `
                    <div class="empty-cart text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Giỏ hàng trống</h5>
                        <p class="text-muted">Hãy thêm món ăn vào giỏ hàng</p>
                    </div>
                `;
            } else {
                data.cart_items.forEach(item => {
                    const priceDisplay = item.is_buffet_item
                        ? '<span class="text-success"><i class="fas fa-gift"></i> MIỄN PHÍ</span>'
                        : `${formatCurrency(item.price)} ₫`;

                    const totalDisplay = item.is_buffet_item
                        ? '<span class="text-success">0 ₫</span>'
                        : `${formatCurrency(item.total)} ₫`;

                    const itemClass = item.is_buffet_item ? 'cart-item buffet-item' : 'cart-item';

                    cartItems.innerHTML += `
                        <div class="${itemClass}">
                            <img src="<?php echo SITE_URL; ?>/uploads/food_images/${item.image}" alt="${item.name}" class="cart-item-image">
                            <div class="cart-item-info">
                                <h4>${item.name}</h4>
                                ${item.is_buffet_item ? '<span class="badge bg-success mb-1">BUFFET</span>' : ''}
                                <div class="cart-item-price">${priceDisplay}</div>
                                <div class="cart-item-quantity">
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})"
                                            ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                                    <span>${item.quantity}</span>
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                                </div>
                            </div>
                            <div class="cart-item-total">${totalDisplay}</div>
                            <button class="cart-item-remove" onclick="removeFromCart(${item.id})" title="Xóa món này">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                });
            }

            document.getElementById('cartTotal').textContent = formatCurrency(data.total) + ' ₫';
            document.getElementById('total-amount').textContent = formatCurrency(data.total) + ' ₫';

            // Update cart count
            const totalItems = data.cart_items.reduce((sum, item) => sum + Number(item.quantity), 0);
            updateCartCount(totalItems);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi khi tải giỏ hàng', 'error');
    });
}

// Cập nhật số lượng món
function updateQuantity(foodId, quantity) {
    const tableId = '<?php echo $table_id; ?>';

    fetch('<?php echo SITE_URL; ?>/dine-in/update-cart-item', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `food_id=${foodId}&table_id=${tableId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCart();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật số lượng', 'error');
    });
}

// Xóa món khỏi giỏ hàng
function removeFromCart(foodId) {
    if (!confirm('Bạn có chắc muốn xóa món này khỏi giỏ hàng?')) {
        return;
    }

    const tableId = '<?php echo $table_id; ?>';

    fetch('<?php echo SITE_URL; ?>/dine-in/update-cart-item', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `food_id=${foodId}&table_id=${tableId}&quantity=0`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã xóa món khỏi giỏ hàng', 'success');
            updateCart();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xóa món', 'error');
    });
}

// Xóa toàn bộ giỏ hàng
function clearCart() {
    if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
        return;
    }

    const tableId = '<?php echo $table_id; ?>';

    fetch('<?php echo SITE_URL; ?>/dine-in/clear-cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `table_id=${tableId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã xóa toàn bộ giỏ hàng', 'success');
            updateCart();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xóa giỏ hàng', 'error');
    });
}

// Gửi đơn đến bếp
function submitOrder() {
    const tableId = '<?php echo $table_id; ?>';
    const notes = document.getElementById('orderNotes')?.value || '';

    // Show confirmation dialog
    if (!confirm('Bạn có chắc muốn gửi đơn hàng này đến bếp?')) {
        return;
    }

    const submitBtn = document.querySelector('.cart-footer button');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';

    fetch('<?php echo SITE_URL; ?>/dine-in/submit-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `table_id=${tableId}&notes=${notes}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đơn hàng đã được gửi đến bếp!', 'success');
            updateCart();
            toggleCart();

            // Reset submit button
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Gửi đơn đến bếp';
            }, 2000);
        } else {
            showNotification(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Gửi đơn đến bếp';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi gửi đơn hàng', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Gửi đơn đến bếp';
    });
}

// Toggle giỏ hàng
function toggleCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    if (cartSidebar) {
        cartSidebar.classList.toggle('open');
    }
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount);
}

// Lọc theo danh mục
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const categoryId = this.dataset.category;

        // Handle buffet vs regular sections
        const buffetSection = document.getElementById('buffet-section');
        const regularSection = document.getElementById('regular-section');

        if (categoryId === 'buffet') {
            // Show only buffet section
            buffetSection.style.display = 'block';
            regularSection.style.display = 'none';
        } else if (categoryId === 'all') {
            // Show both sections
            buffetSection.style.display = 'block';
            regularSection.style.display = 'block';

            // Show all cards in regular section
            document.querySelectorAll('#regular-foods .food-card').forEach(card => {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease';
            });
        } else {
            // Show only regular section with filtered category
            buffetSection.style.display = 'none';
            regularSection.style.display = 'block';

            // Filter regular menu by category
            document.querySelectorAll('#regular-foods .food-card').forEach(card => {
                if (card.dataset.category === categoryId) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.3s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Highlight selected category
        document.querySelectorAll('.category-btn').forEach(b =>
            b.classList.remove('active'));
        this.classList.add('active');
    });
});

// Load cart on page load
if ('<?php echo $table_id; ?>') {
    updateCart();
    loadOrderStatus();

    // Auto refresh cart every 30 seconds
    setInterval(updateCart, 30000);

    // Auto refresh order status every 60 seconds
    setInterval(loadOrderStatus, 60000);
}

// Toggle order status section
function toggleOrderStatus() {
    const orderStatusSection = document.getElementById('orderStatusSection');
    if (orderStatusSection.style.display === 'none') {
        orderStatusSection.style.display = 'block';
        loadOrderStatus();
    } else {
        orderStatusSection.style.display = 'none';
    }
}

// Update order status badge with active order count
function updateOrderStatusBadge() {
    const activeOrders = orderStatusData.filter(order =>
        ['pending', 'preparing'].includes(order.status)
    ).length;

    const badge = document.getElementById('order-status-badge');
    const countElement = document.getElementById('active-orders-count');

    if (activeOrders > 0) {
        countElement.textContent = activeOrders;
        badge.style.display = 'block';
        badge.className = badge.className.replace(/bg-\w+/,
            activeOrders > 3 ? 'bg-warning' : 'bg-info');
    } else {
        badge.style.display = 'none';
    }
}

// Enhanced order status loading with badge update
function loadOrderStatus() {
    const statusContainer = document.getElementById('orderStatusList');

    if (!statusContainer) return;

    statusContainer.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

    const tableId = new URLSearchParams(window.location.search).get('table');
    if (!tableId) {
        statusContainer.innerHTML = '<div class="alert alert-warning">Không xác định được bàn</div>';
        return;
    }

    fetch(`get_order_status.php?table=${tableId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                orderStatusData = data.orders || [];
                currentPage = 1; // Reset to first page when loading new data
                displayOrderStatus(orderStatusData);
                updateOrderStatusBadge(); // Update badge after loading

                // Log debug info for troubleshooting
                if (data.debug_info) {
                    console.log('Order Status Debug Info:', data.debug_info);
                }
            } else {
                // Handle specific error messages
                if (data.message === 'User must be logged in') {
                    statusContainer.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Vui lòng đăng nhập để xem đơn hàng</div>';
                } else {
                    statusContainer.innerHTML = '<div class="alert alert-info">Chưa có đơn hàng nào</div>';
                }
                updateOrderStatusBadge(); // Update badge even when no orders
            }
        })
        .catch(error => {
            console.error('Error loading order status:', error);
            statusContainer.innerHTML = '<div class="alert alert-danger">Lỗi khi tải thông tin đơn hàng</div>';
        });
}

// User identification for order tracking
function getUserIdentifier() {
    // Try to get user ID from session (if logged in)
    // This would need to be set by server-side PHP
    let userId = window.currentUserId || null;

    if (!userId) {
        // If not logged in, use session-based tracking
        let sessionKey = `dine_in_user_${window.location.search.get('table') || 'unknown'}`;
        userId = localStorage.getItem(sessionKey);

        if (!userId) {
            // Generate a unique session identifier
            userId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem(sessionKey, userId);
        }
    }

    return userId;
}

// Enhanced order status loading with user tracking
function loadOrderStatusWithUser() {
    const statusContainer = document.getElementById('orderStatusList');

    if (!statusContainer) return;

    statusContainer.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

    const tableId = new URLSearchParams(window.location.search).get('table');
    if (!tableId) {
        statusContainer.innerHTML = '<div class="alert alert-warning">Không xác định được bàn</div>';
        return;
    }

    const userIdentifier = getUserIdentifier();

    // Add user identifier to request if available
    let url = `get_order_status.php?table=${tableId}`;
    if (userIdentifier && userIdentifier.startsWith('session_')) {
        url += `&session_key=${userIdentifier}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                orderStatusData = data.orders || [];
                displayOrderStatus(orderStatusData);
                updateOrderStatusBadge();

                // Enhanced debug logging
                console.log('Order Status Loaded:', {
                    table: tableId,
                    userIdentifier: userIdentifier,
                    ordersCount: orderStatusData.length,
                    debugInfo: data.debug_info
                });
            } else {
                statusContainer.innerHTML = '<div class="alert alert-info">Chưa có đơn hàng nào</div>';
                updateOrderStatusBadge();
            }
        })
        .catch(error => {
            console.error('Error loading order status:', error);
            statusContainer.innerHTML = '<div class="alert alert-danger">Lỗi khi tải thông tin đơn hàng</div>';
        });
}

// Auto-refresh order status every 30 seconds
let statusRefreshInterval;

function startStatusRefresh() {
    // Clear existing interval if any
    if (statusRefreshInterval) {
        clearInterval(statusRefreshInterval);
    }

    // Refresh every 30 seconds
    statusRefreshInterval = setInterval(() => {
        const statusDiv = document.getElementById('order-status');
        if (statusDiv && statusDiv.style.display === 'block') {
            loadOrderStatus();
        } else {
            // Update badge even when status panel is hidden
            updateOrderStatusSilently();
        }
    }, 30000);
}

// Silent update for badge only (no UI changes to status panel)
function updateOrderStatusSilently() {
    const tableId = new URLSearchParams(window.location.search).get('table');
    if (!tableId) return;

    fetch(`get_order_status.php?table=${tableId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                orderStatusData = data.orders || [];
                updateOrderStatusBadge();

                // Log debug info silently (less verbose)
                if (data.debug_info && data.debug_info.total_orders > 0) {
                    console.log(`Silent update: Found ${data.debug_info.total_orders} orders for table ${data.debug_info.table_id}`);
                }
            }
        })
        .catch(error => {
            console.error('Error in silent update:', error);
        });
}

// Enhanced page load function
document.addEventListener('DOMContentLoaded', function() {
    // Load cart if table is specified
    if ('<?php echo $table_id; ?>') {
        updateCart();

        // Initialize order status and start auto-refresh
        updateOrderStatusSilently();
        startStatusRefresh();
    }
});

// Clean up interval when page unloads
window.addEventListener('beforeunload', function() {
    if (statusRefreshInterval) {
        clearInterval(statusRefreshInterval);
    }
});

// Global variables for order status tracking
let orderStatusData = [];
let currentPage = 1;
const ordersPerPage = 3; // Hiển thị 3 đơn hàng mỗi trang để gọn gàng hơn

// Status mapping functions
function getStatusClass(status) {
    const statusClasses = {
        'pending': 'warning',
        'preparing': 'primary',
        'served': 'success',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return statusClasses[status] || 'secondary';
}

function getStatusText(status) {
    const statusTexts = {
        'pending': 'Chờ xử lý',
        'preparing': 'Đang chuẩn bị',
        'served': 'Đã phục vụ',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy'
    };
    return statusTexts[status] || 'Không xác định';
}

// Display order status function with pagination
function displayOrderStatus(orders) {
    const container = document.getElementById('orderStatusList');
    if (!container) return;

    // Update orders count badge
    const countBadge = document.getElementById('orders-count-badge');
    if (countBadge) {
        if (orders && orders.length > 0) {
            countBadge.textContent = orders.length;
            countBadge.style.display = 'inline';
        } else {
            countBadge.style.display = 'none';
        }
    }

    if (!orders || orders.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có đơn hàng nào</h5>
                <p class="text-muted mb-0">Đơn hàng sẽ hiển thị ở đây sau khi bạn gửi đơn</p>
            </div>
        `;
        return;
    }

    // Calculate pagination
    const totalPages = Math.ceil(orders.length / ordersPerPage);
    const startIndex = (currentPage - 1) * ordersPerPage;
    const endIndex = startIndex + ordersPerPage;
    const currentOrders = orders.slice(startIndex, endIndex);

    // Display orders for current page
    const ordersHtml = currentOrders.map(order => {
        const statusClass = getStatusClass(order.status);
        const statusText = getStatusText(order.status);
        const isPreparing = order.status === 'preparing';

        return `
            <div class="order-status-item border-bottom pb-3 mb-3 ${isPreparing ? 'pulse-animation' : ''}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">Đơn hàng #${order.id}</h6>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            ${order.created_at}
                        </small>
                    </div>
                    <span class="badge bg-${statusClass}">${statusText}</span>
                </div>

                ${order.items ? `
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">Món đã gọi:</small>
                        <ul class="order-items-list mb-0">
                            ${order.items.map(item => `
                                <li>
                                    <span class="fw-bold">${item.quantity}x</span> ${item.name}
                                    <span class="text-primary ms-2">${formatCurrency(item.price)} ₫</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        ${order.notes ? `
                            <i class="fas fa-sticky-note me-1"></i>
                            Ghi chú: ${order.notes}
                        ` : ''}
                    </small>
                    ${order.total !== null ? `
                        <div class="fw-bold text-end">
                            Tổng: <span class="text-primary">${formatCurrency(order.total)} ₫</span>
                        </div>
                    ` : `
                        <div class="text-muted text-end">
                            <small><i class="fas fa-info-circle me-1"></i>Chưa tạo hóa đơn</small>
                        </div>
                    `}
                </div>

                ${(order.has_invoice && (order.status === 'completed' || order.status === 'paid')) ? `
                    <div class="mt-2 text-end">
                        <button class="btn btn-outline-primary btn-sm" onclick="downloadInvoice(${order.id})" title="Tải hóa đơn">
                            <i class="fas fa-download me-1"></i>
                            Tải hóa đơn
                        </button>
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');

    // Create pagination controls - show if more than 3 orders
    const paginationHtml = totalPages > 1 ? `
        <div class="order-pagination-container mt-4 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Hiển thị ${startIndex + 1}-${Math.min(endIndex, orders.length)} trong ${orders.length} đơn hàng
                    </small>
                </div>
                <nav aria-label="Order pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <button class="page-link" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} title="Trang trước">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>
                        ${generatePageNumbers(currentPage, totalPages)}
                        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <button class="page-link" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} title="Trang sau">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    ` : '';

    container.innerHTML = ordersHtml + paginationHtml;
}

// Pagination helper functions
function generatePageNumbers(current, total) {
    let pages = '';
    const maxVisible = 5; // Maximum visible page numbers

    let start = Math.max(1, current - Math.floor(maxVisible / 2));
    let end = Math.min(total, start + maxVisible - 1);

    // Adjust start if we're near the end
    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
    }

    for (let i = start; i <= end; i++) {
        pages += `
            <li class="page-item ${i === current ? 'active' : ''}">
                <button class="page-link" onclick="changePage(${i})">${i}</button>
            </li>
        `;
    }

    return pages;
}

function changePage(page) {
    const totalPages = Math.ceil(orderStatusData.length / ordersPerPage);

    if (page < 1 || page > totalPages) return;

    currentPage = page;
    displayOrderStatus(orderStatusData);

    // Scroll to top of order status section
    const orderSection = document.getElementById('orderStatusSection');
    if (orderSection) {
        orderSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Download invoice function
function downloadInvoice(orderId) {
    // Show loading state
    showNotification('Đang chuẩn bị tải hóa đơn...', 'info');

    // Create a temporary link to trigger download
    const downloadUrl = `<?= SITE_URL ?>/customer_invoice_download.php?order_id=${orderId}`;

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Show success message after a short delay
    setTimeout(() => {
        showNotification('Hóa đơn đang được tải xuống...', 'success');
    }, 500);
}

// Refresh order status function
function refreshOrderStatus() {
    loadOrderStatus();
    showNotification('Đã làm mới thông tin đơn hàng', 'success');
}
</script>

<style>
.dine-in-container {
    min-height: 100vh;
    background-color: #f8f9fa;
}

.table-selection-container {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.table-info {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.table-info h3 {
    color: #2c3e50;
    font-weight: bold;
}

.table-info h4 {
    color: #34495e;
    font-size: 1.1rem;
    margin-bottom: 10px;
}

.table-info p {
    font-size: 1.2rem;
    font-weight: 500;
}

.badge {
    font-size: 0.9rem;
    padding: 5px 10px;
}

.menu-categories {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.category-btn {
    border-radius: 25px;
    padding: 8px 20px;
    margin: 5px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid #007bff;
}

.category-btn.active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.category-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.food-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    padding: 20px 0;
    position: relative;
    z-index: 2;
}

.food-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
}

.food-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.food-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.food-info {
    padding: 20px;
}

.food-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #2c3e50;
}

.food-description {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-bottom: 15px;
    line-height: 1.5;
}

.food-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.food-price {
    font-weight: bold;
    color: #e74c3c;
    font-size: 1.2rem;
}

.food-badges {
    display: flex;
    gap: 5px;
}

.badge-popular {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
}

.badge-new {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
}

.add-to-cart-btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 1rem;
}

.add-to-cart-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40,167,69,0.3);
}

.floating-cart-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 20px rgba(0,123,255,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000;
    transition: all 0.3s ease;
    font-size: 1.5rem;
}

.floating-cart-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(0,123,255,0.5);
}

.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    border: 3px solid white;
}

.cart-sidebar {
    position: fixed;
    top: 0;
    right: -450px;
    width: 450px;
    height: 100vh;
    background: white;
    box-shadow: -4px 0 20px rgba(0,0,0,0.15);
    transition: right 0.4s ease;
    z-index: 1001;
    display: flex;
    flex-direction: column;
}

.cart-sidebar.open {
    right: 0;
}

.cart-header {
    padding: 25px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.cart-items {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
    position: relative;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.cart-item-image {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

.cart-item-info {
    flex: 1;
}

.cart-item-info h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    color: #2c3e50;
}

.cart-item-price {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.cart-item-quantity {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cart-item-quantity button {
    width: 35px;
    height: 35px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.2s ease;
}

.cart-item-quantity button:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.cart-item-quantity span {
    font-weight: bold;
    min-width: 30px;
    text-align: center;
}

.cart-item-total {
    font-weight: bold;
    color: #e74c3c;
    margin-left: 15px;
    font-size: 1.1rem;
}

.cart-item-remove {
    background: none;
    border: none;
    color: #e74c3c;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
    margin-left: 10px;
}

.cart-item-remove:hover {
    background: #e74c3c;
    color: white;
    transform: scale(1.1);
}

.cart-item-remove:active {
    transform: scale(0.95);
}

.cart-footer {
    padding: 25px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.cart-footer h5 {
    color: #2c3e50;
    margin: 0;
}

.cart-footer button {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    color: white;
    padding: 15px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.cart-footer button:hover {
    background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40,167,69,0.3);
}

/* Buffet Section Styles */
.buffet-section {
    background: linear-gradient(135deg, #f8fff8 0%, #e8f8e8 100%);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid #d4edda;
    position: relative;
    overflow: hidden;
}

.buffet-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(40,167,69,0.1) 0%, transparent 70%);
    animation: buffetGlow 4s ease-in-out infinite alternate;
}

@keyframes buffetGlow {
    0% { transform: rotate(0deg) scale(1); }
    100% { transform: rotate(5deg) scale(1.05); }
}

.section-header {
    position: relative;
    z-index: 2;
}

.section-header h3 {
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 10px;
}

.buffet-card {
    position: relative;
    border: 2px solid #28a745;
    box-shadow: 0 8px 25px rgba(40,167,69,0.2);
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff 0%, #f8fff8 100%);
}

.buffet-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(40,167,69,0.3);
    border-color: #20c997;
}

.buffet-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 3;
}

.buffet-badge .badge {
    font-size: 0.8rem;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(40,167,69,0.3);
}

.buffet-btn {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    color: white;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 25px;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
    transition: all 0.3s ease;
}

.buffet-btn:hover {
    background: linear-gradient(45deg, #218838, #17a2b8);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
    color: white;
}

.regular-section {
    background: linear-gradient(135deg, #fff8f0 0%, #f0f8ff 100%);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid #e3f2fd;
}

.regular-card {
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.regular-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,123,255,0.2);
}

/* Category button enhancements */
.category-btn[data-category="buffet"] {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
}

.category-btn[data-category="buffet"]:hover {
    background: linear-gradient(45deg, #218838, #17a2b8);
    transform: translateY(-2px);
    color: white;
}

.category-btn[data-category="buffet"].active {
    background: linear-gradient(45deg, #218838, #17a2b8);
    border: none;
    color: white;
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
}

/* Food price styling for buffet */
.buffet-card .food-price {
    font-size: 1.2rem;
    font-weight: 700;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

/* Animation for section transitions */
.buffet-section, .regular-section {
    animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .buffet-section, .regular-section {
        padding: 20px;
        margin: 15px;
    }

    .section-header h3 {
        font-size: 1.5rem;
    }
}

/* Buffet item styling in cart */
.cart-item.buffet-item {
    border-left: 4px solid #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);
}

.cart-item.buffet-item .cart-item-info h4 {
    color: #28a745;
}

.cart-item.buffet-item .badge {
    font-size: 0.7rem;
    letter-spacing: 0.5px;
}

/* Cart item enhancements */
.cart-item {
    transition: all 0.3s ease;
    border-radius: 8px;
    margin-bottom: 10px;
    padding: 10px;
}

.cart-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>
