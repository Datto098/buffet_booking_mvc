-- Add delivery and customer information fields to orders table
-- Migration: add_delivery_fields_to_orders.sql

ALTER TABLE `orders`
ADD COLUMN `delivery_address` TEXT NULL AFTER `customer_phone`,
ADD COLUMN `delivery_ward` VARCHAR(100) NULL AFTER `delivery_address`,
ADD COLUMN `delivery_district` VARCHAR(100) NULL AFTER `delivery_ward`,
ADD COLUMN `delivery_city` VARCHAR(100) NULL AFTER `delivery_district`,
ADD COLUMN `delivery_notes` TEXT NULL AFTER `delivery_city`,
ADD COLUMN `delivery_fee` DECIMAL(10,2) DEFAULT 0.00 AFTER `delivery_notes`,
ADD COLUMN `service_fee` DECIMAL(10,2) DEFAULT 0.00 AFTER `delivery_fee`,
ADD COLUMN `discount_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `service_fee`,
ADD COLUMN `coupon_code` VARCHAR(50) NULL AFTER `discount_amount`,
ADD COLUMN `order_notes` TEXT NULL AFTER `coupon_code`;

-- Update existing orders to have proper customer information
UPDATE `orders` SET
    `customer_name` = CASE
        WHEN `user_id` = 1 THEN 'Admin User'
        WHEN `user_id` = 2 THEN 'John Doe'
        ELSE 'Customer'
    END,
    `customer_email` = CASE
        WHEN `user_id` = 1 THEN 'admin@buffet.com'
        WHEN `user_id` = 2 THEN 'john@example.com'
        ELSE 'customer@example.com'
    END,
    `customer_phone` = CASE
        WHEN `user_id` = 1 THEN '0123456789'
        WHEN `user_id` = 2 THEN '0987654321'
        ELSE '0123456789'
    END
WHERE `customer_name` = '' OR `customer_name` IS NULL;

-- Update order_items to have proper unit_price and total_price
UPDATE `order_items` oi
JOIN `food_items` fi ON oi.food_item_id = fi.id
SET
    oi.unit_price = fi.price,
    oi.total_price = oi.quantity * fi.price
WHERE oi.unit_price = 0.00 OR oi.total_price = 0.00;

-- Update orders subtotal based on order_items
UPDATE `orders` o
SET o.subtotal = (
    SELECT IFNULL(SUM(oi.total_price), 0)
    FROM `order_items` oi
    WHERE oi.order_id = o.id
)
WHERE o.subtotal = 0.00;
