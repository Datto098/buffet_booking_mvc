<?php
// Test admin bookings functionality

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Booking.php';

try {
    echo "Testing admin bookings functionality...\n";

    $bookingModel = new Booking();

    echo "1. Testing count method...\n";
    $totalCount = $bookingModel->count();
    echo "Total bookings: " . $totalCount . "\n";

    echo "2. Testing count by status...\n";
    $confirmedCount = $bookingModel->count('status', 'confirmed');
    echo "Confirmed bookings: " . $confirmedCount . "\n";

    echo "3. Testing getAllForAdmin method...\n";
    $bookings = $bookingModel->getAllForAdmin(5, 0);
    echo "Retrieved " . count($bookings) . " bookings\n";

    if (count($bookings) > 0) {
        echo "First booking details:\n";
        foreach (['id', 'customer_name', 'booking_date', 'booking_time', 'reservation_time', 'status'] as $field) {
            echo "  $field: " . ($bookings[0][$field] ?? 'NULL') . "\n";
        }
    }

    echo "4. Testing getTodayCount method...\n";
    $todayCount = $bookingModel->getTodayCount();
    echo "Today's bookings: " . $todayCount . "\n";

    echo "5. Testing getUpcomingBookings method...\n";
    $upcomingBookings = $bookingModel->getUpcomingBookings(3);
    echo "Upcoming bookings: " . count($upcomingBookings) . "\n";

    echo "\nAll tests completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
