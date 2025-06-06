<?php
echo "Starting test...\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    echo "Database connected\n";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "Test complete\n";
?>
