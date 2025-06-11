<?php
/**
 * Add missing application_type column to promotions table
 */

require_once 'config/config.php';
require_once 'database/install.php';

echo "<h1>🔧 Adding Missing application_type Column</h1>";

try {
    $db = Database::getInstance()->getConnection();

    echo "<h2>Step 1: Check current table structure</h2>";

    // Check if application_type column exists
    $stmt = $db->query("SHOW COLUMNS FROM promotions LIKE 'application_type'");
    $columnExists = $stmt->rowCount() > 0;

    if ($columnExists) {
        echo "<p style='color: green;'>✅ application_type column already exists</p>";
    } else {
        echo "<p style='color: red;'>❌ application_type column is missing</p>";

        echo "<h2>Step 2: Adding application_type column</h2>";

        // Add the missing column
        $sql = "ALTER TABLE promotions ADD COLUMN application_type ENUM('all', 'specific_items', 'categories') DEFAULT 'all' AFTER type";

        $result = $db->exec($sql);

        if ($result !== false) {
            echo "<p style='color: green;'>✅ Successfully added application_type column</p>";

            // Update existing promotions to have default value
            $updateSql = "UPDATE promotions SET application_type = 'all' WHERE application_type IS NULL";
            $updateResult = $db->exec($updateSql);
            echo "<p style='color: green;'>✅ Updated $updateResult existing promotions with default application_type</p>";

        } else {
            throw new Exception("Failed to add application_type column");
        }
    }

    echo "<h2>Step 3: Verify table structure</h2>";

    // Show updated table structure
    $stmt = $db->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    foreach ($columns as $column) {
        $rowColor = ($column['Field'] === 'application_type') ? 'background-color: #d4edda;' : '';
        echo "<tr style='$rowColor'>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<h2>Step 4: Test promotion update</h2>";

    // Test if we can now update a promotion with application_type
    $testSql = "UPDATE promotions SET application_type = 'all' WHERE id = 1 LIMIT 1";
    $testResult = $db->exec($testSql);

    if ($testResult !== false) {
        echo "<p style='color: green;'>✅ Test update successful - application_type column is working</p>";
    } else {
        echo "<p style='color: red;'>❌ Test update failed</p>";
    }

    echo "<h2>✅ Column addition completed successfully!</h2>";
    echo "<p style='color: green; font-weight: bold;'>The promotions table now has the application_type column and should work with the enhanced promotion system.</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
p { margin: 8px 0; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
</style>
