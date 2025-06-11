<?php
/**
 * Fix missing application_type column in promotions table
 */

echo "=== FIXING PROMOTIONS TABLE STRUCTURE ===\n";

try {
    // Direct database connection
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "✅ Database connected successfully\n";

    // Check current table structure
    echo "\n1. Checking current promotions table structure...\n";
    $stmt = $pdo->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll();

    $hasApplicationType = false;
    echo "Current columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        if ($column['Field'] === 'application_type') {
            $hasApplicationType = true;
        }
    }

    // Add missing column if needed
    if (!$hasApplicationType) {
        echo "\n2. Adding missing application_type column...\n";

        $sql = "ALTER TABLE promotions ADD COLUMN application_type ENUM('all', 'specific_items', 'categories') DEFAULT 'all' AFTER type";
        $pdo->exec($sql);
        echo "✅ Successfully added application_type column\n";

        // Update existing promotions
        $updateSql = "UPDATE promotions SET application_type = 'all' WHERE application_type IS NULL";
        $affected = $pdo->exec($updateSql);
        echo "✅ Updated $affected existing promotions with default application_type\n";

    } else {
        echo "\n2. ✅ application_type column already exists\n";
    }

    // Verify the fix
    echo "\n3. Verifying updated table structure...\n";
    $stmt = $pdo->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll();

    echo "Updated columns:\n";
    foreach ($columns as $column) {
        $mark = ($column['Field'] === 'application_type') ? ' ← NEW' : '';
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")" . $mark . "\n";
    }

    // Test update operation
    echo "\n4. Testing promotion update...\n";
    $testSql = "UPDATE promotions SET application_type = 'all' WHERE id = 1 LIMIT 1";
    $result = $pdo->exec($testSql);
    echo "✅ Test update successful - application_type column is working\n";

    echo "\n=== DATABASE FIX COMPLETED SUCCESSFULLY ===\n";
    echo "The promotions table now has the application_type column.\n";
    echo "You can now update promotions without the column error.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
