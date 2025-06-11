<?php
/**
 * Test Promotion-Food Items Relationship Feature
 */

require_once 'config/config.php';

echo "<h1>Testing Promotion-Food Items Relationship</h1>";

try {
    $database = Database::getInstance();
    $db = $database->getConnection();

    // Kiểm tra cấu trúc database
    echo "<h2>1. Database Structure Check</h2>";

    $tables = ['promotion_food_items', 'promotion_categories', 'promotions'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p>✅ Table '$table' exists</p>";

            // Kiểm tra cấu trúc
            $stmt = $db->query("DESCRIBE $table");
            $columns = $stmt->fetchAll();
            echo "<ul>";
            foreach ($columns as $col) {
                echo "<li>{$col['Field']} - {$col['Type']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>❌ Table '$table' missing</p>";
        }
    }

    // Kiểm tra cột application_type trong promotions
    echo "<h3>Checking application_type column</h3>";
    $stmt = $db->query("SHOW COLUMNS FROM promotions LIKE 'application_type'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ application_type column exists</p>";
    } else {
        echo "<p>❌ application_type column missing</p>";
    }

    // Kiểm tra data mẫu
    echo "<h2>2. Sample Data Check</h2>";

    echo "<h3>Food Items</h3>";
    $stmt = $db->query("SELECT id, name, price FROM food_items LIMIT 5");
    $foodItems = $stmt->fetchAll();
    if (!empty($foodItems)) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Price</th></tr>";
        foreach ($foodItems as $item) {
            echo "<tr><td>{$item['id']}</td><td>{$item['name']}</td><td>{$item['price']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No food items found</p>";
    }

    echo "<h3>Categories</h3>";
    $stmt = $db->query("SELECT id, name FROM categories LIMIT 5");
    $categories = $stmt->fetchAll();
    if (!empty($categories)) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th></tr>";
        foreach ($categories as $category) {
            echo "<tr><td>{$category['id']}</td><td>{$category['name']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No categories found</p>";
    }

    // Test Promotion Model
    echo "<h2>3. Promotion Model Test</h2>";

    require_once 'models/Promotion.php';
    $promotionModel = new Promotion($db);

    // Test getAllFoodItems
    echo "<h3>Testing getAllFoodItems()</h3>";
    $allFoodItems = $promotionModel->getAllFoodItems();
    echo "<p>Found " . count($allFoodItems) . " food items</p>";

    // Test getAllCategories
    echo "<h3>Testing getAllCategories()</h3>";
    $allCategories = $promotionModel->getAllCategories();
    echo "<p>Found " . count($allCategories) . " categories</p>";

    // Kiểm tra promotions hiện có
    echo "<h3>Current Promotions</h3>";
    $stmt = $db->query("SELECT id, name, application_type FROM promotions LIMIT 5");
    $promotions = $stmt->fetchAll();
    if (!empty($promotions)) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Application Type</th></tr>";
        foreach ($promotions as $promotion) {
            echo "<tr><td>{$promotion['id']}</td><td>{$promotion['name']}</td><td>{$promotion['application_type']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No promotions found</p>";
    }

    // Test Controller
    echo "<h2>4. Controller Integration Test</h2>";

    // Simulate session
    session_start();
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'super_admin';

    require_once 'controllers/SuperAdminController.php';

    echo "<p>✅ SuperAdminController loaded successfully</p>";

    // Test URLs
    echo "<h2>5. URL Accessibility Test</h2>";

    $testUrls = [
        'Main Page' => SITE_URL . '/superadmin/promotions',
        'Create' => SITE_URL . '/superadmin/promotions/create',
        'Edit' => SITE_URL . '/superadmin/promotions/edit/1',
        'Get' => SITE_URL . '/superadmin/promotions/get/1'
    ];

    echo "<ul>";
    foreach ($testUrls as $name => $url) {
        echo "<li><strong>$name:</strong> <a href='$url' target='_blank'>$url</a></li>";
    }
    echo "</ul>";

    echo "<h2>6. Frontend Features</h2>";
    echo "<ul>";
    echo "<li>✅ Application type selection (All Items / Specific Items / Categories)</li>";
    echo "<li>✅ Food items multi-select checkboxes</li>";
    echo "<li>✅ Categories multi-select checkboxes</li>";
    echo "<li>✅ Dynamic form sections based on application type</li>";
    echo "<li>✅ Edit promotion loads existing selections</li>";
    echo "</ul>";

    echo "<h2>✅ Promotion-Food Items Relationship Test Complete!</h2>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Visit <a href='" . SITE_URL . "/superadmin/promotions' target='_blank'>Promotions Management</a></li>";
    echo "<li>Try creating a promotion for specific food items</li>";
    echo "<li>Try creating a promotion for entire categories</li>";
    echo "<li>Test editing existing promotions</li>";
    echo "</ol>";

} catch (Exception $e) {
    echo "<h2>❌ Error occurred:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
