<?php require_once 'views/layouts/header.php'; ?>

<style>
.success-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 40px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    text-align: center;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4CAF50, #45a049);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    color: white;
    font-size: 40px;
}

.success-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}

.success-message {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 40px;
    line-height: 1.6;
}

.order-details {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 40px;
    text-align: left;
}

.order-details h3 {
    color: #333;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

.order-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-weight: bold;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.info-value {
    color: #333;
    font-size: 1.1rem;
}

.order-items {
    border-top: 1px solid #ddd;
    padding-top: 20px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.item-info {
    flex: 1;
}

.item-name {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.item-details {
    color: #666;
    font-size: 0.9rem;
}

.item-price {
    font-weight: bold;
    color: #e74c3c;
    font-size: 1.1rem;
}

.total-section {
    border-top: 2px solid #ddd;
    padding-top: 20px;
    margin-top: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
}

.action-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 15px 30px;
    border-radius: 10px;
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
    font-weight: bold;
    font-size: 0.9rem;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-preparing {
    background: #d1ecf1;
    color: #0c5460;
    }

.status-served {
    background: #d4edda;
    color: #155724;
    }

.status-completed {
    background: #c3e6cb;
    color: #155724;
}
</style>

<div class="success-container">
    <div class="success-icon">
        ✓
    </div>

    <h1 class="success-title">Đặt món thành công!</h1>
    <p class="success-message">
        Đơn hàng của bạn đã được gửi đến nhà bếp. Chúng tôi sẽ bắt đầu chế biến ngay lập tức.
    </p>

    <div class="order-details">
        <h3>Chi tiết đơn hàng</h3>

        <div class="order-info">
            <div class="info-item">
                <span class="info-label">Mã đơn hàng</span>
                <span class="info-value">#<?php echo $order['id']; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Bàn</span>
                <span class="info-value">Bàn <?php echo $order['table_number']; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Trạng thái</span>
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php
                    $statusLabels = [
                        'pending' => 'Chờ xử lý',
                        'preparing' => 'Đang chế biến',
                        'served' => 'Đã phục vụ',
                        'completed' => 'Hoàn thành'
                    ];
                    echo $statusLabels[$order['status']] ?? $order['status'];
                    ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Thời gian đặt</span>
                <span class="info-value"><?php echo date('H:i', strtotime($order['created_at'])); ?></span>
            </div>
        </div>

        <div class="order-items">
            <h4>Món đã đặt:</h4>
            <?php foreach ($order['items'] as $item): ?>
            <div class="order-item">
                <div class="item-info">
                    <div class="item-name"><?php echo htmlspecialchars($item['food_name']); ?></div>
                    <div class="item-details">
                        Số lượng: <?php echo $item['quantity']; ?>
                    <?php if (!empty($item['special_instructions'])): ?>
                            <br>Ghi chú: <?php echo htmlspecialchars($item['special_instructions']); ?>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="item-price">
                    <?php echo number_format($item['total_price']); ?> VNĐ
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Tổng cộng:</span>
                <span><?php echo number_format($order['total_amount']); ?> VNĐ</span>
            </div>
        </div>

        <?php if (!empty($order['special_notes'])): ?>
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 10px; border-left: 4px solid #ffc107;">
            <strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['special_notes']); ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="action-buttons">
        <a href="<?php echo SITE_URL; ?>/dine-in/track-order?order_id=<?php echo $order['id']; ?>" class="btn btn-primary">
            Theo dõi đơn hàng
        </a>
        <a href="<?php echo SITE_URL; ?>/dine-in?table_id=<?php echo $order['table_id']; ?>" class="btn btn-secondary">
            Đặt thêm món
        </a>
        <a href="<?php echo SITE_URL; ?>/" class="btn btn-secondary">
            Về trang chủ
        </a>
    </div>
</div>

<script>
// Auto refresh để cập nhật trạng thái đơn hàng
setInterval(function() {
    fetch('<?php echo SITE_URL; ?>/dine-in/get-order-status?order_id=<?php echo $order['id']; ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status && data.status !== '<?php echo $order['status']; ?>') {
                location.reload();
            }
        })
        .catch(error => console.log('Error checking order status:', error));
}, 10000); // Kiểm tra mỗi 10 giây
</script>

<?php require_once 'views/layouts/footer.php'; ?>
