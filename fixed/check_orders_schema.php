<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$result = $db->query('DESCRIBE orders');

echo "Orders table columns:\n";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
