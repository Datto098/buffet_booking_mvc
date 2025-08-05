<?php

/**
 * Booking Index View
 */
?>

<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-5 mb-3">Đặt Bàn</h1>
                <p class="lead text-muted">Đặt bàn trước để có trải nghiệm tuyệt vời nhất</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Booking Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Thông tin đặt bàn</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= SITE_URL ?>/booking/payment" id="bookingForm">
                        <?= csrf_token_field() ?>

                        <div class="row">
                            <!-- Customer Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-primary">Thông tin khách hàng</h6>

                                <?php
                                $customer_name = '';
                                if (isset($_SESSION['form_data']['customer_name'])) {
                                    $customer_name = $_SESSION['form_data']['customer_name'];
                                } elseif (isset($_SESSION['user'])) {
                                    $user = $_SESSION['user'];
                                    if (!empty($user['last_name'])) {
                                        $customer_name = $user['last_name'];
                                    } elseif (!empty($user['first_name'])) {
                                        $customer_name = $user['first_name'];
                                    } elseif (!empty($user['full_name'])) {
                                        $customer_name = $user['full_name'];
                                    }
                                }
                                ?>
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="customer_name"
                                        name="customer_name"
                                        value="<?= htmlspecialchars($customer_name) ?>"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                        class="form-control"
                                        id="customer_email"
                                        name="customer_email"
                                        value="<?= htmlspecialchars($_SESSION['form_data']['customer_email'] ?? ($_SESSION['user_email'] ?? '')) ?>"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="customer_phone" class="form-label">
                                        Số điện thoại <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel"
                                        class="form-control"
                                        id="customer_phone"
                                        name="customer_phone"
                                        value="<?= htmlspecialchars($_SESSION['form_data']['customer_phone'] ?? '') ?>"
                                        required>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-primary">Chi tiết đặt bàn</h6>

                                <div class="mb-3">
                                    <label for="booking_date" class="form-label">
                                        Ngày đặt bàn <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                        class="form-control"
                                        id="booking_date"
                                        name="booking_date"
                                        value="<?= $_SESSION['form_data']['booking_date'] ?? date('Y-m-d') ?>"
                                        min="<?= date('Y-m-d') ?>"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="booking_time" class="form-label">
                                        Giờ đặt bàn <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="booking_time" name="booking_time" required>
                                        <option value="">Chọn giờ</option>
                                        <!-- Time slots will be populated via JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Số lượng khách <span class="text-danger">*</span>
                                    </label>

                                    <!-- Người lớn -->
                                    <div class="row mb-2">
                                        <div class="col-md-8">
                                            <label for="adult_count" class="form-label small">
                                                <i class="fas fa-user"></i> Người lớn (từ 18 tuổi)
                                                <span class="badge bg-primary">299,000đ</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select form-select-sm" id="adult_count" name="adult_count" required>
                                                <option value="">Chọn</option>
                                                <?php for ($i = 0; $i <= 20; $i++): ?>
                                                    <option value="<?= $i ?>"
                                                        <?= ($_SESSION['form_data']['adult_count'] ?? '') == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> người
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Trẻ em 11-17 tuổi -->
                                    <div class="row mb-2">
                                        <div class="col-md-8">
                                            <label for="children_11_17_count" class="form-label small">
                                                <i class="fas fa-child"></i> Trẻ em (11-17 tuổi)
                                                <span class="badge bg-info">199,000đ</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select form-select-sm" id="children_11_17_count" name="children_11_17_count">
                                                <option value="">Chọn</option>
                                                <?php for ($i = 0; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>"
                                                        <?= ($_SESSION['form_data']['children_11_17_count'] ?? '') == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> trẻ
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Trẻ em 6-10 tuổi -->
                                    <div class="row mb-2">
                                        <div class="col-md-8">
                                            <label for="children_6_10_count" class="form-label small">
                                                <i class="fas fa-baby"></i> Trẻ em (6-10 tuổi)
                                                <span class="badge bg-success">99,000đ</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select form-select-sm" id="children_6_10_count" name="children_6_10_count">
                                                <option value="">Chọn</option>
                                                <?php for ($i = 0; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>"
                                                        <?= ($_SESSION['form_data']['children_6_10_count'] ?? '') == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> trẻ
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Trẻ em 0-5 tuổi -->
                                    <div class="row mb-2">
                                        <div class="col-md-8">
                                            <label for="children_0_5_count" class="form-label small">
                                                <i class="fas fa-baby-carriage"></i> Trẻ em (0-5 tuổi)
                                                <span class="badge bg-secondary">Miễn phí</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select form-select-sm" id="children_0_5_count" name="children_0_5_count">
                                                <option value="">Chọn</option>
                                                <?php for ($i = 0; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>"
                                                        <?= ($_SESSION['form_data']['children_0_5_count'] ?? '') == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> trẻ
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Tổng số khách -->
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted">Tổng số khách: </small>
                                        <strong id="totalGuests">0 người</strong>
                                    </div>

                                    <!-- Hidden field để tương thích với code cũ -->
                                    <input type="hidden" id="party_size" name="party_size" value="0">
                                </div>


                            </div>
                             <div class="mb-3">
                                    <label for="booking_location" class="form-label">
                                        Địa chỉ <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="booking_location" name="booking_location" required>
                                        <option value="">Chọn địa chỉ</option>
                                        <?php foreach ($addresses as $address): ?>
                                            <option value="<?= htmlspecialchars($address['address']) ?>"
                                                <?= ($_SESSION['form_data']['booking_location'] ?? '') == $address['address'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($address['address']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="mb-3">
                            <label for="special_requests" class="form-label">Yêu cầu đặc biệt</label>
                            <textarea class="form-control"
                                id="special_requests"
                                name="special_requests"
                                rows="3"
                                placeholder="Ghi chú về bàn ưa thích, dị ứng thực phẩm, kỷ niệm đặc biệt..."><?= htmlspecialchars($_SESSION['form_data']['special_requests'] ?? '') ?></textarea>
                        </div>

                        <!-- Payment Information -->
                        <div class="card mb-3" id="paymentInfo" style="display: none;">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-calculator"></i> Thông tin thanh toán</h6>
                            </div>
                            <div class="card-body">
                                <!-- Payment Breakdown -->
                                <div class="mb-3">
                                    <h6 class="text-primary mb-2">Chi tiết giá:</h6>
                                    <div id="paymentBreakdown" class="mb-2">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold text-primary">
                                        <span>Tổng cộng:</span>
                                        <span id="totalAmount">0 VNĐ</span>
                                    </div>
                                </div>

                                <!-- Payment Summary -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <small class="text-muted">Số khách:</small>
                                            <div class="fw-bold" id="guestCount">0 người</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <small class="text-muted">Cần trả trước (15%):</small>
                                            <div class="fw-bold text-warning" id="prepaidAmount">0 VNĐ</div>
                                        </div>
                                        <div>
                                            <small class="text-muted">Còn lại khi tới ăn:</small>
                                            <div class="fw-bold text-success" id="remainingAmount">0 VNĐ</div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="alert alert-warning mb-0">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Lưu ý:</strong> Quý khách cần thanh toán trước 15% để xác nhận đặt bàn.
                                        Phần còn lại (85%) sẽ thanh toán khi tới nhà hàng.
                                        Có thể phát sinh thêm phụ phí tùy theo yêu cầu thực tế.
                                    </small>
                                </div>
                            </div>
                        </div>                        <!-- Availability Check -->
                        <div class="alert alert-info" id="availabilityResult" style="display: none;"></div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg" id="submitBtn">
                                <i class="fas fa-credit-card"></i> Thanh Toán Ngay (15%)
                            </button>
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Bạn cần thanh toán trước 15% để xác nhận đặt bàn
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Restaurant Info & Guidelines -->
        <div class="col-lg-4">
            <!-- Restaurant Hours -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-clock"></i> Giờ hoạt động</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thứ 2 - Thứ 6:</span>
                        <span>10:00 - 22:00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thứ 7 - Chủ Nhật:</span>
                        <span>09:00 - 23:00</span>
                    </div>
                    <hr>
                    <small class="text-muted">
                        Buffet phục vụ từ 11:00 - 21:30 hàng ngày
                    </small>
                </div>
            </div>

            <!-- Booking Guidelines -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Hướng dẫn đặt bàn</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <strong>Bắt buộc:</strong> Đặt bàn trước ít nhất 2 giờ
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Miễn phí đặt bàn
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Giữ bàn trong 15 phút
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Có thể hủy trước 2 giờ
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Hỗ trợ 24/7:
                            <a href="tel:0123456789" class="text-decoration-none">
                                0123-456-789
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Real-time Booking Info -->
            <div class="card mb-3" id="timeInfoCard">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-clock text-info"></i> Thông tin thời gian</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Thời gian hiện tại:</small><br>
                        <strong id="currentTime"><?= date('d/m/Y H:i:s') ?></strong>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Có thể đặt bàn từ:</small><br>
                        <strong id="minimumTime" class="text-success"><?= date('d/m/Y H:i', time() + 2*3600) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="card mb-3" id="pricingCard">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Bảng giá buffet</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary mb-3">Giá theo độ tuổi:</h6>

                    <!-- Người lớn -->
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                        <div>
                            <i class="fas fa-user text-primary me-2"></i>
                            <span class="fw-medium">Người lớn</span>
                            <small class="text-muted d-block">Từ 18 tuổi trở lên</small>
                        </div>
                        <div class="text-end">
                            <div><span class="badge bg-success">Trưa: 299,000đ</span></div>
                            <div class="mt-1"><span class="badge bg-primary">Tối: 399,000đ</span></div>
                            <div class="mt-1"><span class="badge bg-warning text-dark">C.tuần: 449,000đ</span></div>
                        </div>
                    </div>

                    <!-- Trẻ em 11-17 -->
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                        <div>
                            <i class="fas fa-child text-info me-2"></i>
                            <span class="fw-medium">Trẻ em</span>
                            <small class="text-muted d-block">11-17 tuổi</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-info">199,000đ</span>
                        </div>
                    </div>

                    <!-- Trẻ em 6-10 -->
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                        <div>
                            <i class="fas fa-baby text-success me-2"></i>
                            <span class="fw-medium">Trẻ nhỏ</span>
                            <small class="text-muted d-block">6-10 tuổi</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">99,000đ</span>
                        </div>
                    </div>

                    <!-- Trẻ em 0-5 -->
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                        <div>
                            <i class="fas fa-baby-carriage text-secondary me-2"></i>
                            <span class="fw-medium">Trẻ sơ sinh</span>
                            <small class="text-muted d-block">0-5 tuổi</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-secondary">Miễn phí</span>
                        </div>
                    </div>

                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Giá cuối tuần áp dụng thứ 7 & Chủ nhật
                        </small>
                    </div>
                </div>
            </div>

            <!-- Special Offers -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-gift"></i> Ưu đãi đặc biệt</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Happy Hour</h6>
                        <p class="small text-muted mb-1">14:00 - 17:00 (Thứ 2 - Thứ 6)</p>
                        <p class="small">Giảm 20% cho buffet</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary">Sinh nhật</h6>
                        <p class="small">Miễn phí buffet cho khách sinh nhật (nhóm từ 4 người)</p>
                    </div>

                    <div>
                        <h6 class="text-primary">Nhóm lớn</h6>
                        <p class="small">Giảm 10% cho nhóm từ 10 người trở lên</p>
                    </div>
                </div>
            </div>

            <!-- My Bookings Link -->
            <?php if (isLoggedIn()): ?>
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Quản lý đặt bàn</h6>
                        <a href="<?= SITE_URL ?>/index.php?page=booking&action=myBookings" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> Xem lịch sử đặt bàn
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookingDateInput = document.getElementById('booking_date');
        const bookingTimeSelect = document.getElementById('booking_time');
        const partySizeSelect = document.getElementById('party_size');
        const availabilityResult = document.getElementById('availabilityResult');
        const bookingForm = document.getElementById('bookingForm');        // Generate time slots
        function generateTimeSlots() {
            const slots = [];
            const now = new Date();
            const selectedDate = bookingDateInput.value;
            const isToday = selectedDate === now.toISOString().split('T')[0];
            const minimumTime = new Date(now.getTime() + 2 * 60 * 60 * 1000); // 2 tiếng sau

            // Lunch slots: 11:00 - 14:30
            for (let hour = 11; hour <= 14; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    if (hour === 14 && minute > 30) break;
                    const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;

                    // Kiểm tra xem time slot này có nằm trong vòng 2 tiếng không
                    let isDisabled = false;
                    let disableReason = '';

                    if (isToday) {
                        const slotDateTime = new Date(selectedDate + 'T' + time + ':00');
                        if (slotDateTime <= minimumTime) {
                            isDisabled = true;
                            const timeLeft = Math.ceil((minimumTime - slotDateTime) / (1000 * 60)); // phút
                            disableReason = timeLeft > 0 ? `Cần đặt trước ${Math.ceil(timeLeft/60)} tiếng` : 'Đã qua';
                        }
                    }

                    slots.push({
                        value: time,
                        text: time + (isDisabled ? ` (${disableReason})` : ''),
                        period: 'lunch',
                        disabled: isDisabled
                    });
                }
            }

            // Dinner slots: 17:00 - 21:30
            for (let hour = 17; hour <= 21; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    if (hour === 21 && minute > 30) break;
                    const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;

                    // Kiểm tra xem time slot này có nằm trong vòng 2 tiếng không
                    let isDisabled = false;
                    let disableReason = '';

                    if (isToday) {
                        const slotDateTime = new Date(selectedDate + 'T' + time + ':00');
                        if (slotDateTime <= minimumTime) {
                            isDisabled = true;
                            const timeLeft = Math.ceil((minimumTime - slotDateTime) / (1000 * 60)); // phút
                            disableReason = timeLeft > 0 ? `Cần đặt trước ${Math.ceil(timeLeft/60)} tiếng` : 'Đã qua';
                        }
                    }

                    slots.push({
                        value: time,
                        text: time + (isDisabled ? ` (${disableReason})` : ''),
                        period: 'dinner',
                        disabled: isDisabled
                    });
                }
            }

            return slots;
        }        // Populate time slots
        function populateTimeSlots() {
            const slots = generateTimeSlots();

            // Lưu giá trị đã chọn trước khi clear
            const selectedValue = bookingTimeSelect.value;

            bookingTimeSelect.innerHTML = '<option value="">Chọn giờ</option>';

            let currentPeriod = '';
            slots.forEach(slot => {
                if (slot.period !== currentPeriod) {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = slot.period === 'lunch' ? 'Buffet Trưa' : 'Buffet Tối';
                    bookingTimeSelect.appendChild(optgroup);
                    currentPeriod = slot.period;
                }

                const option = document.createElement('option');
                option.value = slot.value; // Giữ nguyên value cho tất cả options
                option.textContent = slot.text;
                option.disabled = slot.disabled;

                // Thêm styling cho disabled options
                if (slot.disabled) {
                    option.style.color = '#999';
                    option.style.fontStyle = 'italic';
                }

                bookingTimeSelect.lastElementChild.appendChild(option);
            });

            // Khôi phục giá trị đã chọn nếu vẫn còn khả dụng
            if (selectedValue) {
                const optionExists = Array.from(bookingTimeSelect.options).find(opt => opt.value === selectedValue && !opt.disabled);
                if (optionExists) {
                    bookingTimeSelect.value = selectedValue;
                }
            }
        }

        // Check availability when date, time, or party size changes
        function checkAvailability() {
            const date = bookingDateInput.value;
            const time = bookingTimeSelect.value;
            const partySize = partySizeSelect.value;
            const location = document.getElementById('booking_location').value;

            // Update payment information when form data changes
            updatePaymentInfo();

            if (date && time && partySize && location) {
                availabilityResult.style.display = 'block';
                availabilityResult.className = 'alert alert-info';
                availabilityResult.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra tình trạng bàn...';

                fetch('<?= SITE_URL ?>/index.php?page=booking&action=checkAvailability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `booking_date=${encodeURIComponent(date)}&booking_time=${encodeURIComponent(time)}&party_size=${encodeURIComponent(partySize)}&booking_location=${encodeURIComponent(location)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            availabilityResult.className = 'alert alert-success';
                            availabilityResult.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                        } else {
                            availabilityResult.className = 'alert alert-warning';
                            availabilityResult.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        availabilityResult.className = 'alert alert-danger';
                        availabilityResult.innerHTML = '<i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra khi kiểm tra';
                    });
            } else {
                availabilityResult.style.display = 'none';
            }
        }

        // Calculate and update payment information
        function updatePaymentInfo() {
            const date = bookingDateInput.value;
            const time = bookingTimeSelect.value;

            // Get guest counts by age group
            const adultCount = parseInt(document.getElementById('adult_count').value) || 0;
            const children11_17Count = parseInt(document.getElementById('children_11_17_count').value) || 0;
            const children6_10Count = parseInt(document.getElementById('children_6_10_count').value) || 0;
            const children0_5Count = parseInt(document.getElementById('children_0_5_count').value) || 0;

            const totalGuests = adultCount + children11_17Count + children6_10Count + children0_5Count;

            // Update total guests display and hidden field
            const totalGuestsElement = document.getElementById('totalGuests');
            if (totalGuestsElement) {
                totalGuestsElement.textContent = totalGuests + ' người';
            }

            // Update hidden party_size field for compatibility
            const partySizeField = document.getElementById('party_size');
            if (partySizeField) {
                partySizeField.value = totalGuests;
            }

            if (!date || !time || totalGuests === 0) {
                // Hide payment info if incomplete data
                const paymentCard = document.getElementById('paymentInfo');
                if (paymentCard) {
                    paymentCard.style.display = 'none';
                }
                return;
            }

            // Determine pricing based on time and date
            const bookingDate = new Date(date);
            const dayOfWeek = bookingDate.getDay(); // 0 = Sunday, 6 = Saturday
            const timeSlot = parseInt(time.split(':')[0]);

            // Weekend pricing (Saturday = 6, Sunday = 0)
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;

            let adultPrice, childPrice11_17, childPrice6_10, childPrice0_5;

            if (isWeekend) {
                // Weekend pricing
                adultPrice = 449000;
                childPrice11_17 = 199000;
                childPrice6_10 = 99000;
                childPrice0_5 = 0;
            } else if (timeSlot >= 11 && timeSlot < 15) {
                // Lunch pricing
                adultPrice = 299000;
                childPrice11_17 = 199000;
                childPrice6_10 = 99000;
                childPrice0_5 = 0;
            } else if (timeSlot >= 17 && timeSlot < 21) {
                // Dinner pricing
                adultPrice = 399000;
                childPrice11_17 = 199000;
                childPrice6_10 = 99000;
                childPrice0_5 = 0;
            } else {
                // Default to dinner pricing
                adultPrice = 399000;
                childPrice11_17 = 199000;
                childPrice6_10 = 99000;
                childPrice0_5 = 0;
            }

            // Calculate total amount
            const totalAmount = (adultCount * adultPrice) +
                              (children11_17Count * childPrice11_17) +
                              (children6_10Count * childPrice6_10) +
                              (children0_5Count * childPrice0_5);

            const prepaidAmount = Math.round(totalAmount * 0.15); // 15% prepayment
            const remainingAmount = totalAmount - prepaidAmount;

            // Create breakdown object
            const breakdown = {
                adults: { count: adultCount, price: adultPrice, subtotal: adultCount * adultPrice },
                children11_17: { count: children11_17Count, price: childPrice11_17, subtotal: children11_17Count * childPrice11_17 },
                children6_10: { count: children6_10Count, price: childPrice6_10, subtotal: children6_10Count * childPrice6_10 },
                children0_5: { count: children0_5Count, price: childPrice0_5, subtotal: children0_5Count * childPrice0_5 }
            };

            // Update UI
            updatePaymentUI(breakdown, totalAmount, prepaidAmount, remainingAmount, totalGuests);
        }

        // Update payment information in the UI
        function updatePaymentUI(breakdown, totalAmount, prepaidAmount, remainingAmount, totalGuests) {
            const paymentCard = document.getElementById('paymentInfo');
            if (!paymentCard) return;

            const totalElement = document.getElementById('totalAmount');
            const prepaidElement = document.getElementById('prepaidAmount');
            const remainingElement = document.getElementById('remainingAmount');

            if (totalElement) totalElement.textContent = formatCurrency(totalAmount);
            if (prepaidElement) prepaidElement.textContent = formatCurrency(prepaidAmount);
            if (remainingElement) remainingElement.textContent = formatCurrency(remainingAmount);

            // Update breakdown display
            const breakdownElement = document.getElementById('paymentBreakdown');
            if (breakdownElement) {
                let breakdownHTML = '';

                if (breakdown.adults.count > 0) {
                    breakdownHTML += `<div class="d-flex justify-content-between small">
                        <span>${breakdown.adults.count} người lớn × ${formatCurrency(breakdown.adults.price)}</span>
                        <span>${formatCurrency(breakdown.adults.subtotal)}</span>
                    </div>`;
                }

                if (breakdown.children11_17.count > 0) {
                    breakdownHTML += `<div class="d-flex justify-content-between small">
                        <span>${breakdown.children11_17.count} trẻ 11-17 tuổi × ${formatCurrency(breakdown.children11_17.price)}</span>
                        <span>${formatCurrency(breakdown.children11_17.subtotal)}</span>
                    </div>`;
                }

                if (breakdown.children6_10.count > 0) {
                    breakdownHTML += `<div class="d-flex justify-content-between small">
                        <span>${breakdown.children6_10.count} trẻ 6-10 tuổi × ${formatCurrency(breakdown.children6_10.price)}</span>
                        <span>${formatCurrency(breakdown.children6_10.subtotal)}</span>
                    </div>`;
                }

                if (breakdown.children0_5.count > 0) {
                    breakdownHTML += `<div class="d-flex justify-content-between small">
                        <span>${breakdown.children0_5.count} trẻ 0-5 tuổi × Miễn phí</span>
                        <span>0đ</span>
                    </div>`;
                }

                breakdownElement.innerHTML = breakdownHTML;
            }

            // Show the payment card
            paymentCard.style.display = 'block';

            // Add guest count info
            const guestCountElement = document.getElementById('guestCount');
            if (guestCountElement) {
                guestCountElement.textContent = totalGuests + ' khách';
            }
        }        // Format currency in Vietnamese style
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Form validation
        function validateForm() {
            const requiredFields = bookingForm.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Validate date is not in the past
            const selectedDate = new Date(bookingDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                bookingDateInput.classList.add('is-invalid');
                isValid = false;
            }

            // Kiểm tra địa chỉ
            const bookingLocation = document.getElementById('booking_location');
            if (!bookingLocation.value.trim()) {
                bookingLocation.classList.add('is-invalid');
                isValid = false;
            } else {
                bookingLocation.classList.remove('is-invalid');
            }

            return isValid;
        }

        // Update real-time clock and minimum booking time
        function updateTimeInfo() {
            const now = new Date();
            const minimumBookingTime = new Date(now.getTime() + 2 * 60 * 60 * 1000);

            // Format current time
            const currentTimeStr = now.toLocaleDateString('vi-VN') + ' ' +
                                   now.toLocaleTimeString('vi-VN', {hour12: false});

            // Format minimum booking time
            const minimumTimeStr = minimumBookingTime.toLocaleDateString('vi-VN') + ' ' +
                                   minimumBookingTime.toLocaleTimeString('vi-VN', {hour12: false, hour: '2-digit', minute: '2-digit'});

            document.getElementById('currentTime').textContent = currentTimeStr;
            document.getElementById('minimumTime').textContent = minimumTimeStr;

            // Chỉ re-populate time slots nếu cần thiết (mỗi phút một lần thay vì mỗi giây)
            // và chỉ khi ngày được chọn là hôm nay và user không đang tương tác với time select
            const selectedDate = bookingDateInput.value;
            const today = now.toISOString().split('T')[0];
            const currentMinute = now.getMinutes();

            if (selectedDate === today &&
                (!window.lastUpdateMinute || window.lastUpdateMinute !== currentMinute) &&
                !timeSelectFocused &&
                document.activeElement !== bookingTimeSelect) {
                populateTimeSlots();
                window.lastUpdateMinute = currentMinute;
            }
        }

        // Event listeners
        // Ensure elements are ready
        setTimeout(() => {
            populateTimeSlots();
            updateTimeInfo(); // Initial update
        }, 100);

        setInterval(updateTimeInfo, 1000); // Update every second

        // Prevent time select from being updated while user is interacting with it
        let timeSelectFocused = false;
        bookingTimeSelect.addEventListener('focus', () => { timeSelectFocused = true; });
        bookingTimeSelect.addEventListener('blur', () => { timeSelectFocused = false; });
        bookingTimeSelect.addEventListener('mousedown', () => { timeSelectFocused = true; });
        bookingTimeSelect.addEventListener('mouseup', () => {
            setTimeout(() => { timeSelectFocused = false; }, 100);
        });

        bookingDateInput.addEventListener('change', function() {
            // Reset update tracker để time slots được cập nhật ngay lập tức
            window.lastUpdateMinute = null;
            populateTimeSlots(); // Regenerate time slots when date changes
            checkAvailability();
        });
        bookingTimeSelect.addEventListener('change', checkAvailability);

        // Add event listeners for age group selects
        document.getElementById('adult_count').addEventListener('change', checkAvailability);
        document.getElementById('children_11_17_count').addEventListener('change', checkAvailability);
        document.getElementById('children_6_10_count').addEventListener('change', checkAvailability);
        document.getElementById('children_0_5_count').addEventListener('change', checkAvailability);

        document.getElementById('booking_location').addEventListener('change', checkAvailability);

        bookingForm.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                showToast('Vui lòng điền đầy đủ thông tin', 'error');
            }
        });

        // Phone number formatting
        document.getElementById('customer_phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            e.target.value = value;
        });

        // Set minimum date to today
        bookingDateInput.min = new Date().toISOString().split('T')[0];        // Clear form data from session after display
        <?php if (isset($_SESSION['form_data'])): ?>
            // Clear form data from session
            fetch('<?= SITE_URL ?>/index.php?page=booking&action=clearFormData', {
                method: 'POST'
            });
        <?php endif; ?>
    });

    function showToast(message, type = 'info') {
        // Toast implementation (assuming it exists in main.js)
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }
</script>

<style>
    .card {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .card-header {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border-bottom: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }

        .card {
            margin-bottom: 1rem;
        }
    }
</style>

<?php
// Clear form data after displaying
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>
