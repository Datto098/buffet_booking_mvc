<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Tạo Hóa Đơn</h1>
                <a href="<?= SITE_URL ?>/admin/dine-in-orders" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Thông tin Order #<?= $order['id'] ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Bàn số:</strong> <?= $order['table_number'] ?? 'N/A' ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Trạng thái:</strong>
                            <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Thời gian tạo:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Tổng tiền đồ ăn:</strong> <?= number_format($order['total_amount'] ?? 0) ?>đ
                        </div>
                    </div>
                </div>
            </div>

            <form id="invoiceForm" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Thông tin Buffet</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="adult_count" class="form-label">Số người lớn</label>
                                    <input type="number" class="form-control" id="adult_count" name="adult_count" min="0" value="0" required>
                                    <div class="form-text">
                                        <?php
                                        $adultPrice = 0;
                                        foreach ($buffetPricing as $price) {
                                            if ($price['type'] === 'adult') {
                                                $adultPrice = $price['price'];
                                                break;
                                            }
                                        }
                                        ?>
                                        Giá: <?= number_format($adultPrice) ?>đ/người
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="child_count" class="form-label">Số trẻ em</label>
                                    <input type="number" class="form-control" id="child_count" name="child_count" min="0" value="0" required>
                                    <div class="form-text">
                                        <?php
                                        $childPrice = 0;
                                        foreach ($buffetPricing as $price) {
                                            if ($price['type'] === 'child' && $price['age_from'] >= 11) {
                                                $childPrice = $price['price'];
                                                break;
                                            }
                                        }
                                        ?>
                                        Giá: <?= number_format($childPrice) ?>đ/trẻ (11-17 tuổi)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Bảng giá buffet:</strong>
                            <ul class="mb-0">
                                <?php foreach ($buffetPricing as $price): ?>
                                <li>
                                    <?= $price['type'] === 'adult' ? 'Người lớn' : 'Trẻ em' ?>
                                    <?php if ($price['type'] === 'child'): ?>
                                        (<?= $price['age_from'] ?>-<?= $price['age_to'] ?> tuổi)
                                    <?php endif; ?>
                                    : <?= number_format($price['price']) ?>đ
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Phí phát sinh</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($additionalChargeTypes as $index => $charge): ?>
                            <div class="col-md-6 mb-3">
                                <label for="charge_<?= $charge['id'] ?>" class="form-label">
                                    <?= $charge['name'] ?>
                                    <small class="text-muted">(<?= number_format($charge['price']) ?>đ/<?= $charge['unit'] ?>)</small>
                                </label>
                                <input type="number"
                                       class="form-control additional-charge"
                                       id="charge_<?= $charge['id'] ?>"
                                       name="additional_charges[<?= $charge['id'] ?>]"
                                       min="0"
                                       value="0"
                                       data-price="<?= $charge['price'] ?>"
                                       data-name="<?= $charge['name'] ?>">
                                <div class="form-text"><?= $charge['description'] ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Thông tin thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Hình thức thanh toán</label>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="cash">Tiền mặt</option>
                                        <option value="card">Thẻ tín dụng</option>
                                        <option value="bank_transfer">Chuyển khoản</option>
                                        <option value="vnpay">VNPay</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Tóm tắt hóa đơn</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Buffet người lớn:</td>
                                    <td class="text-end" id="adult-total">0đ</td>
                                </tr>
                                <tr>
                                    <td>Buffet trẻ em:</td>
                                    <td class="text-end" id="child-total">0đ</td>
                                </tr>
                                <tr>
                                    <td>Đồ ăn thêm:</td>
                                    <td class="text-end"><?= number_format($order['total_amount'] ?? 0) ?>đ</td>
                                </tr>
                                <tr>
                                    <td>Phí phát sinh:</td>
                                    <td class="text-end" id="additional-total">0đ</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Tổng cộng:</strong></td>
                                    <td class="text-end"><strong id="grand-total"><?= number_format($order['total_amount'] ?? 0) ?>đ</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" onclick="history.back()">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fa fa-save"></i> Tạo hóa đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const SITE_URL = '<?= SITE_URL ?>';
    const adultPrice = <?= $adultPrice ?>;
    const childPrice = <?= $childPrice ?>;
    const foodTotal = <?= $order['total_amount'] ?? 0 ?>;

    function updateTotals() {
        const adultCount = parseInt($('#adult_count').val()) || 0;
        const childCount = parseInt($('#child_count').val()) || 0;

        const adultTotal = adultCount * adultPrice;
        const childTotal = childCount * childPrice;

        $('#adult-total').text(adultTotal.toLocaleString() + 'đ');
        $('#child-total').text(childTotal.toLocaleString() + 'đ');

        // Calculate additional charges
        let additionalTotal = 0;
        $('.additional-charge').each(function() {
            const quantity = parseInt($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            additionalTotal += quantity * price;
        });

        $('#additional-total').text(additionalTotal.toLocaleString() + 'đ');

        const grandTotal = adultTotal + childTotal + foodTotal + additionalTotal;
        $('#grand-total').text(grandTotal.toLocaleString() + 'đ');
    }

    $('#adult_count, #child_count, .additional-charge').on('input', updateTotals);    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault();

        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang tạo...');

        const postUrl = SITE_URL + '/admin/invoice/create';
        const formData = $(this).serialize();
        console.log('POST URL:', postUrl);
        console.log('Form Data:', formData);

        $.ajax({
            url: postUrl,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Tạo hóa đơn thành công!');
                    // Redirect đến trang xem hóa đơn
                    window.location.href = SITE_URL + '/admin/invoice/view/' + response.invoice_id;
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi tạo hóa đơn');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Tạo hóa đơn');
            }
        });
    });
});
</script>
