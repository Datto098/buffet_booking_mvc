<!DOCTYPE html>
<?php require_once 'config/config.php'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Orders Management - Admin</title>
    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">Orders Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Orders</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportOrders()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <a href="<?= SITE_URL ?>/admin/orders/create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Order
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Orders
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalOrders ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Completed
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $completedOrders ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-warning">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $pendingOrders ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Today Revenue
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            $<?php echo number_format($todayRevenue ?? 0, 2); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Actions Bar -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchOrders" placeholder="Search by order ID, customer name, email...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleBulkActions()">
                                <i class="fas fa-tasks"></i> Bulk Actions
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="refreshOrders()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart"></i> Orders List
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">
                                <?php echo count($orders ?? []); ?> total
                            </span>
                        </div>
                    </div>
                    <div class="card-body">                        <?php if (!empty($orders)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="ordersTable" data-dt-disable="true">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                            </th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Date & Time</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input order-checkbox"
                                                           value="<?php echo $order['id']; ?>">
                                                </td>
                                                <td>
                                                    <strong class="text-primary">#<?php echo htmlspecialchars($order['id']); ?></strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium"><?php echo htmlspecialchars($order['customer_name'] ?? ''); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-medium"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></div>
                                                        <small class="text-muted"><?php echo date('g:i A', strtotime($order['created_at'])); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-utensils"></i> <?php echo htmlspecialchars($order['item_count'] ?? 0); ?> items
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-medium text-success">$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></div>
                                                </td>                                                <td>
                                                    <div class="status-update-container">
                                                        <select class="form-select form-select-sm status-select"
                                                                data-order-id="<?php echo $order['id']; ?>"
                                                                data-current-status="<?php echo $order['status'] ?? 'pending'; ?>">
                                                            <option value="pending" <?php echo ($order['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="confirmed" <?php echo ($order['status'] ?? 'pending') === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                            <option value="preparing" <?php echo ($order['status'] ?? 'pending') === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                            <option value="ready" <?php echo ($order['status'] ?? 'pending') === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                                            <option value="delivered" <?php echo ($order['status'] ?? 'pending') === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                            <option value="cancelled" <?php echo ($order['status'] ?? 'pending') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (isset($order['payment_status']) && $order['payment_status'] === 'paid'): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-credit-card"></i> Paid
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Unpaid
                                                        </span>
                                                    <?php endif; ?>
                                                </td>                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-primary"
                                                                onclick="editOrder(<?php echo $order['id']; ?>)"
                                                                title="Edit Order">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-info"
                                                                onclick="viewOrderDetails(<?php echo $order['id']; ?>)"
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-success"
                                                                onclick="printOrder(<?php echo $order['id']; ?>)"
                                                                title="Print Receipt">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="#" onclick="duplicateOrder(<?php echo $order['id']; ?>)">
                                                                    <i class="fas fa-copy"></i> Duplicate Order
                                                                </a></li>
                                                                <li><a class="dropdown-item" href="#" onclick="sendOrderEmail(<?php echo $order['id']; ?>)">
                                                                    <i class="fas fa-envelope"></i> Send Email
                                                                </a></li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteOrder(<?php echo $order['id']; ?>)">
                                                                    <i class="fas fa-trash"></i> Delete Order
                                                                </a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                                </div>
                                <h5 class="text-muted">No Orders Found</h5>
                                <p class="text-muted">There are no orders to display.</p>                                <a href="<?= SITE_URL ?>/admin/orders/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add First Order
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>            </main>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">
                        <i class="fas fa-receipt"></i> Order Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printCurrentOrder()">
                        <i class="fas fa-print"></i> Print Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOrderModalLabel">
                        <i class="fas fa-edit"></i> Edit Order
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editOrderForm">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="edit_customer_name" name="customer_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_customer_email" class="form-label">Customer Email</label>
                                <input type="email" class="form-control" id="edit_customer_email" name="customer_email" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_customer_phone" class="form-label">Customer Phone</label>
                                <input type="tel" class="form-control" id="edit_customer_phone" name="customer_phone">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_status" class="form-label">Order Status</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="preparing">Preparing</option>
                                    <option value="ready">Ready</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="edit_payment_method" name="payment_method">
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit Card</option>
                                    <option value="transfer">Bank Transfer</option>
                                    <option value="digital">Digital Wallet</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_total_amount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control" id="edit_total_amount" name="total_amount" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_delivery_address" class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="edit_delivery_address" name="delivery_address" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_order_notes" class="form-label">Order Notes</label>
                            <textarea class="form-control" id="edit_order_notes" name="order_notes" rows="3"></textarea>
                        </div>

                        <input type="hidden" id="edit_order_id" name="order_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulkActionsBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-3" style="display: none; z-index: 1050;">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold text-primary" id="selectedCount">0 orders selected</span>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkUpdateStatus('confirmed')">
                            <i class="fas fa-check"></i> Confirm All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkUpdateStatus('delivered')">
                            <i class="fas fa-truck"></i> Mark Delivered
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkUpdateStatus('cancelled')">
                            <i class="fas fa-times"></i> Cancel All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="bulkPrintOrders()">
                            <i class="fas fa-print"></i> Print All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
        // Global variables
        let currentOrderId = null;
        let selectedOrders = [];        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Initializing order management...');
            initializeStatusUpdates();
            initializeBulkSelection();
            initializeEditForm();
            console.log('Order management initialization complete');
        });        // Status update functionality
        function initializeStatusUpdates() {
            console.log('Initializing status updates...');
            // Remove existing event listeners to prevent duplicates
            const statusSelects = document.querySelectorAll('.status-select');
            console.log('Found', statusSelects.length, 'status select elements');

            statusSelects.forEach(select => {
                // Clone node to remove all event listeners
                const newSelect = select.cloneNode(true);
                select.parentNode.replaceChild(newSelect, select);
            });

            // Add fresh event listeners
            const newStatusSelects = document.querySelectorAll('.status-select');
            newStatusSelects.forEach((select, index) => {
                console.log('Adding event listener to select', index);
                select.addEventListener('change', function(e) {
                    console.log('Status select changed:', this.value);
                    // Prevent multiple simultaneous calls
                    if (this.classList.contains('updating')) {
                        console.log('Already updating, skipping...');
                        return;
                    }

                    const orderId = this.dataset.orderId;
                    const newStatus = this.value;
                    const currentStatus = this.dataset.currentStatus;

                    if (newStatus !== currentStatus) {
                        console.log('Updating order', orderId, 'from', currentStatus, 'to', newStatus);
                        this.classList.add('updating');
                        updateOrderStatus(orderId, newStatus, this);
                    }
                });
            });
            console.log('Status updates initialization complete');
        }        // Update order status
        function updateOrderStatus(orderId, status, selectElement) {
            console.log('updateOrderStatus called for order', orderId, 'with status', status);
            const originalStatus = selectElement.dataset.currentStatus;

            fetch('<?= SITE_URL ?>/admin/orders/update-status/' + orderId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'csrf_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').getAttribute('content')) +
                      '&status=' + encodeURIComponent(status)
            })
            .then(response => {
                console.log('Response received:', response.status, response.url);
                selectElement.classList.remove('updating');
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    selectElement.dataset.currentStatus = status;
                    showAlert('success', data.message || 'Order status updated successfully');
                } else {
                    selectElement.value = originalStatus;
                    showAlert('error', data.message || 'Failed to update order status');
                }
            })
            .catch(error => {
                console.error('Update error:', error);
                selectElement.classList.remove('updating');
                selectElement.value = originalStatus;
                showAlert('error', 'Failed to update order status');
            });
        }

        // Edit Order Modal
        function editOrder(orderId) {
            currentOrderId = orderId;

            // Fetch order data
            fetch('<?= SITE_URL ?>/admin/orders/get/' + orderId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateEditForm(data.order);
                        new bootstrap.Modal(document.getElementById('editOrderModal')).show();
                    } else {
                        showAlert('error', 'Failed to load order data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Failed to load order data');
                });
        }

        // Populate edit form with order data
        function populateEditForm(order) {
            document.getElementById('edit_order_id').value = order.id;
            document.getElementById('edit_customer_name').value = order.customer_name || '';
            document.getElementById('edit_customer_email').value = order.customer_email || '';
            document.getElementById('edit_customer_phone').value = order.customer_phone || '';
            document.getElementById('edit_status').value = order.status || 'pending';
            document.getElementById('edit_payment_method').value = order.payment_method || 'cash';
            document.getElementById('edit_total_amount').value = order.total_amount || '';
            document.getElementById('edit_delivery_address').value = order.delivery_address || '';
            document.getElementById('edit_order_notes').value = order.order_notes || '';
        }

        // Initialize edit form submission
        function initializeEditForm() {
            const editForm = document.getElementById('editOrderForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    saveOrderChanges();
                });
            }
        }

        // Save order changes
        function saveOrderChanges() {
            const formData = new FormData(document.getElementById('editOrderForm'));

            fetch('<?= SITE_URL ?>/admin/orders/update/' + currentOrderId, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Order updated successfully');
                    bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
                    refreshOrders();
                } else {
                    showAlert('error', data.message || 'Failed to update order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Failed to update order');
            });
        }

        // View Order Details
        function viewOrderDetails(orderId) {
            currentOrderId = orderId;

            fetch('<?= SITE_URL ?>/admin/orders/details/' + orderId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderDetailsContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Failed to load order details');
                });
        }

        // Print order
        function printOrder(orderId) {
            window.open('<?= SITE_URL ?>/admin/orders/print/' + orderId, '_blank');
        }

        function printCurrentOrder() {
            if (currentOrderId) {
                printOrder(currentOrderId);
            }
        }

        // Bulk selection functionality
        function initializeBulkSelection() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const orderCheckboxes = document.querySelectorAll('.order-checkbox');

            selectAllCheckbox?.addEventListener('change', function() {
                orderCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedOrders();
            });

            orderCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedOrders);
            });
        }

        function updateSelectedOrders() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            selectedOrders = Array.from(checkedBoxes).map(cb => cb.value);

            const count = selectedOrders.length;
            document.getElementById('selectedCount').textContent = count + (count === 1 ? ' order selected' : ' orders selected');

            const bulkBar = document.getElementById('bulkActionsBar');
            if (count > 0) {
                bulkBar.style.display = 'block';
            } else {
                bulkBar.style.display = 'none';
            }
        }

        // Bulk operations
        function bulkUpdateStatus(status) {
            if (selectedOrders.length === 0) {
                showAlert('warning', 'Please select orders first');
                return;
            }

            if (confirm(`Are you sure you want to update ${selectedOrders.length} orders to ${status}?`)) {
                Promise.all(selectedOrders.map(orderId => {
                    return fetch('<?= SITE_URL ?>/admin/orders/update-status/' + orderId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'csrf_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').getAttribute('content')) +
                              '&status=' + encodeURIComponent(status)
                    });
                }))
                .then(() => {
                    showAlert('success', 'Orders updated successfully');
                    refreshOrders();
                    clearSelection();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Failed to update some orders');
                });
            }
        }

        function bulkPrintOrders() {
            selectedOrders.forEach(orderId => {
                printOrder(orderId);
            });
        }

        function clearSelection() {
            document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateSelectedOrders();
        }

        // Additional functions
        function duplicateOrder(orderId) {
            if (confirm('Create a duplicate of this order?')) {
                fetch('<?= SITE_URL ?>/admin/orders/duplicate/' + orderId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'csrf_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Order duplicated successfully');
                        refreshOrders();
                    } else {
                        showAlert('error', data.message || 'Failed to duplicate order');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Failed to duplicate order');
                });
            }
        }

        function sendOrderEmail(orderId) {
            fetch('<?= SITE_URL ?>/admin/orders/send-email/' + orderId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'csrf_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Email sent successfully');
                } else {
                    showAlert('error', data.message || 'Failed to send email');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Failed to send email');
            });
        }

        function deleteOrder(orderId) {
            if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
                fetch('<?= SITE_URL ?>/admin/orders/delete/' + orderId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'csrf_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Order deleted successfully');
                        refreshOrders();
                    } else {
                        showAlert('error', data.message || 'Failed to delete order');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Failed to delete order');
                });
            }
        }

        // Utility functions
        function toggleBulkActions() {
            const bulkBar = document.getElementById('bulkActionsBar');
            if (bulkBar) {
                bulkBar.style.display = bulkBar.style.display === 'none' ? 'block' : 'none';
            }
        }

        function refreshOrders() {
            location.reload();
        }

        function exportOrders() {
            window.location.href = '<?= SITE_URL ?>/admin/orders/export-csv';
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Search functionality
        const searchInput = document.getElementById('searchOrders');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#ordersTable tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    </script>
</body>
</html>
