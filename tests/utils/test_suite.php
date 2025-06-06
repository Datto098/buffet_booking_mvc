<?php
// Start session and include necessary files before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';
require_once 'models/User.php';

// Set up admin session
$userModel = new User();
$admins = $userModel->findByCondition(['role' => 'manager']);

$adminFound = false;
if (!empty($admins)) {
    $admin = $admins[0];
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['user_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
    $_SESSION['user_email'] = $admin['email'];
    $adminFound = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management Test Suite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Order Management Test Suite</h1>        <?php if ($adminFound): ?>
            <div class='alert alert-success'>
                <i class='fas fa-check-circle'></i> Admin session established: <?= htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) ?> (<?= htmlspecialchars($admin['role']) ?>)
            </div>
        <?php else: ?>
            <div class='alert alert-danger'>
                <i class='fas fa-exclamation-triangle'></i> No admin users found. Please run database setup first.
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Order Management Access</h5>
                    </div>
                    <div class="card-body">
                        <p>Test the main order management interface:</p>
                        <a href="/admin/orders" class="btn btn-primary" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Open Order Management
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-database"></i> Database Setup</h5>
                    </div>
                    <div class="card-body">
                        <p>Check and setup database structure:</p>
                        <a href="test_database.php" class="btn btn-info" target="_blank">
                            <i class="fas fa-database"></i> Database Check
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-eye"></i> Order Details</h5>
                    </div>
                    <div class="card-body">
                        <p>Test order details modal:</p>
                        <button class="btn btn-primary" onclick="testOrderDetails()">
                            <i class="fas fa-info-circle"></i> Test Details Modal
                        </button>
                        <div id="orderDetailsResult" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-print"></i> Print Order</h5>
                    </div>
                    <div class="card-body">
                        <p>Test printable order receipt:</p>
                        <button class="btn btn-success" onclick="testPrintOrder()">
                            <i class="fas fa-print"></i> Test Print View
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-download"></i> CSV Export</h5>
                    </div>
                    <div class="card-body">
                        <p>Test CSV export functionality:</p>
                        <button class="btn btn-warning" onclick="testCSVExport()">
                            <i class="fas fa-file-csv"></i> Test CSV Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-code"></i> Direct API Tests</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/admin/orders')">
                                    Test Orders List
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/admin/orders/details/1')">
                                    Test Order Details
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/admin/orders/print/1')">
                                    Test Print Order
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/admin/orders/export-csv')">
                                    Test CSV Export
                                </button>
                            </div>
                        </div>
                        <div id="apiTestResults" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-clipboard-check"></i> Test Results</h5>
                    </div>
                    <div class="card-body">
                        <div id="testResults">
                            <p class="text-muted">Run tests above to see results...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printCurrentOrder()">Print Order</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentOrderId = 1;

        function testOrderDetails() {
            fetch('/admin/orders/details/' + currentOrderId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderDetailsContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
                    addTestResult('Order Details', 'success', 'Modal loaded successfully');
                })
                .catch(error => {
                    console.error('Error:', error);
                    addTestResult('Order Details', 'danger', 'Failed to load: ' + error.message);
                });
        }

        function testPrintOrder() {
            window.open('/admin/orders/print/' + currentOrderId, '_blank');
            addTestResult('Print Order', 'success', 'Print window opened');
        }

        function testCSVExport() {
            const link = document.createElement('a');
            link.href = '/admin/orders/export-csv';
            link.download = 'orders_export.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            addTestResult('CSV Export', 'success', 'CSV download initiated');
        }

        function testEndpoint(endpoint) {
            addTestResult('API Test', 'info', `Testing: ${endpoint}`);

            fetch(endpoint)
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                })
                .then(data => {
                    const length = data.length;
                    const preview = data.substring(0, 100) + (length > 100 ? '...' : '');
                    addTestResult('API Test', 'success', `${endpoint} - Success (${length} chars): ${preview}`);
                })
                .catch(error => {
                    addTestResult('API Test', 'danger', `${endpoint} - Failed: ${error.message}`);
                });
        }

        function printCurrentOrder() {
            if (currentOrderId) {
                testPrintOrder();
            }
        }

        function addTestResult(testName, type, message) {
            const resultsDiv = document.getElementById('testResults');
            const timestamp = new Date().toLocaleTimeString();

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mb-2`;
            alertDiv.innerHTML = `
                <strong>${testName}</strong> [${timestamp}]: ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            if (resultsDiv.querySelector('.text-muted')) {
                resultsDiv.innerHTML = '';
            }

            resultsDiv.appendChild(alertDiv);

            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 10000);
        }

        // Initial test to verify page load
        document.addEventListener('DOMContentLoaded', function() {
            addTestResult('Page Load', 'success', 'Test suite initialized successfully');
        });
    </script>
</body>
</html>
