<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$result = $db->query('SHOW TABLES');

echo "Tables in database:\n";
while ($row = $result->fetch(PDO::FETCH_NUM)) {
    echo $row[0] . "\n";
}
?>
