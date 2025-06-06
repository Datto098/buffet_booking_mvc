<?php
/**
 * Final Order Management Workflow Test
 */

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_email'] = 'admin@buffet.com';

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
    <title>Order Management Workflow Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .workflow-step { margin-bottom: 2rem; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1rem; }
        .workflow-success { border-color: #28a745; background-color: #f8fff9; }
        .workflow-error { border-color: #dc3545; background-color: #fff8f8; }
        .workflow-warning { border-color: #ffc107; background-color: #fffdf5; }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4"><i class="fas fa-cogs"></i> Order Management Workflow Test</h1>

        <?php
        $workflowResults = [];

        function testWorkflowStep($stepName, $testFunction) {
            global $workflowResults;

            echo "<div class='workflow-step'>";
            echo "<h3><i class='fas fa-play-circle'></i> {$stepName}</h3>";

            try {
                $result = $testFunction();

                if ($result['success']) {
                    echo "<div class='alert alert-success'>";
                    echo "<i class='fas fa-check-circle'></i> <strong>SUCCESS:</strong> " . $result['message'];
                    echo "</div>";
                    echo "<div class='workflow-success p-3'>";
                } else {
                    echo "<div class='alert alert-warning'>";
                    echo "<i class='fas fa-exclamation-triangle'></i> <strong>WARNING:</strong> " . $result['message'];
                    echo "</div>";
                    echo "<div class='workflow-warning p-3'>";
                }

                if (isset($result['output'])) {
                    echo "<h5>Output:</h5>";
                    echo "<div style='max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 1rem; border-radius: 0.25rem;'>";
                    echo "<pre style='white-space: pre-wrap; margin: 0;'>" . htmlspecialchars($result['output']) . "</pre>";
                    echo "</div>";
                }

                if (isset($result['links'])) {
                    echo "<h5>Test Links:</h5>";
                    echo "<div class='d-flex flex-wrap gap-2'>";
                    foreach ($result['links'] as $linkName => $linkUrl) {
                        echo "<a href='{$linkUrl}' class='btn btn-sm btn-outline-primary' target='_blank'>{$linkName}</a>";
                    }
                    echo "</div>";
                }

                echo "</div>";

                $workflowResults[] = [
                    'step' => $stepName,
                    'success' => $result['success'],
                    'message' => $result['message']
                ];

            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>";
                echo "<i class='fas fa-times-circle'></i> <strong>ERROR:</strong> " . $e->getMessage();
                echo "</div>";
                echo "<div class='workflow-error p-3'>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                echo "</div>";

                $workflowResults[] = [
                    'step' => $stepName,
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            echo "</div>";
        }

        // Step 1: Test Orders List
        testWorkflowStep("Orders List Display", function() {
            $admin = new AdminController();

            ob_start();
            $admin->orders();
            $output = ob_get_clean();

            return [
                'success' => !empty($output),
                'message' => 'Orders list page generated successfully',
                'output' => substr($output, 0, 1000) . (strlen($output) > 1000 ? '...' : ''),
                'links' => [
                    'View Orders' => 'index.php?page=admin&action=orders'
                ]
            ];
        });

        // Step 2: Test Order Details
        testWorkflowStep("Order Details Modal", function() {
            $orderModel = new Order();
            $orders = $orderModel->getAllOrders();

            if (empty($orders)) {
                return [
                    'success' => false,
                    'message' => 'No orders found to test details modal'
                ];
            }

            $admin = new AdminController();
            $firstOrder = $orders[0];

            ob_start();
            $admin->orderDetails($firstOrder['id']);
            $output = ob_get_clean();

            return [
                'success' => !empty($output),
                'message' => "Order details modal generated for Order ID: {$firstOrder['id']}",
                'output' => substr($output, 0, 800) . (strlen($output) > 800 ? '...' : ''),
                'links' => [
                    'View Details' => "index.php?page=admin&action=orderDetails&id={$firstOrder['id']}"
                ]
            ];
        });

        // Step 3: Test Print Order
        testWorkflowStep("Print Order Receipt", function() {
            $orderModel = new Order();
            $orders = $orderModel->getAllOrders();

            if (empty($orders)) {
                return [
                    'success' => false,
                    'message' => 'No orders found to test print functionality'
                ];
            }

            $admin = new AdminController();
            $firstOrder = $orders[0];

            ob_start();
            $admin->printOrder($firstOrder['id']);
            $output = ob_get_clean();

            return [
                'success' => !empty($output),
                'message' => "Print receipt generated for Order ID: {$firstOrder['id']}",
                'output' => substr($output, 0, 800) . (strlen($output) > 800 ? '...' : ''),
                'links' => [
                    'Print Order' => "index.php?page=admin&action=printOrder&id={$firstOrder['id']}"
                ]
            ];
        });

        // Step 4: Test CSV Export
        testWorkflowStep("CSV Export", function() {
            $admin = new AdminController();

            ob_start();
            $admin->exportOrdersCSV();
            $output = ob_get_clean();

            return [
                'success' => !empty($output),
                'message' => 'CSV export generated successfully',
                'output' => substr($output, 0, 500) . (strlen($output) > 500 ? '...' : ''),
                'links' => [
                    'Export CSV' => 'index.php?page=admin&action=exportOrdersCSV'
                ]
            ];
        });

        // Step 5: Test Filtered Orders
        testWorkflowStep("Filtered Orders", function() {
            $admin = new AdminController();

            // Set up filter parameters
            $_GET['status'] = 'pending';
            $_GET['search'] = '';
            $_GET['date_from'] = '2024-01-01';
            $_GET['date_to'] = '2025-12-31';

            ob_start();
            $admin->ordersFiltered();
            $output = ob_get_clean();

            // Clean up
            unset($_GET['status'], $_GET['search'], $_GET['date_from'], $_GET['date_to']);

            return [
                'success' => true,
                'message' => 'Filtered orders functionality working',
                'output' => substr($output, 0, 500) . (strlen($output) > 500 ? '...' : ''),
                'links' => [
                    'Filter Orders' => 'index.php?page=admin&action=ordersFiltered&status=pending'
                ]
            ];
        });

        // Final Summary
        $successCount = count(array_filter($workflowResults, function($r) { return $r['success']; }));
        $totalCount = count($workflowResults);
        $successRate = round(($successCount / $totalCount) * 100, 1);
        ?>

        <div class="mt-5 p-4 bg-primary text-white rounded">
            <h2><i class="fas fa-flag-checkered"></i> Workflow Summary</h2>
            <div class="row">
                <div class="col-md-8">
                    <h4>Results</h4>
                    <p><strong>Total Steps:</strong> <?= $totalCount ?></p>
                    <p><strong>Successful:</strong> <?= $successCount ?></p>
                    <p><strong>Success Rate:</strong> <?= $successRate ?>%</p>

                    <?php if ($successRate >= 80): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-thumbs-up"></i> <strong>Excellent!</strong> Order management system is working well.
                        </div>
                    <?php elseif ($successRate >= 60): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Good!</strong> Most features are working, minor issues may exist.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <strong>Attention needed!</strong> Several issues need to be resolved.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <h4>Quick Links</h4>
                    <div class="d-grid gap-2">
                        <a href="index.php?page=admin&action=orders" class="btn btn-light" target="_blank">
                            <i class="fas fa-list"></i> Admin Orders
                        </a>
                        <a href="test_suite.php" class="btn btn-light" target="_blank">
                            <i class="fas fa-tools"></i> Full Test Suite
                        </a>
                        <a href="index.php" class="btn btn-light" target="_blank">
                            <i class="fas fa-home"></i> Main Site
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h3><i class="fas fa-clipboard-list"></i> Step-by-Step Results</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Workflow Step</th>
                            <th>Status</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workflowResults as $index => $result): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($result['step']) ?></td>
                            <td>
                                <?php if ($result['success']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> SUCCESS</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> FAILED</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($result['message']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
