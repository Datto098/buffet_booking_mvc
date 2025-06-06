<?php
/**
 * Order Details Modal Content
 */
?>

<div class="order-details">
    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="fw-bold mb-2">Order Information</h6>
            <p class="mb-1"><strong>Order ID:</strong> #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></p>
            <p class="mb-1"><strong>Status:</strong>
                <span class="badge bg-<?=
                    $order['status'] === 'pending' ? 'warning' :
                    ($order['status'] === 'confirmed' ? 'info' :
                    ($order['status'] === 'preparing' ? 'primary' :
                    ($order['status'] === 'ready' ? 'success' :
                    ($order['status'] === 'delivered' ? 'success' : 'danger'))))
                ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </p>
            <p class="mb-1"><strong>Order Date:</strong> <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
            <p class="mb-1"><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold mb-2">Customer Information</h6>
            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Guest Customer') ?></p>
            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
            <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
            <?php if (!empty($order['delivery_address'])): ?>
            <p class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Items -->
    <div class="mb-4">
        <h6 class="fw-bold mb-3">Order Items</h6>
        <?php if (!empty($order['items'])): ?>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subtotal = 0;                    foreach ($order['items'] as $item):
                        $itemTotal = ($item['unit_price'] ?? 0) * $item['quantity'];
                        $subtotal += $itemTotal;
                    ?>
                    <tr>
                        <td>                            <div class="d-flex align-items-center">
                                <?php if (!empty($item['image'])): ?>
                                <img src="<?= ASSETS_URL ?>/images/food/<?= htmlspecialchars($item['image']) ?>"
                                     alt="<?= htmlspecialchars($item['food_name']) ?>"
                                     class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($item['food_name']) ?></div>
                                    <?php if (!empty($item['description'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-center"><?= $item['quantity'] ?></td>                        <td class="text-end">$<?= number_format($item['unit_price'] ?? 0, 2) ?></td>
                        <td class="text-end fw-bold">$<?= number_format($itemTotal, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Subtotal:</th>
                        <th class="text-end">$<?= number_format($subtotal, 2) ?></th>
                    </tr>
                    <?php if (!empty($order['delivery_fee']) && $order['delivery_fee'] > 0): ?>
                    <tr>
                        <th colspan="3" class="text-end">Delivery Fee:</th>
                        <th class="text-end">$<?= number_format($order['delivery_fee'], 2) ?></th>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($order['service_fee']) && $order['service_fee'] > 0): ?>
                    <tr>
                        <th colspan="3" class="text-end">Service Fee:</th>
                        <th class="text-end">$<?= number_format($order['service_fee'], 2) ?></th>
                    </tr>
                    <?php endif; ?>
                    <tr class="table-primary">
                        <th colspan="3" class="text-end">Total Amount:</th>
                        <th class="text-end">$<?= number_format($order['total_amount'], 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">No items found for this order.</div>
        <?php endif; ?>
    </div>

    <!-- Order Notes -->
    <?php if (!empty($order['order_notes'])): ?>
    <div class="mb-3">
        <h6 class="fw-bold mb-2">Order Notes</h6>
        <div class="alert alert-light">
            <?= nl2br(htmlspecialchars($order['order_notes'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timestamps -->
    <div class="row">
        <div class="col-md-6">
            <small class="text-muted">
                <strong>Created:</strong> <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
            </small>
        </div>
        <?php if (!empty($order['updated_at']) && $order['updated_at'] !== $order['created_at']): ?>
        <div class="col-md-6">
            <small class="text-muted">
                <strong>Last Updated:</strong> <?= date('M j, Y \a\t g:i A', strtotime($order['updated_at'])) ?>
            </small>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.order-details .table th,
.order-details .table td {
    vertical-align: middle;
}

.order-details .badge {
    font-size: 0.75rem;
}

.order-details img {
    border: 1px solid #dee2e6;
}
</style>
