<?php
/**
 * News Model
 */

require_once 'BaseModel.php';

class News extends BaseModel {
    protected $table = 'news';

    /**
     * Get latest news articles
     * @param int $limit Number of articles to retrieve
     * @return array Array of news articles
     */
    public function getLatestNews($limit = 3) {
        $sql = "SELECT n.*, u.first_name, u.last_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.is_published = 1
                ORDER BY n.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }    /**
     * Get news article by ID
     * @param int $id News article ID
     * @return array|false News article data or false if not found
     */
    public function getNewsById($id) {
        $sql = "SELECT n.*, u.first_name, u.last_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.id = :id AND n.is_published = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Find news article by ID (admin version - includes unpublished)
     * @param int $id News article ID
     * @return array|false News article data or false if not found
     */
    public function findById($id) {
        $sql = "SELECT n.*, u.first_name, u.last_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get all news articles with pagination
     * @param int $limit Number of articles per page
     * @param int $offset Offset for pagination
     * @return array Array of news articles
     */
    public function getAllNews($limit = 10, $offset = 0) {
        $sql = "SELECT n.*, u.first_name, u.last_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.is_published = 1
                ORDER BY n.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a new news article
     * @param array $newsData News article data
     * @return bool Success status
     */
    public function createNews($newsData) {
        $sql = "INSERT INTO {$this->table} (title, content, excerpt, author_id, image_url, is_published, meta_title, meta_description)
                VALUES (:title, :content, :excerpt, :author_id, :image_url, :is_published, :meta_title, :meta_description)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $newsData['title'],
            ':content' => $newsData['content'],
            ':excerpt' => $newsData['excerpt'] ?? '',
            ':author_id' => $newsData['author_id'],
            ':image_url' => $newsData['image_url'] ?? null,
            ':is_published' => $newsData['is_published'] ?? 0,
            ':meta_title' => $newsData['meta_title'] ?? $newsData['title'],
            ':meta_description' => $newsData['meta_description'] ?? ''
        ]);
    }    /**
     * Update an existing news article
     * @param int $id News article ID
     * @param array $newsData Updated news data
     * @return bool Success status
     */
    public function updateNews($id, $newsData) {
        $sql = "UPDATE {$this->table} SET
                title = :title,
                content = :content,
                excerpt = :excerpt,
                image_url = :image_url,
                is_published = :is_published,
                meta_title = :meta_title,
                meta_description = :meta_description,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $newsData['title'],
            ':content' => $newsData['content'],
            ':excerpt' => $newsData['excerpt'] ?? '',
            ':image_url' => $newsData['image_url'] ?? null,
            ':is_published' => $newsData['is_published'] ?? 0,
            ':meta_title' => $newsData['meta_title'] ?? $newsData['title'],
            ':meta_description' => $newsData['meta_description'] ?? '',
            ':id' => $id
        ]);
    }

    /**
     * Delete a news article
     * @param int $id News article ID
     * @return bool Success status
     */
    public function deleteNews($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get published news count
     * @return int Number of published news articles
     */
    public function getPublishedNewsCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_published = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Get featured news articles
     * @param int $limit Number of featured articles to retrieve
     * @return array Array of featured news articles
     */
    public function getFeaturedNews($limit = 3) {
        $sql = "SELECT n.*, u.first_name, u.last_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.is_published = 1 AND n.is_featured = 1
                ORDER BY n.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }    /**
     * Search news articles
     * @param string $query Search query
     * @param int $limit Number of results to return
     * @return array Array of matching news articles
     */
    public function searchNews($query, $limit = 10) {
        $sql = "SELECT n.*, u.first_name, u.last_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.is_published = 1
                AND (n.title LIKE :query OR n.content LIKE :query OR n.excerpt LIKE :query)
                ORDER BY n.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($sql);
        $searchQuery = "%{$query}%";
        $stmt->bindValue(':query', $searchQuery);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all news articles
     * @param bool $publishedOnly Only count published news
     * @return int Number of news articles
     */
    public function countNews($publishedOnly = false) {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        if ($publishedOnly) {
            $sql .= " WHERE is_published = 1";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Get related news articles
     * @param int $currentId Current news article ID
     * @param int $limit Number of related articles to return
     * @return array Array of related news articles
     */
    public function getRelatedNews($currentId, $limit = 3) {
        $sql = "SELECT n.*, u.first_name, u.last_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.id != :id
                AND n.is_published = 1
                ORDER BY n.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $currentId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
