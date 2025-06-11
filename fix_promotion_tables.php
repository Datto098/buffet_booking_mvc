<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    echo "✅ Connected to database successfully<br>";

    // Create promotion_food_items table
    $sql1 = "CREATE TABLE IF NOT EXISTS promotion_food_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        promotion_id INT NOT NULL,
        food_item_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($pdo->exec($sql1) !== false) {
        echo "✅ Created promotion_food_items table<br>";
    } else {
        echo "❌ Failed to create promotion_food_items table<br>";
    }

    // Create promotion_categories table
    $sql2 = "CREATE TABLE IF NOT EXISTS promotion_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        promotion_id INT NOT NULL,
        category_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($pdo->exec($sql2) !== false) {
        echo "✅ Created promotion_categories table<br>";
    } else {
        echo "❌ Failed to create promotion_categories table<br>";
    }

    // Verify tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'promotion_food_items'");
    if ($stmt->rowCount() > 0) {
        echo "✅ promotion_food_items table confirmed<br>";

        // Add sample data
        $pdo->exec("INSERT IGNORE INTO promotion_food_items (promotion_id, food_item_id) VALUES (1, 1), (1, 2), (1, 3)");
        $count = $pdo->query("SELECT COUNT(*) FROM promotion_food_items")->fetchColumn();
        echo "✅ Added sample data - $count records in promotion_food_items<br>";
    }

    $stmt = $pdo->query("SHOW TABLES LIKE 'promotion_categories'");
    if ($stmt->rowCount() > 0) {
        echo "✅ promotion_categories table confirmed<br>";

        // Add sample data
        $pdo->exec("INSERT IGNORE INTO promotion_categories (promotion_id, category_id) VALUES (1, 1), (1, 2)");
        $count = $pdo->query("SELECT COUNT(*) FROM promotion_categories")->fetchColumn();
        echo "✅ Added sample data - $count records in promotion_categories<br>";
    }

    echo "<hr>";
    echo "<a href='test_direct_promotion_api.php'>Test the API again</a><br>";
    echo "<a href='http://localhost/buffet_booking_mvc/superadmin/promotions/get/1'>Test actual endpoint</a>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>
