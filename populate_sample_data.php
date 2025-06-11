<?php
// Sample Data Population for Testing
require_once 'config/Database.php';

echo "<h1>Sample Data Population</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "<h2>1. Adding Sample Food Items</h2>";

    $sampleFoodItems = [
        ['name' => 'Grilled Salmon', 'description' => 'Fresh Atlantic salmon with herbs', 'price' => 24.99, 'category_id' => 1],
        ['name' => 'Caesar Salad', 'description' => 'Classic Caesar with croutons', 'price' => 12.99, 'category_id' => 2],
        ['name' => 'Chocolate Cake', 'description' => 'Rich chocolate layer cake', 'price' => 8.99, 'category_id' => 3],
        ['name' => 'Beef Steak', 'description' => 'Premium ribeye steak', 'price' => 32.99, 'category_id' => 1],
        ['name' => 'Pasta Carbonara', 'description' => 'Traditional Italian pasta', 'price' => 16.99, 'category_id' => 1],
        ['name' => 'Greek Salad', 'description' => 'Fresh vegetables with feta', 'price' => 11.99, 'category_id' => 2],
        ['name' => 'Tiramisu', 'description' => 'Classic Italian dessert', 'price' => 7.99, 'category_id' => 3],
        ['name' => 'Chicken Wings', 'description' => 'Buffalo style wings', 'price' => 14.99, 'category_id' => 4],
    ];

    foreach ($sampleFoodItems as $item) {
        $stmt = $db->prepare("INSERT IGNORE INTO food_items (name, description, price, category_id) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$item['name'], $item['description'], $item['price'], $item['category_id']]);
        echo ($result ? "✅" : "❌") . " Added: {$item['name']}<br>";
    }

    echo "<h2>2. Adding Sample Categories</h2>";

    $sampleCategories = [
        ['name' => 'Main Courses', 'description' => 'Hearty main dishes'],
        ['name' => 'Salads', 'description' => 'Fresh and healthy salads'],
        ['name' => 'Desserts', 'description' => 'Sweet treats and desserts'],
        ['name' => 'Appetizers', 'description' => 'Light starters'],
        ['name' => 'Beverages', 'description' => 'Drinks and refreshments'],
    ];

    foreach ($sampleCategories as $category) {
        $stmt = $db->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
        $result = $stmt->execute([$category['name'], $category['description']]);
        echo ($result ? "✅" : "❌") . " Added: {$category['name']}<br>";
    }

    echo "<h2>3. Adding Sample Promotions</h2>";

    $samplePromotions = [
        [
            'name' => 'Weekend Special',
            'description' => '20% off on weekend dining',
            'type' => 'percentage',
            'discount_value' => 20.00,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+30 days')),
            'is_active' => 1,
            'application_type' => 'all'
        ],
        [
            'name' => 'Dessert Lovers',
            'description' => '$3 off all desserts',
            'type' => 'fixed',
            'discount_value' => 3.00,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+60 days')),
            'is_active' => 1,
            'application_type' => 'categories'
        ],
        [
            'name' => 'Premium Dishes',
            'description' => '15% off selected premium items',
            'type' => 'percentage',
            'discount_value' => 15.00,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+45 days')),
            'is_active' => 1,
            'application_type' => 'specific_items'
        ]
    ];

    foreach ($samplePromotions as $promo) {
        $stmt = $db->prepare("INSERT INTO promotions (name, description, type, discount_value, start_date, end_date, is_active, application_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $promo['name'], $promo['description'], $promo['type'],
            $promo['discount_value'], $promo['start_date'], $promo['end_date'],
            $promo['is_active'], $promo['application_type']
        ]);

        if ($result) {
            $promotionId = $db->lastInsertId();
            echo "✅ Added: {$promo['name']} (ID: $promotionId)<br>";

            // Add relationships based on application type
            if ($promo['application_type'] === 'categories' && $promo['name'] === 'Dessert Lovers') {
                // Link to Desserts category (ID: 3)
                $stmt = $db->prepare("INSERT IGNORE INTO promotion_categories (promotion_id, category_id) VALUES (?, ?)");
                $stmt->execute([$promotionId, 3]);
                echo "&nbsp;&nbsp;→ Linked to Desserts category<br>";
            } elseif ($promo['application_type'] === 'specific_items' && $promo['name'] === 'Premium Dishes') {
                // Link to premium items (Salmon and Beef Steak)
                $premiumItems = [1, 4]; // Assuming these are IDs for salmon and steak
                foreach ($premiumItems as $itemId) {
                    $stmt = $db->prepare("INSERT IGNORE INTO promotion_food_items (promotion_id, food_item_id) VALUES (?, ?)");
                    $stmt->execute([$promotionId, $itemId]);
                }
                echo "&nbsp;&nbsp;→ Linked to premium food items<br>";
            }
        } else {
            echo "❌ Failed: {$promo['name']}<br>";
        }
    }

    echo "<h2>4. Verification</h2>";

    // Count records
    $tables = [
        'food_items' => 'Food Items',
        'categories' => 'Categories',
        'promotions' => 'Promotions',
        'promotion_food_items' => 'Promotion-Food Relationships',
        'promotion_categories' => 'Promotion-Category Relationships'
    ];

    foreach ($tables as $table => $label) {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "📊 $label: $count records<br>";
    }

    echo "<h2>✅ Sample Data Population Complete</h2>";
    echo "<p><a href='superadmin/promotions' target='_blank'>🔗 Test Promotions Management</a></p>";
    echo "<p><a href='test_complete_promotion_system.php' target='_blank'>🔍 Run Complete System Test</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ Error During Data Population</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
a { color: #007cba; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
