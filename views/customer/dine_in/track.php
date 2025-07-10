<?php require_once 'views/layouts/header.php'; ?>

<style>
.track-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 40px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.order-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f8f9fa;
}

.order-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.order-subtitle {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 20px;
}

.order-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.info-item {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 15px;
}

.info-label {
    font-weight: bold;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.info-value {
    color: #333;
    font-size: 1.3rem;
    font-weight: bold;
}

.status-timeline {
    margin-bottom: 40px;
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
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 30px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -15px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #e9ecef;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.active::before {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-item.completed::before {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-content {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 20px;
    transition: all 0.3s ease;
}

.timeline-item.active .timeline-content {
    border-color: #28a745;
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
}

.timeline-item.completed .timeline-content {
    border-color: #28a745;
    background: #f8fff9;
}

.timeline-status {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 8px;
}

.timeline-item.active .timeline-status {
    color: #28a745;
}

.timeline-item.completed .timeline-status {
    color: #28a745;
}

.timeline-description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.timeline-time {
    color: #888;
    font-size: 0.8rem;
}

.order-items {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
}

.items-title {
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.item-list {
    display: grid;
    gap: 15px;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
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

.item-status {
    text-align: right;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
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
    color: white;
    text-decoration: none;
}

.btn-secondary {
    background: #f8f9fa;
    color: #333;
    border: 2px solid #ddd;
}

.btn-secondary:hover {
    background: #e9ecef;
    border-color: #adb5bd;
    color: #333;
    text-decoration: none;
}

.estimated-time {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 30px;
    text-align: center;
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

@media (max-width: 768px) {
    .track-container {
        margin: 20px auto;
        padding: 20px;
    }

    .order-title {
        font-size: 2rem;
    }

    .order-info {
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
    <div class="order-header">
        <h1 class="order-title">📱 Theo dõi đơn hàng</h1>
        <p class="order-subtitle">Đơn hàng #<?php echo $order['id']; ?> - Bàn <?php echo $order['table_number']; ?></p>
    </div>

    <div class="order-info">
        <div class="info-item">
            <div class="info-label">Mã đơn hàng</div>
            <div class="info-value">#<?php echo $order['id']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Bàn</div>
            <div class="info-value">Bàn <?php echo $order['table_number']; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Thời gian đặt</div>
            <div class="info-value"><?php echo date('H:i', strtotime($order['created_at'])); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Tổng tiền</div>
            <div class="info-value"><?php echo number_format($order['total_amount']); ?> VNĐ</div>
        </div>
    </div>

    <?php
    $statusOrder = ['pending', 'preparing', 'served', 'completed'];
    $currentStatusIndex = array_search($order['status'], $statusOrder);
    $statusLabels = [
        'pending' => 'Chờ xử lý',
        'preparing' => 'Đang chế biến',
        'served' => 'Đã phục vụ',
        'completed' => 'Hoàn thành'
    ];
    $statusDescriptions = [
        'pending' => 'Đơn hàng đã được nhận và đang chờ xử lý',
        'preparing' => 'Nhà bếp đang chế biến món ăn của bạn',
        'served' => 'Món ăn đã được phục vụ tại bàn',
        'completed' => 'Đơn hàng đã hoàn thành'
    ];
    ?>

    <div class="status-timeline">
        <h3 style="text-align: center; margin-bottom: 30px; color: #333;">Trạng thái đơn hàng</h3>

        <div class="timeline">
            <?php foreach ($statusOrder as $index => $status): ?>
            <div class="timeline-item <?php
                if ($index < $currentStatusIndex) echo 'completed';
                elseif ($index == $currentStatusIndex) echo 'active';
            ?>">
                <div class="timeline-content">
                    <div class="timeline-status"><?php echo $statusLabels[$status]; ?></div>
                    <div class="timeline-description"><?php echo $statusDescriptions[$status]; ?></div>
                    <?php if ($index <= $currentStatusIndex): ?>
                    <div class="timeline-time"><?php echo date('H:i', strtotime($order['updated_at'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($order['status'] == 'preparing'): ?>
    <div class="estimated-time">
        <h4>⏰ Thời gian dự kiến</h4>
        <p>Món ăn sẽ được phục vụ trong khoảng 10-15 phút</p>
    </div>
    <?php endif; ?>

    <div class="order-items">
        <h3 class="items-title">🍽️ Món đã đặt</h3>
        <div class="item-list">
            <?php foreach ($order['items'] as $item): ?>
            <div class="order-item">
                <?php if (!empty($item['image'])): ?>
                <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="item-image">
                <?php endif; ?>
                <div class="item-info">
                    <div class="item-name"><?php echo htmlspecialchars($item['food_name']); ?></div>
                    <div class="item-details">
                        Số lượng: <?php echo $item['quantity']; ?>
                        <?php if (!empty($item['special_instructions'])): ?>
                            <br>Ghi chú: <?php echo htmlspecialchars($item['special_instructions']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="item-status">
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo $statusLabels[$order['status']]; ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="action-buttons">
        <a href="<?php echo SITE_URL; ?>/dine-in?table_id=<?php echo $order['table_id']; ?>" class="btn btn-primary">
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
}, 5000); // Kiểm tra mỗi 10 giây

// Hiệu ứng loading cho timeline
document.addEventListener('DOMContentLoaded', function() {
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, index * 200);
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>
