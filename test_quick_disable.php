<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Test Time Slot Disable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Quick Test: Time Slot Disable Status</h2>

        <div class="alert alert-info">
            <strong>Test Results:</strong><br>
            <span id="testResults">Đang test...</span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h4>Input Test</h4>
                <input type="date" id="testDate" class="form-control mb-3" value="<?= date('Y-m-d') ?>">
                <select id="testTimeSelect" class="form-select">
                    <option value="">Loading...</option>
                </select>
            </div>
            <div class="col-md-6">
                <h4>Debug Info</h4>
                <div id="debugInfo" style="font-family: monospace; font-size: 12px;"></div>
            </div>
        </div>
    </div>

    <script>
        function generateTimeSlots() {
            const slots = [];
            const now = new Date();
            const selectedDate = document.getElementById('testDate').value;
            const isToday = selectedDate === now.toISOString().split('T')[0];
            const minimumTime = new Date(now.getTime() + 2 * 60 * 60 * 1000);

            console.log('Current time:', now);
            console.log('Selected date:', selectedDate);
            console.log('Is today:', isToday);
            console.log('Minimum time:', minimumTime);

            let debugHTML = `
                <strong>Debug Info:</strong><br>
                Current time: ${now.toLocaleString()}<br>
                Selected date: ${selectedDate}<br>
                Is today: ${isToday}<br>
                Minimum time: ${minimumTime.toLocaleString()}<br><br>
            `;

            // Test a few slots
            const testTimes = ['11:00', '12:00', '13:00', '14:00', '17:00', '18:00', '19:00', '20:00'];
            let disabledCount = 0;

            testTimes.forEach(time => {
                let isDisabled = false;
                let disableReason = '';

                if (isToday) {
                    const slotDateTime = new Date(selectedDate + 'T' + time + ':00');
                    if (slotDateTime <= minimumTime) {
                        isDisabled = true;
                        disabledCount++;
                        const timeLeft = Math.ceil((minimumTime - slotDateTime) / (1000 * 60));
                        disableReason = timeLeft > 0 ? `Cần đặt trước ${Math.ceil(timeLeft/60)} tiếng` : 'Đã qua';
                    }
                }

                debugHTML += `${time}: ${isDisabled ? 'DISABLED' : 'ENABLED'} ${disableReason ? '(' + disableReason + ')' : ''}<br>`;

                slots.push({
                    value: time,
                    text: time + (isDisabled ? ` (${disableReason})` : ''),
                    disabled: isDisabled
                });
            });

            document.getElementById('debugInfo').innerHTML = debugHTML;
            document.getElementById('testResults').innerHTML =
                `Tổng ${testTimes.length} slots test, ${disabledCount} bị disable, ${testTimes.length - disabledCount} available`;

            return slots;
        }

        function populateTimeSlots() {
            const slots = generateTimeSlots();
            const select = document.getElementById('testTimeSelect');
            select.innerHTML = '<option value="">Chọn giờ</option>';

            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.disabled ? '' : slot.value;
                option.textContent = slot.text;
                option.disabled = slot.disabled;

                if (slot.disabled) {
                    option.style.color = '#999';
                    option.style.fontStyle = 'italic';
                }

                select.appendChild(option);
            });
        }

        // Initialize
        populateTimeSlots();

        // Update when date changes
        document.getElementById('testDate').addEventListener('change', populateTimeSlots);

        // Auto refresh every 30 seconds to see time changes
        setInterval(populateTimeSlots, 30000);
    </script>
</body>
</html>
