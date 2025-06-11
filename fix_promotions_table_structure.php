<?php
/**
 * Check and fix promotions table structure
 */

require_once 'config/config.php';
require_once 'database/install.php';

echo "<h1>🔧 Checking and Fixing Promotions Table Structure</h1>";

try {
    $db = Database::getInstance()->getConnection();

    echo "<h2>Step 1: Current promotions table structure</h2>";

    // Check current table structure
    $stmt = $db->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Current Columns:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    $hasApplicationType = false;
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";

        if ($column['Field'] === 'application_type') {
            $hasApplicationType = true;
        }
    }
    echo "</table>";

    echo "<h2>Step 2: Check for missing columns</h2>";

    if (!$hasApplicationType) {
        echo "<p style='color: red;'>❌ Missing column: application_type</p>";
        echo "<p>Adding application_type column...</p>";

        $stmt = $db->exec("ALTER TABLE promotions ADD COLUMN application_type ENUM('all', 'specific_items', 'categories') DEFAULT 'all' AFTER type");

        if ($stmt !== false) {
            echo "<p style='color: green;'>✅ Successfully added application_type column</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to add application_type column</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ application_type column exists</p>";
    }

    echo "<h2>Step 3: Updated table structure</h2>";

    // Check updated table structure
    $stmt = $db->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Updated Columns:</h3>";
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

    echo "<h2>Step 4: Update existing promotions</h2>";

    // Update existing promotions to have a default application_type
    $stmt = $db->exec("UPDATE promotions SET application_type = 'all' WHERE application_type IS NULL");
    echo "<p>Updated $stmt existing promotions with default application_type = 'all'</p>";

    echo "<h2>Step 5: Verify relationship tables</h2>";

    // Check if relationship tables exist
    $tables = ['promotion_food_items', 'promotion_categories'];

    foreach ($tables as $tableName) {
        $stmt = $db->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table $tableName exists</p>";

            // Check table structure
            $stmt = $db->query("DESCRIBE $tableName");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<details>";
            echo "<summary>View $tableName structure</summary>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>" . $column['Field'] . "</td>";
                echo "<td>" . $column['Type'] . "</td>";
                echo "<td>" . $column['Null'] . "</td>";
                echo "<td>" . $column['Key'] . "</td>";
                echo "<td>" . $column['Default'] . "</td>";
                echo "<td>" . $column['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</details>";

        } else {
            echo "<p style='color: orange;'>⚠️ Table $tableName does not exist. Creating...</p>";

            if ($tableName === 'promotion_food_items') {
                $sql = "CREATE TABLE promotion_food_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    promotion_id INT NOT NULL,
                    food_item_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_promotion_id (promotion_id),
                    INDEX idx_food_item_id (food_item_id),
                    UNIQUE KEY unique_promotion_food (promotion_id, food_item_id)
                )";
            } else if ($tableName === 'promotion_categories') {
                $sql = "CREATE TABLE promotion_categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    promotion_id INT NOT NULL,
                    category_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_promotion_id (promotion_id),
                    INDEX idx_category_id (category_id),
                    UNIQUE KEY unique_promotion_category (promotion_id, category_id)
                )";
            }

            $result = $db->exec($sql);
            if ($result !== false) {
                echo "<p style='color: green;'>✅ Successfully created $tableName table</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create $tableName table</p>";
            }
        }
    }

    echo "<h2>✅ Table structure check and fix completed!</h2>";
    echo "<p style='color: green; font-weight: bold;'>The promotions table should now be ready for the enhanced promotion system.</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
p { margin: 8px 0; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
details { margin: 10px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
</style>
