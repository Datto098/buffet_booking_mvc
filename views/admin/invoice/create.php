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
                        <h5>Loại khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Khách hàng</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="customer_type" id="customer_booking" value="booking" checked>
                                        <label class="form-check-label" for="customer_booking">
                                            <i class="fas fa-calendar-check text-success"></i>
                                            <strong>Đã đặt bàn trước</strong> (Đã trả trước 15%)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="customer_type" id="customer_walkin" value="walkin">
                                        <label class="form-check-label" for="customer_walkin">
                                            <i class="fas fa-walking text-primary"></i>
                                            <strong>Khách walk-in</strong> (Tính phí đầy đủ)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Selection (only show when booking is selected) -->
                        <div id="booking-selection" class="mt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="booking_id" class="form-label">Chọn booking</label>
                                    <select class="form-control" id="booking_id" name="booking_id">
                                        <option value="">-- Chọn booking --</option>
                                        <?php
                                        // Get bookings for today that are confirmed and haven't been invoiced
                                        try {
                                            $db = Database::getInstance()->getConnection();
                                            $stmt = $db->prepare("
                                                SELECT b.*,
                                                       CASE
                                                           WHEN b.booking_date = CURDATE() THEN 'Hôm nay'
                                                           ELSE DATE_FORMAT(b.booking_date, '%d/%m/%Y')
                                                       END as formatted_date
                                                FROM bookings b
                                                LEFT JOIN invoices i ON b.id = i.booking_id
                                                WHERE b.status IN ('confirmed', 'seated')
                                                  AND b.booking_date >= CURDATE()
                                                  AND i.id IS NULL
                                                ORDER BY b.booking_date, b.booking_time
                                            ");
                                            $stmt->execute();
                                            $availableBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($availableBookings as $booking):
                                        ?>
                                            <option value="<?= $booking['id'] ?>"
                                                    data-adult="<?= $booking['adult_count'] ?>"
                                                    data-child-11-17="<?= $booking['children_11_17_count'] ?>"
                                                    data-child-6-10="<?= $booking['children_6_10_count'] ?>"
                                                    data-child-0-5="<?= $booking['children_0_5_count'] ?>"
                                                    data-total="<?= $booking['total_amount'] ?>"
                                                    data-prepaid="<?= $booking['prepaid_amount'] ?>"
                                                    data-remaining="<?= $booking['remaining_amount'] ?>">
                                                #<?= $booking['id'] ?> - <?= htmlspecialchars($booking['customer_name']) ?>
                                                (<?= $booking['formatted_date'] ?> <?= substr($booking['booking_time'], 0, 5) ?>) -
                                                <?= $booking['adult_count'] ?> người lớn, <?= $booking['children_11_17_count'] + $booking['children_6_10_count'] + $booking['children_0_5_count'] ?> trẻ em
                                            </option>
                                        <?php
                                            endforeach;
                                        } catch (Exception $e) {
                                            error_log("Error loading bookings: " . $e->getMessage());
                                        }
                                        ?>
                                    </select>
                                    <div class="form-text">Chỉ hiển thị booking đã xác nhận và chưa có hóa đơn</div>
                                </div>
                            </div>

                            <!-- Booking Info Display -->
                            <div id="booking-info" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Thông tin booking:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div><strong>Đã trả trước:</strong> <span id="booking-prepaid">0đ</span></div>
                                            <div><strong>Còn lại phải trả:</strong> <span id="booking-remaining">0đ</span></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div><strong>Người lớn:</strong> <span id="booking-adults">0</span></div>
                                            <div><strong>Trẻ em:</strong> <span id="booking-children">0</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <div class="form-text" id="adult-price-text">
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
                                    <div class="form-text" id="child-price-text">
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
                                <tr id="buffet-discount-row" style="display: none;">
                                    <td class="text-success">Đã trả trước (15%):</td>
                                    <td class="text-end text-success" id="buffet-discount">-0đ</td>
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
    const adultPriceOriginal = <?= $adultPrice ?>;
    const childPriceOriginal = <?= $childPrice ?>;
    const foodTotal = <?= $order['total_amount'] ?? 0 ?>;

    let currentBookingData = null;
    let isBookingCustomer = true;
    let adultPrice = adultPriceOriginal;
    let childPrice = childPriceOriginal;

    // Handle customer type change
    $('input[name="customer_type"]').on('change', function() {
        isBookingCustomer = $(this).val() === 'booking';

        if (isBookingCustomer) {
            $('#booking-selection').show();
            $('#adult_count, #child_count').prop('readonly', true);
            updatePriceDisplay(true);
        } else {
            $('#booking-selection').hide();
            $('#booking-info').hide();
            $('#adult_count, #child_count').prop('readonly', false).val(0);
            currentBookingData = null;
            updatePriceDisplay(false);
        }
        updateTotals();
    });

    // Handle booking selection
    $('#booking_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');

        if (selectedOption.val()) {
            // Load booking data
            currentBookingData = {
                adult_count: parseInt(selectedOption.data('adult')) || 0,
                child_11_17_count: parseInt(selectedOption.data('child-11-17')) || 0,
                child_6_10_count: parseInt(selectedOption.data('child-6-10')) || 0,
                child_0_5_count: parseInt(selectedOption.data('child-0-5')) || 0,
                total_amount: parseFloat(selectedOption.data('total')) || 0,
                prepaid_amount: parseFloat(selectedOption.data('prepaid')) || 0,
                remaining_amount: parseFloat(selectedOption.data('remaining')) || 0
            };

            // Auto-fill form fields
            $('#adult_count').val(currentBookingData.adult_count);
            $('#child_count').val(currentBookingData.child_11_17_count + currentBookingData.child_6_10_count);

            // Show booking info
            $('#booking-info').show();
            $('#booking-prepaid').text(currentBookingData.prepaid_amount.toLocaleString() + 'đ');
            $('#booking-remaining').text(currentBookingData.remaining_amount.toLocaleString() + 'đ');
            $('#booking-adults').text(currentBookingData.adult_count);
            $('#booking-children').text(currentBookingData.child_11_17_count + currentBookingData.child_6_10_count + currentBookingData.child_0_5_count);

            updateTotals();
        } else {
            $('#booking-info').hide();
            $('#adult_count, #child_count').val(0);
            currentBookingData = null;
            updateTotals();
        }
    });

    function updatePriceDisplay(isBooking) {
        const discountText = isBooking ? ' (Đã trừ 15% trả trước)' : '';
        $('#adult-price-text').html(`Giá: ${adultPriceOriginal.toLocaleString()}đ/người${discountText}`);
        $('#child-price-text').html(`Giá: ${childPriceOriginal.toLocaleString()}đ/trẻ (11-17 tuổi)${discountText}`);
    }

    function updateTotals() {
        const adultCount = parseInt($('#adult_count').val()) || 0;
        const childCount = parseInt($('#child_count').val()) || 0;

        let adultTotal = adultCount * adultPriceOriginal;
        let childTotal = childCount * childPriceOriginal;
        let buffetDiscount = 0;

        // If booking customer, calculate discount (15% already paid)
        if (isBookingCustomer && currentBookingData) {
            buffetDiscount = Math.round((adultTotal + childTotal) * 0.15);
            $('#buffet-discount-row').show();
            $('#buffet-discount').text('-' + buffetDiscount.toLocaleString() + 'đ');
        } else {
            $('#buffet-discount-row').hide();
        }

        const finalBuffetTotal = adultTotal + childTotal - buffetDiscount;

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

        const grandTotal = finalBuffetTotal + foodTotal + additionalTotal;
        $('#grand-total').text(grandTotal.toLocaleString() + 'đ');
    }

    $('#adult_count, #child_count, .additional-charge').on('input', updateTotals);

    // Initialize
    updatePriceDisplay(true);
    updateTotals();    $('#invoiceForm').on('submit', function(e) {
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
