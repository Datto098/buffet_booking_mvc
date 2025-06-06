<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=buffet_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Checking for NEWS table ===\n";
    $result = $pdo->query('SHOW TABLES');
    $tables = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tableName = $row[array_keys($row)[0]];
        $tables[] = $tableName;
        if (stripos($tableName, 'news') !== false) {
            echo "Found table: $tableName\n";
        }
    }

    if (in_array('news', $tables)) {
        echo "\n=== NEWS Table Structure ===\n";
        $result = $pdo->query('DESCRIBE news');
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . ' - ' . $row['Type'] . "\n";
        }
    } else {
        echo "NEWS table does not exist\n";
        echo "Available tables: " . implode(', ', $tables) . "\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
