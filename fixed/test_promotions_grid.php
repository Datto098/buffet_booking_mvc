<?php
// Test Promotions Grid Layout
// This script verifies the promotions page displays properly with 3-column grid

// Configuration
define('SITE_URL', 'http://localhost/buffet_booking_mvc');
define('BASE_PATH', dirname(__FILE__));

// Include necessary files
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/Food.php';

// Initialize database and Food model
$database = new Database();
$db = $database->getConnection();
$foodModel = new Food($db);

// Get promotional foods (sample data for testing)
$promotionalFoods = $foodModel->getAll();

// Limit to 6 items for testing
$promotionalFoods = array_slice($promotionalFoods, 0, 6);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Promotions Grid Layout - Buffet Restaurant</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/luxury-style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/promotion-styles.css">

    <style>
        .test-info {
            background: linear-gradient(135deg, #1B2951 0%, #2c3e50 100%);
            color: white;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 10px;
        }
        .test-grid-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #D4AF37;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="luxury-body">

    <!-- Test Information -->
    <div class="container mt-4">
        <div class="test-info">
            <h2><i class="fas fa-vial me-2"></i> Promotions Grid Layout Test</h2>
            <p class="mb-1"><strong>Purpose:</strong> Verify 3-column grid layout displays correctly</p>
            <p class="mb-1"><strong>Expected:</strong> Food items should display in 3 columns on desktop, 2 on tablet, 1 on mobile</p>
            <p class="mb-0"><strong>Date:</strong> <?= date('Y-m-d H:i:s') ?></p>
        </div>

        <div class="test-grid-info">
            <h5><i class="fas fa-info-circle me-2"></i> Grid Layout Information</h5>
            <ul class="mb-0">
                <li><strong>Desktop (>992px):</strong> 3 columns with 2rem gap</li>
                <li><strong>Tablet (≤992px):</strong> 2 columns with 1.5rem gap</li>
                <li><strong>Mobile (≤576px):</strong> 1 column with 1.5rem gap</li>
                <li><strong>Total Items:</strong> <?= count($promotionalFoods) ?> items</li>
            </ul>
        </div>
    </div>

    <!-- Test the actual promotions section -->
    <section class="section-luxury">
        <div class="container">
            <div class="text-center mb-5 fade-in-up">
                <h2 class="section-title">
                    <span class="text-gold">Món Ăn</span> <span class="text-navy">Khuyến Mãi</span>
                </h2>
                <div class="title-divider"></div>
                <p class="section-subtitle">
                    Testing the 3-column grid layout for promotional food items
                </p>
            </div>

            <?php if (!empty($promotionalFoods)): ?>
                <div class="food-grid">
                    <?php foreach ($promotionalFoods as $index => $food): ?>
                        <?php
                            // Create mock promotion data
                            $discountPercent = [15, 20, 25, 30, 35, 40][array_rand([15, 20, 25, 30, 35, 40])];
                            $originalPrice = (int)$food['price'];
                            $discountedPrice = $originalPrice * (100 - $discountPercent) / 100;
                            $isHotDeal = $discountPercent >= 30;
                        ?>
                        <div class="food-item promotion-food-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                            <div class="food-image">
                                <?php if (!empty($food['image']) && $food['image'] !== 'placeholder.jpg'): ?>
                                    <img src="<?= SITE_URL ?>/uploads/food_images/<?= htmlspecialchars($food['image']) ?>"
                                        class="card-img-luxury"
                                        alt="<?= htmlspecialchars($food['name']) ?>">
                                <?php else: ?>
                                    <img src="<?= SITE_URL ?>/assets/images/food-placeholder.svg"
                                        class="card-img-luxury"
                                        alt="<?= htmlspecialchars($food['name']) ?>">
                                <?php endif; ?>

                                <!-- Discount Badge -->
                                <div class="food-badge discount-badge">
                                    <i class="fas fa-percent"></i> -<?= $discountPercent ?>%
                                </div>

                                <!-- Hot Deal Badge -->
                                <?php if ($isHotDeal): ?>
                                    <div class="food-badge hot-deal-badge-small">
                                        <i class="fas fa-fire"></i> HOT
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="food-content">
                                <div class="food-category">
                                    <?= htmlspecialchars($food['category_name'] ?? 'Đặc sản'); ?>
                                </div>

                                <h3 class="food-title"><?= htmlspecialchars($food['name']) ?></h3>

                                <p class="food-description">
                                    <?= !empty($food['description'])
                                        ? htmlspecialchars(substr($food['description'], 0, 120)) . '...'
                                        : 'Món ăn ngon, đầy đủ dinh dưỡng với hương vị đặc trưng tuyệt vời.' ?>
                                </p>

                                <!-- Price Section -->
                                <div class="food-price promotion-price">
                                    <span class="price-original">
                                        <?= number_format($originalPrice, 0, ',', '.') ?>đ
                                    </span>
                                    <span class="price-current">
                                        <?= number_format($discountedPrice, 0, ',', '.') ?>đ
                                    </span>
                                    <div class="savings-text">
                                        <i class="fas fa-piggy-bank"></i>
                                        Tiết kiệm: <?= number_format($originalPrice - $discountedPrice, 0, ',', '.') ?>đ
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-luxury add-to-cart flex-grow-1"
                                        data-food-id="<?= $food['id'] ?>"
                                        data-food-name="<?= htmlspecialchars($food['name']) ?>"
                                        data-food-price="<?= $discountedPrice ?>"
                                        data-original-price="<?= $originalPrice ?>">
                                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                    </button>
                                    <a href="<?= SITE_URL ?>/food/detail/<?= $food['id'] ?>"
                                        class="btn btn-outline-luxury">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>

                                <!-- Limited Time Notice -->
                                <div class="limited-time">
                                    Ưu đãi có hạn - Còn lại: <span class="text-danger fw-bold">2 ngày</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No promotional foods available for testing
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Test Results -->
    <div class="container mt-5 mb-5">
        <div class="test-info">
            <h4><i class="fas fa-check-circle me-2"></i> Test Instructions</h4>
            <ol>
                <li>Verify that food items display in exactly 3 columns on desktop screens</li>
                <li>Resize browser to tablet width (≤992px) and verify 2 columns</li>
                <li>Resize browser to mobile width (≤576px) and verify 1 column</li>
                <li>Check that all food items are properly aligned and spaced</li>
                <li>Verify luxury styling and animations work correctly</li>
            </ol>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript for grid testing -->
    <script>
        // Display current viewport width for testing
        function showViewportWidth() {
            const width = window.innerWidth;
            const columns = width > 992 ? 3 : width > 576 ? 2 : 1;
            console.log(`Viewport: ${width}px - Expected columns: ${columns}`);
        }

        // Show width on load and resize
        window.addEventListener('load', showViewportWidth);
        window.addEventListener('resize', showViewportWidth);

        // Add visual indicators for debugging
        document.addEventListener('DOMContentLoaded', function() {
            const grid = document.querySelector('.food-grid');
            if (grid) {
                console.log('Grid element found:', grid);
                console.log('Grid computed style:', window.getComputedStyle(grid).gridTemplateColumns);
            }
        });
    </script>

</body>
</html>
