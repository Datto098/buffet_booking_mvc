<?php
$booking = $booking ?? [];
?>
<div class="container my-5">
    <h2>Sửa Đặt Bàn</h2>
    <form method="post">
        <div class="mb-3">
            <label for="customer_name" class="form-label">Họ và tên</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name"
                   value="<?= htmlspecialchars($booking['customer_name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number"
                   value="<?= htmlspecialchars($booking['phone_number'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="booking_date" class="form-label">Ngày đặt bàn</label>
            <input type="date"
                   class="form-control"
                   id="booking_date"
                   name="booking_date"
                   value="<?= isset($booking['reservation_time']) ? date('Y-m-d', strtotime($booking['reservation_time'])) : '' ?>"
                   min="<?= date('Y-m-d') ?>"
                   required>
        </div>
        <div class="mb-3">
            <label for="booking_time" class="form-label">Giờ đặt bàn</label>
            <select class="form-select" id="booking_time" name="booking_time" required>
                <option value="">Chọn giờ</option>
                <!-- Time slots sẽ được JS tự động điền -->
            </select>
        </div>
        <div class="mb-3">
            <label for="number_of_guests" class="form-label">Số lượng khách</label>
            <input type="number" class="form-control" id="number_of_guests" name="number_of_guests"
                   value="<?= htmlspecialchars($booking['number_of_guests'] ?? '') ?>" min="1" required>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Yêu cầu đặc biệt</label>
            <textarea class="form-control" id="notes" name="notes"><?= htmlspecialchars($booking['notes'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="index.php?page=booking" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingDateInput = document.getElementById('booking_date');
    const bookingTimeSelect = document.getElementById('booking_time');

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
            // Nếu là giờ đã chọn thì set selected
            <?php if (isset($booking['reservation_time'])): ?>
            if (slot.value === "<?= date('H:i', strtotime($booking['reservation_time'])) ?>") {
                option.selected = true;
            }
            <?php endif; ?>
            bookingTimeSelect.lastElementChild.appendChild(option);
        });
    }

    populateTimeSlots();

    // Set minimum date to today
    bookingDateInput.min = new Date().toISOString().split('T')[0];
});
</script>