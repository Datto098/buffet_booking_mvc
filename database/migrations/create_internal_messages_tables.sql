-- Migration: Create Internal Messages Tables
-- Date: 2025-07-08
-- Description: Add internal messaging system for Super Admin to Admin communication

-- Create internal_messages table
CREATE TABLE IF NOT EXISTS `internal_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_type` enum('system_update','policy_change','maintenance','personal','general') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `priority` enum('low','normal','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `is_broadcast` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_internal_messages_sender` (`sender_id`),
  KEY `idx_message_type` (`message_type`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_internal_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create internal_message_recipients table
CREATE TABLE IF NOT EXISTS `internal_message_recipients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message_id` int NOT NULL,
  `recipient_id` int NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_message_recipient` (`message_id`,`recipient_id`),
  KEY `fk_message_recipients_message` (`message_id`),
  KEY `fk_message_recipients_recipient` (`recipient_id`),
  KEY `idx_is_read` (`is_read`),
  CONSTRAINT `fk_message_recipients_message` FOREIGN KEY (`message_id`) REFERENCES `internal_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_message_recipients_recipient` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO `internal_messages` (`sender_id`, `title`, `content`, `message_type`, `priority`, `is_broadcast`, `created_at`) VALUES
(4, 'Chào mừng sử dụng hệ thống thông báo nội bộ', 'Đây là thông báo đầu tiên để kiểm tra hệ thống thông báo nội bộ. Hệ thống này cho phép Super Admin gửi thông báo đến các Admin một cách nhanh chóng và hiệu quả.', 'general', 'normal', 1, NOW());

-- Get the message ID for recipient assignment
SET @message_id = LAST_INSERT_ID();

-- Assign recipients (assuming user IDs 1 and 4 are admin users)
INSERT INTO `internal_message_recipients` (`message_id`, `recipient_id`, `created_at`) VALUES
(@message_id, 1, NOW()),
(@message_id, 4, NOW());

-- Update success message
SELECT 'Internal messages tables created successfully!' as status;
