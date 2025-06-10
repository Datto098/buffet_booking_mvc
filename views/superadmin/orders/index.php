<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-shopping-cart"></i>
                    Order Management
                </h1>
                <div class="btn-toolbar">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshOrders()">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Order Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-primary me-3">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Total Orders</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['total_orders'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-warning me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Pending Orders</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['pending_orders'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-success me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Completed Orders</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['completed_orders'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-info me-3">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Revenue</div>
                                <div class="h4 mb-0 fw-bold text-dark">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
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
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Order ID, Customer..."
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>                    <div class="col-12">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="<?= SITE_URL ?>/superadmin/orders" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-danger">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
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
                                            <span class="badge bg-secondary"><?php echo $order['item_count'] ?? 0; ?> items</span>
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
                                        </td>
                                        <td>
                                            <div><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                                            <div class="small text-muted"><?php echo date('H:i', strtotime($order['order_date'])); ?></div>
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
?>

</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
