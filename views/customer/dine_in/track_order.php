<?php require_once 'views/layouts/header.php'; ?>

<style>
.track-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
}

.track-header {
    text-align: center;
    margin-bottom: 40px;
}

.track-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.track-subtitle {
    color: #666;
    font-size: 1.1rem;
}

.order-info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 30px;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.order-info-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.order-info-label {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 5px;
}

.order-info-value {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
}

.status-timeline {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 30px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #ddd;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -7px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #ddd;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #ddd;
}

.timeline-item.completed::before {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-item.current::before {
    background: #ffc107;
    box-shadow: 0 0 0 2px #ffc107;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

.timeline-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid #ddd;
}

.timeline-item.completed .timeline-content {
    border-left-color: #28a745;
    background: #d4edda;
}

.timeline-item.current .timeline-content {
    border-left-color: #ffc107;
    background: #fff3cd;
}

.timeline-title {
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.timeline-time {
    font-size: 0.9rem;
    color: #666;
}

.timeline-description {
    margin-top: 10px;
    color: #666;
    font-size: 0.9rem;
}

.order-items-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 30px;
}

.order-items-title {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
    text-align: center;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
    gap: 15px;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.item-info {
    flex: 1;
}

.item-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.item-price {
    font-size: 0.9rem;
    color: #28a745;
    font-weight: 500;
    margin-bottom: 3px;
}

.item-special {
    font-size: 0.8rem;
    color: #666;
    font-style: italic;
}

.item-right {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
}

.item-quantity {
    font-weight: bold;
    color: #e74c3c;
    font-size: 0.9rem;
}

.item-total {
    font-weight: bold;
    color: #2c3e50;
    font-size: 1.1rem;
}

.order-summary {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 2px solid #e9ecef;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 10px;
}

.summary-label {
    font-weight: 600;
    color: #495057;
    font-size: 1.1rem;
}

.summary-value {
    font-weight: bold;
    color: #28a745;
    font-size: 1.3rem;
}

.estimated-time {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    margin-bottom: 30px;
}

.estimated-time h4 {
    margin: 0 0 10px 0;
    font-size: 1.3rem;
}

.estimated-time p {
    margin: 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 15px 30px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #f8f9fa;
    color: #333;
    border: 2px solid #ddd;
}

.btn-secondary:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending {
    background: #ffeaa7;
    color: #d63031;
}

.status-preparing {
    background: #74b9ff;
    color: white;
}

.status-served {
    background: #00b894;
    color: white;
}

.status-completed {
    background: #6c5ce7;
    color: white;
}

.refresh-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 999;
    font-size: 1.2rem;
}

.refresh-btn:hover {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .track-container {
        margin: 20px auto;
        padding: 15px;
    }

    .track-title {
        font-size: 2rem;
    }

    .order-info-grid {
        grid-template-columns: 1fr;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>

<div class="track-container">
    <div class="track-header">
        <h1 class="track-title">📱 Theo dõi đơn hàng</h1>
        <p class="track-subtitle">Đơn hàng #<?= $order['id'] ?></p>
    </div>

    <!-- Thông tin đơn hàng -->
    <div class="order-info-card">
        <div class="order-info-grid">
            <div class="order-info-item">
                <div class="order-info-label">Bàn</div>
                <div class="order-info-value"><?= htmlspecialchars($table['table_number']) ?></div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Thời gian đặt</div>
                <div class="order-info-value"><?= date('H:i', strtotime($order['created_at'])) ?></div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Tổng tiền</div>
                <div class="order-info-value"><?= number_format($order['total_amount']) ?>đ</div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Trạng thái</div>
                <div class="order-info-value">
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline trạng thái -->
    <div class="status-timeline">
        <h3 style="text-align: center; margin-bottom: 30px; color: #333;">📋 Tiến trình đơn hàng</h3>

        <div class="timeline">
            <!-- Đã đặt -->
            <div class="timeline-item completed">
                <div class="timeline-content">
                    <div class="timeline-title">✅ Đã đặt món</div>
                    <div class="timeline-time"><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></div>
                    <div class="timeline-description">Đơn hàng đã được tiếp nhận và đang được xử lý</div>
                </div>
            </div>

            <!-- Đang chuẩn bị -->
            <div class="timeline-item <?= in_array($order['status'], ['preparing', 'served', 'completed']) ? 'completed' : ($order['status'] == 'pending' ? 'current' : '') ?>">
                <div class="timeline-content">
                    <div class="timeline-title">👨‍🍳 Đang chuẩn bị</div>
                    <div class="timeline-time">
                        <?= in_array($order['status'], ['preparing', 'served', 'completed']) ? date('H:i d/m/Y', strtotime($order['updated_at'])) : 'Đang chờ...' ?>
                    </div>
                    <div class="timeline-description">Đầu bếp đang chuẩn bị món ăn của bạn</div>
                </div>
            </div>

            <!-- Đã phục vụ -->
            <div class="timeline-item <?= in_array($order['status'], ['served', 'completed']) ? 'completed' : ($order['status'] == 'preparing' ? 'current' : '') ?>">
                <div class="timeline-content">
                    <div class="timeline-title">🍽️ Đã phục vụ</div>
                    <div class="timeline-time">
                        <?= in_array($order['status'], ['served', 'completed']) ? date('H:i d/m/Y', strtotime($order['updated_at'])) : 'Đang chờ...' ?>
                    </div>
                    <div class="timeline-description">Món ăn đã được phục vụ tại bàn</div>
                </div>
            </div>

            <!-- Hoàn thành -->
            <div class="timeline-item <?= $order['status'] == 'completed' ? 'completed' : '' ?>">
                <div class="timeline-content">
                    <div class="timeline-title">🎉 Hoàn thành</div>
                    <div class="timeline-time">
                        <?= $order['status'] == 'completed' ? date('H:i d/m/Y', strtotime($order['updated_at'])) : 'Đang chờ...' ?>
                    </div>
                    <div class="timeline-description">Đơn hàng đã hoàn thành</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thời gian dự kiến -->
    <div class="estimated-time">
        <h4>⏰ Thời gian dự kiến</h4>
        <?php
        $estimatedTime = '';
        switch($order['status']) {
            case 'pending':
                $estimatedTime = 'Món ăn sẽ được chuẩn bị trong 15-20 phút';
                break;
            case 'preparing':
                $estimatedTime = 'Món ăn sẽ được phục vụ trong 5-10 phút';
                break;
            case 'served':
                $estimatedTime = 'Món ăn đã được phục vụ. Chúc bạn ngon miệng!';
                break;
            case 'completed':
                $estimatedTime = 'Đơn hàng đã hoàn thành. Cảm ơn bạn!';
                break;
        }
        ?>
        <p><?= $estimatedTime ?></p>
    </div>

    <!-- Chi tiết món -->
    <div class="order-items-card">
        <h3 class="order-items-title">🍽️ Chi tiết món đã đặt</h3>

        <?php foreach ($orderItems as $item): ?>
        <div class="order-item">
            <img src="<?= SITE_URL ?>/<?= $item['food_image'] ?>" alt="<?= htmlspecialchars($item['food_name']) ?>" class="item-image">
            <div class="item-info">
                <div class="item-name"><?= htmlspecialchars($item['food_name']) ?></div>
                <div class="item-price"><?= number_format($item['price']) ?>đ / món</div>
                <?php if (!empty($item['special_instructions'])): ?>
                <div class="item-special"><?= htmlspecialchars($item['special_instructions']) ?></div>
                <?php endif; ?>
            </div>
            <div class="item-right">
                <div class="item-quantity">x<?= $item['quantity'] ?></div>
                <div class="item-total"><?= number_format($item['total']) ?>đ</div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Tổng kết đơn hàng -->
        <div class="order-summary">
            <div class="summary-row">
                <span class="summary-label">Tổng tiền:</span>
                <span class="summary-value"><?= number_format($order['total_amount']) ?>đ</span>
            </div>
        </div>

        <?php if (!empty($order['special_notes'])): ?>
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
            <strong>📝 Ghi chú đặc biệt:</strong>
            <p style="margin: 5px 0 0 0;"><?= htmlspecialchars($order['special_notes']) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Nút hành động -->
    <div class="action-buttons">
        <a href="<?= SITE_URL ?>/dine-in?table_id=<?= $table['id'] ?>" class="btn btn-primary">
            🍽️ Đặt thêm món
        </a>
        <a href="<?= SITE_URL ?>/" class="btn btn-secondary">
            🏠 Về trang chủ
        </a>
    </div>
</div>

<!-- Nút refresh -->
<button class="refresh-btn" onclick="refreshOrderStatus()" title="Làm mới trạng thái">
    🔄
</button>

<script>
let refreshInterval;

// Auto refresh mỗi 30 giây
function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        refreshOrderStatus();
    }, 30000);
}

function refreshOrderStatus() {
    fetch('<?= SITE_URL ?>/dine-in/get-order-status?order_id=<?= $order['id'] ?>')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật trạng thái nếu có thay đổi
            if (data.status !== '<?= $order['status'] ?>') {
                window.location.reload();
            }
        }
    })
    .catch(error => {
        console.error('Error refreshing order status:', error);
    });
}

// Bắt đầu auto refresh khi trang load
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Dừng auto refresh khi rời trang
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

// Manual refresh
function refreshOrderStatus() {
    window.location.reload();
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
