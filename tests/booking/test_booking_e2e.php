<?php
/**
 * Comprehensive Test for the Booking System
 * This test simulates a full booking workflow, from availability check to creating a booking
 */

// Include necessary files
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'helpers/functions.php';
require_once 'models/Booking.php';
require_once 'models/User.php';

echo "<!DOCTYPE html><html><head><title>Booking System End-to-End Test</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
    h1, h2 { color: #333; }
    .test-section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .test-section h2 { margin-top: 0; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";
echo "</head><body>";
echo "<h1>Comprehensive Booking System Test</h1>";

// Initialize test session
echo "<div class='test-section'>";
echo "<h2>Test Setup</h2>";

// Create models
$bookingModel = new Booking();
$userModel = new User();

// Test parameters
$testDate = date('Y-m-d', strtotime('+1 day')); // Tomorrow
$testTime = '18:00';
$testPartySize = 4;
$testCustomerName = 'Test Customer';
$testCustomerEmail = 'test@example.com';
$testCustomerPhone = '0987654321';
$testSpecialRequests = 'Test special request';

echo "<p>Test Date: {$testDate}</p>";
echo "<p>Test Time: {$testTime}</p>";
echo "<p>Test Party Size: {$testPartySize}</p>";

echo "</div>";

// Test the database tables
echo "<div class='test-section'>";
echo "<h2>Database Tables Check</h2>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check tables table
    $result = $pdo->query("SHOW TABLES LIKE 'tables'");
    if ($result->rowCount() > 0) {
        echo "<p class='success'>✅ Tables table exists</p>";

        // Count records
        $count = $pdo->query("SELECT COUNT(*) as count FROM tables")->fetch();
        echo "<p>Tables count: {$count['count']}</p>";

        if ($count['count'] > 0) {
            echo "<p class='success'>✅ Tables data is available</p>";
        } else {
            echo "<p class='error'>❌ No tables data found</p>";
        }
    } else {
        echo "<p class='error'>❌ Tables table doesn't exist</p>";
    }

    // Check reservations table
    $result = $pdo->query("SHOW TABLES LIKE 'reservations'");
    if ($result->rowCount() > 0) {
        echo "<p class='success'>✅ Reservations table exists</p>";
    } else {
        echo "<p class='error'>❌ Reservations table doesn't exist</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test availability check
echo "<div class='test-section'>";
echo "<h2>Testing Availability Check</h2>";

try {
    $availability = $bookingModel->checkAvailability($testDate, $testTime, $testPartySize);

    echo "<p>Available: " . ($availability['available'] ? "Yes" : "No") . "</p>";
    echo "<p>Message: " . $availability['message'] . "</p>";

    if (!$availability['available'] && !empty($availability['suggestedTimes'])) {
        echo "<p>Suggested Times:</p>";
        echo "<ul>";
        foreach ($availability['suggestedTimes'] as $time) {
            echo "<li>{$time['display_time']}</li>";
        }
        echo "</ul>";
    }

    if ($availability['available']) {
        echo "<p class='success'>✅ Tables are available for booking</p>";
    } else {
        echo "<p class='warning'>⚠️ No tables available at the requested time</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking availability: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test booking creation
echo "<div class='test-section'>";
echo "<h2>Testing Booking Creation</h2>";

// Prepare booking data
$bookingData = [
    'customer_name' => $testCustomerName,
    'customer_phone' => $testCustomerPhone,
    'customer_email' => $testCustomerEmail,
    'booking_datetime' => $testDate . ' ' . $testTime,
    'party_size' => $testPartySize,
    'special_requests' => $testSpecialRequests,
    'status' => 'pending'
];

try {
    // Try to create the booking
    $bookingId = $bookingModel->createBookingFromController($bookingData);

    if ($bookingId) {
        echo "<p class='success'>✅ Booking created successfully with ID: {$bookingId}</p>";

        // Verify the booking was saved correctly
        $savedBooking = $bookingModel->findById($bookingId);

        if ($savedBooking) {
            echo "<p class='success'>✅ Booking retrieved from database successfully</p>";
            echo "<h3>Booking Details:</h3>";
            echo "<pre>";
            print_r($savedBooking);
            echo "</pre>";
        } else {
            echo "<p class='error'>❌ Failed to retrieve booking from database</p>";
        }
    } else {
        echo "<p class='error'>❌ Failed to create booking</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error creating booking: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test getting available time slots
echo "<div class='test-section'>";
echo "<h2>Testing Available Time Slots</h2>";

try {
    $availableSlots = $bookingModel->getAvailableTimeSlots($testDate, $testPartySize);

    if (!empty($availableSlots)) {
        echo "<p class='success'>✅ Available time slots retrieved successfully</p>";
        echo "<h3>Available Time Slots:</h3>";
        echo "<table>";
        echo "<tr><th>Time</th><th>Display Time</th><th>Available</th></tr>";

        foreach ($availableSlots as $slot) {
            echo "<tr>";
            echo "<td>{$slot['time']}</td>";
            echo "<td>{$slot['display_time']}</td>";
            echo "<td>" . ($slot['available'] ? "Yes" : "No") . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No available time slots found for the test date</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error getting available time slots: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Clean up test data
echo "<div class='test-section'>";
echo "<h2>Test Cleanup</h2>";

try {
    // Delete the test booking if it was created
    if (isset($bookingId) && $bookingId) {
        $sql = "DELETE FROM reservations WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $bookingId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<p class='success'>✅ Test booking deleted successfully</p>";
        } else {
            echo "<p class='error'>❌ Failed to delete test booking</p>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error during cleanup: " . $e->getMessage() . "</p>";
}

echo "</div>";

echo "<h2>Test Completed</h2>";
echo "</body></html>";
?>
