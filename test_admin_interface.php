<?php
/**
 * Test Admin Interface Access
 */

// Start session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'controllers/BaseController.php';
require_once 'controllers/AdminController.php';
require_once 'models/Order.php';
require_once 'models/User.php';
require_once 'models/Food.php';
require_once 'helpers/functions.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Admin Interface Test</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body>";

echo "<div class='container mt-4'>";
echo "<h1>Admin Interface Test</h1>";

try {
    // Test 1: Check BaseController
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'><h3>Test 1: BaseController</h3></div>";
    echo "<div class='card-body'>";

    $baseController = new BaseController();
    echo "<div class='alert alert-success'>✅ BaseController instantiated successfully</div>";

    echo "</div></div>";

    // Test 2: Check AdminController
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'><h3>Test 2: AdminController</h3></div>";
    echo "<div class='card-body'>";

    $adminController = new AdminController();
    echo "<div class='alert alert-success'>✅ AdminController instantiated successfully</div>";

    // Check if methods exist
    $methods = ['orders', 'orderDetails', 'printOrder', 'exportOrdersCSV', 'ordersFiltered'];
    foreach ($methods as $method) {
        if (method_exists($adminController, $method)) {
            echo "<div class='alert alert-success'>✅ Method '{$method}' exists</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Method '{$method}' missing</div>";
        }
    }

    echo "</div></div>";

    // Test 3: Test database connection
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'><h3>Test 3: Database Connection</h3></div>";
    echo "<div class='card-body'>";

    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
        $result = $stmt->fetch();
        echo "<div class='alert alert-success'>✅ Database connected. Orders table has {$result['count']} records</div>";
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Database error: " . $e->getMessage() . "</div>";
    }

    echo "</div></div>";

    // Test 4: Test admin login simulation
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'><h3>Test 4: Admin Login Simulation</h3></div>";
    echo "<div class='card-body'>";

    // Simulate admin login
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
    $_SESSION['user_email'] = 'admin@buffet.com';

    echo "<div class='alert alert-info'>Admin session simulated:</div>";
    echo "<ul>";
    echo "<li>User ID: " . $_SESSION['user_id'] . "</li>";
    echo "<li>Role: " . $_SESSION['user_role'] . "</li>";
    echo "<li>Email: " . $_SESSION['user_email'] . "</li>";
    echo "</ul>";

    echo "</div></div>";

    // Test 5: Test Order model
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'><h3>Test 5: Order Model</h3></div>";
    echo "<div class='card-body'>";

    try {
        $orderModel = new Order();
        $orders = $orderModel->getAllOrders();
        echo "<div class='alert alert-success'>✅ Order model working. Found " . count($orders) . " orders</div>";

        if (!empty($orders)) {
            echo "<h5>Sample Order:</h5>";
            $firstOrder = $orders[0];
            echo "<pre>" . print_r($firstOrder, true) . "</pre>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Order model error: " . $e->getMessage() . "</div>";
    }

    echo "</div></div>";

    // Test 6: Test routing
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'><h3>Test 6: Admin URLs</h3></div>";
    echo "<div class='card-body'>";

    $baseUrl = "http://localhost:8000";
    $adminUrls = [
        'Admin Dashboard' => "$baseUrl/index.php?page=admin&action=dashboard",
        'Orders List' => "$baseUrl/index.php?page=admin&action=orders",
        'Export CSV' => "$baseUrl/index.php?page=admin&action=exportOrdersCSV",
        'Test Suite' => "$baseUrl/test_suite.php"
    ];

    echo "<div class='alert alert-info'>Available Admin URLs:</div>";
    echo "<ul>";
    foreach ($adminUrls as $name => $url) {
        echo "<li><a href='$url' target='_blank'>$name</a></li>";
    }
    echo "</ul>";

    echo "</div></div>";

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>Fatal Error:</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "</div>"; // container
echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>";
echo "</body></html>";
?>
