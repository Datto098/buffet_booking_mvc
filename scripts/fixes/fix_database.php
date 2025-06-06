<?php
require_once 'config/config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "<h1>Database Schema Check</h1>";

    // Check existing tables
    $stmt = $db->prepare("SHOW TABLES");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h2>Existing Tables:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";

    // Check if sub_categories table exists
    if (!in_array('sub_categories', $tables)) {
        echo "<h2>Creating sub_categories table...</h2>";

        $sql = "CREATE TABLE `sub_categories` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `category_id` int(11) NOT NULL,
            `name` varchar(255) NOT NULL,
            `description` text,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `category_id` (`category_id`),
            CONSTRAINT `sub_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $db->exec($sql);
        echo "<p>✅ sub_categories table created successfully</p>";

        // Add some sample subcategories
        $subcategories = [
            [1, 'Soup', 'Hot soups'],
            [1, 'Salad', 'Fresh salads'],
            [2, 'Rice Dishes', 'Rice-based main courses'],
            [2, 'Pasta', 'Italian pasta dishes'],
            [2, 'Grilled', 'Grilled meats and seafood'],
            [3, 'Ice Cream', 'Cold desserts'],
            [3, 'Cakes', 'Sweet cakes and pastries'],
            [4, 'Hot Drinks', 'Coffee, tea, hot chocolate'],
            [4, 'Cold Drinks', 'Juices, sodas, cold beverages']
        ];

        $stmt = $db->prepare("INSERT INTO sub_categories (category_id, name, description) VALUES (?, ?, ?)");
        foreach ($subcategories as $subcat) {
            $stmt->execute($subcat);
        }
        echo "<p>✅ Sample subcategories added</p>";

    } else {
        echo "<p>✅ sub_categories table already exists</p>";
    }

    // Also check if the food table has the right structure
    echo "<h2>Checking food table structure...</h2>";
    $stmt = $db->prepare("DESCRIBE food_items");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>food_items columns:</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>{$column['Field']} - {$column['Type']}</li>";
    }
    echo "</ul>";

    // Check if subcategory_id column exists
    $hasSubcategoryId = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'subcategory_id') {
            $hasSubcategoryId = true;
            break;
        }
    }

    if (!$hasSubcategoryId) {
        echo "<h3>Adding subcategory_id column to food_items...</h3>";
        $db->exec("ALTER TABLE food_items ADD COLUMN subcategory_id INT NULL AFTER category_id");
        $db->exec("ALTER TABLE food_items ADD CONSTRAINT fk_food_subcategory
                   FOREIGN KEY (subcategory_id) REFERENCES sub_categories(id) ON DELETE SET NULL");
        echo "<p>✅ subcategory_id column added</p>";
    } else {
        echo "<p>✅ subcategory_id column already exists</p>";
    }

    echo "<h2>Database schema check completed!</h2>";

} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
