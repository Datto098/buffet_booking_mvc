<?php
// Simple test for food detail page by simulating GET request
require_once 'config/database.php';
require_once 'models/Food.php';

echo "<h1>Testing Food Detail Page - Simple Version</h1>";

// Test 1: Check database connection and get food items
try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Test 1: Database Connection</h2>";
    echo "✓ Database connected successfully<br>";

    // Get food items
    $stmt = $pdo->query("SELECT * FROM food_items WHERE is_available = 1 LIMIT 5");
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($foods) . " available food items<br>";

    if (count($foods) > 0) {
        echo "<h3>Food Items:</h3>";
        foreach ($foods as $food) {
            echo "- ID: {$food['id']}, Name: {$food['name']}, Price: ${$food['price']}<br>";
        }
    }

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 2: Test food detail view directly
echo "<h2>Test 2: Food Detail View</h2>";

if (count($foods) > 0) {
    $testFood = $foods[0];

    // Simulate the data that would be passed to the view
    $data = [
        'food' => $testFood,
        'relatedFoods' => array_slice($foods, 1, 3),
        'pageTitle' => $testFood['name'],
        'currentPage' => 'menu'
    ];

    echo "Testing with food: {$testFood['name']} (ID: {$testFood['id']})<br>";

    // Test if the view file exists and can be loaded
    $viewFile = 'views/customer/menu/detail.php';
    if (file_exists($viewFile)) {
        echo "✓ View file exists: $viewFile<br>";

        // Try to capture the view output
        ob_start();
        try {
            // Extract data for the view
            extract($data);
            include $viewFile;
            $output = ob_get_clean();

            echo "✓ View rendered successfully<br>";
            echo "Output length: " . strlen($output) . " characters<br>";

            // Check for key elements in the output
            if (strpos($output, $testFood['name']) !== false) {
                echo "✓ Food name found in output<br>";
            } else {
                echo "❌ Food name not found in output<br>";
            }

            if (strpos($output, 'btn-add-to-cart') !== false) {
                echo "✓ Add to cart button found<br>";
            } else {
                echo "❌ Add to cart button not found<br>";
            }

            if (strpos($output, 'breadcrumb') !== false) {
                echo "✓ Breadcrumb navigation found<br>";
            } else {
                echo "❌ Breadcrumb navigation not found<br>";
            }

        } catch (Exception $e) {
            ob_end_clean();
            echo "❌ Error rendering view: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ View file not found: $viewFile<br>";
    }
} else {
    echo "❌ No food items available for testing<br>";
}

echo "<h2>Test 3: URL Access Test</h2>";
echo "You can test the food detail page by visiting:<br>";
if (count($foods) > 0) {
    foreach ($foods as $food) {
        echo "- <a href='/buffet_booking_mvc/menu/detail?id={$food['id']}' target='_blank'>View {$food['name']}</a><br>";
    }
}

echo "<br><strong>Testing Complete!</strong>";
?>
