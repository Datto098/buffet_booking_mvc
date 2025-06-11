<?php
// Manual table creation for promotion relationships
echo "<h1>Manual Promotion Table Creation</h1>";

try {
    // Direct database connection
    $pdo = new PDO('mysql:host=localhost;dbname=buffet_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>✅ Connected to database</p>";

    // Create promotion_food_items table
    $sql1 = "CREATE TABLE IF NOT EXISTS promotion_food_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        promotion_id INT NOT NULL,
        food_item_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_promotion_id (promotion_id),
        INDEX idx_food_item_id (food_item_id)
    )";

    $pdo->exec($sql1);
    echo "<p>✅ Created/verified promotion_food_items table</p>";

    // Create promotion_categories table
    $sql2 = "CREATE TABLE IF NOT EXISTS promotion_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        promotion_id INT NOT NULL,
        category_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_promotion_id (promotion_id),
        INDEX idx_category_id (category_id)
    )";

    $pdo->exec($sql2);
    echo "<p>✅ Created/verified promotion_categories table</p>";

    // Verify tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'promotion_food_items'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ promotion_food_items table confirmed</p>";
    } else {
        echo "<p>❌ promotion_food_items table not found</p>";
    }

    $stmt = $pdo->query("SHOW TABLES LIKE 'promotion_categories'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ promotion_categories table confirmed</p>";
    } else {
        echo "<p>❌ promotion_categories table not found</p>";
    }

    // Add some sample data
    echo "<hr><h2>Adding Sample Data</h2>";

    // Insert sample data for promotion 1
    try {
        $pdo->exec("INSERT IGNORE INTO promotion_food_items (promotion_id, food_item_id) VALUES (1, 1), (1, 2), (1, 3)");
        $count = $pdo->query("SELECT COUNT(*) FROM promotion_food_items WHERE promotion_id = 1")->fetchColumn();
        echo "<p>✅ Added $count food items to promotion 1</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Could not add food items: " . $e->getMessage() . "</p>";
    }

    try {
        $pdo->exec("INSERT IGNORE INTO promotion_categories (promotion_id, category_id) VALUES (1, 1), (1, 2)");
        $count = $pdo->query("SELECT COUNT(*) FROM promotion_categories WHERE promotion_id = 1")->fetchColumn();
        echo "<p>✅ Added $count categories to promotion 1</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Could not add categories: " . $e->getMessage() . "</p>";
    }

    echo "<hr><h2>Test Links</h2>";
    echo '<p><a href="test_direct_promotion_api.php">Test Direct API</a></p>';
    echo '<p><a href="http://localhost/buffet_booking_mvc/superadmin/promotions/get/1">Test Real API Endpoint</a></p>';

} catch (PDOException $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure WAMP server is running and database 'buffet_booking' exists.</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
p { margin: 10px 0; }
hr { margin: 30px 0; }
a { color: #007cba; }
</style>
