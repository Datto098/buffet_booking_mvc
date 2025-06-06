<?php
require_once 'config/config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "<h1>Database Content Check</h1>";

    // Check existing categories
    $stmt = $db->prepare("SELECT * FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Existing Categories:</h2>";
    if (empty($categories)) {
        echo "<p>No categories found. Creating sample categories...</p>";

        $sampleCategories = [
            ['Appetizers', 'Starter dishes and small plates'],
            ['Main Courses', 'Primary dishes and entrees'],
            ['Desserts', 'Sweet treats and desserts'],
            ['Beverages', 'Drinks and refreshments']
        ];

        $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        foreach ($sampleCategories as $cat) {
            $stmt->execute($cat);
        }
        echo "<p>✅ Sample categories created</p>";

        // Re-fetch categories
        $stmt = $db->prepare("SELECT * FROM categories");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo "<ul>";
    foreach ($categories as $category) {
        echo "<li>ID: {$category['id']} - {$category['name']}</li>";
    }
    echo "</ul>";

    // Now add subcategories with correct category IDs
    echo "<h2>Adding Subcategories...</h2>";

    if (count($categories) >= 4) {
        $subcategories = [
            [$categories[0]['id'], 'Soup', 'Hot soups'],
            [$categories[0]['id'], 'Salad', 'Fresh salads'],
            [$categories[1]['id'], 'Rice Dishes', 'Rice-based main courses'],
            [$categories[1]['id'], 'Pasta', 'Italian pasta dishes'],
            [$categories[1]['id'], 'Grilled', 'Grilled meats and seafood'],
            [$categories[2]['id'], 'Ice Cream', 'Cold desserts'],
            [$categories[2]['id'], 'Cakes', 'Sweet cakes and pastries'],
            [$categories[3]['id'], 'Hot Drinks', 'Coffee, tea, hot chocolate'],
            [$categories[3]['id'], 'Cold Drinks', 'Juices, sodas, cold beverages']
        ];

        // Clear existing subcategories first
        $db->exec("DELETE FROM sub_categories");

        $stmt = $db->prepare("INSERT INTO sub_categories (category_id, name, description) VALUES (?, ?, ?)");
        foreach ($subcategories as $subcat) {
            $stmt->execute($subcat);
        }
        echo "<p>✅ Subcategories added successfully</p>";
    }

    // Check if food_items needs subcategory_id
    $stmt = $db->prepare("SHOW COLUMNS FROM food_items LIKE 'subcategory_id'");
    $stmt->execute();
    $hasSubcategoryId = $stmt->rowCount() > 0;

    if (!$hasSubcategoryId) {
        echo "<h2>Adding subcategory_id to food_items...</h2>";
        $db->exec("ALTER TABLE food_items ADD COLUMN subcategory_id INT NULL AFTER category_id");
        echo "<p>✅ subcategory_id column added</p>";
    } else {
        echo "<p>✅ subcategory_id column already exists</p>";
    }

    echo "<h2>Database setup completed successfully!</h2>";

} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
