-- Add missing application_type column to promotions table
ALTER TABLE promotions
ADD COLUMN application_type ENUM('all', 'specific_items', 'categories') DEFAULT 'all'
AFTER type;

-- Update existing promotions to have default application_type
UPDATE promotions SET application_type = 'all' WHERE application_type IS NULL;

-- Show the updated table structure
DESCRIBE promotions;
