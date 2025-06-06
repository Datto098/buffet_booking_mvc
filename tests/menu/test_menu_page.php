<?php
// Test menu page for errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing menu page...\n";

try {
    $_GET['page'] = 'menu';
    ob_start();
    include 'index.php';
    $output = ob_get_clean();
    echo "SUCCESS: Menu page loaded without errors!\n";
    echo "Output length: " . strlen($output) . " characters\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
}
?>
