<?php
require_once 'config/database.php';

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "<h1>Creating Missing Promotion Tables</h1>";

    // Create promotion_food_items table
    $sql1 = "CREATE TABLE IF NOT EXISTS promotion_food_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        promotion_id INT NOT NULL,
        food_item_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE,
        FOREIGN KEY (food_item_id) REFERENCES food_items(id) ON DELETE CASCADE,
        UNIQUE KEY unique_promotion_food (promotion_id, food_item_id)
    )";

    if ($pdo->exec($sql1) !== false) {
        echo "<p>✅ Table 'promotion_food_items' created successfully</p>";
    } else {
        echo "<p>❌ Error creating 'promotion_food_items' table</p>";
    }

    // Create promotion_categories table
    $sql2 = "CREATE TABLE IF NOT EXISTS promotion_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        promotion_id INT NOT NULL,
        category_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
        UNIQUE KEY unique_promotion_category (promotion_id, category_id)
    )";

    if ($pdo->exec($sql2) !== false) {
        echo "<p>✅ Table 'promotion_categories' created successfully</p>";
    } else {
        echo "<p>❌ Error creating 'promotion_categories' table</p>";
    }

    // Add some sample data
    echo "<hr><h2>Adding Sample Data</h2>";

    // Get some food item IDs
    $stmt = $pdo->query("SELECT id FROM food_items LIMIT 5");
    $foodItems = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($foodItems)) {
        // Add food items to promotion 1
        foreach (array_slice($foodItems, 0, 3) as $foodId) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO promotion_food_items (promotion_id, food_item_id) VALUES (1, ?)");
            $stmt->execute([$foodId]);
        }
        echo "<p>✅ Added food items to promotion 1</p>";

        // Add food items to promotion 2
        foreach (array_slice($foodItems, 2, 3) as $foodId) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO promotion_food_items (promotion_id, food_item_id) VALUES (2, ?)");
            $stmt->execute([$foodId]);
        }
        echo "<p>✅ Added food items to promotion 2</p>";
    }

    // Get some category IDs
    $stmt = $pdo->query("SELECT id FROM categories LIMIT 3");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($categories)) {
        // Add categories to promotion 1
        foreach (array_slice($categories, 0, 2) as $catId) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO promotion_categories (promotion_id, category_id) VALUES (1, ?)");
            $stmt->execute([$catId]);
        }
        echo "<p>✅ Added categories to promotion 1</p>";

        // Add categories to promotion 3
        foreach (array_slice($categories, 1, 2) as $catId) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO promotion_categories (promotion_id, category_id) VALUES (3, ?)");
            $stmt->execute([$catId]);
        }
        echo "<p>✅ Added categories to promotion 3</p>";
    }

    echo "<hr><h2>Verification</h2>";

    // Check if tables exist now
    $tables = ['promotion_food_items', 'promotion_categories'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<p>✅ Table '$table' exists with $count records</p>";
        } else {
            echo "<p>❌ Table '$table' still doesn't exist</p>";
        }
    }

    echo "<hr><p><a href='test_direct_promotion_api.php'>Test the API again</a></p>";

} catch (PDOException $e) {
    echo "<h2>Database Error:</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
p { margin: 10px 0; }
hr { margin: 30px 0; }
a { color: #007cba; }
</style>
