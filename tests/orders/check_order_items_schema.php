<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$result = $db->query('DESCRIBE order_items');

echo "Order items table columns:\n";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
