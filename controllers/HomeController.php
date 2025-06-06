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
    }

    public function index() {
        // Get featured foods
        $featuredFoods = $this->foodModel->getFoodWithCategory(8);

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
        // Get promotional foods with category information
        $promotionalFoods = $this->foodModel->getFoodWithCategory(16); // Lấy nhiều món hơn để thể hiện khuyến mãi

        // Add mock discount information to foods
        foreach ($promotionalFoods as &$food) {
            // Generate random discount percentage between 15-40%
            $discountPercent = rand(15, 40);
            $food['discount_percent'] = $discountPercent;
            $food['original_price'] = $food['price'];
            $food['discounted_price'] = $food['price'] * (100 - $discountPercent) / 100;
            $food['is_hot_deal'] = $discountPercent >= 30;
        }

        $data = [
            'title' => 'Khuyến Mãi Đặc Biệt - ' . SITE_NAME,
            'promotionalFoods' => $promotionalFoods,
            'meta_description' => 'Khám phá những món ăn khuyến mãi đặc biệt với giá ưu đãi tại ' . SITE_NAME . '. Tiết kiệm đến 40% cho các món ăn ngon nhất!'
        ];

        $this->loadView('customer/promotions', $data);
    }
}
?>
