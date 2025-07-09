<?php
/**
 * Home Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/News.php';

class HomeController extends BaseController {
    private $foodModel;
    private $categoryModel;

    public function __construct() {
        $this->foodModel = new Food();
        $this->categoryModel = new Category();
    }    public function index() {
        // Get featured foods (popular, new, or seasonal items limited to 6)
        $featuredFoods = $this->foodModel->getFeaturedFoods(6);

        // Get categories
        $categories = $this->categoryModel->findAll();        // Get latest news (if News model exists)
        $latestNews = [];
        try {
            $newsModel = new News();
            $latestNews = $newsModel->getLatestNews(3);
        } catch (Exception $e) {
            // News table might not exist yet, ignore error
            $latestNews = [];
        }

        // Get restaurant info
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'about_us_content' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Trang Chủ - ' . SITE_NAME,
            'featuredFoods' => $featuredFoods,
            'categories' => $categories,
            'latestNews' => $latestNews,
            'info' => $restaurantInfo
        ];
        // echo '<pre>';
        // print_r($data['info']);
        // echo '</pre>';

        $this->loadView('customer/home', $data);
    }    public function about() {
        // Get restaurant info
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'about_us_content' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Giới Thiệu - ' . SITE_NAME,
            'restaurantInfo' => $restaurantInfo,
            'info' => $restaurantInfo // Thêm dòng này để footer nhận được info
        ];

        $this->loadView('customer/about', $data);
    }    public function promotions() {
        // Load Promotion model
        require_once 'models/Promotion.php';
        $promotionModel = new Promotion();

        // Get active promotions with their associated food items
        $activePromotions = $promotionModel->getActivePromotionsWithFoodItems();

        // Lấy promotion đầu tiên (nếu có)
        $promotionalFoods = [];
        if (!empty($activePromotions)) {
            $firstPromotion = $activePromotions[0];
            $promotionModel = new Promotion();
            $promotionalFoods = $promotionModel->getFoodsByPromotion($firstPromotion['id']);
            // Tính toán lại các trường giảm giá cho từng món ăn (giống như trong hàm foods)
            foreach ($promotionalFoods as &$food) {
                $originalPrice = (float)($food['price'] ?? 0);
                $discountedPrice = $originalPrice;
                $discountPercent = 0;
                if ($firstPromotion['type'] === 'percentage') {
                    $discountPercent = (int)$firstPromotion['discount_value'];
                    $discountedPrice = $originalPrice * (100 - $discountPercent) / 100;
                } elseif ($firstPromotion['type'] === 'fixed') {
                    $discountAmount = (float)$firstPromotion['discount_value'];
                    $discountedPrice = max(0, $originalPrice - $discountAmount);
                    $discountPercent = $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100) : 0;
                } elseif ($firstPromotion['type'] === 'buy_one_get_one') {
                    $discountPercent = 50;
                    $discountedPrice = $originalPrice * 0.5;
                }
                $food['original_price'] = $originalPrice;
                $food['discounted_price'] = $discountedPrice;
                $food['discount_percent'] = $discountPercent;
                $food['is_hot_deal'] = $discountPercent >= 30;
                $food['promotion_name'] = $firstPromotion['name'];
                $food['promotion_code'] = $firstPromotion['code'];
                $food['promotion_type'] = $firstPromotion['type'];
                $food['promotion_end_date'] = $firstPromotion['end_date'];
            }
        }

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Khuyến Mãi Đặc Biệt - ' . SITE_NAME,
            'promotionalFoods' => $promotionalFoods,
            'activePromotions' => $activePromotions,
            'meta_description' => 'Khám phá những món ăn khuyến mãi đặc biệt với giá ưu đãi tại ' . SITE_NAME . '. Tiết kiệm đến ' . (isset($promotionalFoods[0]) ? $promotionalFoods[0]['discount_percent'] : '40') . '% cho các món ăn ngon nhất!',
            'info' => $restaurantInfo // Thêm dòng này
        ];

        $this->loadView('customer/promotions', $data);
    }
public function foods() {
    require_once 'models/Promotion.php';
    $promotionModel = new Promotion();
    $promotionId = intval($_GET['promotion_id'] ?? 0);
    $foods = $promotionModel->getFoodsByPromotion($promotionId);
    $promotion = $promotionModel->getPromotionById($promotionId);

    // TÍNH TOÁN GIẢM GIÁ CHO TỪNG MÓN ĂN
    if (!empty($foods) && $promotion) {
        foreach ($foods as &$food) {
            $originalPrice = (float)($food['price'] ?? 0);
            $discountedPrice = $originalPrice;
            $discountPercent = 0;
            if ($promotion['type'] === 'percentage') {
                $discountPercent = (int)$promotion['discount_value'];
                $discountedPrice = $originalPrice * (100 - $discountPercent) / 100;
            } elseif ($promotion['type'] === 'fixed') {
                $discountAmount = (float)$promotion['discount_value'];
                $discountedPrice = max(0, $originalPrice - $discountAmount);
                $discountPercent = $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100) : 0;
            } elseif ($promotion['type'] === 'buy_one_get_one') {
                $discountPercent = 50;
                $discountedPrice = $originalPrice * 0.5;
            }
            $food['original_price'] = $originalPrice;
            $food['discounted_price'] = $discountedPrice;
            $food['discount_percent'] = $discountPercent;
            $food['is_hot_deal'] = $discountPercent >= 30;
            $food['promotion_name'] = $promotion['name'];
            $food['promotion_code'] = $promotion['code'];
            $food['promotion_type'] = $promotion['type'];
            $food['promotion_end_date'] = $promotion['end_date'];
        }
        unset($food);
    }

    ob_start();
    if (!empty($foods)) {
        foreach ($foods as $index => $food) {
            $discountPercent = (int)($food['discount_percent'] ?? 0);
            $originalPrice = (float)($food['original_price'] ?? 0);
            $discountedPrice = (float)($food['discounted_price'] ?? 0);
            $isHotDeal = $food['is_hot_deal'] ?? ($discountPercent >= 30);
            $endDate = $food['promotion_end_date'] ?? null;
            $daysLeft = null;
            if ($endDate) {
                $endDateTime = new DateTime($endDate);
                $now = new DateTime();
                $interval = $now->diff($endDateTime);
                $daysLeft = $interval->days;
            }
            ?>
            <div class="food-item promotion-food-item fade-in-up" style="animation-delay: <?= $index * 0.1 ?>s" data-delay="<?= $index * 0.1 ?>s">
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
                    <div class="food-badge discount-badge">
                        -<?= $discountPercent ?><i class="fas fa-percent"></i>
                    </div>
                    <?php if ($isHotDeal): ?>
                        <div class="food-badge hot-deal-badge-small">
                            <i class="fas fa-fire"></i> HOT
                        </div>
                    <?php endif; ?>
                    <?php if (isset($food['promotion_type']) && $food['promotion_type'] === 'buy_one_get_one'): ?>
                        <div class="food-badge bogo-badge" style="top: 60px; right: -10px; background: linear-gradient(135deg, #e67e22, #d35400);">
                            <i class="fas fa-plus"></i> BOGO
                        </div>
                    <?php endif; ?>
                </div>
                <div class="food-content">
                    <div class="food-category">
                        <?= htmlspecialchars($food['category_name'] ?? 'Đặc sản'); ?>
                    </div>
                    <h3 class="food-title"><?= htmlspecialchars($food['name']) ?></h3>
                    <?php if (isset($food['promotion_name'])): ?>
                        <div class="promotion-tag mb-2">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($food['promotion_name']) ?>
                            <?php if (isset($food['promotion_code'])): ?>
                                <span class="promotion-code"><?= htmlspecialchars($food['promotion_code']) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <p class="food-description">
                        <?= !empty($food['description'])
                            ? htmlspecialchars(substr($food['description'], 0, 120)) . '...'
                            : 'Món ăn ngon, đầy đủ dinh dưỡng với hương vị đặc trưng tuyệt vời.' ?>
                    </p>
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
                        <button class="btn btn-luxury add-to-cart-btn flex-grow-1"
                            data-food-id="<?= $food['id'] ?>"
                            data-food-name="<?= htmlspecialchars($food['name']) ?>"
                            data-food-price="<?= $discountedPrice ?>"
                            data-original-price="<?= $originalPrice ?>">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                        <button class="btn btn-outline-luxury favorite-btn"
                            data-food-id="<?= $food['id'] ?>">
                            <i class="far fa-heart"></i>
                        </button>
                        <a href="<?= SITE_URL ?>/food/detail/<?= $food['id'] ?>"
                            class="btn btn-outline-luxury">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <div class="limited-time">
                        <?php if ($daysLeft !== null): ?>
                            Ưu đãi có hạn - Còn lại: <span class="text-danger fw-bold"><?= $daysLeft ?> ngày</span>
                        <?php else: ?>
                            Ưu đãi có hạn - Đừng bỏ lỡ!
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="text-center py-5 fade-in-up">
                <div class="empty-state">
                    <i class="fas fa-sad-tear fa-4x text-muted mb-4"></i>
                    <h3 class="text-navy mb-3">Hiện Tại Chưa Có Món Ăn Khuyến Mãi</h3>
                    <p class="text-muted mb-4">Vui lòng quay lại sau để khám phá những ưu đãi mới nhất!</p>
                    <a href="' . SITE_URL . '/menu" class="btn-luxury btn-primary-luxury">
                        <i class="fas fa-utensils me-2"></i>
                        <span>Xem Menu Đầy Đủ</span>
                    </a>
                </div>
            </div>';
    }
    $html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode(['html' => $html]);
    exit;
}
}
?>
