<?php
// Debug trang dine-in orders
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'models/DineInOrder.php';
require_once 'models/Table.php';

echo "<h1>Debug Dine-in Orders</h1>";

try {
    echo "<h2>1. Testing Database Connection</h2>";
    $orderModel = new DineInOrder();
    echo "✅ DineInOrder model created successfully<br>";

    $tableModel = new Table();
    echo "✅ Table model created successfully<br>";

    echo "<h2>2. Testing Tables Query</h2>";
    $tables = $tableModel->getAllTables();
    echo "✅ Tables count: " . count($tables) . "<br>";

    echo "<h2>3. Testing Basic Orders Query</h2>";
    $orders = $orderModel->getDineInOrdersAdmin('', '', '', 5, 0);
    echo "✅ Orders count: " . count($orders) . "<br>";

    if (!empty($orders)) {
        echo "<h3>Sample Order:</h3>";
        echo "<pre>";
        print_r($orders[0]);
        echo "</pre>";
    }

    echo "<h2>4. Testing Count Query</h2>";
    $totalOrders = $orderModel->countDineInOrdersAdmin('', '', '');
    echo "✅ Total orders: " . $totalOrders . "<br>";

    echo "<h2>5. Testing Controller</h2>";
    require_once 'controllers/DineInOrderAdminController.php';
    $controller = new DineInOrderAdminController();
    echo "✅ Controller created successfully<br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>6. Database Tables Check</h2>";
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check if tables exist
    $tables = ['dine_in_orders', 'dine_in_order_items', 'tables', 'users'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' NOT exists<br>";
        }
    }

    // Check columns in dine_in_orders
    echo "<h3>dine_in_orders columns:</h3>";
    $stmt = $pdo->query("DESCRIBE dine_in_orders");
    while ($row = $stmt->fetch()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }

    // Check columns in users table
    echo "<h3>users columns:</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }

} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
}
?>
