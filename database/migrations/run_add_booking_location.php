<?php
// Run migration to add booking_location field to bookings table

require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Adding booking_location field to bookings table...\n";

    $sql = "ALTER TABLE `bookings`
            ADD COLUMN `booking_location` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
            AFTER `guest_count`";

    $db->exec($sql);

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";

    // Check if column already exists
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column booking_location already exists!\n";
    }
}
?>
