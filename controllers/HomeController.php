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

        $data = [
            'title' => 'Trang Chủ - ' . SITE_NAME,
            'featuredFoods' => $featuredFoods,
            'categories' => $categories,
            'latestNews' => $latestNews
        ];

        $this->loadView('customer/home', $data);
    }    public function about() {
        // Get restaurant info
        try {
            $stmt = Database::getInstance()->getConnection()->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch();
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
            'restaurantInfo' => $restaurantInfo
        ];

        $this->loadView('customer/about', $data);
    }    public function promotions() {
        // Load Promotion model
        require_once 'models/Promotion.php';
        $promotionModel = new Promotion();

        // Get active promotions with their associated food items
        $activePromotions = $promotionModel->getActivePromotionsWithFoodItems();

        // Process food items to include promotion discount information
        $promotionalFoods = [];

        foreach ($activePromotions as $promotion) {
            foreach ($promotion['food_items'] as $food) {
                // Skip if this food item is already processed (to avoid duplicates)
                $exists = false;
                foreach ($promotionalFoods as $existingFood) {
                    if ($existingFood['id'] === $food['id']) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    // Calculate discounted price based on promotion
                    $originalPrice = (float)$food['price'];
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
                        $discountPercent = 50; // 50% off for BOGO
                        $discountedPrice = $originalPrice * 0.5;
                    }

                    // Add promotion information to food item
                    $food['original_price'] = $originalPrice;
                    $food['discounted_price'] = $discountedPrice;
                    $food['discount_percent'] = $discountPercent;
                    $food['is_hot_deal'] = $discountPercent >= 30;
                    $food['promotion_id'] = $promotion['id'];
                    $food['promotion_name'] = $promotion['name'];
                    $food['promotion_code'] = $promotion['code'];
                    $food['promotion_type'] = $promotion['type'];
                    $food['promotion_end_date'] = $promotion['end_date'];

                    $promotionalFoods[] = $food;
                }
            }
        }

        // If no promotion foods found, get some featured foods as fallback
        if (empty($promotionalFoods)) {
            $fallbackFoods = $this->foodModel->getFoodWithCategory(12);
            foreach ($fallbackFoods as &$food) {
                // Add minimal discount for display purposes
                $discountPercent = rand(10, 25);
                $food['discount_percent'] = $discountPercent;
                $food['original_price'] = $food['price'];
                $food['discounted_price'] = $food['price'] * (100 - $discountPercent) / 100;
                $food['is_hot_deal'] = $discountPercent >= 20;
                $food['promotion_name'] = 'Special Offer';
                $food['promotion_type'] = 'percentage';
            }
            $promotionalFoods = $fallbackFoods;
        }

        $data = [
            'title' => 'Khuyến Mãi Đặc Biệt - ' . SITE_NAME,
            'promotionalFoods' => $promotionalFoods,
            'activePromotions' => $activePromotions,
            'meta_description' => 'Khám phá những món ăn khuyến mãi đặc biệt với giá ưu đãi tại ' . SITE_NAME . '. Tiết kiệm đến ' . (isset($promotionalFoods[0]) ? $promotionalFoods[0]['discount_percent'] : '40') . '% cho các món ăn ngon nhất!'
        ];

        $this->loadView('customer/promotions', $data);
    }
}
?>
