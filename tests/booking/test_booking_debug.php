<?php
/**
 * Debug script for testing the booking system
 */

// Include necessary files
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'models/Booking.php';
require_once 'helpers/functions.php';

// Disable warning output for a cleaner test
error_reporting(E_ERROR | E_PARSE);

echo "<h1>Booking System Debug</h1>";

// Create a booking model instance
$bookingModel = new Booking();

// Test data for createBooking method
$testData = [
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '0987654321',
    'booking_datetime' => '2025-06-20 18:00:00',
    'party_size' => 4,
    'special_requests' => 'Test request',
    'status' => 'pending',
    'user_id' => 1 // Optional: for logged in users
];

echo "<h2>Testing createBooking method</h2>";
echo "<h3>Test Data:</h3>";
echo "<pre>";
print_r($testData);
echo "</pre>";

// Test the createBooking method
try {
    $bookingId = $bookingModel->createBooking($testData);

    if ($bookingId) {
        echo "<p style='color:green'>✅ Booking created successfully with ID: " . $bookingId . "</p>";

        // Verify the booking was created
        echo "<h3>Verifying created booking:</h3>";
        $savedBooking = $bookingModel->findById($bookingId);

        if ($savedBooking) {
            echo "<p style='color:green'>✅ Booking retrieved successfully</p>";
            echo "<pre>";
            print_r($savedBooking);
            echo "</pre>";
        } else {
            echo "<p style='color:red'>❌ Could not retrieve the created booking</p>";
        }
    } else {
        echo "<p style='color:red'>❌ Failed to create booking</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Testing createBookingFromController method</h2>";

// Test the createBookingFromController method
try {
    $bookingId = $bookingModel->createBookingFromController($testData);

    if ($bookingId) {
        echo "<p style='color:green'>✅ Booking created successfully with ID: " . $bookingId . "</p>";

        // Verify the booking was created
        echo "<h3>Verifying created booking:</h3>";
        $savedBooking = $bookingModel->findById($bookingId);

        if ($savedBooking) {
            echo "<p style='color:green'>✅ Booking retrieved successfully</p>";
            echo "<pre>";
            print_r($savedBooking);
            echo "</pre>";
        } else {
            echo "<p style='color:red'>❌ Could not retrieve the created booking</p>";
        }
    } else {
        echo "<p style='color:red'>❌ Failed to create booking</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}

// Clean up test data
echo "<h2>Cleaning up test data</h2>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete test bookings created during this test
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE customer_name = 'Test Customer' AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $result = $stmt->execute();

    if ($result) {
        echo "<p style='color:green'>✅ Test data cleaned up successfully</p>";
    } else {
        echo "<p style='color:red'>❌ Failed to clean up test data</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error cleaning up: " . $e->getMessage() . "</p>";
}

echo "<h2>Test completed</h2>";
?>
