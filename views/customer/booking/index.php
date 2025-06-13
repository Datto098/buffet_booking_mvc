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
                    <form method="POST" action="<?= SITE_URL ?>/booking/create" id="bookingForm">
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
                                        value="<?= $_SESSION['form_data']['booking_date'] ?? '' ?>"
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
                                    <label for="party_size" class="form-label">
                                        Số lượng khách <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="party_size" name="party_size" required>
                                        <option value="">Chọn số lượng</option>
                                        <?php for ($i = 1; $i <= 20; $i++): ?>
                                            <option value="<?= $i ?>"
                                                <?= ($_SESSION['form_data']['party_size'] ?? '') == $i ? 'selected' : '' ?>>
                                                <?= $i ?> <?= $i == 1 ? 'người' : 'người' ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
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

                        <!-- Availability Check -->
                        <div class="alert alert-info" id="availabilityResult" style="display: none;"></div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-calendar-check"></i> Đặt Bàn Ngay
                            </button>
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
                            <i class="fas fa-check text-success me-2"></i>
                            Đặt bàn trước ít nhất 2 giờ
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
        const bookingForm = document.getElementById('bookingForm');

        // Generate time slots
        function generateTimeSlots() {
            const slots = [];

            // Lunch slots: 11:00 - 14:30
            for (let hour = 11; hour <= 14; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    if (hour === 14 && minute > 30) break;
                    const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    slots.push({
                        value: time,
                        text: time,
                        period: 'lunch'
                    });
                }
            }

            // Dinner slots: 17:00 - 21:30
            for (let hour = 17; hour <= 21; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    if (hour === 21 && minute > 30) break;
                    const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    slots.push({
                        value: time,
                        text: time,
                        period: 'dinner'
                    });
                }
            }

            return slots;
        }

        // Populate time slots
        function populateTimeSlots() {
            const slots = generateTimeSlots();
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
                option.value = slot.value;
                option.textContent = slot.text;
                bookingTimeSelect.lastElementChild.appendChild(option);
            });
        }

        // Check availability when date, time, or party size changes
        function checkAvailability() {
            const date = bookingDateInput.value;
            const time = bookingTimeSelect.value;
            const partySize = partySizeSelect.value;

            if (date && time && partySize) {
                availabilityResult.style.display = 'block';
                availabilityResult.className = 'alert alert-info';
                availabilityResult.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra tình trạng bàn...';

                fetch('<?= SITE_URL ?>/index.php?page=booking&action=checkAvailability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `booking_date=${date}&booking_time=${time}&party_size=${partySize}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            availabilityResult.className = 'alert alert-success';
                            availabilityResult.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                        } else {
                            availabilityResult.className = 'alert alert-warning';
                            availabilityResult.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;

                            if (data.suggestedTimes && data.suggestedTimes.length > 0) {
                                availabilityResult.innerHTML += '<br><strong>Giờ có thể đặt:</strong> ' + data.suggestedTimes.join(', ');
                            }
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

            return isValid;
        }

        // Event listeners
        populateTimeSlots();

        bookingDateInput.addEventListener('change', checkAvailability);
        bookingTimeSelect.addEventListener('change', checkAvailability);
        partySizeSelect.addEventListener('change', checkAvailability);

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
