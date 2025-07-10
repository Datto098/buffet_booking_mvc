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
                            <button class="btn btn-outline-primary" onclick="toggleOrderStatus()">
                                <i class="fas fa-list-alt me-2"></i>Tình trạng đơn hàng
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Section -->
            <div class="order-status-section mb-4" id="orderStatusSection" style="display: none;">
                <div class="container">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Tình trạng đơn hàng gần đây
                            </h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="refreshOrderStatus()">
                                <i class="fas fa-sync-alt me-1"></i>Làm mới
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="orderStatusList">
                                <div class="text-center py-3">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">Đang tải...</p>
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
                        <button class="btn btn-outline-primary m-1 category-btn active" data-category="all">
                            Tất cả
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
                <div class="menu-section">
                    <div class="food-grid">
                        <?php foreach ($foods as $food): ?>
                        <div class="food-card" data-category="<?php echo $food['category_id']; ?>">
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
    button.innerHTML = '<i class="fas fa-plus me-2"></i>Thêm vào giỏ';
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
                    cartItems.innerHTML += `
                        <div class="cart-item">
                            <img src="<?php echo SITE_URL; ?>/uploads/food_images/${item.image}" alt="${item.name}" class="cart-item-image">
                            <div class="cart-item-info">
                                <h4>${item.name}</h4>
                                <div class="cart-item-price">${formatCurrency(item.price)} ₫</div>
                                <div class="cart-item-quantity">
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})"
                                            ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                                    <span>${item.quantity}</span>
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                                </div>
                            </div>
                            <div class="cart-item-total">${formatCurrency(item.total)} ₫</div>
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

        document.querySelectorAll('.food-card').forEach(card => {
            if (categoryId === 'all' || card.dataset.category === categoryId) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease';
            } else {
                card.style.display = 'none';
            }
        });

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

// Load order status
function loadOrderStatus() {
    const tableId = '<?php echo $table_id; ?>';
    const orderStatusList = document.getElementById('orderStatusList');

    fetch(`<?php echo SITE_URL; ?>/dine-in/get-order-status?table_id=${tableId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.orders.length === 0) {
                orderStatusList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có đơn hàng nào</h6>
                        <p class="text-muted small">Đơn hàng sẽ hiển thị ở đây sau khi bạn gửi</p>
                    </div>
                `;
            } else {
                let html = '';
                data.orders.forEach(order => {
                    const statusClass = getStatusClass(order.status);
                    const statusText = getStatusText(order.status);
                    const createdAt = new Date(order.created_at).toLocaleString('vi-VN');

                    let itemsHtml = '';
                    if (order.items && order.items.length > 0) {
                        itemsHtml = '<ul class="order-items-list mb-1">';
                        order.items.forEach(item => {
                            itemsHtml += `<li><span class='fw-bold'>${item.food_name}</span> x${item.quantity}</li>`;
                        });
                        itemsHtml += '</ul>';
                    } else {
                        itemsHtml = '<div class="text-muted small">(Chưa có món nào)</div>';
                    }

                    html += `
                        <div class="order-status-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Đơn hàng #${order.id}</h6>
                                    <p class="text-muted small mb-1">Tạo lúc: ${createdAt}</p>
                                    ${order.notes ? `<p class="text-muted small mb-1">Ghi chú: ${order.notes}</p>` : ''}
                                    ${itemsHtml}
                                </div>
                                <div class="text-end">
                                    <span class="badge ${statusClass} mb-1">${statusText}</span>
                                    <div class="text-primary fw-bold">${formatCurrency(order.total_amount)} ₫</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                orderStatusList.innerHTML = html;
            }
        } else {
            orderStatusList.innerHTML = `
                <div class="text-center py-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <p class="text-muted">Không thể tải tình trạng đơn hàng</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading order status:', error);
        orderStatusList.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                <p class="text-muted">Lỗi khi tải tình trạng đơn hàng</p>
            </div>
        `;
    });
}

// Refresh order status
function refreshOrderStatus() {
    loadOrderStatus();
}

// Get status class for badge
function getStatusClass(status) {
    switch(status) {
        case 'pending': return 'bg-warning';
        case 'preparing': return 'bg-info';
        case 'ready': return 'bg-success';
        case 'served': return 'bg-primary';
        case 'completed': return 'bg-secondary';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

// Get status text
function getStatusText(status) {
    switch(status) {
        case 'pending': return 'Chờ xử lý';
        case 'preparing': return 'Đang chế biến';
        case 'ready': return 'Sẵn sàng';
        case 'served': return 'Đã phục vụ';
        case 'completed': return 'Hoàn thành';
        case 'cancelled': return 'Đã hủy';
        default: return status;
    }
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
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
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

/* Responsive Design */
@media (max-width: 768px) {
    .cart-sidebar {
        width: 100%;
        right: -100%;
    }

    .food-grid {
        grid-template-columns: 1fr;
        padding: 10px;
    }

    .table-info {
        margin: 10px;
        padding: 15px;
    }

    .table-info .row > div {
        text-align: center !important;
        margin-bottom: 15px;
    }

    .menu-categories {
        margin: 10px;
        padding: 15px;
    }

    .category-btn {
        margin: 3px;
        padding: 6px 15px;
        font-size: 0.9rem;
    }

    .floating-cart-btn {
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
    }
}

@media (max-width: 576px) {
    .food-card {
        margin: 0 10px;
    }

    .cart-header {
        padding: 20px;
    }

    .cart-header h3 {
        font-size: 1.1rem;
    }
}

/* Animation cho loading */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #007bff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Thêm animations */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.notification-toast {
    animation: slideIn 0.3s ease;
}

.empty-cart {
    animation: fadeIn 0.5s ease;
}

.food-card {
    animation: fadeIn 0.5s ease;
}

.cart-item {
    animation: fadeIn 0.3s ease;
}

.floating-cart-btn.pulse {
    animation: pulse 2s infinite;
}

/* Order Status Styles */
.order-status-section .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border-radius: 15px;
}

.order-status-section .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: 15px 15px 0 0;
}

.order-status-item {
    transition: all 0.3s ease;
    padding: 15px 0;
}

.order-status-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    /* margin: -15px 0; */
}

.order-status-item:last-child {
    border-bottom: none !important;
}

.order-status-item h6 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 5px;
}

.order-status-item .text-muted {
    font-size: 0.85rem;
}

.order-status-item .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.order-status-item .fw-bold {
    font-size: 1.1rem;
}
</style>
