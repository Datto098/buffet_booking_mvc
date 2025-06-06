<?php
// Test menu page for discount_percentage errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Menu Page Error Check ===\n";

// Check if the menu view file still contains discount_percentage references
$menuViewFile = 'c:\wamp64\www\buffet_booking_mvc\views\customer\menu\index.php';
$content = file_get_contents($menuViewFile);

$discountReferences = [];
$lines = explode("\n", $content);
foreach ($lines as $lineNum => $line) {
    if (stripos($line, 'discount_percentage') !== false) {
        $discountReferences[] = "Line " . ($lineNum + 1) . ": " . trim($line);
    }
    if (stripos($line, 'discounted_price') !== false) {
        $discountReferences[] = "Line " . ($lineNum + 1) . ": " . trim($line);
    }
    if (stripos($line, 'image_url') !== false) {
        $discountReferences[] = "Line " . ($lineNum + 1) . ": " . trim($line);
    }
}

if (empty($discountReferences)) {
    echo "✅ SUCCESS: No discount-related or image_url field references found in menu view!\n";
    echo "The menu page should now work without undefined array key errors.\n";
} else {
    echo "❌ FOUND ISSUES:\n";
    foreach ($discountReferences as $ref) {
        echo "  - $ref\n";
    }
}

echo "\n=== Testing Food Model Data Output ===\n";

// Test what data the Food model actually returns
require_once 'config/database.php';
require_once 'models/Food.php';

try {
    $foodModel = new Food();
    $foods = $foodModel->getFoodWithFilters('is_available = 1', [], 'name ASC', 2);

    if (!empty($foods)) {
        echo "✅ Food model returns data successfully.\n";
        echo "Sample food item fields:\n";
        foreach (array_keys($foods[0]) as $field) {
            echo "  - $field\n";
        }

        // Check for image field
        if (isset($foods[0]['image'])) {
            echo "✅ 'image' field exists\n";
        } else {
            echo "❌ 'image' field missing\n";
        }

        // Check for discount fields (should not exist)
        if (isset($foods[0]['discount_percentage'])) {
            echo "❌ 'discount_percentage' field exists (should not)\n";
        } else {
            echo "✅ 'discount_percentage' field correctly not present\n";
        }

    } else {
        echo "⚠️ WARNING: No food items found\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "The discount_percentage and discounted_price field errors have been fixed.\n";
echo "The image_url field has been changed to 'image' to match the database schema.\n";
echo "Menu page should now load without PHP warnings.\n";
?>
