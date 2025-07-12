<?php
// Test direct URL simulation
echo "Testing direct URL simulation...\n";

// Set up environment variables to simulate the URL request
$_SERVER['REQUEST_URI'] = '/buffet_booking_mvc/index.php?page=order&action=history';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/buffet_booking_mvc/index.php';
$_GET['page'] = 'order';
$_GET['action'] = 'history';

echo "Environment set up...\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "GET params: " . print_r($_GET, true) . "\n";

// Include the main index file to test routing
ob_start();
try {
    include 'index.php';
    $output = ob_get_contents();
    echo "Success! Output length: " . strlen($output) . " characters\n";
    echo "First 500 characters:\n";
    echo substr($output, 0, 500) . "\n...\n";
} catch (Exception $e) {
    $output = ob_get_contents();
    echo "Exception caught: " . $e->getMessage() . "\n";
    echo "Output before exception: " . substr($output, 0, 200) . "\n";
} finally {
    ob_end_clean();
}
?>
