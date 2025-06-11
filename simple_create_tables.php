<?php
// Simple database table creation script
$host = 'localhost';
$dbname = 'buffet_booking';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully\n";

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
    echo "Created promotion_food_items table\n";

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
    echo "Created promotion_categories table\n";

    // Check if tables exist
    $result = $pdo->query("SHOW TABLES LIKE 'promotion_food_items'");
    if ($result->rowCount() > 0) {
        echo "promotion_food_items table exists\n";
    }

    $result = $pdo->query("SHOW TABLES LIKE 'promotion_categories'");
    if ($result->rowCount() > 0) {
        echo "promotion_categories table exists\n";
    }

    // Add some sample data
    $pdo->exec("INSERT IGNORE INTO promotion_food_items (promotion_id, food_item_id) VALUES (1, 1), (1, 2), (1, 3)");
    $pdo->exec("INSERT IGNORE INTO promotion_categories (promotion_id, category_id) VALUES (1, 1), (1, 2)");

    echo "Sample data added\n";
    echo "Tables created successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
