<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Review.php';
class ReviewController extends BaseController {
    protected $reviewModel;

    public function __construct() {
        $this->reviewModel = new Review();
    }

    public function delete($reviewId) {
        $this->requireLogin();
        $review = $this->reviewModel->getReviewById($reviewId);
        if ($review && $review['user_id'] == $_SESSION['user_id']) {
            $this->reviewModel->deleteReview($reviewId);
            $_SESSION['message'] = 'Xóa bình luận thành công!';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền xóa bình luận này!';
        }

        redirect(SITE_URL . '/food/detail/' . $review['food_item_id']);
    }
    public function like($reviewId) {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập!']);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $result = $this->reviewModel->toggleLike($reviewId, $userId);
        echo json_encode([
            'success' => true,
            'liked' => $result['liked'],
            'like_count' => $result['like_count']
        ]);
        exit;
    }
public function update($id)
{
    $this->requireLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $content = trim($_POST['content'] ?? '');
        $rate = intval($_POST['rate'] ?? 0);

        $review = $this->reviewModel->getReviewById($id);
        if (!$review || $review['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền sửa bình luận này!';
            redirect(SITE_URL . '/food/detail/' . ($review['food_item_id'] ?? ''));
            return;
        }

        // Xử lý xóa ảnh cũ
        $photos = !empty($review['photos']) ? json_decode($review['photos'], true) : [];
        $deletePhotos = $_POST['delete_photos'] ?? [];
        $photos = array_diff($photos, $deletePhotos);
        // Xóa file vật lý nếu cần
        foreach ($deletePhotos as $del) {
            $file = __DIR__ . '/../assets/images/' . $del;
            if (file_exists($file)) unlink($file);
        }

        // Xử lý upload ảnh mới
        if (!empty($_FILES['photo']['name'][0])) {
            foreach ($_FILES['photo']['tmp_name'] as $idx => $tmpName) {
                if ($_FILES['photo']['error'][$idx] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['photo']['name'][$idx], PATHINFO_EXTENSION);
                    $newName = uniqid('review_') . '.' . $ext;
                    $dest = __DIR__ . '/../assets/images/' . $newName;
                    if (move_uploaded_file($tmpName, $dest)) {
                        $photos[] = $newName;
                    }
                }
            }
        }

        // Cập nhật bình luận
        $this->reviewModel->updateReview($id, $content, $rate, json_encode(array_values($photos)));

        $_SESSION['message'] = 'Cập nhật bình luận thành công!';
        redirect(SITE_URL . '/food/detail/' . $review['food_item_id']);
    } else {
        $_SESSION['error'] = 'Yêu cầu không hợp lệ!';
        redirect(SITE_URL . '/');
    }
}
}
