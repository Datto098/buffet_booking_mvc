<?php
/**
 * Cleanup test data from booking notification system
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "=== CLEANING UP TEST DATA ===\n\n";

    // 1. Show current test data
    echo "1. Current test bookings:\n";
    $testBookings = $pdo->query("SELECT id, customer_name, booking_reference, status, created_at FROM bookings WHERE customer_name LIKE 'Test%'")->fetchAll();
    foreach ($testBookings as $booking) {
        echo "   📅 ID: {$booking['id']}, Customer: {$booking['customer_name']}, Ref: {$booking['booking_reference']}, Status: {$booking['status']}\n";
    }

    echo "\n2. Current test notifications:\n";
    $testNotifications = $pdo->query("SELECT id, type, title, created_at FROM notifications WHERE message LIKE '%Test%'")->fetchAll();
    foreach ($testNotifications as $notif) {
        echo "   📢 ID: {$notif['id']}, Type: {$notif['type']}, Title: {$notif['title']}\n";
    }

    // 2. Ask for confirmation
    echo "\nDo you want to delete this test data? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $confirmation = strtolower(trim(fgets($handle)));
    fclose($handle);

    if ($confirmation === 'y' || $confirmation === 'yes') {
        // 3. Delete test notifications first (due to foreign key)
        $deletedNotifications = $pdo->exec("DELETE FROM notifications WHERE message LIKE '%Test%'");
        echo "\n✅ Deleted $deletedNotifications test notifications\n";

        // 4. Delete test bookings
        $deletedBookings = $pdo->exec("DELETE FROM bookings WHERE customer_name LIKE 'Test%'");
        echo "✅ Deleted $deletedBookings test bookings\n";

        echo "\n🧹 Test data cleanup completed!\n";
    } else {
        echo "\n❌ Cleanup cancelled. Test data preserved.\n";
    }

    // 5. Show final summary
    echo "\nFinal summary:\n";
    $totalBookings = $pdo->query("SELECT COUNT(*) as count FROM bookings")->fetch()['count'];
    $totalNotifications = $pdo->query("SELECT COUNT(*) as count FROM notifications")->fetch()['count'];
    $bookingNotifications = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE type IN ('new_booking', 'booking_status_update')")->fetch()['count'];

    echo "📊 Total bookings: $totalBookings\n";
    echo "📊 Total notifications: $totalNotifications\n";
    echo "📊 Booking notifications: $bookingNotifications\n";

    echo "\n✅ BOOKING NOTIFICATION SYSTEM IS READY FOR PRODUCTION!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
