<?php
// Check if promotions exist in database
require_once 'config/config.php';
require_once 'config/Database.php';

echo "<h1>Database Promotion Check</h1>";

try {
    $db = Database::getInstance()->getConnection();

    // Check if promotions table exists
    $stmt = $db->query("SHOW TABLES LIKE 'promotions'");
    if ($stmt->rowCount() == 0) {
        echo "<h2>❌ Table 'promotions' does not exist</h2>";

        // Show all tables
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<h3>Available tables:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        exit;
    }

    echo "<h2>✅ Table 'promotions' exists</h2>";

    // Count promotions
    $stmt = $db->query("SELECT COUNT(*) FROM promotions");
    $count = $stmt->fetchColumn();
    echo "<p>Total promotions: $count</p>";

    if ($count == 0) {
        echo "<h3>No promotions found. Creating sample promotion...</h3>";

        // Create a sample promotion
        $stmt = $db->prepare("
            INSERT INTO promotions (name, code, description, type, discount_value, start_date, end_date, is_active, application_type, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $result = $stmt->execute([
            'Test Promotion',
            'TEST20',
            'Test promotion for debugging',
            'percentage',
            20.00,
            date('Y-m-d'),
            date('Y-m-d', strtotime('+30 days')),
            1,
            'all'
        ]);

        if ($result) {
            $newId = $db->lastInsertId();
            echo "<p>✅ Sample promotion created with ID: $newId</p>";

            // Test the new promotion
            $stmt = $db->prepare("SELECT * FROM promotions WHERE id = ?");
            $stmt->execute([$newId]);
            $promotion = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<h4>Created promotion:</h4>";
            echo "<pre>" . print_r($promotion, true) . "</pre>";
        } else {
            echo "<p>❌ Failed to create sample promotion</p>";
        }
    } else {
        // Show existing promotions
        $stmt = $db->query("SELECT * FROM promotions LIMIT 5");
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Existing promotions:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Code</th><th>Type</th><th>Application Type</th><th>Active</th></tr>";
        foreach ($promotions as $promo) {
            echo "<tr>";
            echo "<td>{$promo['id']}</td>";
            echo "<td>{$promo['name']}</td>";
            echo "<td>{$promo['code']}</td>";
            echo "<td>{$promo['type']}</td>";
            echo "<td>" . ($promo['application_type'] ?? 'all') . "</td>";
            echo "<td>" . ($promo['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Check relationship tables
    echo "<hr><h2>Relationship Tables Check</h2>";

    $relationTables = ['promotion_food_items', 'promotion_categories'];
    foreach ($relationTables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<p>✅ Table '$table' exists with $count records</p>";

            if ($count > 0) {
                $stmt = $db->query("SELECT * FROM $table LIMIT 3");
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<pre>" . print_r($records, true) . "</pre>";
            }
        } else {
            echo "<p>❌ Table '$table' does not exist</p>";
        }
    }

    // Test the API endpoint now
    echo "<hr><h2>Test API Endpoint</h2>";
    $testId = $promotions[0]['id'] ?? 1;
    $url = "http://localhost/buffet_booking_mvc/superadmin/promotions/get/$testId";
    echo "<p><a href='$url' target='_blank'>Test API: $url</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
hr { margin: 30px 0; }
a { color: #007cba; }
</style>
