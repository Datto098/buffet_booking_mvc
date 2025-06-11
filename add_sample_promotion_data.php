<?php
// Add sample promotion relationships for testing
echo "<h1>Adding Sample Promotion Relationships</h1>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=buffet_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>✅ Connected to database</p>";

    // Get some food items
    $stmt = $pdo->query("SELECT id, name FROM food_items LIMIT 5");
    $foodItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Available Food Items:</h2>";
    foreach ($foodItems as $item) {
        echo "<p>ID: {$item['id']}, Name: {$item['name']}</p>";
    }

    // Get categories
    $stmt = $pdo->query("SELECT id, name FROM categories LIMIT 3");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Available Categories:</h2>";
    foreach ($categories as $cat) {
        echo "<p>ID: {$cat['id']}, Name: {$cat['name']}</p>";
    }

    // Clear existing relationships for promotion 1
    $pdo->exec("DELETE FROM promotion_food_items WHERE promotion_id = 1");
    $pdo->exec("DELETE FROM promotion_categories WHERE promotion_id = 1");
    echo "<p>✅ Cleared existing relationships for promotion 1</p>";

    // Add food items to promotion 1
    if (!empty($foodItems)) {
        foreach (array_slice($foodItems, 0, 3) as $item) {
            $stmt = $pdo->prepare("INSERT INTO promotion_food_items (promotion_id, food_item_id) VALUES (1, ?)");
            $stmt->execute([$item['id']]);
        }
        echo "<p>✅ Added 3 food items to promotion 1</p>";
    }

    // Add categories to promotion 1
    if (!empty($categories)) {
        foreach (array_slice($categories, 0, 2) as $cat) {
            $stmt = $pdo->prepare("INSERT INTO promotion_categories (promotion_id, category_id) VALUES (1, ?)");
            $stmt->execute([$cat['id']]);
        }
        echo "<p>✅ Added 2 categories to promotion 1</p>";
    }

    // Verify the data
    echo "<h2>Verification:</h2>";
    $count = $pdo->query("SELECT COUNT(*) FROM promotion_food_items WHERE promotion_id = 1")->fetchColumn();
    echo "<p>Food items for promotion 1: $count</p>";

    $count = $pdo->query("SELECT COUNT(*) FROM promotion_categories WHERE promotion_id = 1")->fetchColumn();
    echo "<p>Categories for promotion 1: $count</p>";

    echo "<hr><h2>Test Links</h2>";
    echo '<p><a href="test_direct_promotion_api.php">Test Direct API (should show food items & categories)</a></p>';
    echo '<p><a href="http://localhost/buffet_booking_mvc/superadmin/promotions/get/1">Test Real API Endpoint</a></p>';
    echo '<p><a href="views/superadmin/promotions.php">Go to Promotions Page</a></p>';

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
