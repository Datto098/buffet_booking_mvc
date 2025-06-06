<?php
$title = "Order History - " . APP_NAME;
$current_page = 'orders';
include VIEW_PATH . '/layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order History</h2>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                        <option value="">All Orders</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="preparing">Preparing</option>
                        <option value="ready">Ready</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">Last 3 Months</option>
                    </select>
                </div>
            </div>

            <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No Orders Found</h4>
                <p class="text-muted mb-4">You haven't placed any orders yet. Start exploring our delicious menu!</p>
                <a href="<?= BASE_URL ?>/menu" class="btn btn-primary">
                    <i class="fas fa-utensils me-2"></i>Browse Menu
                </a>
            </div>
            <?php else: ?>

            <div class="row" id="ordersContainer">
                <?php foreach ($orders as $order): ?>
                <div class="col-lg-6 col-xl-4 mb-4 order-card"
                     data-status="<?= $order['status'] ?>"
                     data-date="<?= $order['created_at'] ?>">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title mb-1">Order #<?= $order['id'] ?></h6>
                                    <small class="text-muted">
                                        <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="badge badge-status badge-<?= strtolower($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Items:</span>
                                    <span><?= $order['total_items'] ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Type:</span>
                                    <span><?= ucfirst($order['order_type']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total:</span>
                                    <span class="fw-bold">$<?= number_format($order['total_amount'], 2) ?></span>
                                </div>
                            </div>

                            <?php if (!empty($order['order_items'])): ?>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Items:</small>
                                <div class="order-items-preview">
                                    <?php
                                    $displayed_items = array_slice($order['order_items'], 0, 2);
                                    $remaining_count = count($order['order_items']) - 2;
                                    ?>
                                    <?php foreach ($displayed_items as $item): ?>
                                    <span class="badge bg-light text-dark me-1 mb-1">
                                        <?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?>)
                                    </span>
                                    <?php endforeach; ?>
                                    <?php if ($remaining_count > 0): ?>
                                    <span class="badge bg-secondary">+<?= $remaining_count ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2 mt-auto">
                                <a href="<?= BASE_URL ?>/order/detail/<?= $order['id'] ?>"
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <?php if ($order['status'] === 'completed'): ?>
                                <button class="btn btn-success btn-sm" onclick="reorderItems(<?= $order['id'] ?>)">
                                    <i class="fas fa-redo me-1"></i>Reorder
                                </button>
                                <?php elseif ($order['status'] === 'pending'): ?>
                                <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Order history pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $current_page - 1 ?><?= $filter_params ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= $filter_params ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $current_page + 1 ?><?= $filter_params ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.badge-status {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
.badge-pending { background-color: #ffc107; color: #000; }
.badge-confirmed { background-color: #17a2b8; color: #fff; }
.badge-preparing { background-color: #fd7e14; color: #fff; }
.badge-ready { background-color: #20c997; color: #fff; }
.badge-completed { background-color: #28a745; color: #fff; }
.badge-cancelled { background-color: #dc3545; color: #fff; }

.order-items-preview .badge {
    font-size: 0.65rem;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.order-card.filtered-out {
    display: none !important;
}
</style>

<script>
// Filter functionality
document.getElementById('statusFilter').addEventListener('change', filterOrders);
document.getElementById('dateFilter').addEventListener('change', filterOrders);

function filterOrders() {
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const orderCards = document.querySelectorAll('.order-card');

    orderCards.forEach(card => {
        let showCard = true;

        // Status filter
        if (statusFilter && card.dataset.status !== statusFilter) {
            showCard = false;
        }

        // Date filter
        if (dateFilter && showCard) {
            const orderDate = new Date(card.dataset.date);
            const now = new Date();
            let cutoffDate = new Date();

            switch (dateFilter) {
                case 'today':
                    cutoffDate.setHours(0, 0, 0, 0);
                    if (orderDate < cutoffDate) showCard = false;
                    break;
                case 'week':
                    cutoffDate.setDate(now.getDate() - 7);
                    if (orderDate < cutoffDate) showCard = false;
                    break;
                case 'month':
                    cutoffDate.setMonth(now.getMonth() - 1);
                    if (orderDate < cutoffDate) showCard = false;
                    break;
                case 'quarter':
                    cutoffDate.setMonth(now.getMonth() - 3);
                    if (orderDate < cutoffDate) showCard = false;
                    break;
            }
        }

        if (showCard) {
            card.classList.remove('filtered-out');
        } else {
            card.classList.add('filtered-out');
        }
    });

    // Show/hide no results message
    const visibleCards = document.querySelectorAll('.order-card:not(.filtered-out)');
    const container = document.getElementById('ordersContainer');

    if (visibleCards.length === 0) {
        if (!document.getElementById('noResultsMessage')) {
            const noResultsDiv = document.createElement('div');
            noResultsDiv.id = 'noResultsMessage';
            noResultsDiv.className = 'col-12 text-center py-5';
            noResultsDiv.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted mb-3">No Orders Found</h5>
                <p class="text-muted">No orders match your current filters.</p>
            `;
            container.appendChild(noResultsDiv);
        }
    } else {
        const noResultsMessage = document.getElementById('noResultsMessage');
        if (noResultsMessage) {
            noResultsMessage.remove();
        }
    }
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch(`<?= BASE_URL ?>/order/cancel/${orderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Order cancelled successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message || 'Failed to cancel order', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while cancelling the order', 'error');
        });
    }
}

function reorderItems(orderId) {
    fetch(`<?= BASE_URL ?>/order/reorder/${orderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Items added to cart successfully', 'success');
            updateCartCount();
        } else {
            showAlert(data.message || 'Failed to reorder items', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while reordering', 'error');
    });
}
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
