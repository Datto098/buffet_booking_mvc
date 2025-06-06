<?php
// Simple database check script
try {
    echo "Starting database check...\n";

    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully\n";

    // Check tables table
    $result = $pdo->query("SHOW TABLES LIKE 'tables'");
    if ($result->rowCount() > 0) {
        echo "Tables table exists\n";

        // Count records in tables table
        $count = $pdo->query("SELECT COUNT(*) as count FROM tables")->fetch();
        echo "Tables count: " . $count['count'] . "\n";

        // Show sample records
        if ($count['count'] > 0) {
            $tables = $pdo->query("SELECT * FROM tables LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "Sample tables:\n";
            print_r($tables);
        }
    } else {
        echo "Tables table does not exist\n";
    }

    // Check reservations table
    $result = $pdo->query("SHOW TABLES LIKE 'reservations'");
    if ($result->rowCount() > 0) {
        echo "Reservations table exists\n";

        // Count records in reservations table
        $count = $pdo->query("SELECT COUNT(*) as count FROM reservations")->fetch();
        echo "Reservations count: " . $count['count'] . "\n";
    } else {
        echo "Reservations table does not exist\n";
    }

    echo "Database check completed.\n";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

echo "Script execution completed.\n";
?>
