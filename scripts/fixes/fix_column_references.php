<?php
/**
 * Fix Database Column References
 * This script fixes all references to 'name' and 'full_name' columns
 * to use the proper 'first_name' and 'last_name' structure
 */

require_once 'config/config.php';

echo "Fixing database column references...\n";

// Fix Order.php
$orderPath = 'models/Order.php';
$orderContent = file_get_contents($orderPath);

// Replace all occurrences of u.name with CONCAT(u.first_name, ' ', u.last_name)
$orderContent = str_replace('u.name', "CONCAT(u.first_name, ' ', u.last_name)", $orderContent);
$orderContent = str_replace('u.full_name', "CONCAT(u.first_name, ' ', u.last_name)", $orderContent);

file_put_contents($orderPath, $orderContent);
echo "✓ Fixed Order.php\n";

// Fix Food.php
$foodPath = 'models/Food.php';
if (file_exists($foodPath)) {
    $foodContent = file_get_contents($foodPath);
    $foodContent = str_replace('u.full_name', "CONCAT(u.first_name, ' ', u.last_name)", $foodContent);
    file_put_contents($foodPath, $foodContent);
    echo "✓ Fixed Food.php\n";
}

// Fix Booking.php
$bookingPath = 'models/Booking.php';
if (file_exists($bookingPath)) {
    $bookingContent = file_get_contents($bookingPath);
    $bookingContent = str_replace('u.name', "CONCAT(u.first_name, ' ', u.last_name)", $bookingContent);
    file_put_contents($bookingPath, $bookingContent);
    echo "✓ Fixed Booking.php\n";
}

echo "\nAll column references have been fixed!\n";

// Test the fixes
echo "\nTesting Order model...\n";
try {
    $orderModel = new Order();
    $orders = $orderModel->getRecentOrders(1);
    echo "✓ Order model working correctly\n";
} catch (Exception $e) {
    echo "❌ Order model error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
?>
