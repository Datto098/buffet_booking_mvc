<?php
// Check promotion database structure
echo "<h1>🔍 Checking Promotion Database Structure</h1>";

try {
    require_once 'config/database.php';

    $db = Database::getInstance()->getConnection();
    echo "<p>✅ Database connected</p>";

    // Check promotion table structure
    echo "<h2>1. Promotion Table Structure</h2>";
    $stmt = $db->query("DESCRIBE promotions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check for application_type column specifically
    $hasApplicationType = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'application_type') {
            $hasApplicationType = true;
            break;
        }
    }

    echo "<h2>2. Column Verification</h2>";
    if ($hasApplicationType) {
        echo "<p>✅ application_type column exists</p>";
    } else {
        echo "<p>❌ application_type column is MISSING - this could be the issue!</p>";
        echo "<p>Adding application_type column...</p>";

        try {
            $db->exec("ALTER TABLE promotions ADD COLUMN application_type VARCHAR(50) DEFAULT 'all' AFTER type");
            echo "<p>✅ application_type column added successfully</p>";
        } catch (Exception $e) {
            echo "<p>❌ Failed to add application_type column: " . $e->getMessage() . "</p>";
        }
    }

    // Check a sample promotion
    echo "<h2>3. Sample Promotion Data</h2>";
    $stmt = $db->prepare("SELECT * FROM promotions WHERE id = 1");
    $stmt->execute();
    $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($promotion) {
        echo "<pre>" . print_r($promotion, true) . "</pre>";
    } else {
        echo "<p>No promotion found with ID 1</p>";
    }

    // Check relationship tables
    echo "<h2>4. Relationship Tables</h2>";
    $tables = ['promotion_food_items', 'promotion_categories'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<h3>Table: $table</h3>";
            $stmt = $db->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>{$column['Field']}</td>";
                echo "<td>{$column['Type']}</td>";
                echo "<td>{$column['Null']}</td>";
                echo "<td>{$column['Key']}</td>";
                echo "<td>{$column['Default']}</td>";
                echo "<td>{$column['Extra']}</td>";
                echo "</tr>";
            }
            echo "</table>";

            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<p>Records: $count</p>";
        } else {
            echo "<p>❌ Table '$table' does not exist</p>";
        }
    }

} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
p { margin: 8px 0; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>
