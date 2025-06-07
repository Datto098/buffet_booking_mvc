<!DOCTYPE html>
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
                    <div class="card-body">
                        <?php if (!empty($orders)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="ordersTable">
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
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'secondary';
                                                    $statusIcon = 'circle';
                                                    switch($order['status']) {
                                                        case 'completed':
                                                            $statusClass = 'success';
                                                            $statusIcon = 'check-circle';
                                                            break;
                                                        case 'pending':
                                                            $statusClass = 'warning';
                                                            $statusIcon = 'clock';
                                                            break;
                                                        case 'processing':
                                                            $statusClass = 'info';
                                                            $statusIcon = 'cog';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'danger';
                                                            $statusIcon = 'times-circle';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                                        <i class="fas fa-<?php echo $statusIcon; ?>"></i> <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                                    </span>
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
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">                                                        <a href="<?= SITE_URL ?>/admin/orders/edit/<?php echo $order['id']; ?>"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Edit Order">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-info"
                                                                onclick="viewOrder(<?php echo $order['id']; ?>)"
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-success"
                                                                onclick="printOrder(<?php echo $order['id']; ?>)"
                                                                title="Print Receipt">
                                                            <i class="fas fa-print"></i>
                                                        </button>
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
                </div>

            </main>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>

    <script>
        // Orders Management JavaScript
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
            window.location.href = '<?= SITE_URL ?>/admin/orders?export=csv';
        }

        function viewOrder(orderId) {
            window.location.href = '<?= SITE_URL ?>/admin/orders/view/' + orderId;
        }

        function printOrder(orderId) {
            window.open('<?= SITE_URL ?>/admin/orders/print/' + orderId, '_blank');
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
