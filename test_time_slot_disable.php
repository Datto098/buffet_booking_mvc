<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Time Slot Disable Feature</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Test Time Slot Disable Feature</h1>

        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Test Instructions</h5>
            <ul class="mb-0">
                <li>Chọn ngày hôm nay để thấy các time slot bị disable</li>
                <li>Chọn ngày mai để thấy tất cả time slot đều available</li>
                <li>Thời gian sẽ update real-time, time slots cũng sẽ update theo</li>
                <li>Các time slot trong vòng 2 tiếng sẽ bị disable và hiển thị lý do</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Form</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Ngày đặt bàn</label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date"
                                   value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="booking_time" class="form-label">Giờ đặt bàn</label>
                            <select class="form-select" id="booking_time" name="booking_time">
                                <option value="">Chọn giờ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" onclick="testAllCases()">
                                Test All Cases
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="setToday()">
                                Set Today
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="setTomorrow()">
                                Set Tomorrow
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><i class="fas fa-clock"></i> Real-time Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Thời gian hiện tại:</strong><br>
                            <span id="currentTime" class="text-primary"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Thời gian tối thiểu đặt bàn:</strong><br>
                            <span id="minimumTime" class="text-success"></span>
                        </div>
                        <div>
                            <strong>Ngày được chọn:</strong><br>
                            <span id="selectedDate" class="text-info"></span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Time Slots Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div id="slotsAnalysis">
                            <p class="text-muted">Chọn ngày để phân tích time slots</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const bookingDateInput = document.getElementById('booking_date');
        const bookingTimeSelect = document.getElementById('booking_time');

        // Generate time slots function (copy from main booking page)
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
                option.value = slot.disabled ? '' : slot.value;
                option.textContent = slot.text;
                option.disabled = slot.disabled;

                if (slot.disabled) {
                    option.style.color = '#999';
                    option.style.fontStyle = 'italic';
                }

                bookingTimeSelect.lastElementChild.appendChild(option);
            });

            // Update analysis
            updateSlotsAnalysis(slots);
        }

        function updateSlotsAnalysis(slots) {
            const analysis = document.getElementById('slotsAnalysis');
            const totalSlots = slots.length;
            const disabledSlots = slots.filter(s => s.disabled).length;
            const availableSlots = totalSlots - disabledSlots;

            analysis.innerHTML = `
                <div class="mb-2"><strong>Tổng slots:</strong> ${totalSlots}</div>
                <div class="mb-2"><strong>Available:</strong> <span class="text-success">${availableSlots}</span></div>
                <div class="mb-2"><strong>Disabled:</strong> <span class="text-danger">${disabledSlots}</span></div>
                <hr>
                <div class="small">
                    ${slots.map(slot =>
                        `<span class="${slot.disabled ? 'text-danger' : 'text-success'}">${slot.value}</span>`
                    ).join(' | ')}
                </div>
            `;
        }

        function updateTimeInfo() {
            const now = new Date();
            const minimumBookingTime = new Date(now.getTime() + 2 * 60 * 60 * 1000);

            document.getElementById('currentTime').textContent = now.toLocaleString('vi-VN');
            document.getElementById('minimumTime').textContent = minimumBookingTime.toLocaleString('vi-VN');
            document.getElementById('selectedDate').textContent = bookingDateInput.value || 'Chưa chọn';

            // Re-populate time slots if today is selected
            const selectedDate = bookingDateInput.value;
            const today = now.toISOString().split('T')[0];
            if (selectedDate === today) {
                populateTimeSlots();
            }
        }

        function setToday() {
            bookingDateInput.value = new Date().toISOString().split('T')[0];
            populateTimeSlots();
            updateTimeInfo();
        }

        function setTomorrow() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            bookingDateInput.value = tomorrow.toISOString().split('T')[0];
            populateTimeSlots();
            updateTimeInfo();
        }

        function testAllCases() {
            console.log('Testing all cases...');
            setToday();
            setTimeout(() => {
                console.log('Today slots:', Array.from(bookingTimeSelect.options).map(o => ({value: o.value, text: o.textContent, disabled: o.disabled})));
                setTomorrow();
                setTimeout(() => {
                    console.log('Tomorrow slots:', Array.from(bookingTimeSelect.options).map(o => ({value: o.value, text: o.textContent, disabled: o.disabled})));
                }, 100);
            }, 100);
        }

        // Event listeners
        bookingDateInput.addEventListener('change', function() {
            populateTimeSlots();
            updateTimeInfo();
        });

        // Initialize
        populateTimeSlots();
        updateTimeInfo();
        setInterval(updateTimeInfo, 1000); // Update every second
    </script>
</body>
</html>
