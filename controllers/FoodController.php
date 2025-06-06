<?php
/**
 * Food/Menu Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Food.php';
require_once __DIR__ . '/../models/Category.php';

class FoodController extends BaseController {
    private $foodModel;
    private $categoryModel;

    public function __construct() {
        $this->foodModel = new Food();
        $this->categoryModel = new Category();
    }

    public function index() {
        $this->menu();
    }

    public function menu() {
        // Get pagination parameters
        $page = intval($_GET['p'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $categoryId = intval($_GET['category'] ?? 0);
        $subcategoryId = intval($_GET['subcategory'] ?? 0);
        $search = sanitizeInput($_GET['search'] ?? '');
        $sortBy = $_GET['sort'] ?? 'name';
        $priceRange = $_GET['price_range'] ?? '';        // Build filter conditions
        $conditions = ['f.is_available = 1'];
        $params = [];

        if ($categoryId > 0) {
            $conditions[] = 'f.category_id = :category_id';
            $params[':category_id'] = $categoryId;
        }

        if ($subcategoryId > 0) {
            $conditions[] = 'f.subcategory_id = :subcategory_id';
            $params[':subcategory_id'] = $subcategoryId;
        }

        if (!empty($search)) {
            $conditions[] = '(f.name LIKE :search OR f.description LIKE :search)';
            $params[':search'] = "%$search%";
        }        if (!empty($priceRange)) {
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

        $whereClause = implode(' AND ', $conditions);        // Get sorting
        switch ($sortBy) {
            case 'price_asc':
                $orderBy = 'f.price ASC';
                break;
            case 'price_desc':
                $orderBy = 'f.price DESC';
                break;
            case 'name':
            default:
                $orderBy = 'f.name ASC';
                break;
        }

        // Get foods with pagination
        $foods = $this->foodModel->getFoodWithFilters($whereClause, $params, $orderBy, $limit, $offset);

        // Get total count for pagination
        $totalFoods = $this->foodModel->countFoodWithFilters($whereClause, $params);
        $totalPages = ceil($totalFoods / $limit);

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
            'filters' => [
                'category' => $categoryId,
                'subcategory' => $subcategoryId,
                'search' => $search,
                'sort' => $sortBy,
                'price_range' => $priceRange
            ]
        ];

        $this->loadView('customer/menu/index', $data);
    }    public function detail() {
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

        $data = [
            'title' => $food['name'] . ' - ' . SITE_NAME,
            'food' => $food,
            'category' => $category,
            'relatedFoods' => $relatedFoods
        ];

        $this->loadView('customer/menu/detail', $data);
    }

    public function category() {
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

    public function search() {
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
    public function getSubcategories() {
        $categoryId = intval($_GET['category_id'] ?? 0);

        if ($categoryId <= 0) {
            $this->jsonResponse(['error' => 'Invalid category ID'], 400);
        }

        $subcategories = $this->categoryModel->getSubcategories($categoryId);
        $this->jsonResponse(['subcategories' => $subcategories]);
    }

    public function getFoodInfo() {
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
}
?>
