<?php
/**
 * Test URL through browser simulation
 */

// Start session first
session_start();

// Set up authenticated session
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'super_admin';

// Load config first
require_once 'config/config.php';
require_once 'config/database.php';

// Simulate browser request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/superadmin/promotions/edit/1';
$_SERVER['HTTP_HOST'] = 'localhost';

echo "Testing URL: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Session: user_id=" . $_SESSION['user_id'] . ", role=" . $_SESSION['user_role'] . "\n";

// Capture output to see what happens
ob_start();

try {
    // Include the main index.php which handles routing
    include 'index.php';

    $output = ob_get_contents();
    ob_end_clean();

    echo "✅ Routing completed successfully\n";
    echo "Output length: " . strlen($output) . " characters\n";

    if (strlen($output) > 0) {
        echo "First 500 characters of output:\n";
        echo substr($output, 0, 500) . "\n";

        if (strpos($output, 'error') !== false || strpos($output, 'Error') !== false) {
            echo "⚠️ Output contains errors\n";
        } else {
            echo "✅ No obvious errors in output\n";
        }
    }

} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Error occurred: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
