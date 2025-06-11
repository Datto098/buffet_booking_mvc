<?php
echo "Basic PHP test\n";
echo "Current working directory: " . getcwd() . "\n";
echo "PHP version: " . PHP_VERSION . "\n";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    echo "Database connection successful\n";

    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "MySQL version: $version\n";

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
