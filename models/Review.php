<?php
require_once 'BaseModel.php';
class Review extends BaseModel
{
    protected $table = 'reviews';
    /**
     * Get reviews for a food item
     *
     * @param int $foodItemId
     * @param int $limit
     * @return array
     */
    public function addReview($userId, $orderId, $foodItemId, $rating, $comment, $photos = [], $title = '')
    {

        $photosJson = json_encode($photos);
        $sql = "INSERT INTO reviews (user_id, order_id, food_item_id, rating, title, comment, photos, is_verified, is_approved, helpful_count, created_at, updated_at)
                VALUES (:user_id, :order_id, :food_item_id, :rating, :title, :comment, :photos, 1, 1, 0, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindValue(':food_item_id', $foodItemId, PDO::PARAM_INT);
        $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindValue(':photos', $photosJson, PDO::PARAM_STR);
        $success = $stmt->execute();
        if ($success) {
            return [
                'success' => true,
                'insert_id' => $this->db->lastInsertId()
            ];
        } else {
            return [
                'success' => false,
                'error' => $stmt->errorInfo()
            ];
        }
    }
    public function getReviewsByFood($foodItemId, $limit = 10)
    {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.avatar
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.food_item_id = :food_item_id AND r.is_approved = 1
                ORDER BY r.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':food_item_id', $foodItemId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAverageRatingByFood($foodItemId)
    {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews WHERE food_item_id = :food_item_id AND is_approved = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':food_item_id', $foodItemId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getReviewById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteReview($id)
    {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function hasUserReviewedFood($userId, $foodItemId)
    {
        $sql = "SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND food_item_id = :food_item_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':food_item_id', $foodItemId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function toggleLike($reviewId, $userId)
    {
        // Kiểm tra đã like chưa
        $stmt = $this->db->prepare("SELECT 1 FROM review_likes WHERE user_id = ? AND review_id = ?");
        $stmt->execute([$userId, $reviewId]);
        $liked = $stmt->fetchColumn();

        if ($liked) {
            // Unlike: xóa like và giảm helpful_count
            $this->db->prepare("DELETE FROM review_likes WHERE user_id = ? AND review_id = ?")->execute([$userId, $reviewId]);
            $this->db->prepare("UPDATE reviews SET helpful_count = GREATEST(helpful_count - 1, 0) WHERE id = ?")->execute([$reviewId]);
            $isLiked = false;
        } else {
            // Like: thêm like và tăng helpful_count
            $this->db->prepare("INSERT INTO review_likes (user_id, review_id) VALUES (?, ?)")->execute([$userId, $reviewId]);
            $this->db->prepare("UPDATE reviews SET helpful_count = helpful_count + 1 WHERE id = ?")->execute([$reviewId]);
            $isLiked = true;
        }

        // Lấy lại số like mới
        $stmt = $this->db->prepare("SELECT helpful_count FROM reviews WHERE id = ?");
        $stmt->execute([$reviewId]);
        $count = (int)$stmt->fetchColumn();

        return [
            'liked' => $isLiked,
            'like_count' => $count
        ];
    }
    public function hasUserLiked($userId, $reviewId)
    {
        $stmt = $this->db->prepare("SELECT 1 FROM review_likes WHERE user_id = ? AND review_id = ?");
        $stmt->execute([$userId, $reviewId]);
        return $stmt->fetchColumn() ? true : false;
    }
    public function updateReview($id, $content, $rating, $photos = null)
    {
        $sql = "UPDATE reviews SET comment = :comment, rating = :rating, updated_at = NOW()";
        if ($photos !== null) {
            $sql .= ", photos = :photos";
        }
        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':comment', $content, PDO::PARAM_STR);
        $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
        if ($photos !== null) {
            $stmt->bindValue(':photos', $photos, PDO::PARAM_STR);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function getAverageRating($foodId) {
        $sql = "SELECT AVG(rating) as avg FROM reviews WHERE food_item_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$foodId]);
        $row = $stmt->fetch();
        return $row ? floatval($row['avg']) : 0;
    }

    public function getTotalRating($foodId) {
        $sql = "SELECT COUNT(*) as total FROM reviews WHERE food_item_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$foodId]);
        $row = $stmt->fetch();
        return $row ? intval($row['total']) : 0;
    }

    /**
     * Get all reviews with pagination and filters for admin management
     * @param int $limit
     * @param int $offset
     * @param array $filters
     * @return array
     */    public function getAllReviews($limit = 20, $offset = 0, $filters = [])
    {
        $sql = "SELECT r.*,
                       u.first_name, u.last_name, u.email as user_email, u.avatar,
                       u.phone as user_phone, u.created_at as user_created_at,
                       f.name as food_name, f.image as food_image,
                       c.name as category_name
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN food_items f ON r.food_item_id = f.id
                LEFT JOIN categories c ON f.category_id = c.id
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'approved') {
                $sql .= " AND r.is_approved = 1";
            } elseif ($filters['status'] === 'pending') {
                $sql .= " AND r.is_approved = 0";
            } elseif ($filters['status'] === 'verified') {
                $sql .= " AND r.is_verified = 1";
            }
        }

        if (!empty($filters['rating'])) {
            $sql .= " AND r.rating = :rating";
            $params[':rating'] = (int)$filters['rating'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (r.title LIKE :search OR r.comment LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search OR f.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count reviews with filters
     * @param array $filters
     * @return int
     */
    public function countReviews($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN food_items f ON r.food_item_id = f.id
                WHERE 1=1";

        $params = [];

        // Apply same filters as getAllReviews
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'approved') {
                $sql .= " AND r.is_approved = 1";
            } elseif ($filters['status'] === 'pending') {
                $sql .= " AND r.is_approved = 0";
            } elseif ($filters['status'] === 'verified') {
                $sql .= " AND r.is_verified = 1";
            }
        }

        if (!empty($filters['rating'])) {
            $sql .= " AND r.rating = :rating";
            $params[':rating'] = (int)$filters['rating'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (r.title LIKE :search OR r.comment LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search OR f.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get review statistics for admin dashboard
     * @return array
     */
    public function getReviewStats()
    {
        $stats = [];

        // Total reviews
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews");
        $stmt->execute();
        $stats['total_reviews'] = (int)$stmt->fetchColumn();

        // Approved reviews
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE is_approved = 1");
        $stmt->execute();
        $stats['approved_reviews'] = (int)$stmt->fetchColumn();

        // Pending reviews
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE is_approved = 0");
        $stmt->execute();
        $stats['pending_reviews'] = (int)$stmt->fetchColumn();

        // Verified reviews
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE is_verified = 1");
        $stmt->execute();
        $stats['verified_reviews'] = (int)$stmt->fetchColumn();

        // Average rating
        $stmt = $this->db->prepare("SELECT AVG(rating) FROM reviews WHERE is_approved = 1");
        $stmt->execute();
        $stats['average_rating'] = (float)$stmt->fetchColumn() ?: 0;

        return $stats;
    }

    /**
     * Approve a review
     * @param int $reviewId
     * @return bool
     */
    public function approveReview($reviewId)
    {
        $stmt = $this->db->prepare("UPDATE reviews SET is_approved = 1, updated_at = NOW() WHERE id = :id");
        $stmt->bindValue(':id', $reviewId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Reject a review
     * @param int $reviewId
     * @return bool
     */
    public function rejectReview($reviewId)
    {
        $stmt = $this->db->prepare("UPDATE reviews SET is_approved = 0, updated_at = NOW() WHERE id = :id");
        $stmt->bindValue(':id', $reviewId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verify a review
     * @param int $reviewId
     * @return bool
     */
    public function verifyReview($reviewId)
    {
        $stmt = $this->db->prepare("UPDATE reviews SET is_verified = 1, updated_at = NOW() WHERE id = :id");
        $stmt->bindValue(':id', $reviewId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get review details with related data
     * @param int $reviewId
     * @return array|false
     */    public function getReviewDetails($reviewId)
    {
        $sql = "SELECT r.*,
                       u.first_name, u.last_name, u.email as user_email, u.avatar,
                       u.phone as user_phone, u.created_at as user_created_at,
                       f.name as food_name, f.image as food_image, f.price as food_price,
                       c.name as category_name,
                       o.id as order_id, o.created_at as order_date
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN food_items f ON r.food_item_id = f.id
                LEFT JOIN categories c ON f.category_id = c.id
                LEFT JOIN orders o ON r.order_id = o.id
                WHERE r.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $reviewId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Bulk approve reviews
     * @param array $reviewIds
     * @return bool
     */
    public function bulkApproveReviews($reviewIds)
    {
        if (empty($reviewIds)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($reviewIds) - 1) . '?';
        $sql = "UPDATE reviews SET is_approved = 1, updated_at = NOW() WHERE id IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($reviewIds);
    }

    /**
     * Bulk delete reviews
     * @param array $reviewIds
     * @return bool
     */
    public function bulkDeleteReviews($reviewIds)
    {
        if (empty($reviewIds)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($reviewIds) - 1) . '?';
        $sql = "DELETE FROM reviews WHERE id IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($reviewIds);
    }
}
