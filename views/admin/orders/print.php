<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?> - Print</title>
    <style>
        @media print {
            @page { margin: 0.5in; }
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .restaurant-info {
            color: #666;
            margin-bottom: 15px;
        }

        .order-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-section {
            flex: 1;
            margin-right: 20px;
        }

        .info-section:last-child {
            margin-right: 0;
        }

        .info-section h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .label {
            font-weight: bold;
            margin-right: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-preparing { background: #cce7ff; color: #004085; }
        .status-ready { background: #d4edda; color: #155724; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .items-table .text-center { text-align: center; }
        .items-table .text-right { text-align: right; }

        .total-section {
            margin-left: auto;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .total-row.final {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
            padding-top: 10px;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        @media print {
            .print-button { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <div class="restaurant-name">Buffet Restaurant</div>
        <div class="restaurant-info">
            123 Restaurant Street, Food City, FC 12345<br>
            Phone: (555) 123-4567 | Email: orders@buffetrestaurant.com
        </div>
        <div class="order-title">ORDER RECEIPT</div>
    </div>

    <div class="order-info">
        <div class="info-section">
            <h3>Order Details</h3>
            <div class="info-item">
                <span class="label">Order #:</span>
                <?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
            </div>
            <div class="info-item">
                <span class="label">Date:</span>
                <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
            </div>
            <div class="info-item">
                <span class="label">Status:</span>
                <span class="status-badge status-<?= $order['status'] ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </div>
            <div class="info-item">
                <span class="label">Payment:</span>
                <?= ucfirst($order['payment_method']) ?>
            </div>
        </div>

        <div class="info-section">
            <h3>Customer Information</h3>
            <div class="info-item">
                <span class="label">Name:</span>
                <?= htmlspecialchars($order['customer_name'] ?? 'Guest Customer') ?>
            </div>
            <div class="info-item">
                <span class="label">Email:</span>
                <?= htmlspecialchars($order['customer_email']) ?>
            </div>
            <div class="info-item">
                <span class="label">Phone:</span>
                <?= htmlspecialchars($order['customer_phone']) ?>
            </div>
            <?php if (!empty($order['delivery_address'])): ?>
            <div class="info-item">
                <span class="label">Address:</span>
                <?= htmlspecialchars($order['delivery_address']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($order['items'])): ?>
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $subtotal = 0;
            foreach ($order['items'] as $item):
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($item['food_name']) ?></strong>
                    <?php if (!empty($item['description'])): ?>
                    <br><small style="color: #666;"><?= htmlspecialchars(substr($item['description'], 0, 60)) ?>...</small>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?= $item['quantity'] ?></td>
                <td class="text-right">$<?= number_format($item['price'], 2) ?></td>
                <td class="text-right">$<?= number_format($itemTotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>$<?= number_format($subtotal, 2) ?></span>
        </div>
        <?php if (!empty($order['delivery_fee']) && $order['delivery_fee'] > 0): ?>
        <div class="total-row">
            <span>Delivery Fee:</span>
            <span>$<?= number_format($order['delivery_fee'], 2) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($order['service_fee']) && $order['service_fee'] > 0): ?>
        <div class="total-row">
            <span>Service Fee:</span>
            <span>$<?= number_format($order['service_fee'], 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="total-row final">
            <span>TOTAL:</span>
            <span>$<?= number_format($order['total_amount'], 2) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($order['order_notes'])): ?>
    <div class="notes">
        <strong>Order Notes:</strong><br>
        <?= nl2br(htmlspecialchars($order['order_notes'])) ?>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Thank you for your order!</p>
        <p>For questions about this order, please contact us at (555) 123-4567</p>
        <p>Printed on <?= date('M j, Y \a\t g:i A') ?></p>
    </div>    <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>
    <script>
        autoPrintOrder();
    </script>
</body>
</html>
