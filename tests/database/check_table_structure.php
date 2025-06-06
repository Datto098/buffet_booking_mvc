<?php
require_once 'config/database.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

    echo "order_items table structure:\n";
    $stmt = $pdo->query('DESCRIBE order_items');
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
    }

    echo "\nSample order_items data:\n";
    $stmt = $pdo->query('SELECT * FROM order_items LIMIT 3');
    while ($row = $stmt->fetch()) {
        print_r($row);
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
