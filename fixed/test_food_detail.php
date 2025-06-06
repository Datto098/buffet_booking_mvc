<?php
/**
 * Test script for food detail page functionality
 */

require_once 'config/config.php';
require_once 'controllers/FoodController.php';
require_once 'models/Food.php';

// Start session
session_start();

echo "<h1>Testing Food Detail Page Functionality</h1>\n";

try {
    // Test 1: Check if food items exist
    echo "<h2>Test 1: Check existing food items</h2>\n";
    $foodModel = new Food();
    $foods = $foodModel->findAll(5);
    echo "Found " . count($foods) . " food items in database.\n";

    if (count($foods) > 0) {
        $firstFood = $foods[0];
        echo "First food ID: " . $firstFood['id'] . " - " . $firstFood['name'] . "\n";

        // Test 2: Test food detail functionality
        echo "<h2>Test 2: Test food detail page</h2>\n";
        $controller = new FoodController();

        // Capture output
        ob_start();
        $controller->detail($firstFood['id']);
        $output = ob_get_contents();
        ob_end_clean();

        if (strpos($output, 'food-detail') !== false || strpos($output, $firstFood['name']) !== false) {
            echo "✓ Food detail page generated successfully!\n";
            echo "Output length: " . strlen($output) . " characters\n";
        } else {
            echo "✗ Food detail page not generating expected output\n";
            echo "Output preview: " . substr($output, 0, 200) . "...\n";
        }

        // Test 3: Check for specific elements in output
        echo "<h2>Test 3: Check for essential page elements</h2>\n";
        $checks = [
            'Food name' => $firstFood['name'],
            'Price display' => '$',
            'Breadcrumb navigation' => 'breadcrumb',
            'Add to cart' => 'add-to-cart'
        ];

        foreach ($checks as $check => $needle) {
            if (stripos($output, $needle) !== false) {
                echo "✓ $check found\n";
            } else {
                echo "✗ $check not found\n";
            }
        }

    } else {
        echo "No food items found in database.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n<h2>Test Complete!</h2>\n";
?>
