<?php
/**
 * Test for the getAvailableTables function in the Booking model
 */

// Include necessary files
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'models/Booking.php';

echo "<!DOCTYPE html><html><head><title>Booking Tables Test</title></head><body>";
echo "<h1>Testing getAvailableTables Function</h1>";

// Create a booking model
$bookingModel = new Booking();

// Test with different party sizes
$partySizes = [1, 2, 4, 6, 8, 10, 20];

echo "<h2>Testing with different party sizes</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Party Size</th><th>Available Tables</th><th>Result</th></tr>";

foreach ($partySizes as $partySize) {
    // Test current time plus 1 day to ensure future booking
    $bookingDateTime = date('Y-m-d H:i:s', strtotime('+1 day'));

    try {
        $availableTables = $bookingModel->getAvailableTables($bookingDateTime, $partySize);

        echo "<tr>";
        echo "<td>{$partySize}</td>";
        echo "<td>" . count($availableTables) . "</td>";

        if (count($availableTables) > 0) {
            echo "<td class='success'>✅ Tables available</td>";
        } else {
            echo "<td class='warning'>⚠️ No tables available for this party size</td>";
        }

        echo "</tr>";
    } catch (Exception $e) {
        echo "<tr>";
        echo "<td>{$partySize}</td>";
        echo "<td>Error</td>";
        echo "<td class='error'>❌ " . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

// Test the checkAvailability function
echo "<h2>Testing checkAvailability Function</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Party Size</th><th>Date/Time</th><th>Available</th><th>Message</th></tr>";

foreach ($partySizes as $partySize) {
    // Test current date plus 1 day
    $date = date('Y-m-d', strtotime('+1 day'));
    $time = '18:00';

    try {
        $availability = $bookingModel->checkAvailability($date, $time, $partySize);

        echo "<tr>";
        echo "<td>{$partySize}</td>";
        echo "<td>{$date} {$time}</td>";
        echo "<td>" . ($availability['available'] ? "Yes" : "No") . "</td>";
        echo "<td>" . $availability['message'] . "</td>";
        echo "</tr>";
    } catch (Exception $e) {
        echo "<tr>";
        echo "<td>{$partySize}</td>";
        echo "<td>{$date} {$time}</td>";
        echo "<td>Error</td>";
        echo "<td class='error'>❌ " . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<style>
.success { background-color: #dff0d8; color: #3c763d; }
.warning { background-color: #fcf8e3; color: #8a6d3b; }
.error { background-color: #f2dede; color: #a94442; }
</style>";

echo "</body></html>";
?>
