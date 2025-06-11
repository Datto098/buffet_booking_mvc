<?php
// Complete Promotion System Test
require_once 'config/Database.php';
require_once 'models/Promotion.php';

echo "<h1>Complete Promotion System Test</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    $promotion = new Promotion($db);

    echo "<h2>1. Testing Database Structure</h2>";

    // Check if tables exist
    $tables = ['promotions', 'promotion_food_items', 'promotion_categories', 'food_items', 'categories'];
    foreach ($tables as $table) {
        $stmt = $db->prepare("SHOW TABLES LIKE '$table'");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' missing<br>";
        }
    }

    echo "<h2>2. Testing Data Availability</h2>";

    // Check food items
    $foodItems = $promotion->getAllFoodItems();
    echo "📋 Available Food Items: " . count($foodItems) . "<br>";
    if (count($foodItems) > 0) {
        echo "<ul>";
        foreach (array_slice($foodItems, 0, 5) as $item) {
            echo "<li>ID: {$item['id']}, Name: {$item['name']}</li>";
        }
        if (count($foodItems) > 5) {
            echo "<li>... and " . (count($foodItems) - 5) . " more</li>";
        }
        echo "</ul>";
    }

    // Check categories
    $categories = $promotion->getAllCategories();
    echo "📂 Available Categories: " . count($categories) . "<br>";
    if (count($categories) > 0) {
        echo "<ul>";
        foreach ($categories as $category) {
            echo "<li>ID: {$category['id']}, Name: {$category['name']}</li>";
        }
        echo "</ul>";
    }

    echo "<h2>3. Testing Existing Promotions</h2>";

    // Check existing promotions
    $promotions = $promotion->getAllPromotions();
    echo "🎯 Existing Promotions: " . count($promotions) . "<br>";

    if (count($promotions) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Discount</th><th>Application Type</th><th>Status</th></tr>";

        foreach ($promotions as $promo) {
            $appType = $promo['application_type'] ?? 'Not Set';
            $status = $promo['is_active'] ? 'Active' : 'Inactive';
            echo "<tr>";
            echo "<td>{$promo['id']}</td>";
            echo "<td>{$promo['name']}</td>";
            echo "<td>{$promo['type']}</td>";
            echo "<td>{$promo['discount_value']}" . ($promo['type'] === 'percentage' ? '%' : ' $') . "</td>";
            echo "<td>$appType</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<h2>4. Testing Promotion Relationships</h2>";

    // Test relationships for existing promotions
    foreach ($promotions as $promo) {
        echo "<h3>Promotion: {$promo['name']} (ID: {$promo['id']})</h3>";

        // Get associated food items
        $stmt = $db->prepare("SELECT fi.id, fi.name FROM food_items fi
                             JOIN promotion_food_items pfi ON fi.id = pfi.food_item_id
                             WHERE pfi.promotion_id = ?");
        $stmt->execute([$promo['id']]);
        $associatedFoods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "🍽️ Associated Food Items: " . count($associatedFoods) . "<br>";
        if (count($associatedFoods) > 0) {
            foreach ($associatedFoods as $food) {
                echo "- {$food['name']} (ID: {$food['id']})<br>";
            }
        }

        // Get associated categories
        $stmt = $db->prepare("SELECT c.id, c.name FROM categories c
                             JOIN promotion_categories pc ON c.id = pc.category_id
                             WHERE pc.promotion_id = ?");
        $stmt->execute([$promo['id']]);
        $associatedCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "📁 Associated Categories: " . count($associatedCategories) . "<br>";
        if (count($associatedCategories) > 0) {
            foreach ($associatedCategories as $category) {
                echo "- {$category['name']} (ID: {$category['id']})<br>";
            }
        }

        echo "<hr>";
    }

    echo "<h2>5. Testing Model Methods</h2>";

    // Test specific methods
    echo "🔧 Testing getAllFoodItems(): " . (method_exists($promotion, 'getAllFoodItems') ? "✅ Method exists" : "❌ Method missing") . "<br>";
    echo "🔧 Testing getAllCategories(): " . (method_exists($promotion, 'getAllCategories') ? "✅ Method exists" : "❌ Method missing") . "<br>";
    echo "🔧 Testing saveFoodItems(): " . (method_exists($promotion, 'saveFoodItems') ? "✅ Method exists" : "❌ Method missing") . "<br>";
    echo "🔧 Testing saveCategories(): " . (method_exists($promotion, 'saveCategories') ? "✅ Method exists" : "❌ Method missing") . "<br>";

    echo "<h2>6. Database Constraints Test</h2>";

    // Test foreign key constraints
    try {
        $db->beginTransaction();

        // Try to insert invalid promotion_food_items relationship
        $stmt = $db->prepare("INSERT INTO promotion_food_items (promotion_id, food_item_id) VALUES (99999, 1)");
        $stmt->execute();

        $db->rollback();
        echo "❌ Foreign key constraint not working properly<br>";
    } catch (Exception $e) {
        $db->rollback();
        echo "✅ Foreign key constraints working properly<br>";
    }

    echo "<h2>✅ Test Complete</h2>";
    echo "<p><strong>System Status:</strong> Ready for browser testing</p>";
    echo "<p><a href='superadmin/promotions' target='_blank'>🔗 Open Promotions Management</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ Error During Testing</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
ul { margin: 5px 0; }
hr { margin: 20px 0; border: 1px solid #ddd; }
a { color: #007cba; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
