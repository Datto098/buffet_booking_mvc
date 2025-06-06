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
    }

    public function about() {
        // Get restaurant info
        try {
            $stmt = (new Database())->getConnection()->prepare("SELECT * FROM restaurant_info WHERE id = 1");
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
    }
}
?>
