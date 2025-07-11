<?php
// Update tables to make them available for booking

require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Making tables available for booking...\n";

    $sql = "UPDATE `tables` SET `is_available` = 1 WHERE `status` = 'available'";

    $affected = $db->exec($sql);

    echo "Updated $affected tables to be available.\n";
    echo "Update completed successfully!\n";

} catch (Exception $e) {
    echo "Update failed: " . $e->getMessage() . "\n";
}
?>
