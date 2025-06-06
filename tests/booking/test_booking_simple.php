<?php
/**
 * Simple Booking System Test
 */

// Include necessary files
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'models/Booking.php';

echo "<h1>Simple Booking System Test</h1>";

// Create a booking model
$bookingModel = new Booking();

// Test parameters
$testDate = date('Y-m-d', strtotime('+1 day')); // Tomorrow
$testTime = '18:00';
$testPartySize = 4;

echo "<h2>Test Parameters</h2>";
echo "<p>Date: $testDate</p>";
echo "<p>Time: $testTime</p>";
echo "<p>Party Size: $testPartySize</p>";

// Test getAvailableTables
echo "<h2>Testing getAvailableTables</h2>";
try {
    $availableTables = $bookingModel->getAvailableTables("$testDate $testTime", $testPartySize);
    echo "<p>Available Tables: " . count($availableTables) . "</p>";
    if (count($availableTables) > 0) {
        echo "<pre>";
        print_r($availableTables[0]);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Test checkAvailability
echo "<h2>Testing checkAvailability</h2>";
try {
    $availability = $bookingModel->checkAvailability($testDate, $testTime, $testPartySize);
    echo "<p>Available: " . ($availability['available'] ? 'Yes' : 'No') . "</p>";
    echo "<p>Message: " . $availability['message'] . "</p>";
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Test with different party sizes
echo "<h2>Testing with Different Party Sizes</h2>";
$partySizes = [2, 4, 6, 8, 10, 20];
echo "<table border='1'>";
echo "<tr><th>Party Size</th><th>Available</th><th>Message</th></tr>";

foreach ($partySizes as $size) {
    try {
        $availability = $bookingModel->checkAvailability($testDate, $testTime, $size);
        echo "<tr>";
        echo "<td>$size</td>";
        echo "<td>" . ($availability['available'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $availability['message'] . "</td>";
        echo "</tr>";
    } catch (Exception $e) {
        echo "<tr>";
        echo "<td>$size</td>";
        echo "<td colspan='2'>Error: " . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}
echo "</table>";

echo "<h2>Test Complete</h2>";
?>
