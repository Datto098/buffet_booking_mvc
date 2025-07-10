<style>
.select-table-container {
    max-width: 1000px;
    margin: 50px auto;
    padding: 40px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.page-subtitle {
    font-size: 1.2rem;
    color: #666;
    line-height: 1.6;
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.table-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.table-card:hover {
    transform: translateY(-5px);
    border-color: #667eea;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
}

.table-card.available {
    border-color: #28a745;
}

.table-card.available:hover {
    border-color: #20c997;
    box-shadow: 0 10px 25px rgba(40, 167, 69, 0.2);
}

.table-card.occupied {
    border-color: #dc3545;
    opacity: 0.7;
    cursor: not-allowed;
}

.table-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.table-capacity {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 15px;
}

.table-location {
    font-size: 0.9rem;
    color: #888;
    margin-bottom: 20px;
}

.table-status {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.status-available {
    background: #d4edda;
    color: #155724;
}

.status-occupied {
    background: #f8d7da;
    color: #721c24;
}

.select-table-btn {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.select-table-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.select-table-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.table-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    text-align: center;
}

.info-number {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 5px;
}

.info-label {
    color: #666;
    font-size: 0.9rem;
}

.qr-section {
    text-align: center;
    margin-top: 40px;
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
}

.qr-section h3 {
    margin-bottom: 15px;
    font-size: 1.5rem;
}

.qr-section p {
    margin-bottom: 20px;
    opacity: 0.9;
}

.qr-code {
    background: white;
    padding: 20px;
    border-radius: 10px;
    display: inline-block;
    margin-bottom: 20px;
}

.qr-code img {
    width: 150px;
    height: 150px;
}

@media (max-width: 768px) {
    .select-table-container {
        margin: 20px auto;
        padding: 20px;
    }

    .page-title {
        font-size: 2rem;
    }

    .tables-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .table-card {
        padding: 20px;
    }

    .table-number {
        font-size: 2rem;
    }
}
</style>

<div class="select-table-container" style="margin-top: 100px">
    <div class="page-header">
        <h1 class="page-title">Chọn bàn để đặt món</h1>
        <p class="page-subtitle">
            Vui lòng chọn bàn phù hợp để bắt đầu đặt món. Bạn có thể quét mã QR trên bàn hoặc chọn bàn từ danh sách bên dưới.
        </p>
    </div>

    <div class="table-info">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-number"><?php echo count($tables); ?></div>
                <div class="info-label">Tổng số bàn</div>
            </div>
            <div class="info-item">
                <div class="info-number"><?php echo count(array_filter($tables, function($table) { return $table['is_available'] == 1; })); ?></div>
                <div class="info-label">Bàn có sẵn</div>
            </div>
            <div class="info-item">
                <div class="info-number"><?php echo count(array_filter($tables, function($table) { return $table['is_available'] == 0; })); ?></div>
                <div class="info-label">Bàn đã đặt</div>
            </div>
        </div>
    </div>

    <div class="tables-grid">
        <?php foreach ($tables as $table): ?>
        <div class="table-card <?php echo $table['is_available'] ? 'available' : 'occupied'; ?>">
            <div class="table-number">Bàn <?php echo htmlspecialchars($table['table_number']); ?></div>
            <div class="table-capacity">Sức chứa: <?php echo $table['capacity']; ?> người</div>
            <?php if (!empty($table['location'])): ?>
            <div class="table-location"><?php echo htmlspecialchars($table['location']); ?></div>
            <?php endif; ?>

            <div class="table-status <?php echo $table['is_available'] ? 'status-available' : 'status-occupied'; ?>">
                <?php echo $table['is_available'] ? 'Có sẵn' : 'Đã đặt'; ?>
            </div>

            <?php if ($table['is_available']): ?>
            <a href="<?php echo SITE_URL; ?>/dine-in?table=<?php echo $table['table_number']; ?>" class="select-table-btn">
                Chọn bàn này
            </a>
            <?php else: ?>
            <button class="select-table-btn" disabled>
                Bàn đã đặt
            </button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- <div class="qr-section">
        <h3>📱 Quét mã QR để đặt món</h3>
        <p>
            Mỗi bàn đều có mã QR riêng. Quét mã QR trên bàn để truy cập menu và đặt món trực tiếp.
        </p>
        <div class="qr-code">
            <img src="data:image/svg+xml;base64,<?php echo base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 150 150"><rect width="150" height="150" fill="white"/><text x="75" y="75" text-anchor="middle" dy=".3em" font-family="Arial" font-size="12" fill="black">QR Code</text></svg>'); ?>" alt="QR Code Example">
        </div>
        <p style="font-size: 0.9rem; opacity: 0.8;">
            Sử dụng camera điện thoại để quét mã QR trên bàn
        </p>
    </div> -->
</div>

<script>
// Auto refresh để cập nhật trạng thái bàn
setInterval(function() {
    location.reload();
}, 30000); // Refresh mỗi 30 giây

// Thêm hiệu ứng click cho table card
document.querySelectorAll('.table-card.available').forEach(card => {
    card.addEventListener('click', function() {
        const link = this.querySelector('a');
        if (link) {
            link.click();
        }
    });
});
</script>
