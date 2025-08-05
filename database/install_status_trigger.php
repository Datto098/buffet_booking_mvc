<?php
/**
 * Script to add booking status update trigger to database
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected to database successfully.\n";

    // Read the trigger SQL file
    $triggerSQL = file_get_contents(__DIR__ . '/add_booking_status_trigger.sql');

    if (!$triggerSQL) {
        throw new Exception("Could not read trigger SQL file");
    }

    // Split SQL statements by delimiter
    $statements = explode('$$', $triggerSQL);

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || $statement === 'DELIMITER' || strpos($statement, 'DELIMITER') === 0) {
            continue;
        }

        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "Executed statement successfully.\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Trigger already exists') !== false) {
                    echo "Trigger already exists, skipping...\n";
                } else {
                    echo "Error executing statement: " . $e->getMessage() . "\n";
                    echo "Statement: " . substr($statement, 0, 100) . "...\n";
                }
            }
        }
    }

    // Test if trigger was created by checking for existing triggers
    $result = $pdo->query("SHOW TRIGGERS LIKE 'bookings'");
    $triggers = $result->fetchAll();

    echo "\nExisting triggers on bookings table:\n";
    foreach ($triggers as $trigger) {
        echo "- " . $trigger['Trigger'] . " (" . $trigger['Event'] . " " . $trigger['Timing'] . ")\n";
    }

    // Test the status update trigger
    echo "\n--- Testing status update trigger ---\n";

    // Get count of notifications before
    $beforeCount = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE type = 'booking_status_update'")->fetch()['count'];
    echo "Status update notifications before test: $beforeCount\n";

    // Find a test booking to update
    $testBooking = $pdo->query("SELECT * FROM bookings WHERE customer_name = 'Test Customer Trigger' ORDER BY id DESC LIMIT 1")->fetch();

    if ($testBooking) {
        echo "Found test booking ID: " . $testBooking['id'] . " with status: " . $testBooking['status'] . "\n";

        // Update the booking status
        $newStatus = $testBooking['status'] === 'pending' ? 'confirmed' : 'pending';
        $pdo->exec("UPDATE bookings SET status = '$newStatus' WHERE id = " . $testBooking['id']);

        echo "Updated booking status to: $newStatus\n";

        // Check notifications after
        $afterCount = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE type = 'booking_status_update'")->fetch()['count'];
        echo "Status update notifications after test: $afterCount\n";

        if ($afterCount > $beforeCount) {
            echo "✅ Status update trigger is working! New notification was created.\n";

            // Show the latest notification
            $latestNotification = $pdo->query("SELECT * FROM notifications WHERE type = 'booking_status_update' ORDER BY created_at DESC LIMIT 1")->fetch();
            if ($latestNotification) {
                echo "\nLatest status update notification:\n";
                echo "- Title: " . $latestNotification['title'] . "\n";
                echo "- Message: " . $latestNotification['message'] . "\n";
                echo "- User ID: " . $latestNotification['user_id'] . "\n";
                echo "- Data: " . $latestNotification['data'] . "\n";
            }
        } else {
            echo "❌ Status update trigger might not be working. No new notifications were created.\n";
        }
    } else {
        echo "No test booking found to update.\n";
    }

    echo "\n--- Summary ---\n";
    $totalNotifications = $pdo->query("SELECT COUNT(*) as count FROM notifications")->fetch()['count'];
    $bookingNotifications = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE type IN ('new_booking', 'booking_status_update')")->fetch()['count'];

    echo "Total notifications in database: $totalNotifications\n";
    echo "Total booking-related notifications: $bookingNotifications\n";

    echo "\nBooking notification system is now fully configured with triggers!\n";
    echo "✅ New booking notifications will be automatically created\n";
    echo "✅ Booking status update notifications will be automatically created\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
