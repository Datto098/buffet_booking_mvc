<?php
echo "Starting database test...\n";

try {
    $dsn = "mysql:host=localhost;dbname=buffet_booking;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Database connected successfully\n";

    // Check if buffet_booking database exists
    $stmt = $pdo->query("SELECT DATABASE()");
    $db = $stmt->fetchColumn();
    echo "Current database: $db\n";

    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(', ', $tables) . "\n";

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
