<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=buffet_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== FOOD_ITEMS Table Structure ===" . PHP_EOL;
    $result = $pdo->query('DESCRIBE food_items');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Default']}" . PHP_EOL;
    }

    echo PHP_EOL . "=== Looking for discount-related fields ===" . PHP_EOL;
    $result = $pdo->query("DESCRIBE food_items");
    $discountFields = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if (stripos($row['Field'], 'discount') !== false) {
            $discountFields[] = $row['Field'];
        }
    }

    if (empty($discountFields)) {
        echo "No discount-related fields found in food_items table." . PHP_EOL;
    } else {
        echo "Found discount fields: " . implode(', ', $discountFields) . PHP_EOL;
    }

    echo PHP_EOL . "=== Sample food_items data ===" . PHP_EOL;
    $result = $pdo->query('SELECT * FROM food_items LIMIT 2');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Food ID: {$row['id']}, Name: {$row['name']}" . PHP_EOL;
        foreach ($row as $key => $value) {
            echo "  $key: " . ($value ?? 'NULL') . PHP_EOL;
        }
        echo "---" . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
