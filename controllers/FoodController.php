<?php

/**
 * Food/Menu Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Order.php';

class FoodController extends BaseController
{
    private $foodModel;
    private $categoryModel;
    protected $orderModel;
    protected $reviewModel;

    public function __construct()
    {
        $this->foodModel = new Food();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
        $this->reviewModel = new Review();
    }

    public function index()
    {
        $this->menu();
    }
    public function menu()
    {
        // Get pagination parameters
        $page = intval($_GET['p'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Get filter parameters with better UTF-8 handling
        $categoryId = intval($_GET['category'] ?? 0);
        $subcategoryId = intval($_GET['subcategory'] ?? 0);        // Handle search with proper UTF-8 decoding
        $rawSearch = $_GET['search'] ?? '';
        $search = '';
        if (!empty($rawSearch)) {
            // Just trim, don't use htmlspecialchars for database search
            $search = trim(urldecode($rawSearch));
        }

        $sortBy = $_GET['sort'] ?? 'name';
        $priceRange = $_GET['price_range'] ?? '';

        // Debug logging
        error_log("Menu Search Debug - Raw search: " . $rawSearch);
        error_log("Menu Search Debug - Decoded search: " . $search);
        error_log("Menu Search Debug - Search term length: " . strlen($search));
        error_log("Menu Search Debug - Search term: " . $search);
        error_log("Menu Search Debug - Raw search: " . ($_GET['search'] ?? 'empty'));

        // Build filter conditions
        $conditions = ['f.is_available = 1'];
        $params = [];

        if ($categoryId > 0) {
            $conditions[] = 'f.category_id = :category_id';
            $params[':category_id'] = $categoryId;
        }

        if ($subcategoryId > 0) {
            $conditions[] = 'f.subcategory_id = :subcategory_id';
            $params[':subcategory_id'] = $subcategoryId;
        }        if (!empty($search)) {
            // Debug: Test search directly
            $debugResults = $this->foodModel->debugSearch($search);
            error_log("Menu Search Debug - Direct search found: " . count($debugResults) . " items");

            // Fix: Use separate parameters for name and description search
            $searchTerm = "%$search%";
            $conditions[] = '(f.name LIKE :search_name OR f.description LIKE :search_desc)';
            $params[':search_name'] = $searchTerm;
            $params[':search_desc'] = $searchTerm;

            error_log("Menu Search Debug - Search condition added");
            error_log("Menu Search Debug - Search term used: " . $searchTerm);
            error_log("Menu Search Debug - Search original: " . $search);
            error_log("Menu Search Debug - Current conditions: " . json_encode($conditions));
        }
        if (!empty($priceRange)) {
            switch ($priceRange) {
                case 'under-50000':
                    $conditions[] = 'f.price < 50000';
                    break;
                case '50000-100000':
                    $conditions[] = 'f.price BETWEEN 50000 AND 100000';
                    break;
                case '100000-200000':
                    $conditions[] = 'f.price BETWEEN 100000 AND 200000';
                    break;
                case 'over-200000':
                    $conditions[] = 'f.price > 200000';
                    break;
            }
        }
        $whereClause = implode(' AND ', $conditions);

        // Debug SQL query
        error_log("Menu Search Debug - Final where clause: " . $whereClause);
        error_log("Menu Search Debug - All params: " . json_encode($params));
        error_log("Menu Search Debug - Category ID: " . $categoryId);
        error_log("Menu Search Debug - Search term: " . $search);

        switch ($sortBy) {
            case 'price_asc':
                $orderBy = 'f.price ASC';
                break;
            case 'price_desc':
                $orderBy = 'f.price DESC';
                break;
            default:
                $orderBy = 'f.name ASC';
        }

        try {
            // Quick test: get all foods first to see if there are any
            $testFoods = $this->foodModel->getFoodWithFilters('f.is_available = 1', [], $orderBy, 5, 0);
            error_log("Menu Search Debug - Test query found " . count($testFoods) . " foods");
            if (count($testFoods) > 0) {
                error_log("Menu Search Debug - First food name: " . $testFoods[0]['name']);
            }

            // Get foods with pagination
            $foods = $this->foodModel->getFoodWithFilters($whereClause, $params, $orderBy, $limit, $offset);

            // Get total count for pagination
            $totalFoods = $this->foodModel->countFoodWithFilters($whereClause, $params);
            $totalPages = ceil($totalFoods / $limit);
        } catch (Exception $e) {
            error_log("Menu Search Error: " . $e->getMessage());
            error_log("Menu Search SQL Error - Where: " . $whereClause);
            error_log("Menu Search SQL Error - Params: " . json_encode($params));

            // Set default values to prevent fatal error
            $foods = [];
            $totalFoods = 0;
            $totalPages = 1;
        }

        // Get categories for filter
        $categories = $this->categoryModel->getCategoriesWithSubcategories();

        // Get subcategories for selected category
        $subcategories = [];
        if ($categoryId > 0) {
            $subcategories = $this->categoryModel->getSubcategories($categoryId);
        }
        $data = [
            'title' => 'Thực Đơn - ' . SITE_NAME,
            'foods' => $foods,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalFoods' => $totalFoods,
            'SITE_URL' => SITE_URL,
            'filters' => [
                'category' => $categoryId,
                'subcategory' => $subcategoryId,
                'search' => $search,
                'sort' => $sortBy,
                'price_range' => $priceRange
            ]
        ];
        // print_r($data);
        // echo "</pre>";

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

        $data['info'] = $restaurantInfo;

        $this->loadView('customer/menu/index', $data);
    }
    public function detail()
    {
        // Get food id from URL segment
        $segments = $this->getUrlSegments();
        $id = isset($segments[2]) ? intval($segments[2]) : 0;

        // Fallback to GET parameter if URL segment not available
        if ($id <= 0) {
            $id = intval($_GET['id'] ?? 0);
        }

        if ($id <= 0) {
            redirect('/menu');
        }

        $food = $this->foodModel->getFoodDetails($id);

        if (!$food || !$food['is_available']) {
            redirect('/menu');
        }

        // Get related foods (same category)
        $relatedFoods = $this->foodModel->getFoodByCategory($food['category_id'], 6, $id);

        // Get food category info
        $category = $this->categoryModel->findById($food['category_id']);
        $userOrdered = false;
        if (isset($_SESSION['user_id'])) {
            $userOrdered = $this->orderModel->hasUserOrderedFood($_SESSION['user_id'], $food['id']);
        }
        $comments = $this->reviewModel->getReviewsByFood($food['id']);
        $avgRating = $this->reviewModel->getAverageRating($food['id']);
        $totalRating = $this->reviewModel->getTotalRating($food['id']);
        $isReviewed = false;
        if (isset($_SESSION['user_id'])) {
            $isReviewed = $this->reviewModel->hasUserReviewedFood($_SESSION['user_id'], $food['id']);
        }        // Add liked status for each comment
        foreach ($comments as &$comment) {
            $comment['liked'] = false;
            if (isset($_SESSION['user_id'])) {
                $comment['liked'] = $this->reviewModel->hasUserLiked($_SESSION['user_id'], $comment['id']);
            }
        }

        $data = [
            'title' => $food['name'] . ' - ' . SITE_NAME,
            'food' => $food,
            'category' => $category,
            'relatedFoods' => $relatedFoods,
            'userOrdered' => $userOrdered,
            'comments' => $comments,
            'avgRating' => $avgRating,
            'totalRating' => $totalRating,
            'isReviewed' => $isReviewed
        ];

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

        $data['info'] = $restaurantInfo;

        $this->loadView('customer/menu/detail', $data);
    }

    public function category()
    {
        // Get category id from URL segment
        $segments = $this->getUrlSegments();
        $categoryId = isset($segments[2]) ? intval($segments[2]) : 0;

        // Fallback to GET parameter if URL segment not available
        if ($categoryId <= 0) {
            $categoryId = intval($_GET['id'] ?? 0);
        }

        if ($categoryId <= 0) {
            redirect('/menu');
        }

        $category = $this->categoryModel->findById($categoryId);

        if (!$category) {
            redirect('/menu');
        }

        // Get foods in this category
        $foods = $this->foodModel->getFoodByCategory($categoryId);

        // Get subcategories
        $subcategories = $this->categoryModel->getSubcategories($categoryId);

        $data = [
            'title' => $category['name'] . ' - ' . SITE_NAME,
            'category' => $category,
            'foods' => $foods,
            'subcategories' => $subcategories
        ];

        $this->loadView('customer/menu/category', $data);
    }

    public function search()
    {
        // Try to get query from URL segment
        $segments = $this->getUrlSegments();
        $query = isset($segments[2]) ? sanitizeInput($segments[2]) : '';

        // Fallback to GET parameter if URL segment not available
        if (empty($query)) {
            $query = sanitizeInput($_GET['q'] ?? '');
        }

        if (empty($query)) {
            redirect('/menu');
        }

        $foods = $this->foodModel->searchFood($query);

        $data = [
            'title' => 'Kết quả tìm kiếm: ' . $query . ' - ' . SITE_NAME,
            'foods' => $foods,
            'query' => $query,
            'resultCount' => count($foods)
        ];

        $this->loadView('customer/menu/search', $data);
    }

    // AJAX endpoints
    public function getSubcategories()
    {
        $categoryId = intval($_GET['category_id'] ?? 0);

        if ($categoryId <= 0) {
            $this->jsonResponse(['error' => 'Invalid category ID'], 400);
        }

        $subcategories = $this->categoryModel->getSubcategories($categoryId);
        $this->jsonResponse(['subcategories' => $subcategories]);
    }

    public function getFoodInfo()
    {
        $foodId = intval($_GET['id'] ?? 0);

        if ($foodId <= 0) {
            $this->jsonResponse(['error' => 'Invalid food ID'], 400);
        }

        $food = $this->foodModel->getFoodDetails($foodId);

        if (!$food || !$food['is_available']) {
            $this->jsonResponse(['error' => 'Food not found'], 404);
        }

        $this->jsonResponse(['food' => $food]);
    }

    public function comment($foodId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {


            $userId = $_SESSION['user']['id'];
            $rating = intval($_POST['rate'] ?? 0);
            $comment = trim($_POST['content'] ?? '');
            $photos = [];



            // Xử lý upload ảnh (nếu có)
            if (!empty($_FILES['photo']['name'][0])) {
                foreach ($_FILES['photo']['tmp_name'] as $idx => $tmpName) {
                    if ($_FILES['photo']['error'][$idx] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($_FILES['photo']['name'][$idx], PATHINFO_EXTENSION);
                        $fileName = uniqid('review_', true) . '.' . $ext;
                        move_uploaded_file($tmpName, __DIR__ . '/../assets/images/' . $fileName);
                        $photos[] = $fileName;
                    }
                }
            }

            // Lấy orderId của đơn hàng completed gần nhất có món này
            $orderId = $this->orderModel->getCompletedOrderIdByUserAndFood($userId, $foodId);
           
            //test data
            // echo "User ID: $userId<br>";
            // echo "Order ID: $orderId<br>";
            // echo "Food ID: $foodId<br>";
            // echo "Rating: $rating<br>";
            // echo "Comment: $comment<br>";
            // echo "Photos: " . implode(', ', $photos) . "<br>";
            // Kiểm tra dữ liệu
            if ($rating < 1 || $rating > 5) {
                $this->jsonResponse(['error' => 'Invalid rating'], 400);
            }
            echo '</pre>';
            // Lưu vào DB
            $this->reviewModel->addReview($userId, $orderId, $foodId, $rating, $comment, $photos);

            // Redirect về trang chi tiết món ăn
            header('Location: ' . SITE_URL . '/food/detail/' . $foodId);
            exit;
        }
    }
}
