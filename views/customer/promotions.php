<?php
// Header will be included by BaseController loadView method
// require_once 'views/customer/layouts/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <!-- <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center" style="background: rgba(255,255,255,0.1); border-radius: 30px; padding: 0.75rem 1.5rem;">
                    <li class="breadcrumb-item">
                        <a href="<?= SITE_URL ?>" style="color: rgba(255,255,255,0.8);">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-gold);">Khuyến Mãi</li>
                </ol>
            </nav> -->
            <h1 class="hero-title " >
                <span  style="color: #fff">Ưu Đãi</span> <br>
                <span class="text-white">Độc Quyền</span>
            </h1>
            <p class="hero-subtitle banner-subtitle" style="color: #fff">
                Khám phá những món ăn tinh tế với mức giá ưu đãi đặc biệt.
                Thưởng thức ẩm thực đẳng cấp mà không cần lo về chi phí.
            </p>
            <div class="hero-cta banner-cta">
                <div class="d-flex align-items-center justify-content-center gap-4 flex-wrap">
                    <div class="promotion-highlight">
                        <div class="bg-white rounded-pill px-4 py-2 shadow">
                            <span class="text-danger fw-bold fs-5">
                                <i class="fas fa-fire text-warning"></i> Giảm đến 50%
                            </span>
                        </div>
                    </div>
                    <div class="promotion-highlight">
                        <div class="bg-white rounded-pill px-4 py-2 shadow">
                            <span class="text-primary fw-bold fs-6">
                                <i class="fas fa-clock text-warning"></i> Thời gian có hạn
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Special Offers Banner -->
<section class="section-luxury bg-luxury">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="text-gold mb-3 banner-title">🔥 Chương Trình Khuyến Mãi Đặc Biệt</h2>
                <p class="mb-4 banner-subtitle">
                    Nhận ngay ưu đãi lên đến 50% cho các món ăn được chọn lọc.
                    Áp dụng cho tất cả khách hàng đặt bàn trước 48 giờ.
                </p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-percentage fa-2x text-gold mb-2"></i>
                            <h5>Giảm Giá Ngay</h5>
                            <p class="text-muted small mb-0">Không cần mã coupon</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-gift fa-2x text-gold mb-2"></i>
                            <h5>Tặng Kèm</h5>
                            <p class="text-muted small mb-0">Món tráng miệng miễn phí</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-star fa-2x text-gold mb-2"></i>
                            <h5>Ưu Tiên</h5>
                            <p class="text-muted small mb-0">Phục vụ VIP</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="position-relative"> <!-- Large Discount Circle with Animations -->
                    <div class="discount-circle" style="width: 200px; height: 200px; margin: 0 auto;">
                        <div class="discount-percent" style="font-size: 3rem;">50%</div>
                        <div class="discount-text" style="font-size: 1.2rem;">OFF</div>
                    </div>

                    <!-- Floating Promotional Badges -->
                    <div class="floating-badge" style="top: 10px; right: 20px;">
                        <span class="hot-deal-badge">HOT</span>
                    </div>
                    <div class="floating-badge" style="bottom: 10px; left: 20px; animation-delay: 1.5s;">
                        <span class="hot-deal-badge" style="background: linear-gradient(135deg, var(--primary-navy) 0%, #2c3e50 100%);">NEW</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Promotional Foods Section - Main Focus -->
<section class="section-luxury">
    <div class="container">
        <!-- Active Promotions Summary -->
        <?php if (!empty($activePromotions)): ?>
            <div class="row mb-5">
                <div class="col-12">
                    <div class="promotions-summary">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-fire text-warning"></i> Chương Trình Khuyến Mãi Đang Diễn Ra
                        </h3>
                        <div class="row g-3">
                            <?php foreach ($activePromotions as $promotion): ?>
                                <div class="col-md-4">
                                    <div class="promotion-summary-card" data-promotion-id="<?= $promotion['id'] ?>">
                                        <div class="promotion-summary-header">
                                            <h5><?= htmlspecialchars($promotion['name']) ?></h5>
                                            <span class="promotion-summary-code"><?= htmlspecialchars($promotion['code']) ?></span>
                                        </div>
                                        <div class="promotion-summary-body">
                                            <div class="promotion-discount">
                                                <?php if ($promotion['type'] === 'percentage'): ?>
                                                    <span class="discount-value"><?= $promotion['discount_value'] ?>%</span>
                                                    <span class="discount-text">GIẢM</span>
                                                <?php elseif ($promotion['type'] === 'fixed'): ?>
                                                    <span class="discount-value"><?= number_format($promotion['discount_value'], 0, ',', '.') ?>đ</span>
                                                    <span class="discount-text">GIẢM</span>
                                                <?php else: ?>
                                                    <span class="discount-value">1+1</span>
                                                    <span class="discount-text">BOGO</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="promotion-details">
                                                <?php if (!empty($promotion['description'])): ?>
                                                    <p class="promotion-desc"><?= htmlspecialchars($promotion['description']) ?></p>
                                                <?php endif; ?>
                                                <div class="promotion-validity">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    Đến <?= date('d/m/Y', strtotime($promotion['end_date'])) ?>
                                                </div>
                                                <div class="promotion-applies">
                                                    <i class="fas fa-tag"></i>
                                                    <?php
                                                    switch($promotion['application_type']) {
                                                        case 'specific_items':
                                                            echo 'Áp dụng cho ' . count($promotion['food_items']) . ' món đã chọn';
                                                            break;
                                                        case 'categories':
                                                            echo 'Áp dụng theo danh mục';
                                                            break;
                                                        default:
                                                            echo 'Áp dụng toàn menu';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="text-center mb-5 fade-in-up">
            <h2 class="section-title">
                <span class="text-gold">Món Ăn</span> <span class="text-navy">Khuyến Mãi</span>
            </h2>
            <div class="title-divider"></div>
            <p class="section-subtitle">
                Những món ăn tinh tế với mức giá ưu đãi đặc biệt. Thưởng thức đẳng cấp mà không cần lo chi phí.
            </p>
        </div>
        <div class="food-grid" id="promotion-food-grid"></div>
    </div>
</section>

<!-- Special Offers Banner -->
<section class="section-luxury bg-light-luxury">
    <div class="container">
        <div class="luxury-grid-3" >
            <div class="feature-card text-center fade-in-up" style="" data-delay="0">
                <div class="feature-icon bg-gradient-primary">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h5 class="feature-title">Miễn Phí Giao Hàng</h5>
                <p class="feature-text">Đơn hàng từ 500.000₫</p>
            </div>
            <div class="feature-card text-center fade-in-up" style="" data-delay="100">
                <div class="feature-icon bg-gradient-success">
                    <i class="fas fa-gift"></i>
                </div>
                <h5 class="feature-title">Tặng Kèm Quà</h5>
                <p class="feature-text">Với mỗi đơn hàng</p>
            </div>
            <div class="feature-card text-center fade-in-up" style="" data-delay="200">
                <div class="feature-icon bg-gradient-info">
                    <i class="fas fa-headset"></i>
                </div>
                <h5 class="feature-title">Hỗ Trợ 24/7</h5>
                <p class="feature-text">Luôn sẵn sàng hỗ trợ</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="cta-text fade-in-up">
                        <h2 class="cta-title" style="color: #fff;">
                            <i class="fas fa-calendar-check me-3"></i>
                            Đặt Bàn Ngay Hôm Nay!
                        </h2>
                        <p class="cta-subtitle">
                            Trải nghiệm buffet cao cấp với những món ăn khuyến mãi đặc biệt và dịch vụ tận tâm.
                        </p>
                        <div class="contact-info">
                            <span class="contact-badge phone">
                                <i class="fas fa-phone me-2"></i>Hotline: 0123-456-789
                            </span>
                            <span class="contact-badge hours">
                                <i class="fas fa-clock me-2"></i>Mở cửa: 10:00 - 22:00
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="cta-buttons fade-in-up" data-delay="200">
                        <a href="<?= SITE_URL ?>/booking" class="btn-luxury btn-gold btn-lg mb-3">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <span>Đặt Bàn Ngay</span>
                        </a>
                        <br>
                        <a href="<?= SITE_URL ?>/menu" class="btn-luxury btn-outline-light btn-lg">
                            <i class="fas fa-utensils me-2"></i>
                            <span>Xem Menu Đầy Đủ</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Promotion Card Styles */
    .promotion-card {
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }

    .promotion-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-gold-strong);
    }

    .badge-luxury {
        padding: 0.5rem 1rem;
        border-radius: 0 1rem 1rem 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bg-gradient-fire {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
    }

    .discount-circle {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-gold), var(--accent-copper));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        transform: translate(10px, -10px);
        box-shadow: var(--shadow-gold);
    }

    .card-image-wrapper {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .placeholder-image {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--neutral-light);
    }

    .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(27, 41, 81, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .promotion-card:hover .card-overlay {
        opacity: 1;
    }

    .promotion-card:hover .card-image {
        transform: scale(1.1);
    }

    .overlay-content {
        text-align: center;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .promotion-card:hover .overlay-content {
        transform: translateY(0);
    }

    .card-content {
        padding: 2rem;
    }

    .card-title {
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0;
        line-height: 1.3;
    }

    .rating-stars {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .rating-stars i {
        font-size: 0.8rem;
    }

    .rating-text {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-left: 0.25rem;
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: rgba(212, 175, 55, 0.1);
        color: var(--primary-gold);
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .card-description {
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .price-section {
        border-top: 1px solid var(--border-light);
        padding-top: 1rem;
        margin-bottom: 1.5rem;
    }

    .original-price {
        text-decoration: line-through;
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-right: 0.75rem;
    }

    .discounted-price {
        color: var(--accent-rose);
        font-weight: 700;
        font-size: 1.5rem;
    }

    .savings-text {
        color: var(--success-color);
        font-size: 0.85rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .card-actions {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .card-actions .btn-luxury:first-child {
        flex: 1;
    }

    .time-notice {
        text-align: center;
        font-size: 0.8rem;
        color: var(--text-muted);
        padding: 0.5rem;
        background: rgba(212, 175, 55, 0.05);
        border-radius: 0.5rem;
        border-left: 3px solid var(--primary-gold);
    }

    /* Feature Cards */
    .feature-card {
        padding: 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-medium);
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.5rem;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary-navy), var(--primary-gold));
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #6610f2);
    }

    .feature-title {
        font-family: var(--font-heading);
        color: var(--primary-navy);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .feature-text {
        color: var(--text-muted);
        margin: 0;
    }

    /* CTA Section */
    .cta-section {
        background:  var(--primary-navy);
        /* background: linear-gradient(135deg, var(--primary-navy), var(--primary-gold)); */
        color: white;
        padding: 5rem 0;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
        pointer-events: none;
    }

    .cta-content {
        position: relative;
        z-index: 1;
    }

    .cta-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .cta-subtitle {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }

    .contact-info {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .contact-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .contact-badge.phone {
        background: rgba(212, 175, 55, 0.2);
        border-color: var(--primary-gold);
    }

    .cta-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--accent-copper));
        border: none;
        color: white;
        box-shadow: var(--shadow-gold);
    }

    .btn-gold:hover {
        background: linear-gradient(135deg, var(--accent-copper), var(--primary-gold));
        transform: translateY(-2px);
        box-shadow: var(--shadow-gold-strong);
        color: white;
    }

    .btn-outline-light {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(10px);
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
        color: white;
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state {
        padding: 3rem;
        text-align: center;
    }

    /* Promotion Tags and BOGO Badges */
    .promotion-tag {
        background: linear-gradient(135deg, var(--primary-gold), var(--accent-copper));
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
    }

    .promotion-code {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.1rem 0.4rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        margin-left: 0.5rem;
    }

    .bogo-badge {
        font-size: 0.7rem;
        animation: pulse-bogo 2s infinite;
    }

    @keyframes pulse-bogo {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* CSS for Promotion Summary Cards */
    .promotions-summary {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(27, 41, 81, 0.05));
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 3rem;
    }

    .promotion-summary-card {
        background: white;
        border-radius: 1rem;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .promotion-summary-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-medium);
    }

    .promotion-summary-card.active {
           box-shadow:
        0 0 0 3px var(--primary-gold, #d4af37) inset,
        0 0 16px 4px rgba(212,175,55,0.25),
        0 2px 8px rgba(212,175,55,0.10);
    border: none;
    transition: box-shadow 0.2s;
}

    .promotion-summary-header {
        background: linear-gradient(135deg, var(--primary-navy), var(--primary-gold));
        color: white;
        padding: 1rem;
        text-align: center;
    }

    .promotion-summary-header h5 {
        margin: 0 0 0.5rem 0;
        font-weight: 600;
        font-size: 1rem;
    }

    .promotion-summary-code {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .promotion-summary-body {
        padding: 1.5rem;
    }

    .promotion-discount {
        text-align: center;
        margin-bottom: 1rem;
    }

    .discount-value {
        display: block;
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent-rose);
        line-height: 1;
    }

    .discount-text {
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .promotion-details {
        font-size: 0.85rem;
    }

    .promotion-desc {
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }

    .promotion-validity, .promotion-applies {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
    }

    .promotion-validity i, .promotion-applies i {
        color: var(--primary-gold);
        width: 12px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .promotion-card {
            margin-bottom: 2rem;
        }

        .cta-title {
            font-size: 2rem;
        }

        .contact-info {
            justify-content: center;
        }

        .cta-buttons {
            margin-top: 2rem;
        }

        .card-content {
            padding: 1.5rem;
        }

        .discounted-price {
            font-size: 1.25rem;
        }
    }

    /* Loading Animation */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Pulse Animation */
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

    .pulse {
        animation: pulse 0.3s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart functionality with loading state
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const foodId = this.dataset.foodId;
                const foodName = this.dataset.foodName;
                const foodPrice = this.dataset.foodPrice;
                const originalText = this.innerHTML;

                console.log('Add to cart clicked:', { foodId, foodName, foodPrice });

                // Add loading state
                this.classList.add('btn-loading');
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';
                this.disabled = true;

                // Make actual API call to add to cart
                fetch('<?= SITE_URL ?>/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `food_id=${foodId}&quantity=1`
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);

                    // Remove loading state
                    this.classList.remove('btn-loading');

                    if (data.success) {
                        this.innerHTML = '<i class="fas fa-check me-2"></i>Đã thêm';
                        this.classList.remove('btn-primary', 'btn-luxury');
                        this.classList.add('btn-success');

                        // Show success toast
                        showToast('Thành công!', 'Đã thêm ' + foodName + ' vào giỏ hàng', 'success');

                        // Update cart count if exists
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount && data.cartInfo && data.cartInfo.itemCount) {
                            cartCount.textContent = data.cartInfo.itemCount;
                        }

                        // Reset button after 2 seconds
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.classList.remove('btn-success');
                            this.classList.add('btn-luxury');
                            this.disabled = false;
                        }, 2000);
                    } else {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        showToast('Lỗi!', data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.classList.remove('btn-loading');
                    this.innerHTML = originalText;
                    this.disabled = false;
                    showToast('Lỗi!', 'Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
                });
            });
        });

        // Favorite functionality with animation
        document.querySelectorAll('.favorite-btn').forEach(button => {
            button.addEventListener('click', function() {
                const heartIcon = this.querySelector('i');

                // Add pulse animation
                this.style.animation = 'pulse 0.3s ease';

                // Toggle favorite status
                if (this.classList.contains('active')) {
                    this.classList.remove('active');
                    heartIcon.classList.remove('fas');
                    heartIcon.classList.add('far');
                    showToast('Đã xóa', 'Đã xóa khỏi danh sách yêu thích', 'info');
                } else {
                    this.classList.add('active');
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas');
                    showToast('Đã thêm', 'Đã thêm vào danh sách yêu thích', 'success');
                }

                // Remove animation after it completes
                setTimeout(() => {
                    this.style.animation = '';
                }, 300);
            });
        });

        // Promotion summary card click event
        document.querySelectorAll('.promotion-summary-card').forEach(card => {
            card.addEventListener('click', function() {
                const promotionId = this.getAttribute('data-promotion-id');
                document.querySelectorAll('.promotion-summary-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const foodGrid = document.getElementById('promotion-food-grid');
                if (foodGrid) {
                    foodGrid.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-gold"></div></div>';
                }
                fetch('<?= SITE_URL ?>/promotion/foods?promotion_id=' + promotionId)
                    .then(res => res.json())
                    .then(data => {
                        foodGrid.innerHTML = data.html || '';
                        // Thêm dòng này để hiện món ăn:
                        foodGrid.querySelectorAll('.fade-in-up').forEach(el => el.classList.add('fade-in-active'));
                        // Gắn lại sự kiện cho nút mới nếu cần
                    })
                    .catch(() => {
                        if (foodGrid) {
                            foodGrid.innerHTML = '<div class="text-center py-5 text-danger">Không thể tải dữ liệu!</div>';
                        }
                    });
            });
        });

        // Toast notification function
        function showToast(title, message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show toast-notification`;
            toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';

            toast.innerHTML = `
            <strong>${title}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 3000);
        }

        // Tự động click promotion đầu tiên khi vào trang
        document.querySelector('.promotion-summary-card')?.click();
    });

    // Add pulse animation to CSS
    const style = document.createElement('style');
    style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
`;
    document.head.appendChild(style);
</script>

<?php
// Footer will be included by BaseController loadView method
// require_once 'views/customer/layouts/footer.php';
?>
