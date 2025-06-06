<?php
/**
 * Test Booking Functionality
 */

// Include necessary files
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'controllers/BookingController.php';

echo "<!DOCTYPE html><html><head><title>Booking System Test</title></head><body>";
echo "<h1>Testing Booking System</h1>";

// Test the database tables first
try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if tables table exists
    $result = $pdo->query("SHOW TABLES LIKE 'tables'");

    echo "<h2>Database Check</h2>";

    if ($result->rowCount() > 0) {
        echo "✅ The 'tables' table exists.<br>";

        // Get data count
        $count = $pdo->query("SELECT COUNT(*) as count FROM tables")->fetch(PDO::FETCH_ASSOC);
        echo "There are {$count['count']} records in the tables table.<br>";

        // Show sample tables if they exist
        if ($count['count'] > 0) {
            $tables = $pdo->query("SELECT * FROM tables LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<h3>Sample Tables:</h3>";
            echo "<pre>";
            print_r($tables);
            echo "</pre>";
        } else {
            echo "❌ No tables found in the 'tables' table. This will cause booking availability checks to fail.<br>";
        }
    } else {
        echo "❌ The 'tables' table does not exist. This will cause booking availability checks to fail.<br>";
    }

    // Check if reservations table exists
    $result = $pdo->query("SHOW TABLES LIKE 'reservations'");

    if ($result->rowCount() > 0) {
        echo "✅ The 'reservations' table exists.<br>";

        // Get data count
        $count = $pdo->query("SELECT COUNT(*) as count FROM reservations")->fetch(PDO::FETCH_ASSOC);
        echo "There are {$count['count']} records in the reservations table.<br>";
    } else {
        echo "❌ The 'reservations' table does not exist.<br>";
    }

    // Now let's test the BookingController checkAvailability method
    echo "<h2>Testing Booking Availability Check</h2>";
    echo "<p>Simulating: booking_date=2025-06-10&booking_time=18:00&party_size=4</p>";

    // Mock the POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['booking_date'] = '2025-06-10';
    $_POST['booking_time'] = '18:00';
    $_POST['party_size'] = '4';    // Create a booking controller
    $controller = new BookingController();

    // Modified to not output to browser but capture output
    ob_start();
    try {
        $controller->checkAvailability();
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    }
    $output = ob_get_clean();

    // Try to decode the JSON
    $result = json_decode($output, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ JSON response received: <pre>";
        print_r($result);
        echo "</pre>";
    } else {
        echo "❌ Invalid JSON response: " . $output . "<br>";
        echo "JSON error: " . json_last_error_msg() . "<br>";
    }

} catch(PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage();
}

echo "</body></html>";
?>
