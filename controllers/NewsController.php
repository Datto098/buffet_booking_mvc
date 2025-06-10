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
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;        if ($id <= 0) {
            redirect('/news');
        }

        $newsItem = $this->newsModel->getNewsById($id);

        if (!$newsItem) {
            redirect('/news');
        }

        // Get related news
        $relatedNews = $this->newsModel->getRelatedNews($id, 3);

        $data = [
            'title' => $newsItem['title'] . ' - ' . SITE_NAME,
            'news_item' => $newsItem,
            'related_news' => $relatedNews
        ];

        $this->loadView('customer/news/detail', $data);
    }    /**
     * Admin: List all news articles
     */
    public function manage() {
        $this->requireAdmin();

        // Use getAllForAdmin() to get properly formatted news data with status field
        $news = $this->newsModel->getAllForAdmin();

        // Calculate statistics for the dashboard cards
        $totalNews = count($news);
        $publishedNews = count(array_filter($news, function($article) {
            return $article['status'] === 'published';
        }));

        $draftNews = count(array_filter($news, function($article) {
            return $article['status'] === 'draft';
        }));

        $newToday = count(array_filter($news, function($article) {
            return date('Y-m-d', strtotime($article['created_at'])) === date('Y-m-d');
        }));

        $data = [
            'title' => 'Quản Lý Tin Tức - Admin',
            'news' => $news,
            'totalNews' => $totalNews,
            'publishedNews' => $publishedNews,
            'draftNews' => $draftNews,
            'newToday' => $newToday
        ];

        $this->loadAdminView('news/index', $data);
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

            $this->loadAdminView('news/create', $data);
        }
    }    /**
     * Admin: Edit a news article
     */
    public function edit($id = null) {
        $this->requireAdmin();

        $id = $id ? (int)$id : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

        if ($id <= 0) {
            redirect('/admin/news');
        }

        $newsItem = $this->newsModel->findById($id);

        if (!$newsItem) {
            $_SESSION['error'] = 'Không tìm thấy bài viết';
            redirect('/admin/news');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleNewsSubmission($id);
        } else {            $data = [
                'title' => 'Chỉnh Sửa Tin Tức - Admin',
                'news_item' => $newsItem
            ];

            $this->loadAdminView('news/edit', $data);
        }
    }    /**
     * Admin: Delete a news article
     */
    public function delete($id = null) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/news');
        }

        $this->validateCSRF();

        $id = $id ? (int)$id : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'ID không hợp lệ';
            redirect('/admin/news');
        }

        if ($this->newsModel->deleteNews($id)) {
            $_SESSION['success'] = 'Xóa tin tức thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tin tức';
        }

        redirect('/admin/news');
    }

    /**
     * Toggle news status (publish/unpublish)
     */
    public function toggleStatus() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/news');
        }

        $this->validateCSRF();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = sanitizeInput($_POST['status'] ?? '');

        if ($id <= 0 || !in_array($status, ['published', 'draft', 'archived'])) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ';
            redirect('/admin/news');
        }

        if ($this->newsModel->updateNewsStatus($id, $status)) {
            $_SESSION['success'] = 'Cập nhật trạng thái thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái';
        }

        redirect('/admin/news');
    }

    /**
     * Handle bulk actions on news
     */
    public function bulkAction() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/news');
        }

        $this->validateCSRF();

        $action = sanitizeInput($_POST['action'] ?? '');
        $ids = $_POST['ids'] ?? [];

        if (empty($action) || empty($ids) || !is_array($ids)) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ';
            redirect('/admin/news');
        }

        // Sanitize IDs
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, function($id) { return $id > 0; });

        if (empty($ids)) {
            $_SESSION['error'] = 'Không có bài viết nào được chọn';
            redirect('/admin/news');
        }

        $success = false;
        $successMessage = '';

        switch ($action) {
            case 'publish':
                $success = $this->newsModel->bulkUpdateStatus($ids, 'published');
                $successMessage = 'Đã xuất bản ' . count($ids) . ' bài viết';
                break;

            case 'unpublish':
                $success = $this->newsModel->bulkUpdateStatus($ids, 'draft');
                $successMessage = 'Đã hủy xuất bản ' . count($ids) . ' bài viết';
                break;

            case 'delete':
                $success = $this->newsModel->bulkDelete($ids);
                $successMessage = 'Đã xóa ' . count($ids) . ' bài viết';
                break;

            default:
                $_SESSION['error'] = 'Hành động không hợp lệ';
                redirect('/admin/news');
        }

        if ($success) {
            $_SESSION['success'] = $successMessage;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thực hiện hành động';
        }

        redirect('/admin/news');
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
        }        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;

            if ($id) {
                redirect('/admin/news/edit?id=' . $id);
            } else {
                redirect('/admin/news/create');
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
            $uploadResult = $this->uploadNewsImage($_FILES['image']);            if ($uploadResult['success']) {
                $newsData['image_url'] = $uploadResult['filename'];
            } else {
                $_SESSION['error'] = $uploadResult['message'];
                $_SESSION['form_data'] = $_POST;

                if ($id) {
                    redirect('/admin/news/edit?id=' . $id);
                } else {
                    redirect('/admin/news/create');
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
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật tin tức';            }

            redirect('/admin/news/edit?id=' . $id);
        } else {
            // Create new news
            if ($this->newsModel->createNews($newsData)) {
                $_SESSION['success'] = 'Thêm tin tức thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi thêm tin tức';
            }

            redirect('/admin/news/create');
        }
    }    /**
     * Upload news image
     */
    private function uploadNewsImage($file) {
        $uploadDir = 'uploads/news_images/';

        // Create directory if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Check for upload errors first
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File quá lớn (vượt quá upload_max_filesize).',
                UPLOAD_ERR_FORM_SIZE => 'File quá lớn (vượt quá MAX_FILE_SIZE).',
                UPLOAD_ERR_PARTIAL => 'File chỉ được upload một phần.',
                UPLOAD_ERR_NO_FILE => 'Không có file nào được upload.',
                UPLOAD_ERR_NO_TMP_DIR => 'Thư mục tạm không tồn tại.',
                UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file lên đĩa.',
                UPLOAD_ERR_EXTENSION => 'Upload bị dừng bởi extension.'
            ];

            $message = $errorMessages[$file['error']] ?? 'Có lỗi không xác định khi upload file.';
            return [
                'success' => false,
                'message' => $message
            ];
        }

        // Check if tmp_name is valid
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return [
                'success' => false,
                'message' => 'File upload không hợp lệ.'
            ];
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
     */    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'CSRF token không hợp lệ';
            redirect('/news');
            exit;
        }
    }/**
     * Require admin authentication
     */
    protected function requireAdmin() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['manager', 'super_admin'])) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            redirect('/auth/login');
            exit;
        }
    }
}
?>
