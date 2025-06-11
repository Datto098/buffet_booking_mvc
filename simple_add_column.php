<?php
/**
 * Simple script to add application_type column
 */

echo "Starting database fix...\n";

try {
    // Direct database connection
    $dsn = "mysql:host=localhost;dbname=buffet_booking;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Database connected successfully\n";

    // Check if application_type column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM promotions LIKE 'application_type'");
    $columnExists = $stmt->rowCount() > 0;

    if ($columnExists) {
        echo "application_type column already exists\n";
    } else {
        echo "Adding application_type column...\n";

        // Add the missing column
        $sql = "ALTER TABLE promotions ADD COLUMN application_type ENUM('all', 'specific_items', 'categories') DEFAULT 'all' AFTER type";

        $pdo->exec($sql);
        echo "Successfully added application_type column\n";

        // Update existing promotions
        $updateSql = "UPDATE promotions SET application_type = 'all' WHERE application_type IS NULL";
        $affected = $pdo->exec($updateSql);
        echo "Updated $affected existing promotions\n";
    }

    // Verify the column was added
    $stmt = $pdo->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Current promotions table columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }

    echo "Column addition completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
