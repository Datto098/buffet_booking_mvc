-- Add missing columns to orders table for enhanced admin functionality
ALTER TABLE orders ADD COLUMN delivery_address TEXT NULL AFTER special_instructions;
ALTER TABLE orders ADD COLUMN order_notes TEXT NULL AFTER delivery_address;

-- Update the status enum to include 'delivered' if not already present
ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','ready','completed','delivered','cancelled') DEFAULT 'pending';

-- Show table structure after modifications
DESCRIBE orders;
