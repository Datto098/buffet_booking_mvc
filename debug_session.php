<?php
session_start();
require_once 'config/config.php';
require_once 'helpers/functions.php';

echo "<h3>Session Debug:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Auth Functions:</h3>";
echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "<br>";
echo "isAdmin(): " . (isAdmin() ? 'true' : 'false') . "<br>";
echo "isManager(): " . (isManager() ? 'true' : 'false') . "<br>";

if (isset($_SESSION['user'])) {
    echo "<h3>User Info:</h3>";
    echo "<pre>";
    print_r($_SESSION['user']);
    echo "</pre>";
}

// Test direct database query
try {
    require_once 'config/database.php';
    $stmt = $pdo->prepare("SELECT * FROM invoices LIMIT 1");
    $stmt->execute();
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h3>Sample Invoice from DB:</h3>";
    echo "<pre>";
    print_r($invoice);
    echo "</pre>";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}
?>
