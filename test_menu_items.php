<?php
// Test to check food items and categories in database
echo "Checking food items and categories in database...\n";

require_once 'config/config.php';
require_once 'models/Food.php';
require_once 'models/Category.php';

try {
    $foodModel = new Food();
    $categoryModel = new Category();

    echo "\n=== All Categories ===\n";
    $categories = $categoryModel->getAllCategories();
    foreach ($categories as $category) {
        echo "Category ID: {$category['id']}, Name: {$category['name']}\n";
    }

    echo "\n=== All Food Items ===\n";
    $foods = $foodModel->getAllWithCategories();
    echo "Total food items: " . count($foods) . "\n";

    foreach ($foods as $food) {
        echo "Food ID: {$food['id']}, Name: {$food['name']}, Category ID: {$food['category_id']}, Available: {$food['is_available']}\n";
    }

    echo "\n=== Food Items by Category ===\n";
    foreach ($categories as $category) {
        $categoryFoods = array_filter($foods, function($food) use ($category) {
            return $food['category_id'] == $category['id'];
        });
        echo "Category '{$category['name']}' has " . count($categoryFoods) . " food items\n";

        foreach ($categoryFoods as $food) {
            echo "  - {$food['name']} (Available: {$food['is_available']})\n";
        }
    }

    echo "\n=== Buffet Items Check ===\n";
    $buffetItems = $foodModel->getBuffetItems();
    echo "Buffet items found: " . count($buffetItems) . "\n";

    if (count($buffetItems) > 0) {
        echo "Buffet items:\n";
        foreach ($buffetItems as $item) {
            $categoryName = isset($item['category_name']) ? $item['category_name'] : 'N/A';
            echo "  - {$item['name']} (Available: {$item['is_available']}, Category: $categoryName)\n";
        }
    } else {
        echo "❌ No buffet items found!\n";

        // Check if there are any items marked as buffet by type
        echo "\nChecking food types...\n";
        $regularItems = $foodModel->getRegularMenuItems();
        echo "Regular menu items: " . count($regularItems) . "\n";

        $buffetTypeItems = $foodModel->getFoodsByType(true);
        echo "Items marked as buffet type: " . count($buffetTypeItems) . "\n";
    }

    echo "\n=== Buffet Category Check ===\n";
    $buffetCategories = array_filter($categories, function($cat) {
        return stripos($cat['name'], 'buffet') !== false;
    });

    if (empty($buffetCategories)) {
        echo "❌ No category with 'buffet' in the name found!\n";
        echo "Available categories: \n";
        foreach ($categories as $cat) {
            echo "  - {$cat['name']}\n";
        }
    } else {
        echo "✅ Buffet categories found:\n";
        foreach ($buffetCategories as $cat) {
            echo "  - {$cat['name']} (ID: {$cat['id']})\n";

            $buffetFoods = array_filter($foods, function($food) use ($cat) {
                return $food['category_id'] == $cat['id'];
            });
            echo "    Has " . count($buffetFoods) . " food items\n";
        }
    }

    echo "\n=== Testing Menu Filter ===\n";
    // Test the same filter that menu page uses
    $conditions = ['f.is_available = 1'];
    $params = [];
    $whereClause = implode(' AND ', $conditions);

    $menuFoods = $foodModel->getFoodWithFilters($whereClause, $params, 'f.name ASC', 50, 0);
    echo "Menu page would show " . count($menuFoods) . " food items\n";

    if (count($menuFoods) > 0) {
        echo "First few items:\n";
        for ($i = 0; $i < min(5, count($menuFoods)); $i++) {
            echo "  - {$menuFoods[$i]['name']} (Category: {$menuFoods[$i]['category_name']})\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
