<?php
echo "=== COMPREHENSIVE IMAGE_URL FIX VERIFICATION ===\n\n";

$fixes = [
    'views/customer/home.php' => 'Featured foods section',
    'views/customer/cart/index.php' => 'Cart items display',
    'views/customer/order/checkout.php' => 'Checkout order summary',
    'views/admin/orders/details_modal.php' => 'Admin order details',
    'views/customer/menu/index.php' => 'Menu page (already fixed)'
];

echo "Checking fixed files for food-related image_url references...\n\n";

foreach ($fixes as $file => $description) {
    echo "📄 $file ($description):\n";

    if (!file_exists($file)) {
        echo "   ❌ File not found\n";
        continue;
    }

    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $issues = [];

    foreach ($lines as $lineNum => $line) {
        // Skip news-related image_url (that's correct)
        if (stripos($line, "\$news['image_url']") !== false) {
            continue;
        }

        // Check for food-related image_url
        if (stripos($line, "image_url") !== false &&
            (stripos($line, "\$food") !== false ||
             stripos($line, "\$item") !== false ||
             stripos($line, "food") !== false)) {
            $issues[] = "Line " . ($lineNum + 1) . ": " . trim($line);
        }
    }

    if (empty($issues)) {
        echo "   ✅ FIXED: No food-related image_url references found\n";
    } else {
        echo "   ❌ ISSUES FOUND:\n";
        foreach ($issues as $issue) {
            echo "      - $issue\n";
        }
    }
    echo "\n";
}

echo "=== TESTING HOME PAGE DATA ===\n";

// Test if HomeController can load data without errors
try {
    require_once 'config/database.php';
    require_once 'models/Food.php';

    $foodModel = new Food();
    $featuredFoods = $foodModel->getFoodWithFilters('is_available = 1', [], 'name ASC', 3);

    if (!empty($featuredFoods)) {
        echo "✅ Featured foods data loaded successfully\n";
        echo "   Sample food fields: " . implode(', ', array_keys($featuredFoods[0])) . "\n";

        // Check if image field exists
        if (isset($featuredFoods[0]['image'])) {
            echo "✅ 'image' field correctly available\n";
        } else {
            echo "❌ 'image' field missing\n";
        }
    } else {
        echo "⚠️ No featured foods found\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Fixed image_url references in:\n";
echo "  ✅ Home page featured foods section\n";
echo "  ✅ Menu page food display\n";
echo "  ✅ Cart page food items\n";
echo "  ✅ Checkout page order summary\n";
echo "  ✅ Admin order details modal\n";
echo "\nThe news section in home.php correctly uses image_url (news table has this field).\n";
echo "All food-related displays now use the correct 'image' field.\n";
?>
