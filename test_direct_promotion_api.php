<?php
// Direct API test for getPromotion
session_start();

// Mock authentication for testing
$_SESSION['user'] = [
    'id' => 1,
    'role' => 'super_admin',
    'email' => 'admin@test.com'
];

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'models/Promotion.php';

echo "<h1>Direct getPromotion Test</h1>";

try {
    $promotionModel = new Promotion();
    $promotionId = 1;

    echo "<h2>Testing promotion ID: $promotionId</h2>";

    // Test findById
    echo "<h3>1. Testing findById($promotionId)</h3>";
    $promotion = $promotionModel->findById($promotionId);
    if ($promotion) {
        echo "✅ Promotion found:<br>";
        echo "<pre>" . print_r($promotion, true) . "</pre>";
    } else {
        echo "❌ Promotion not found<br>";

        // List available promotions
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, name, code FROM promotions LIMIT 5");
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Available promotions:<br>";
        echo "<pre>" . print_r($promotions, true) . "</pre>";
    }

    if ($promotion) {
        // Test getFoodItemIds
        echo "<h3>2. Testing getFoodItemIds($promotionId)</h3>";
        $foodItems = $promotionModel->getFoodItemIds($promotionId);
        echo "Food items: " . count($foodItems) . " items<br>";
        echo "<pre>" . print_r($foodItems, true) . "</pre>";

        // Test getCategoryIds
        echo "<h3>3. Testing getCategoryIds($promotionId)</h3>";
        $categories = $promotionModel->getCategoryIds($promotionId);
        echo "Categories: " . count($categories) . " categories<br>";
        echo "<pre>" . print_r($categories, true) . "</pre>";

        // Simulate the full response
        echo "<h3>4. Full API Response Simulation</h3>";
        $fullResponse = [
            'success' => true,
            'promotion' => $promotion
        ];
        $fullResponse['promotion']['food_items'] = $foodItems;
        $fullResponse['promotion']['categories'] = $categories;

        echo "<pre>" . json_encode($fullResponse, JSON_PRETTY_PRINT) . "</pre>";
    }

} catch (Exception $e) {
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test database tables
echo "<hr><h2>Database Tables Check</h2>";
try {
    $db = Database::getInstance()->getConnection();

    $tables = ['promotions', 'promotion_food_items', 'promotion_categories', 'food_items', 'categories'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "✅ Table '$table' exists with $count records<br>";
        } else {
            echo "❌ Table '$table' does not exist<br>";
        }
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
hr { margin: 30px 0; }
</style>
