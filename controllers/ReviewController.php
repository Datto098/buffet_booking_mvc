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

        redirect('/food/details/' . $review['food_id']);
    }
    public function like($reviewId) {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập!']);
            exit;
        }
        $likeCount = $this->reviewModel->toggleLike($reviewId, $_SESSION['user_id']);
        echo json_encode(['success' => true, 'like_count' => $likeCount]);
        exit;
    }
}