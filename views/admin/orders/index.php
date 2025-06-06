<?php
$pageTitle = $data['title'];
require_once 'views/admin/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once 'views/admin/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Order Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshOrders()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Filter Bar -->
            <div class="card mb-4">
                <div class="card-body">                    <form method="GET" class="row g-3">
                        <div class="col-md-2">
                            <input type="text" name="search" class="form-control" value="<?= $_GET['search'] ?? '' ?>" placeholder="Search orders...">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" <?= $data['statusFilter'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $data['statusFilter'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="preparing" <?= $data['statusFilter'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                                <option value="ready" <?= $data['statusFilter'] === 'ready' ? 'selected' : '' ?>>Ready</option>
                                <option value="delivered" <?= $data['statusFilter'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled" <?= $data['statusFilter'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>" placeholder="To Date">
                        </div>
                        <div class="col-md-4">
                            <div class="btn-group" role="group">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="/admin/orders" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                                <button type="button" class="btn btn-success" onclick="exportToCSV()">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        All Orders (<?= number_format($data['totalOrders']) ?> total)
                    </h6>
                    <div class="d-flex gap-2">
                        <?php
                        $statusCounts = [
                            'pending' => count(array_filter($data['orders'], fn($o) => $o['status'] === 'pending')),
                            'confirmed' => count(array_filter($data['orders'], fn($o) => $o['status'] === 'confirmed')),
                            'preparing' => count(array_filter($data['orders'], fn($o) => $o['status'] === 'preparing')),
                            'ready' => count(array_filter($data['orders'], fn($o) => $o['status'] === 'ready'))
                        ];
                        ?>
                        <span class="badge bg-warning"><?= $statusCounts['pending'] ?> Pending</span>
                        <span class="badge bg-info"><?= $statusCounts['confirmed'] ?> Confirmed</span>
                        <span class="badge bg-primary"><?= $statusCounts['preparing'] ?> Preparing</span>
                        <span class="badge bg-success"><?= $statusCounts['ready'] ?> Ready</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data['orders'])): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No orders found.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($data['orders'] as $order): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($order['customer_name']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($order['customer_email']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <small><?= $order['total_items'] ?> items</small>
                                                <br>
                                                <button class="btn btn-sm btn-outline-info" onclick="viewOrderDetails(<?= $order['id'] ?>)">
                                                    View Details
                                                </button>
                                            </td>
                                            <td>
                                                <strong>$<?= number_format($order['total_amount'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <div class="status-update-container">
                                                    <?php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'preparing' => 'primary',
                                                        'ready' => 'success',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $color = $statusColors[$order['status']] ?? 'secondary';
                                                    ?>
                                                    <select class="form-select form-select-sm status-select"
                                                            data-order-id="<?= $order['id'] ?>"
                                                            data-current-status="<?= $order['status'] ?>">
                                                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                        <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                                                        <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Ready</option>
                                                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <?= date('M j, Y', strtotime($order['created_at'])) ?><br>
                                                    <small class="text-muted"><?= date('g:i A', strtotime($order['created_at'])) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewOrderDetails(<?= $order['id'] ?>)" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="printOrder(<?= $order['id'] ?>)" title="Print">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['totalPages'] > 1): ?>
                        <nav aria-label="Orders pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($data['currentPage'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="/admin/orders?page=<?= $data['currentPage'] - 1 ?><?= $data['statusFilter'] ? '&status=' . $data['statusFilter'] : '' ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                                    <li class="page-item <?= $i === $data['currentPage'] ? 'active' : '' ?>">
                                        <a class="page-link" href="/admin/orders?page=<?= $i ?><?= $data['statusFilter'] ? '&status=' . $data['statusFilter'] : '' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($data['currentPage'] < $data['totalPages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="/admin/orders?page=<?= $data['currentPage'] + 1 ?><?= $data['statusFilter'] ? '&status=' . $data['statusFilter'] : '' ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
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
                <!-- Order details will be loaded here -->
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

<script>
let currentOrderId = null;

// Status update functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');

    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;

            if (newStatus !== currentStatus) {
                updateOrderStatus(orderId, newStatus, this);
            }
        });
    });
});

function updateOrderStatus(orderId, status, selectElement) {
    const originalStatus = selectElement.dataset.currentStatus;

    fetch('/admin/orders/update-status/' + orderId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'csrf_token=' + encodeURIComponent(window.csrfToken) + '&status=' + encodeURIComponent(status)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectElement.dataset.currentStatus = status;
            showAlert('success', data.message);

            // Update status badge color
            const statusColors = {
                'pending': 'warning',
                'confirmed': 'info',
                'preparing': 'primary',
                'ready': 'success',
                'delivered': 'success',
                'cancelled': 'danger'
            };

            selectElement.className = 'form-select form-select-sm status-select';
        } else {
            selectElement.value = originalStatus;
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        selectElement.value = originalStatus;
        showAlert('error', 'Failed to update order status');
        console.error('Error:', error);
    });
}

function viewOrderDetails(orderId) {
    currentOrderId = orderId;

    fetch('/admin/orders/details/' + orderId)
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

function printOrder(orderId) {
    window.open('/admin/orders/print/' + orderId, '_blank');
}

function printCurrentOrder() {
    if (currentOrderId) {
        printOrder(currentOrderId);
    }
}

function refreshOrders() {
    location.reload();
}

function exportToCSV() {
    // Get current filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    const exportUrl = '/admin/orders/export-csv?' + urlParams.toString();

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'orders_export.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showAlert('success', 'CSV export started. Your download should begin shortly.');
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.querySelector('main').insertBefore(alertDiv, document.querySelector('main').firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<?php require_once 'views/admin/layouts/footer.php'; ?>
