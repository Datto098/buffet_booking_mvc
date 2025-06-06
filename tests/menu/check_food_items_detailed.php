<?php
// Check food_items table structure
require_once 'config/database.php';

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    echo "<h3>Food Items Table Structure:</h3>";
    $result = $pdo->query("DESCRIBE food_items");
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<h3>Sample Food Items Data:</h3>";
    $result = $pdo->query("SELECT * FROM food_items LIMIT 3");
    echo "<table border='1'>";
    $first = true;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if ($first) {
            echo "<tr>";
            foreach (array_keys($row) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
