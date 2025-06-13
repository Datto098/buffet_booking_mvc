-- Add VNPay payment integration tables
-- File: create_vnpay_payment_tables.sql

-- Create payments table to track VNPay transactions
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'vnpay',
  `vnp_txn_ref` varchar(100) NOT NULL, -- Mã giao dịch VNPay
  `vnp_amount` bigint NOT NULL, -- Số tiền (VND * 100)
  `vnp_order_info` text, -- Thông tin đơn hàng
  `vnp_response_code` varchar(2) DEFAULT NULL, -- Mã phản hồi từ VNPay
  `vnp_transaction_no` varchar(100) DEFAULT NULL, -- Mã giao dịch của VNPay
  `vnp_bank_code` varchar(20) DEFAULT NULL, -- Mã ngân hàng
  `vnp_pay_date` varchar(14) DEFAULT NULL, -- Thời gian thanh toán
  `vnp_secure_hash` text, -- Secure hash từ VNPay
  `payment_status` enum('pending','processing','completed','failed','cancelled','refunded') DEFAULT 'pending',
  `payment_data` json DEFAULT NULL, -- Lưu toàn bộ response data từ VNPay
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vnp_txn_ref` (`vnp_txn_ref`),
  KEY `fk_payments_order` (`order_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_vnp_transaction_no` (`vnp_transaction_no`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

-- Update orders table to add vnpay payment method
ALTER TABLE `orders`
MODIFY COLUMN `payment_method` enum('cod','cash','credit_card','debit_card','digital_wallet','bank_transfer','vnpay') COLLATE utf8mb4_unicode_ci NOT NULL;

-- Add payment_id reference to orders table (optional - for quick lookup)
ALTER TABLE `orders`
ADD COLUMN `payment_id` int DEFAULT NULL AFTER `payment_status`,
ADD KEY `fk_orders_payment` (`payment_id`);

-- Add foreign key (optional)
-- ALTER TABLE `orders`
-- ADD CONSTRAINT `fk_orders_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

-- Create admin view for payment management
CREATE OR REPLACE VIEW `v_payments_with_orders` AS
SELECT
    p.*,
    o.order_number,
    o.customer_name,
    o.customer_email,
    o.customer_phone,
    o.total_amount as order_total,
    o.status as order_status,
    o.created_at as order_created_at
FROM payments p
LEFT JOIN orders o ON p.order_id = o.id
ORDER BY p.created_at DESC;
