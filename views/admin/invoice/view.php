<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Hóa Đơn #<?= $invoiceDetails['invoice_number'] ?></h1>
                <div>
                    <a href="<?= SITE_URL ?>/admin/invoice/print/<?= $invoiceDetails['id'] ?>" class="btn btn-primary" target="_blank">
                        <i class="fa fa-print"></i> In hóa đơn
                    </a>
                    <a href="<?= SITE_URL ?>/admin/dine-in-orders" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <!-- Thông tin khách hàng -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tên khách hàng:</strong> <?= $invoiceDetails['customer_name'] ?? 'Khách vãng lai' ?></p>
                            <p><strong>Số điện thoại:</strong> <?= $invoiceDetails['customer_phone'] ?? 'N/A' ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?= $invoiceDetails['customer_email'] ?? 'N/A' ?></p>
                            <p><strong>Bàn số:</strong> <?= $invoiceDetails['table_number'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin hóa đơn -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thông tin hóa đơn</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Số hóa đơn:</strong> <?= $invoiceDetails['invoice_number'] ?></p>
                            <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($invoiceDetails['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Trạng thái thanh toán:</strong>
                                <span class="badge bg-<?= $invoiceDetails['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                    <?= $invoiceDetails['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                                </span>
                            </p>
                            <p><strong>Phương thức:</strong>
                                <?php
                                $methods = [
                                    'cash' => 'Tiền mặt',
                                    'card' => 'Thẻ tín dụng',
                                    'bank_transfer' => 'Chuyển khoản',
                                    'vnpay' => 'VNPay'
                                ];
                                echo $methods[$invoiceDetails['payment_method']] ?? $invoiceDetails['payment_method'];
                                ?>
                            </p>
                        </div>
                    </div>
                    <?php if (!empty($invoiceDetails['notes'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($invoiceDetails['notes'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chi tiết hóa đơn -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Chi tiết hóa đơn</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Mô tả</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Buffet người lớn -->
                            <?php if ($invoiceDetails['adult_count'] > 0): ?>
                            <tr>
                                <td>Buffet người lớn</td>
                                <td class="text-center"><?= $invoiceDetails['adult_count'] ?></td>
                                <td class="text-end"><?= number_format($invoiceDetails['adult_price']) ?>đ</td>
                                <td class="text-end"><?= number_format($invoiceDetails['adult_count'] * $invoiceDetails['adult_price']) ?>đ</td>
                            </tr>
                            <?php endif; ?>

                            <!-- Buffet trẻ em -->
                            <?php if ($invoiceDetails['child_count'] > 0): ?>
                            <tr>
                                <td>Buffet trẻ em (11-17 tuổi)</td>
                                <td class="text-center"><?= $invoiceDetails['child_count'] ?></td>
                                <td class="text-end"><?= number_format($invoiceDetails['child_price']) ?>đ</td>
                                <td class="text-end"><?= number_format($invoiceDetails['child_count'] * $invoiceDetails['child_price']) ?>đ</td>
                            </tr>
                            <?php endif; ?>

                            <!-- Đồ ăn thêm -->
                            <?php if ($invoiceDetails['food_total'] > 0): ?>
                            <tr>
                                <td>Đồ ăn thêm</td>
                                <td class="text-center">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end"><?= number_format($invoiceDetails['food_total']) ?>đ</td>
                            </tr>
                            <?php endif; ?>

                            <!-- Phí phát sinh -->
                            <?php if ($invoiceDetails['additional_total'] > 0): ?>
                            <tr>
                                <td>Phí phát sinh</td>
                                <td class="text-center">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end"><?= number_format($invoiceDetails['additional_total']) ?>đ</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-active">
                            <tr>
                                <th colspan="3" class="text-end">Tổng cộng:</th>
                                <th class="text-end"><?= number_format($invoiceDetails['total_amount']) ?>đ</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Hành động -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="<?= SITE_URL ?>/admin/dine-in-orders" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Quay lại danh sách
                        </a>

                        <div>
                            <?php if ($invoiceDetails['payment_status'] !== 'paid'): ?>
                            <button class="btn btn-success" onclick="updateInvoicePaymentStatus(<?= $invoiceDetails['id'] ?>, 'paid')">
                                <i class="fa fa-check"></i> Đánh dấu đã thanh toán
                            </button>
                            <?php endif; ?>

                            <a href="<?= SITE_URL ?>/admin/invoice/print/<?= $invoiceDetails['id'] ?>" class="btn btn-primary" target="_blank">
                                <i class="fa fa-print"></i> In hóa đơn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateInvoicePaymentStatus(invoiceId, status) {
    if (confirm('Xác nhận đánh dấu hóa đơn này đã thanh toán?')) {
        fetch('<?= SITE_URL ?>/admin/invoice/update-payment-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                invoice_id: invoiceId,
                payment_status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cập nhật trạng thái thanh toán thành công!');
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi cập nhật trạng thái');
        });
    }
}
</script>
