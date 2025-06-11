<?php
// Test promotion edit functionality
echo "<h1>🧪 Testing Promotion Edit Functionality</h1>";

try {
    require_once 'config/database.php';

    $db = Database::getInstance()->getConnection();
    echo "<p>✅ Database connected</p>";

    // Test 1: Check if promotion exists
    echo "<h2>1. Checking if promotion ID 1 exists</h2>";
    $stmt = $db->prepare("SELECT * FROM promotions WHERE id = 1");
    $stmt->execute();
    $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($promotion) {
        echo "<p>✅ Promotion found: <strong>{$promotion['name']}</strong></p>";
        echo "<pre>" . print_r($promotion, true) . "</pre>";
    } else {
        echo "<p>❌ Promotion not found</p>";
        exit;
    }

    // Test 2: Check relationship tables
    echo "<h2>2. Checking relationship tables</h2>";
    $tables = ['promotion_food_items', 'promotion_categories'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<p>✅ Table '$table' exists with $count records</p>";
        } else {
            echo "<p>❌ Table '$table' missing</p>";
        }
    }

    // Test 3: Simulate the edit request
    echo "<h2>3. Simulating promotion edit request</h2>";

    $testData = [
        'name' => 'Welcome Discount',
        'code' => 'WELCOME10',
        'type' => 'percentage',
        'discount_value' => 10.00,
        'application_type' => 'specific_items',
        'food_items' => [5, 13],
        'categories' => [1],
        'start_date' => '2025-06-10',
        'end_date' => '2025-07-10',
        'usage_limit' => 100,
        'minimum_amount' => 50.00,
        'description' => 'Get 10% off your first order'
    ];

    echo "<p>Test data:</p>";
    echo "<pre>" . print_r($testData, true) . "</pre>";

    // Test update query
    echo "<h2>4. Testing direct database update</h2>";

    $sql = "UPDATE promotions
            SET name = :name, code = :code, description = :description, type = :type, application_type = :application_type,
                discount_value = :discount_value, start_date = :start_date, end_date = :end_date,
                usage_limit = :usage_limit, minimum_amount = :minimum_amount, updated_at = NOW()
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        ':id' => 1,
        ':name' => $testData['name'],
        ':code' => $testData['code'],
        ':description' => $testData['description'],
        ':type' => $testData['type'],
        ':application_type' => $testData['application_type'],
        ':discount_value' => $testData['discount_value'],
        ':start_date' => $testData['start_date'],
        ':end_date' => $testData['end_date'],
        ':usage_limit' => $testData['usage_limit'],
        ':minimum_amount' => $testData['minimum_amount']
    ]);

    if ($result) {
        echo "<p>✅ Basic promotion update successful</p>";

        // Test food items update
        echo "<h3>4a. Testing food items update</h3>";

        // Delete existing food items
        $stmt = $db->prepare("DELETE FROM promotion_food_items WHERE promotion_id = 1");
        $deleteResult = $stmt->execute();
        echo "<p>✅ Deleted existing food items: " . ($deleteResult ? 'success' : 'failed') . "</p>";

        // Insert new food items
        if (!empty($testData['food_items'])) {
            $sql = "INSERT INTO promotion_food_items (promotion_id, food_item_id) VALUES (?, ?)";
            $stmt = $db->prepare($sql);

            foreach ($testData['food_items'] as $foodItemId) {
                $insertResult = $stmt->execute([1, $foodItemId]);
                echo "<p>✅ Added food item $foodItemId: " . ($insertResult ? 'success' : 'failed') . "</p>";
            }
        }

        // Test categories update
        echo "<h3>4b. Testing categories update</h3>";

        // Delete existing categories
        $stmt = $db->prepare("DELETE FROM promotion_categories WHERE promotion_id = 1");
        $deleteResult = $stmt->execute();
        echo "<p>✅ Deleted existing categories: " . ($deleteResult ? 'success' : 'failed') . "</p>";

        // Insert new categories
        if (!empty($testData['categories'])) {
            $sql = "INSERT INTO promotion_categories (promotion_id, category_id) VALUES (?, ?)";
            $stmt = $db->prepare($sql);

            foreach ($testData['categories'] as $categoryId) {
                $insertResult = $stmt->execute([1, $categoryId]);
                echo "<p>✅ Added category $categoryId: " . ($insertResult ? 'success' : 'failed') . "</p>";
            }
        }

    } else {
        echo "<p>❌ Basic promotion update failed</p>";
        $errorInfo = $stmt->errorInfo();
        echo "<p>Error: " . print_r($errorInfo, true) . "</p>";
    }

    // Test 5: Verify final state
    echo "<h2>5. Verifying final state</h2>";

    $stmt = $db->prepare("SELECT * FROM promotions WHERE id = 1");
    $stmt->execute();
    $updatedPromotion = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Updated promotion:</p>";
    echo "<pre>" . print_r($updatedPromotion, true) . "</pre>";

    $stmt = $db->prepare("SELECT food_item_id FROM promotion_food_items WHERE promotion_id = 1");
    $stmt->execute();
    $foodItems = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>✅ Food items: " . implode(', ', $foodItems) . "</p>";

    $stmt = $db->prepare("SELECT category_id FROM promotion_categories WHERE promotion_id = 1");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>✅ Categories: " . implode(', ', $categories) . "</p>";

    echo "<hr>";
    echo "<h2>🎉 Test Complete - Database operations working!</h2>";
    echo "<p><a href='http://localhost/buffet_booking_mvc/superadmin/promotions/edit/1' target='_blank'>Now test the actual API endpoint</a></p>";

} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
p { margin: 8px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
hr { margin: 30px 0; }
a { color: #007bff; }
</style>
