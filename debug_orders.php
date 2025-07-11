<?php
require_once __DIR__ . '/config/config.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Debug Order Status - Table Lookup</h2>\n";

    // Check all tables
    echo "<h3>All Tables:</h3>\n";
    $stmt = $pdo->query("SELECT id, table_number, location FROM tables ORDER BY id");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($tables);
    echo "</pre>";

    // Check all orders
    echo "<h3>All Dine-in Orders:</h3>\n";
    $stmt = $pdo->query("SELECT o.id, o.table_id, o.user_id, o.status, o.created_at, t.table_number
                         FROM dine_in_orders o
                         LEFT JOIN tables t ON o.table_id = t.id
                         ORDER BY o.created_at DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($orders);
    echo "</pre>";

    // Test specific query for D2 table
    echo "<h3>Query for table D2:</h3>\n";
    $stmt = $pdo->prepare("SELECT o.*, t.table_number FROM dine_in_orders o
                           INNER JOIN tables t ON o.table_id = t.id
                           WHERE t.table_number = 'D2'");
    $stmt->execute();
    $d2Orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($d2Orders);
    echo "</pre>";

    // Test with user_id = 1
    echo "<h3>Query for table D2 with user_id = 1:</h3>\n";
    $stmt = $pdo->prepare("SELECT o.*, t.table_number FROM dine_in_orders o
                           INNER JOIN tables t ON o.table_id = t.id
                           WHERE t.table_number = 'D2' AND o.user_id = 1");
    $stmt->execute();
    $d2UserOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($d2UserOrders);
    echo "</pre>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
