<?php
/**
 * Order Details View
 */
$title = "Order Details - " . SITE_NAME;
$current_page = 'orders';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Order Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h4 mb-1">Order #<?= $order['id'] ?></h2>
                            <p class="text-muted mb-0">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                        </div>
                        <span class="badge badge-status badge-<?= strtolower($order['status']) ?> fs-6">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Customer Information</h6>
                            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                            <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Order Summary</h6>
                            <p class="mb-1"><strong>Order Type:</strong> <?= ucfirst($order['order_type']) ?></p>
                            <p class="mb-1"><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                            <p class="mb-1"><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['image']): ?>
                                            <img src="<?= ASSETS_URL ?>/images/food/<?= $item['image'] ?>"
                                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                                 class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                                <?php if ($item['description']): ?>
                                                <small class="text-muted"><?= htmlspecialchars(substr($item['description'], 0, 80)) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end">$<?= number_format($item['unit_price'] ?? 0, 2) ?></td>
                                    <td class="text-end fw-bold">$<?= number_format(($item['unit_price'] ?? 0) * $item['quantity'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th class="text-end">$<?= number_format($order['subtotal'], 2) ?></th>
                                </tr>
                                <?php if ($order['tax_amount'] > 0): ?>
                                <tr>
                                    <th colspan="3" class="text-end">Tax:</th>
                                    <th class="text-end">$<?= number_format($order['tax_amount'], 2) ?></th>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">$<?= number_format($order['total_amount'], 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Status Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php
                        $timeline_items = [
                            ['status' => 'pending', 'label' => 'Order Placed', 'icon' => 'clock'],
                            ['status' => 'confirmed', 'label' => 'Order Confirmed', 'icon' => 'check-circle'],
                            ['status' => 'preparing', 'label' => 'Preparing Food', 'icon' => 'utensils'],
                            ['status' => 'ready', 'label' => 'Ready for Pickup/Delivery', 'icon' => 'box'],
                            ['status' => 'completed', 'label' => 'Order Completed', 'icon' => 'check-double']
                        ];

                        $current_status_index = array_search($order['status'], array_column($timeline_items, 'status'));
                        ?>

                        <?php foreach ($timeline_items as $index => $item): ?>
                        <div class="timeline-item <?= $index <= $current_status_index ? 'active' : '' ?>">
                            <div class="timeline-marker">
                                <i class="fas fa-<?= $item['icon'] ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1"><?= $item['label'] ?></h6>
                                <?php if ($index <= $current_status_index): ?>
                                <small class="text-muted">
                                    <?= $index == $current_status_index ? 'Current Status' : 'Completed' ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Special Instructions -->
            <?php if (!empty($order['special_instructions'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Special Instructions</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($order['special_instructions'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="text-center">
                <a href="<?= BASE_URL ?>/order/history" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
                <?php if ($order['status'] === 'pending'): ?>
                <button class="btn btn-danger me-2" onclick="cancelOrder(<?= $order['id'] ?>)">
                    <i class="fas fa-times me-2"></i>Cancel Order
                </button>
                <?php endif; ?>
                <button class="btn btn-success" onclick="reorderItems(<?= $order['id'] ?>)">
                    <i class="fas fa-redo me-2"></i>Reorder
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.badge-status {
    padding: 0.5rem 1rem;
}
.badge-pending { background-color: #ffc107; color: #000; }
.badge-confirmed { background-color: #17a2b8; color: #fff; }
.badge-preparing { background-color: #fd7e14; color: #fff; }
.badge-ready { background-color: #20c997; color: #fff; }
.badge-completed { background-color: #28a745; color: #fff; }
.badge-cancelled { background-color: #dc3545; color: #fff; }

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-item.active .timeline-marker {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.timeline-item.active::before {
    background: var(--primary-color);
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.timeline-content {
    margin-left: 1rem;
}
</style>

<script>
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
            setTimeout(() => window.location.href = '<?= BASE_URL ?>/cart', 1500);
        } else {
            showAlert(data.message || 'Failed to reorder items', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while reordering', 'error');    });
}
</script>
