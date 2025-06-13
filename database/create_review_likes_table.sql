-- Create review_likes table for supporting review like functionality
-- This table stores which users have liked which reviews

CREATE TABLE IF NOT EXISTS `review_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `review_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_review` (`user_id`, `review_id`),
  KEY `fk_review_likes_user` (`user_id`),
  KEY `fk_review_likes_review` (`review_id`),
  CONSTRAINT `fk_review_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_likes_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
