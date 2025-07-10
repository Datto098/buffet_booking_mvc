<?php require_once 'views/layouts/header.php'; ?>

<style>
.qr-container {
    max-width: 600px;
    margin: 50px auto;
    text-align: center;
    padding: 20px;
}

.qr-header {
    margin-bottom: 40px;
}

.qr-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.qr-subtitle {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
}

.qr-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    padding: 40px;
    margin-bottom: 30px;
}

.qr-code {
    margin-bottom: 30px;
}

.qr-code img {
    max-width: 300px;
    width: 100%;
    height: auto;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.qr-info {
    margin-bottom: 30px;
}

.table-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
}

.table-info h3 {
    margin: 0 0 10px 0;
    font-size: 1.5rem;
}

.table-info p {
    margin: 5px 0;
    opacity: 0.9;
}

.instructions {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 15px;
    text-align: left;
    margin-bottom: 20px;
}

.instructions h4 {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.2rem;
}

.instruction-steps {
    list-style: none;
    padding: 0;
}

.instruction-steps li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 15px;
}

.instruction-steps li:last-child {
    border-bottom: none;
}

.step-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.step-text {
    flex: 1;
    color: #666;
}

.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.feature-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
}

.feature-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.feature-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.feature-description {
    color: #666;
    font-size: 0.9rem;
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

.download-section {
    background: #e8f5e8;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.download-section h4 {
    color: #28a745;
    margin-bottom: 15px;
}

.download-links {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.download-link {
    background: #28a745;
    color: white;
    padding: 10px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.download-link:hover {
    background: #218838;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .qr-container {
        margin: 20px auto;
        padding: 15px;
    }

    .qr-title {
        font-size: 2rem;
    }

    .qr-card {
        padding: 20px;
    }

    .features {
        grid-template-columns: 1fr;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

    .download-links {
        flex-direction: column;
    }

    .download-link {
        text-align: center;
    }
}

@media print {
    .action-buttons,
    .download-section {
        display: none;
    }

    .qr-container {
        margin: 0;
        padding: 20px;
    }

    .qr-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<div class="qr-container">
    <div class="qr-header">
        <h1 class="qr-title">📱 Menu QR Code</h1>
        <p class="qr-subtitle">
            Quét mã QR bên dưới để truy cập menu và đặt món trực tiếp từ điện thoại của bạn
        </p>
    </div>

    <div class="qr-card">
        <div class="qr-code">
            <img src="<?= $qrCodeUrl ?>" alt="QR Code Menu" id="qrCodeImage">
        </div>

        <div class="qr-info">
            <div class="table-info">
                <h3>Bàn <?= htmlspecialchars($table['table_number']) ?></h3>
                <p>Sức chứa: <?= htmlspecialchars($table['capacity']) ?> người</p>
                <p>Vị trí: <?= htmlspecialchars($table['location']) ?></p>
            </div>

            <div class="instructions">
                <h4>📋 Hướng dẫn sử dụng</h4>
                <ol class="instruction-steps">
                    <li>
                        <div class="step-number">1</div>
                        <div class="step-text">Mở ứng dụng camera hoặc QR scanner trên điện thoại</div>
                    </li>
                    <li>
                        <div class="step-number">2</div>
                        <div class="step-text">Quét mã QR bên trên</div>
                    </li>
                    <li>
                        <div class="step-number">3</div>
                        <div class="step-text">Truy cập menu và chọn món ăn yêu thích</div>
                    </li>
                    <li>
                        <div class="step-number">4</div>
                        <div class="step-text">Thêm vào giỏ hàng và đặt món</div>
                    </li>
                    <li>
                        <div class="step-number">5</div>
                        <div class="step-text">Theo dõi trạng thái đơn hàng realtime</div>
                    </li>
                </ol>
            </div>
        </div>

        <div class="features">
            <div class="feature-item">
                <div class="feature-icon">🍽️</div>
                <div class="feature-title">Menu đầy đủ</div>
                <div class="feature-description">Xem tất cả món ăn với hình ảnh và mô tả chi tiết</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">⚡</div>
                <div class="feature-title">Đặt món nhanh</div>
                <div class="feature-description">Đặt món chỉ với vài thao tác đơn giản</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">📱</div>
                <div class="feature-title">Theo dõi realtime</div>
                <div class="feature-description">Cập nhật trạng thái đơn hàng theo thời gian thực</div>
            </div>
        </div>
    </div>

    <div class="download-section">
        <h4>💾 Tải xuống QR Code</h4>
        <p style="margin-bottom: 15px; color: #666;">
            Tải xuống QR code để in và dán tại bàn hoặc chia sẻ với khách hàng
        </p>
        <div class="download-links">
            <a href="<?= $qrCodeUrl ?>" download="menu-qr-code.png" class="download-link">
                📥 Tải PNG
            </a>
            <a href="<?= $qrCodeUrl ?>" download="menu-qr-code.jpg" class="download-link">
                📥 Tải JPG
            </a>
            <button onclick="printQRCode()" class="download-link" style="border: none; cursor: pointer;">
                🖨️ In QR Code
            </button>
        </div>
    </div>

    <div class="action-buttons">
        <a href="<?= SITE_URL ?>/dine-in?table_id=<?= $table['id'] ?>" class="btn btn-primary">
            🍽️ Xem menu trực tiếp
        </a>
        <a href="<?= SITE_URL ?>/admin/dine-in-orders" class="btn btn-secondary">
            📊 Quản lý đơn hàng
        </a>
    </div>
</div>

<script>
function printQRCode() {
    window.print();
}

// Auto refresh QR code mỗi 5 phút để đảm bảo tính bảo mật
setInterval(() => {
    const img = document.getElementById('qrCodeImage');
    if (img) {
        img.src = img.src + '?t=' + new Date().getTime();
    }
}, 300000); // 5 phút

// Copy URL to clipboard
function copyMenuUrl() {
    const menuUrl = '<?= SITE_URL ?>/dine-in?table_id=<?= $table['id'] ?>';
    navigator.clipboard.writeText(menuUrl).then(() => {
        alert('Đã sao chép link menu vào clipboard!');
    });
}

// Generate QR code with different sizes
function generateQRCode(size = 'medium') {
    const sizes = {
        small: 200,
        medium: 300,
        large: 400
    };

    const img = document.getElementById('qrCodeImage');
    if (img) {
        img.style.maxWidth = sizes[size] + 'px';
    }
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'p':
                e.preventDefault();
                printQRCode();
                break;
            case 'c':
                e.preventDefault();
                copyMenuUrl();
                break;
        }
    }
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>
