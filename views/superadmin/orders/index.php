<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>Order Management - Super Admin</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-shopping-cart me-2 text-success"></i>Order Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Orders</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-primary" onclick="refreshOrders()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh Data
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php
                $flash = $_SESSION['flash'] ?? [];
                foreach ($flash as $type => $message):
                ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php
                endforeach;
                unset($_SESSION['flash']);
                ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Orders</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total_orders'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Orders</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['pending_orders'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Completed Orders</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['completed_orders'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Revenue</div>
                                        <div class="h4 mb-0 font-weight-bold">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                </div>

                <!-- Filter Bar -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2 text-success"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= SITE_URL ?>/superadmin/orders" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Search Orders</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control" name="search" placeholder="Order ID, Customer..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="preparing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'preparing') ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="ready" <?php echo (isset($_GET['status']) && $_GET['status'] == 'ready') ? 'selected' : ''; ?>>Ready</option>
                                    <option value="delivered" <?php echo (isset($_GET['status']) && $_GET['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">From Date</label>
                                <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">To Date</label>
                                <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="<?= SITE_URL ?>/superadmin/orders" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">
                                <i class="fas fa-shopping-cart me-2 text-success"></i>All Orders
                            </h6>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                <?php echo count($orders ?? []); ?> orders displayed
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-bold">Order ID</th>
                                        <th class="border-0 fw-bold">Customer</th>
                                        <th class="border-0 fw-bold">Items</th>
                                        <th class="border-0 fw-bold">Total Amount</th>
                                        <th class="border-0 fw-bold">Status</th>
                                        <th class="border-0 fw-bold">Order Date</th>
                                        <th class="border-0 fw-bold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo $order['id']; ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($order['customer_email'] ?? 'N/A'); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $itemCount = 0;
                                            if (!empty($order['items'])) {
                                                $itemCount = count($order['items']);
                                            } elseif (isset($order['item_count'])) {
                                                $itemCount = $order['item_count'];
                                            } elseif (isset($order['total_items'])) {
                                                $itemCount = $order['total_items'];
                                            }
                                            ?>
                                            <span class="badge bg-secondary"><?php echo $itemCount; ?> items</span>
                                            <?php if (!empty($order['items'])): ?>
                                                <div class="small text-muted mt-1">
                                                    <?php
                                                    $itemNames = array_slice(array_column($order['items'], 'name'), 0, 2);
                                                    echo implode(', ', $itemNames);
                                                    if (count($order['items']) > 2) {
                                                        echo '...';
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="text-success">$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getStatusBadgeColor($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>                                        <td>
                                            <div><?php echo date('M d, Y', strtotime($order['created_at'] ?? $order['order_date'] ?? 'now')); ?></div>
                                            <div class="small text-muted"><?php echo date('H:i', strtotime($order['created_at'] ?? $order['order_date'] ?? 'now')); ?></div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'confirmed')">Mark Confirmed</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'preparing')">Mark Preparing</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'ready')">Mark Ready</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'delivered')">Mark Delivered</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">Mark Completed</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">Cancel Order</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No orders found</h5>
                                        <p class="text-muted">Try adjusting your filters or check back later.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                    <nav aria-label="Order pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&<?php echo http_build_query($_GET); ?>">Previous</a>
                            </li>

                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&<?php echo http_build_query($_GET); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        </main>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Get the site URL from PHP with debug logging
    const SITE_URL = '<?= SITE_URL ?>';

    // Debug logging
    console.log('🔧 SuperAdmin Orders Debug Info:');
    console.log('SITE_URL:', SITE_URL);
    console.log('Current window location:', window.location.href);

    function refreshOrders() {
        location.reload();
    }

    function viewOrderDetails(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        const content = document.getElementById('orderDetailsContent');

        // Construct URL and log it
        const detailUrl = `${SITE_URL}/superadmin/orders/details/${orderId}`;
        console.log('🔍 Fetching order details from:', detailUrl);

        // Show loading
        content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

        modal.show();

        // Fetch order details with correct URL and better error handling
        fetch(detailUrl)
            .then(response => {
                console.log('📡 Fetch response status:', response.status);
                console.log('📡 Fetch response URL:', response.url);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Received data:', data);
                if (data.success) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error loading order details: ${data.message || 'Unknown error'}
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
                content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading order details: ${error.message}<br>
                    <small>Attempted URL: ${detailUrl}</small>
                </div>
            `;
            });
    }

    function updateOrderStatus(orderId, status) {
        if (confirm(`Are you sure you want to update this order status to "${status}"?`)) {
            const updateUrl = `${SITE_URL}/superadmin/orders/updateStatus/${orderId}`;
            console.log('📝 Updating order status at:', updateUrl);

            fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => {
                    console.log('📡 Update response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Update response data:', data);
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating order status: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('❌ Update error:', error);
                    alert('Error updating order status: ' + error.message);
                });
        }
    }
</script>

<?php
function getStatusBadgeColor($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'confirmed':
            return 'info';
        case 'preparing':
            return 'primary';
        case 'ready':
            return 'success';
        case 'delivered':
            return 'success';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Helper function for order status icons
function getStatusIcon($status) {
    switch($status) {
        case 'pending': return 'clock';
        case 'confirmed': return 'check';
        case 'preparing': return 'utensils';
        case 'ready': return 'bell';
        case 'delivered': return 'truck';
        case 'completed': return 'check-circle';
        case 'cancelled': return 'times';
        default: return 'question';
    }
}
?>

</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
