<?php
/**
 * Direct Test for URL Access
 * Test the exact URL that's failing
 */

// Start session
session_start();

// Set up session to simulate logged-in super admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'super_admin';

echo "Session setup: user_id=" . $_SESSION['user_id'] . ", role=" . $_SESSION['user_role'] . "\n\n";

// Simulate the URL request
$_SERVER['REQUEST_URI'] = '/superadmin/promotions/edit/1';
$_SERVER['REQUEST_METHOD'] = 'GET';

echo "Testing URL: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n\n";

// Parse the URL like index.php does
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = array_filter(explode('/', $url));
$segments = array_values($segments); // reindex

echo "URL segments: " . print_r($segments, true) . "\n";

// Check if we have the expected structure
if (count($segments) >= 3) {
    $page = $segments[0];      // 'superadmin'
    $section = $segments[1];   // 'promotions'
    $action = $segments[2];    // 'edit'
    $param = $segments[3] ?? null; // '1'

    echo "Parsed route:\n";
    echo "- page: $page\n";
    echo "- section: $section\n";
    echo "- action: $action\n";
    echo "- param: $param\n\n";

    if ($page === 'superadmin' && $section === 'promotions' && $action === 'edit' && $param) {
        echo "✅ Route structure is correct!\n";

        // Include necessary files
        require_once 'config/database.php';
        require_once 'models/Promotion.php';
        require_once 'controllers/SuperAdminController.php';

        // Create controller instance
        $database = new Database();
        $db = $database->getConnection();
        $controller = new SuperAdminController($db);

        echo "✅ Controller created successfully\n";

        // Test if editPromotion method exists
        if (method_exists($controller, 'editPromotion')) {
            echo "✅ editPromotion method exists\n";

            try {
                // Call the method
                echo "Calling editPromotion($param)...\n";
                $controller->editPromotion($param);
                echo "✅ Method executed successfully\n";

            } catch (Exception $e) {
                echo "❌ Error executing editPromotion: " . $e->getMessage() . "\n";
                echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
            }
        } else {
            echo "❌ editPromotion method does not exist\n";
        }

    } else {
        echo "❌ Route structure is incorrect\n";
    }
} else {
    echo "❌ Not enough URL segments\n";
}
