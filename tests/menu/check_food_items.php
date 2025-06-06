<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=buffet_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== FOOD_ITEMS Table Structure ===" . PHP_EOL;
    $result = $pdo->query('DESCRIBE food_items');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
    }

    echo PHP_EOL . "=== Sample FOOD_ITEMS Data ===" . PHP_EOL;
    $result = $pdo->query('SELECT * FROM food_items LIMIT 3');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
