<?php
/**
 * SuperAdmin Order Details Modal Content
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
                    ($order['status'] === 'processing' ? 'info' :
                    ($order['status'] === 'preparing' ? 'primary' :
                    ($order['status'] === 'ready' ? 'success' :
                    ($order['status'] === 'delivered' ? 'success' : 'danger'))))
                ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </p>
            <p class="mb-1"><strong>Order Date:</strong> <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
            <p class="mb-1"><strong>Payment Method:</strong> <?= ucfirst($order['payment_method'] ?? 'N/A') ?></p>
            <p class="mb-1"><strong>Payment Status:</strong>
                <span class="badge bg-<?=
                    $order['payment_status'] === 'paid' ? 'success' :
                    ($order['payment_status'] === 'pending' ? 'warning' :
                    ($order['payment_status'] === 'failed' ? 'danger' : 'secondary'))
                ?>">
                    <?= ucfirst($order['payment_status'] ?? 'pending') ?>
                </span>
            </p>
            <p class="mb-1"><strong>Order Type:</strong> <?= ucfirst($order['order_type'] ?? 'delivery') ?></p>
            <?php if (!empty($order['coupon_code'])): ?>
            <p class="mb-1"><strong>Coupon:</strong>
                <span class="badge bg-success"><?= htmlspecialchars($order['coupon_code']) ?></span>
            </p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold mb-2">Customer Information</h6>
            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Guest Customer') ?></p>
            <p class="mb-1"><strong>Email:</strong>
                <?php if (!empty($order['customer_email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($order['customer_email']) ?>"><?= htmlspecialchars($order['customer_email']) ?></a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </p>
            <p class="mb-1"><strong>Phone:</strong>
                <?php if (!empty($order['customer_phone'])): ?>
                    <a href="tel:<?= htmlspecialchars($order['customer_phone']) ?>"><?= htmlspecialchars($order['customer_phone']) ?></a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </p>
            <p class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($order['delivery_address'] ?? 'N/A') ?></p>
        </div>
    </div>

    <!-- Order Items -->
    <div class="mb-4">
        <h6 class="fw-bold mb-3">Order Items</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($order['items']) && is_array($order['items'])): ?>
                        <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= SITE_URL ?>/assets/images/foods/<?= htmlspecialchars($item['image']) ?>"
                                             alt="<?= htmlspecialchars($item['food_name']) ?>"
                                             class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div>                                        <div class="fw-medium"><?= htmlspecialchars($item['food_name']) ?></div>
                                        <?php if (!empty($item['description'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars(substr($item['description'], 0, 50)) ?><?= strlen($item['description']) > 50 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>                            <td class="text-center">
                                <span class="badge bg-light text-dark"><?= $item['quantity'] ?></span>
                            </td>
                            <td class="text-end">$<?= number_format($item['unit_price'] ?? $item['price'] ?? 0, 2) ?></td>
                            <td class="text-end fw-medium">$<?= number_format(($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="row">
        <div class="col-md-6 offset-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Order Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($order['subtotal'] ?? 0, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <span>$<?= number_format($order['tax_amount'] ?? 0, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee:</span>
                        <span>$<?= number_format($order['delivery_fee'] ?? 0, 2) ?></span>
                    </div>
                    <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount:</span>
                        <span>-$<?= number_format($order['discount_amount'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span>$<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <?php if (!empty($order['notes'])): ?>
    <div class="mt-4">
        <h6 class="fw-bold mb-2">Special Instructions</h6>
        <div class="alert alert-info mb-0">
            <i class="fas fa-sticky-note me-2"></i>
            <?= nl2br(htmlspecialchars($order['notes'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Order Timeline -->
    <?php if (!empty($order['status_history'])): ?>
    <div class="mt-4">
        <h6 class="fw-bold mb-3">Order Timeline</h6>
        <div class="timeline">
            <?php foreach ($order['status_history'] as $history): ?>
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <h6 class="timeline-title"><?= ucfirst($history['status']) ?></h6>
                    <p class="timeline-description text-muted mb-0">
                        <?= date('M j, Y \a\t g:i A', strtotime($history['created_at'])) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 12px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -18px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    color: #495057;
}
</style>
