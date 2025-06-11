<?php
// Check database relationships
echo "<h1>Database Relationship Check</h1>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=buffet_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check promotion_food_items table
    echo "<h2>Promotion Food Items:</h2>";
    $stmt = $pdo->query("SELECT * FROM promotion_food_items");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($items)) {
        echo "<p>No food items found in promotion_food_items table</p>";

        // Try to add some manually
        echo "<p>Adding sample data...</p>";
        $pdo->exec("INSERT INTO promotion_food_items (promotion_id, food_item_id) VALUES (1, 1), (1, 2), (1, 3)");
        echo "<p>✅ Added sample food items</p>";

        // Check again
        $stmt = $pdo->query("SELECT * FROM promotion_food_items");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach ($items as $item) {
        echo "<p>Promotion ID: {$item['promotion_id']}, Food Item ID: {$item['food_item_id']}</p>";
    }

    // Check promotion_categories table
    echo "<h2>Promotion Categories:</h2>";
    $stmt = $pdo->query("SELECT * FROM promotion_categories");
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($cats)) {
        echo "<p>No categories found in promotion_categories table</p>";

        // Try to add some manually
        echo "<p>Adding sample data...</p>";
        $pdo->exec("INSERT INTO promotion_categories (promotion_id, category_id) VALUES (1, 1), (1, 2)");
        echo "<p>✅ Added sample categories</p>";

        // Check again
        $stmt = $pdo->query("SELECT * FROM promotion_categories");
        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach ($cats as $cat) {
        echo "<p>Promotion ID: {$cat['promotion_id']}, Category ID: {$cat['category_id']}</p>";
    }

    echo "<hr><p><a href='test_direct_promotion_api.php'>Test API Again</a></p>";

} catch (PDOException $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
p { margin: 10px 0; }
hr { margin: 30px 0; }
a { color: #007cba; }
</style>
