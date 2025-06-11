<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "=== PROMOTIONS TABLE STRUCTURE ===\n";
    $stmt = $db->query('DESCRIBE promotions');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    echo "\n=== SAMPLE PROMOTION DATA ===\n";
    $stmt = $db->query('SELECT * FROM promotions LIMIT 1');
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($sample) {
        foreach ($sample as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "No promotions found\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
