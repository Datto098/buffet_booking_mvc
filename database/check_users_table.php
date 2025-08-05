<?php
require_once __DIR__ . '/../config/database.php';

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$result = $pdo->query('DESCRIBE users');
echo "Users table structure:\n";
while ($row = $result->fetch()) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . "\n";
}
?>
