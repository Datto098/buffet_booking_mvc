<?php
/**
 * Complete Order Management Testing Suite
 */

// Start session and set admin credentials
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_email'] = 'admin@buffet.com';

// Include necessary files
require_once 'config/database.php';
require_once 'controllers/BaseController.php';
require_once 'controllers/AdminController.php';
require_once 'models/Order.php';
require_once 'models/User.php';
require_once 'models/Food.php';
require_once 'helpers/functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management Testing Suite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-section { margin-bottom: 2rem; }
        .status-success { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .test-result { padding: 0.5rem; margin: 0.25rem 0; border-radius: 0.25rem; }
        .pre-wrap { white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4"><i class="fas fa-clipboard-check"></i> Order Management Testing Suite</h1>

                <?php
                $testResults = [];
                $totalTests = 0;
                $passedTests = 0;

                function runTest($testName, $testFunction) {
                    global $testResults, $totalTests, $passedTests;
                    $totalTests++;

                    echo "<div class='test-section'>";
                    echo "<h3><i class='fas fa-vial'></i> {$testName}</h3>";

                    try {
                        $result = $testFunction();
                        if ($result['success']) {
                            $passedTests++;
                            echo "<div class='alert alert-success'>";
                            echo "<i class='fas fa-check'></i> <strong>PASS:</strong> " . $result['message'];
                        } else {
                            echo "<div class='alert alert-danger'>";
                            echo "<i class='fas fa-times'></i> <strong>FAIL:</strong> " . $result['message'];
                        }

                        if (isset($result['details'])) {
                            echo "<br><small>" . $result['details'] . "</small>";
                        }
                        echo "</div>";

                        $testResults[] = [
                            'name' => $testName,
                            'success' => $result['success'],
                            'message' => $result['message']
                        ];

                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger'>";
                        echo "<i class='fas fa-exclamation-triangle'></i> <strong>ERROR:</strong> " . $e->getMessage();
                        echo "<br><small class='pre-wrap'>" . $e->getTraceAsString() . "</small>";
                        echo "</div>";

                        $testResults[] = [
                            'name' => $testName,
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }

                    echo "</div>";
                }

                // Test 1: Controllers instantiation
                runTest("Controllers Instantiation", function() {
                    $base = new BaseController();
                    $admin = new AdminController();
                    return [
                        'success' => true,
                        'message' => 'BaseController and AdminController instantiated successfully'
                    ];
                });

                // Test 2: AdminController methods
                runTest("AdminController Methods", function() {
                    $admin = new AdminController();
                    $requiredMethods = ['orders', 'orderDetails', 'printOrder', 'exportOrdersCSV', 'ordersFiltered'];
                    $missing = [];

                    foreach ($requiredMethods as $method) {
                        if (!method_exists($admin, $method)) {
                            $missing[] = $method;
                        }
                    }

                    if (empty($missing)) {
                        return [
                            'success' => true,
                            'message' => 'All required methods exist',
                            'details' => 'Methods: ' . implode(', ', $requiredMethods)
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Missing methods: ' . implode(', ', $missing)
                        ];
                    }
                });

                // Test 3: Database connection and orders
                runTest("Database and Orders", function() {
                    $orderModel = new Order();
                    $orders = $orderModel->getAllOrders();

                    return [
                        'success' => true,
                        'message' => 'Database connected successfully',
                        'details' => 'Found ' . count($orders) . ' orders in database'
                    ];
                });

                // Test 4: Order details functionality
                runTest("Order Details Method", function() {
                    $admin = new AdminController();
                    $orderModel = new Order();
                    $orders = $orderModel->getAllOrders();

                    if (empty($orders)) {
                        return [
                            'success' => false,
                            'message' => 'No orders found to test order details'
                        ];
                    }

                    $firstOrder = $orders[0];

                    // Capture output
                    ob_start();
                    $admin->orderDetails($firstOrder['id']);
                    $output = ob_get_clean();

                    if (strpos($output, 'Order Details') !== false || strpos($output, 'order_id') !== false) {
                        return [
                            'success' => true,
                            'message' => 'Order details method working',
                            'details' => 'Generated output for order ID: ' . $firstOrder['id']
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Order details method not generating expected output'
                        ];
                    }
                });

                // Test 5: Print order functionality
                runTest("Print Order Method", function() {
                    $admin = new AdminController();
                    $orderModel = new Order();
                    $orders = $orderModel->getAllOrders();

                    if (empty($orders)) {
                        return [
                            'success' => false,
                            'message' => 'No orders found to test print functionality'
                        ];
                    }

                    $firstOrder = $orders[0];

                    // Capture output
                    ob_start();
                    $admin->printOrder($firstOrder['id']);
                    $output = ob_get_clean();

                    if (strpos($output, 'Receipt') !== false || strpos($output, 'Order') !== false) {
                        return [
                            'success' => true,
                            'message' => 'Print order method working',
                            'details' => 'Generated print output for order ID: ' . $firstOrder['id']
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Print order method not generating expected output'
                        ];
                    }
                });

                // Test 6: CSV export functionality
                runTest("CSV Export Method", function() {
                    $admin = new AdminController();

                    // Capture output
                    ob_start();
                    $admin->exportOrdersCSV();
                    $output = ob_get_clean();

                    if (strpos($output, 'Order ID') !== false || strpos($output, 'Customer') !== false) {
                        return [
                            'success' => true,
                            'message' => 'CSV export method working',
                            'details' => 'Generated CSV output with headers'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'CSV export method not generating expected output'
                        ];
                    }
                });

                // Test 7: Filtered orders functionality
                runTest("Filtered Orders Method", function() {
                    $admin = new AdminController();

                    // Set up some filter parameters
                    $_GET['status'] = 'pending';
                    $_GET['date_from'] = '2024-01-01';
                    $_GET['date_to'] = '2025-12-31';

                    // Capture output
                    ob_start();
                    $admin->ordersFiltered();
                    $output = ob_get_clean();

                    // Clean up
                    unset($_GET['status'], $_GET['date_from'], $_GET['date_to']);

                    if (strpos($output, 'orders') !== false || !empty($output)) {
                        return [
                            'success' => true,
                            'message' => 'Filtered orders method working',
                            'details' => 'Generated filtered output'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Filtered orders method not working as expected'
                        ];
                    }
                });

                // Test 8: View files existence
                runTest("View Files Existence", function() {
                    $viewFiles = [
                        'views/admin/orders/index.php',
                        'views/admin/orders/details_modal.php',
                        'views/admin/orders/print.php'
                    ];

                    $missing = [];
                    foreach ($viewFiles as $file) {
                        if (!file_exists($file)) {
                            $missing[] = $file;
                        }
                    }

                    if (empty($missing)) {
                        return [
                            'success' => true,
                            'message' => 'All required view files exist',
                            'details' => 'Files: ' . implode(', ', $viewFiles)
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Missing view files: ' . implode(', ', $missing)
                        ];
                    }
                });

                // Test Summary
                echo "<div class='mt-5 p-4 bg-light rounded'>";
                echo "<h2><i class='fas fa-chart-pie'></i> Test Summary</h2>";
                echo "<div class='row'>";
                echo "<div class='col-md-6'>";
                echo "<h4>Results Overview</h4>";
                echo "<p><strong>Total Tests:</strong> {$totalTests}</p>";
                echo "<p><strong>Passed:</strong> <span class='status-success'>{$passedTests}</span></p>";
                echo "<p><strong>Failed:</strong> <span class='status-error'>" . ($totalTests - $passedTests) . "</span></p>";
                echo "<p><strong>Success Rate:</strong> " . round(($passedTests / $totalTests) * 100, 1) . "%</p>";
                echo "</div>";
                echo "<div class='col-md-6'>";
                echo "<h4>Quick Actions</h4>";
                echo "<div class='btn-group-vertical w-100'>";
                echo "<a href='index.php?page=admin&action=orders' class='btn btn-primary' target='_blank'>";
                echo "<i class='fas fa-list'></i> View Orders List</a>";
                echo "<a href='index.php?page=admin&action=exportOrdersCSV' class='btn btn-success' target='_blank'>";
                echo "<i class='fas fa-download'></i> Export CSV</a>";
                echo "<a href='test_suite.php' class='btn btn-info' target='_blank'>";
                echo "<i class='fas fa-tools'></i> Full Test Suite</a>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                ?>

                <!-- Detailed Results -->
                <div class="mt-4">
                    <h3><i class="fas fa-list-ul"></i> Detailed Results</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($testResults as $result): ?>
                                <tr>
                                    <td><?= htmlspecialchars($result['name']) ?></td>
                                    <td>
                                        <?php if ($result['success']): ?>
                                            <span class="badge bg-success"><i class="fas fa-check"></i> PASS</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> FAIL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($result['message']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- System Information -->
                <div class="mt-4 p-3 bg-info text-white rounded">
                    <h4><i class="fas fa-info-circle"></i> System Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>PHP Version:</strong> <?= PHP_VERSION ?></p>
                            <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Built-in server' ?></p>
                            <p><strong>Session ID:</strong> <?= session_id() ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>User ID:</strong> <?= $_SESSION['user_id'] ?? 'Not set' ?></p>
                            <p><strong>User Role:</strong> <?= $_SESSION['user_role'] ?? 'Not set' ?></p>
                            <p><strong>Current Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
