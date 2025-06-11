<?php
/**
 * Debug Promotion Route Issue
 */

echo "=== Debugging Promotion Edit Route Issue ===\n\n";

// Test if we can access the controller method directly
require_once 'config/database.php';
require_once 'controllers/SuperAdminController.php';
require_once 'models/Promotion.php';

// Mock session for super_admin
$_SESSION['user'] = [
    'id' => 1,
    'role' => 'super_admin'
];
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'super_admin';

try {
    echo "1. Testing database connection...\n";
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connected\n\n";

    echo "2. Testing Promotion model...\n";
    $promotionModel = new Promotion();
    $promotions = $promotionModel->getAllPromotions(1, 0, []);

    if (empty($promotions)) {
        echo "⚠️  No promotions found in database\n";
        echo "Creating a test promotion...\n";

        // Create test promotion
        $testData = [
            'name' => 'Test Promotion',
            'code' => 'TEST123',
            'type' => 'percentage',
            'discount_value' => 10.00,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+30 days')),
            'is_active' => 1,
            'description' => 'Test promotion for debugging'
        ];

        $stmt = $db->prepare("INSERT INTO promotions (name, code, type, discount_value, start_date, end_date, is_active, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute(array_values($testData))) {
            $testPromotionId = $db->lastInsertId();
            echo "✅ Test promotion created with ID: $testPromotionId\n";
        } else {
            echo "❌ Failed to create test promotion\n";
        }
    } else {
        $testPromotionId = $promotions[0]['id'];
        echo "✅ Using existing promotion ID: $testPromotionId\n";
    }
    echo "\n";

    echo "3. Testing Controller editPromotion method...\n";

    // Test GET request (fetch promotion data)
    $_SERVER['REQUEST_METHOD'] = 'GET';

    // Capture output
    ob_start();

    $controller = new SuperAdminController();
    $controller->editPromotion($testPromotionId);

    $output = ob_get_clean();

    echo "Controller output: " . $output . "\n";

    if (strpos($output, '"success":true') !== false) {
        echo "✅ Controller method works correctly\n";
    } else {
        echo "❌ Controller method issue\n";
    }
    echo "\n";

    echo "4. Testing URL structure...\n";
    $testUrl = "/superadmin/promotions/edit/$testPromotionId";
    echo "Expected URL: $testUrl\n";

    // Parse URL to check routing
    $urlParts = explode('/', trim($testUrl, '/'));
    echo "URL parts: " . implode(' | ', $urlParts) . "\n";

    if (count($urlParts) >= 4) {
        $section = $urlParts[1]; // 'promotions'
        $action = $urlParts[2];  // 'edit'
        $param = $urlParts[3];   // promotion ID

        echo "Section: $section\n";
        echo "Action: $action\n";
        echo "Param: $param\n";

        if ($section === 'promotions' && $action === 'edit' && is_numeric($param)) {
            echo "✅ URL structure is correct\n";
        } else {
            echo "❌ URL structure issue\n";
        }
    }
    echo "\n";

    echo "5. Recommended Solutions:\n";
    echo "Option A: Add 'get' route for fetching data\n";
    echo "Option B: Use query parameter: /superadmin/promotions/edit?id=$testPromotionId\n";
    echo "Option C: Check .htaccess rewrite rules\n";
    echo "Option D: Verify current route handles GET requests properly\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
