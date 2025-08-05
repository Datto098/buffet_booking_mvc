<?php
/**
 * Script to add booking notification trigger to database
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected to database successfully.\n";

    // Read the trigger SQL file
    $triggerSQL = file_get_contents(__DIR__ . '/add_booking_notification_trigger.sql');

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

    // Create a test booking to see if trigger works
    echo "\n--- Testing trigger with a sample booking ---\n";

    // First check if we have super_admin users
    $adminCheck = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'super_admin'");
    $adminCount = $adminCheck->fetch()['count'];

    if ($adminCount == 0) {
        echo "No super_admin users found. Creating a test super_admin user...\n";

        $pdo->exec("INSERT INTO users (username, email, password, role, created_at) VALUES
                    ('test_admin', 'admin@test.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'super_admin', NOW())");
        echo "Test super_admin user created.\n";
    }

    // Get count of notifications before
    $beforeCount = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE type = 'new_booking'")->fetch()['count'];
    echo "Notifications before test: $beforeCount\n";

    // Insert test booking
    $bookingReference = 'TEST' . strtoupper(substr(md5(uniqid()), 0, 6));
    $pdo->exec("INSERT INTO bookings (
        customer_name, customer_email, customer_phone, booking_date, booking_time,
        guest_count, booking_location, special_requests, status, booking_reference
    ) VALUES (
        'Test Customer Trigger', 'test.trigger@example.com', '0123456789',
        '2025-07-25', '19:00:00', 4, 'Test Location',
        'Test trigger functionality', 'pending', '$bookingReference'
    )");

    echo "Test booking created with reference: $bookingReference\n";

    // Check notifications after
    $afterCount = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE type = 'new_booking'")->fetch()['count'];
    echo "Notifications after test: $afterCount\n";

    if ($afterCount > $beforeCount) {
        echo "✅ Trigger is working! New notifications were created.\n";

        // Show the latest notification
        $latestNotification = $pdo->query("SELECT * FROM notifications WHERE type = 'new_booking' ORDER BY created_at DESC LIMIT 1")->fetch();
        if ($latestNotification) {
            echo "\nLatest notification:\n";
            echo "- Title: " . $latestNotification['title'] . "\n";
            echo "- Message: " . $latestNotification['message'] . "\n";
            echo "- User ID: " . $latestNotification['user_id'] . "\n";
            echo "- Data: " . $latestNotification['data'] . "\n";
        }
    } else {
        echo "❌ Trigger might not be working. No new notifications were created.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
