<?php
// Check table status

require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Checking table status...\n";

    $stmt = $db->query("SELECT id, table_number, is_available, status FROM tables");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($tables as $table) {
        echo "Table " . $table['table_number'] . ": is_available=" . $table['is_available'] . ", status=" . $table['status'] . "\n";
    }

    echo "\nNow updating all tables to be available...\n";

    $sql = "UPDATE `tables` SET `is_available` = 1";
    $affected = $db->exec($sql);

    echo "Updated $affected tables to be available.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
