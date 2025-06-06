<?php
/**
 * News Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/News.php';

class NewsController extends BaseController {
    private $newsModel;

    public function __construct() {
        $this->newsModel = new News();
    }

    /**
     * Display the news listing page
     */
    public function index() {
        // Get latest news with pagination
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $limit = 6; // News articles per page
        $offset = ($page - 1) * $limit;

        $news = $this->newsModel->getAllNews($limit, $offset);
        $totalNews = $this->newsModel->countNews(true); // Only published news
        $totalPages = ceil($totalNews / $limit);

        // Data to pass to the view
        $data = [
            'title' => 'Tin Tức - ' . SITE_NAME,
            'news' => $news,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_news' => $totalNews
        ];

        $this->loadView('customer/news/index', $data);
    }

    /**
     * Display a single news article
     */
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            redirect('/index.php?page=news');
        }

        $newsItem = $this->newsModel->getNewsById($id);

        if (!$newsItem) {
            redirect('/index.php?page=news');
        }

        // Get related news
        $relatedNews = $this->newsModel->getRelatedNews($id, 3);

        $data = [
            'title' => $newsItem['title'] . ' - ' . SITE_NAME,
            'news_item' => $newsItem,
            'related_news' => $relatedNews
        ];

        $this->loadView('customer/news/detail', $data);
    }

    /**
     * Admin: List all news articles
     */
    public function manage() {
        $this->requireAdmin();

        $news = $this->newsModel->getAllNews();

        $data = [
            'title' => 'Quản Lý Tin Tức - Admin',
            'news' => $news
        ];

        $this->loadView('admin/news/index', $data);
    }

    /**
     * Admin: Create a new news article
     */
    public function create() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleNewsSubmission();
        } else {
            $data = [
                'title' => 'Thêm Tin Tức Mới - Admin'
            ];

            $this->loadView('admin/news/create', $data);
        }
    }

    /**
     * Admin: Edit a news article
     */
    public function edit() {
        $this->requireAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            redirect('/index.php?page=news&action=manage');
        }

        $newsItem = $this->newsModel->findById($id);

        if (!$newsItem) {
            $_SESSION['error'] = 'Không tìm thấy bài viết';
            redirect('/index.php?page=news&action=manage');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleNewsSubmission($id);
        } else {
            $data = [
                'title' => 'Chỉnh Sửa Tin Tức - Admin',
                'news_item' => $newsItem
            ];

            $this->loadView('admin/news/edit', $data);
        }
    }

    /**
     * Admin: Delete a news article
     */
    public function delete() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=news&action=manage');
        }

        $this->validateCSRF();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id <= 0) {
            $_SESSION['error'] = 'ID không hợp lệ';
            redirect('/index.php?page=news&action=manage');
        }

        if ($this->newsModel->deleteNews($id)) {
            $_SESSION['success'] = 'Xóa tin tức thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tin tức';
        }

        redirect('/index.php?page=news&action=manage');
    }

    /**
     * Handle news submission (create/update)
     */
    private function handleNewsSubmission($id = null) {
        $this->validateCSRF();

        // Get form data
        $title = sanitizeInput($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $excerpt = sanitizeInput($_POST['excerpt'] ?? '');
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        // Validation
        $errors = [];

        if (empty($title)) {
            $errors[] = 'Vui lòng nhập tiêu đề';
        }

        if (empty($content)) {
            $errors[] = 'Vui lòng nhập nội dung';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;

            if ($id) {
                redirect('/index.php?page=news&action=edit&id=' . $id);
            } else {
                redirect('/index.php?page=news&action=create');
            }
            return;
        }

        // Prepare news data
        $newsData = [
            'title' => $title,
            'content' => $content,
            'excerpt' => $excerpt,
            'is_published' => $isPublished,
            'author_id' => $_SESSION['user_id']
        ];
          // Handle image upload if provided
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = $this->uploadNewsImage($_FILES['image']);

            if ($uploadResult['success']) {
                $newsData['image_url'] = $uploadResult['filename'];
            } else {
                $_SESSION['error'] = $uploadResult['message'];
                $_SESSION['form_data'] = $_POST;

                if ($id) {
                    redirect('/index.php?page=news&action=edit&id=' . $id);
                } else {
                    redirect('/index.php?page=news&action=create');
                }
                return;
            }
        }

        // Create or update news
        if ($id) {
            // Update existing news
            if ($this->newsModel->updateNews($id, $newsData)) {
                $_SESSION['success'] = 'Cập nhật tin tức thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật tin tức';
            }

            redirect('/index.php?page=news&action=edit&id=' . $id);
        } else {
            // Create new news
            if ($this->newsModel->createNews($newsData)) {
                $_SESSION['success'] = 'Thêm tin tức thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi thêm tin tức';
            }

            redirect('/index.php?page=news&action=create');
        }
    }
      /**
     * Upload news image
     */
    private function uploadNewsImage($file) {
        $uploadDir = 'uploads/news_images/';

        // Create directory if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $uploadFile = $uploadDir . $filename;

        // Check if image file is a actual image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return [
                'success' => false,
                'message' => 'File không phải là hình ảnh.'
            ];
        }

        // Check file size (max 2MB)
        if ($file['size'] > 2000000) {
            return [
                'success' => false,
                'message' => 'Kích thước file quá lớn (tối đa 2MB).'
            ];
        }

        // Allow certain file formats
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            return [
                'success' => false,
                'message' => 'Chỉ chấp nhận file JPG, JPEG, PNG & GIF.'
            ];
        }

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            return [
                'success' => true,
                'filename' => $filename
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lên file.'
            ];
        }
    }    /**
     * Validate CSRF token
     */
    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'CSRF token không hợp lệ';
            redirect('/index.php?page=news');
            exit;
        }
    }

    /**
     * Require admin authentication
     */
    protected function requireAdmin() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['manager', 'super_admin'])) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            redirect('/index.php?page=auth&action=login');
            exit;
        }
    }
}
?>
