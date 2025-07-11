<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #<?= $invoice['invoice_number'] ?></title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }

        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 10px;
        }

        .restaurant-info {
            font-size: 11px;
            color: #666;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .invoice-info .left, .invoice-info .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-info .right {
            text-align: right;
        }

        .customer-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .customer-info h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table th,
        .details-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .details-table th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .details-table .text-center {
            text-align: center;
        }

        .details-table .text-right {
            text-align: right;
        }

        .total-row {
            background: #e3f2fd;
            font-weight: bold;
        }

        .grand-total {
            background: #d32f2f;
            color: white;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signature-area {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-area .left, .signature-area .right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="restaurant-name">BUFFET RESTAURANT</div>
        <div class="restaurant-info">
            Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh<br>
            Điện thoại: (028) 1234 5678 | Email: info@buffet-restaurant.com
        </div>
    </div>

    <!-- Invoice Title -->
    <div class="invoice-title">HÓA ĐƠN THANH TOÁN</div>

    <!-- Invoice Info -->
    <div class="invoice-info">
        <div class="left">
            <strong>Số hóa đơn:</strong> <?= $invoice['invoice_number'] ?><br>
            <strong>Ngày tạo:</strong> <?= date('d/m/Y H:i:s', strtotime($invoice['created_at'])) ?><br>
            <strong>Bàn số:</strong> <?= $order['table_number'] ?? 'N/A' ?>
        </div>
        <div class="right">
            <strong>Trạng thái:</strong> <?= $invoice['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?><br>
            <strong>Phương thức:</strong>
            <?php
            $methods = [
                'cash' => 'Tiền mặt',
                'card' => 'Thẻ tín dụng',
                'bank_transfer' => 'Chuyển khoản',
                'vnpay' => 'VNPay'
            ];
            echo $methods[$invoice['payment_method']] ?? $invoice['payment_method'];
            ?>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="customer-info">
        <h4>Thông tin khách hàng:</h4>
        <strong>Tên:</strong> <?= $invoiceDetails['customer_name'] ?? 'Khách vãng lai' ?><br>
        <strong>Điện thoại:</strong> <?= $invoiceDetails['customer_phone'] ?? 'N/A' ?><br>
        <strong>Email:</strong> <?= $invoiceDetails['customer_email'] ?? 'N/A' ?>
    </div>

    <!-- Details Table -->
    <table class="details-table">
        <thead>
            <tr>
                <th width="50%">Mô tả</th>
                <th width="15%" class="text-center">Số lượng</th>
                <th width="20%" class="text-right">Đơn giá</th>
                <th width="15%" class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <!-- Buffet người lớn -->
            <?php if ($invoice['adult_count'] > 0): ?>
            <tr>
                <td>Buffet người lớn</td>
                <td class="text-center"><?= $invoice['adult_count'] ?></td>
                <td class="text-right"><?= number_format($invoice['adult_price']) ?>đ</td>
                <td class="text-right"><?= number_format($invoice['adult_count'] * $invoice['adult_price']) ?>đ</td>
            </tr>
            <?php endif; ?>

            <!-- Buffet trẻ em -->
            <?php if ($invoice['child_count'] > 0): ?>
            <tr>
                <td>Buffet trẻ em (11-17 tuổi)</td>
                <td class="text-center"><?= $invoice['child_count'] ?></td>
                <td class="text-right"><?= number_format($invoice['child_price']) ?>đ</td>
                <td class="text-right"><?= number_format($invoice['child_count'] * $invoice['child_price']) ?>đ</td>
            </tr>
            <?php endif; ?>

            <!-- Đồ ăn thêm -->
            <?php if ($invoice['food_total'] > 0): ?>
            <tr>
                <td>Đồ ăn thêm</td>
                <td class="text-center">-</td>
                <td class="text-right">-</td>
                <td class="text-right"><?= number_format($invoice['food_total']) ?>đ</td>
            </tr>
            <?php endif; ?>

            <!-- Phí phát sinh -->
            <?php if ($invoice['additional_total'] > 0): ?>
            <tr>
                <td>Phí phát sinh</td>
                <td class="text-center">-</td>
                <td class="text-right">-</td>
                <td class="text-right"><?= number_format($invoice['additional_total']) ?>đ</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="text-right"><strong>TỔNG CỘNG:</strong></td>
                <td class="text-right"><strong><?= number_format($invoice['total_amount']) ?>đ</strong></td>
            </tr>
        </tfoot>
    </table>

    <?php if (!empty($invoice['notes'])): ?>
    <div style="margin-bottom: 20px;">
        <strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
    </div>
    <?php endif; ?>

    <!-- Signature Area -->
    <div class="signature-area">
        <div class="left">
            <strong>Khách hàng</strong>
            <div class="signature-line"></div>
            <div style="margin-top: 5px;">(Ký và ghi rõ họ tên)</div>
        </div>
        <div class="right">
            <strong>Thu ngân</strong>
            <div class="signature-line"></div>
            <div style="margin-top: 5px;">(Ký và ghi rõ họ tên)</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Cảm ơn quý khách đã sử dụng dịch vụ!</strong></p>
        <p>Hóa đơn được in tự động từ hệ thống quản lý nhà hàng</p>
        <p>Thời gian in: <?= date('d/m/Y H:i:s') ?></p>
    </div>
</body>
</html>
