<?php
/**
 * Test Menu Filtering to identify HTTP 500 error
 */

// Simulate the GET parameters that cause the error
$_GET['search'] = '';
$_GET['category'] = '4';
$_GET['price_range'] = '';
$_GET['sort'] = 'name';

// Include necessary files
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'controllers/FoodController.php';

echo "<!DOCTYPE html><html><head><title>Menu Filtering Test</title></head><body>";
echo "<h1>Testing Menu Filtering with Parameters</h1>";
echo "<p>Simulating: search=&category=4&price_range=&sort=name</p>";

// Add check for tables table
try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if tables table exists
    $result = $pdo->query("SHOW TABLES LIKE 'tables'");

    if ($result->rowCount() > 0) {
        echo "<h2>Database Check</h2>";
        echo "The 'tables' table exists.<br>";

        // Get schema
        $schema = $pdo->query("DESCRIBE tables");
        echo "<h3>Table Schema:</h3>";
        echo "<pre>";
        while ($row = $schema->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";

        // Get data count
        $count = $pdo->query("SELECT COUNT(*) as count FROM tables")->fetch(PDO::FETCH_ASSOC);
        echo "There are {$count['count']} records in the tables table.<br>";
    } else {
        echo "<h2>Database Check</h2>";
        echo "The 'tables' table does not exist.<br>";
    }

    // Check if reservations table exists
    $result = $pdo->query("SHOW TABLES LIKE 'reservations'");

    if ($result->rowCount() > 0) {
        echo "The 'reservations' table exists.<br>";

        // Get schema
        $schema = $pdo->query("DESCRIBE reservations");
        echo "<h3>Reservations Schema:</h3>";
        echo "<pre>";
        while ($row = $schema->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";

        // Get data count
        $count = $pdo->query("SELECT COUNT(*) as count FROM reservations")->fetch(PDO::FETCH_ASSOC);
        echo "There are {$count['count']} records in the reservations table.<br>";
    } else {
        echo "The 'reservations' table does not exist.<br>";
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}

try {
    // Create controller instance
    $controller = new FoodController();

    echo "<h2>Testing Food Controller instantiation</h2>";
    echo "✅ FoodController created successfully<br>";

    // Try to call the menu method
    echo "<h2>Testing menu() method</h2>";

    // Start output buffering to catch any output
    ob_start();
    $controller->menu();
    $output = ob_get_clean();

    echo "✅ menu() method executed successfully<br>";
    echo "<h3>Method Output:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";

} catch (Exception $e) {
    echo "<h2>❌ ERROR CAUGHT:</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>
