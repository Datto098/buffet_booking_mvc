-- Update dine_in_orders status enum to match orders table
ALTER TABLE `dine_in_orders` MODIFY COLUMN `status`
ENUM('pending','confirmed','preparing','ready','completed','delivered','cancelled')
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending';

-- Update any existing 'served' status to 'delivered' to maintain data consistency
UPDATE `dine_in_orders` SET `status` = 'delivered' WHERE `status` = 'served';
