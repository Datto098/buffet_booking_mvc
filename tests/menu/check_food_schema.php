<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$result = $db->query('DESCRIBE food_items');

echo "Food items table schema:\n";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Default']}\n";
}
?>
