-- Add booking_location field to bookings table
ALTER TABLE `bookings`
ADD COLUMN `booking_location` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
AFTER `guest_count`;

-- Update existing records to have a default location (optional)
-- UPDATE `bookings` SET `booking_location` = 'Hẻm 153 Bà Hom, Quận 6, Thành phố Hồ Chí Minh, Việt Nam' WHERE `booking_location` IS NULL;
