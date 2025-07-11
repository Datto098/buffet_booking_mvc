<?php
// Test admin bookings route directly

session_start();

// Change to correct directory
chdir(__DIR__ . '/..');

// Set up fake admin session for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'super_admin';

require_once 'config/config.php';
require_once 'helpers/functions.php';

echo "Testing admin bookings route...\n";
echo "Current directory: " . getcwd() . "\n";

// Simulate the routing
require_once 'controllers/AdminController.php';

try {
    $controller = new AdminController();
    echo "AdminController created successfully\n";

    echo "Calling bookings() method...\n";
    ob_start();
    $controller->bookings();
    $output = ob_get_clean();

    echo "Method executed successfully\n";
    echo "Output length: " . strlen($output) . " characters\n";

    // Check for any errors in output
    if (strpos($output, 'Warning:') !== false || strpos($output, 'Error:') !== false) {
        echo "Errors found in output:\n";
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            if (strpos($line, 'Warning:') !== false || strpos($line, 'Error:') !== false) {
                echo $line . "\n";
            }
        }
    } else {
        echo "No errors found in output\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
