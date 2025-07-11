<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Validation 2 Tiếng Đặt Bàn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Test Validation 2 Tiếng Đặt Bàn</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Test AJAX Check Availability</h5>
                    </div>
                    <div class="card-body">
                        <form id="testForm">
                            <div class="mb-3">
                                <label for="booking_date" class="form-label">Ngày đặt bàn</label>
                                <input type="date" class="form-control" id="booking_date" name="booking_date" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="booking_time" class="form-label">Giờ đặt bàn</label>
                                <select class="form-control" id="booking_time" name="booking_time" required>
                                    <option value="">Chọn giờ</option>
                                    <?php
                                    // Tạo các option giờ
                                    for ($hour = 9; $hour <= 22; $hour++) {
                                        for ($minute = 0; $minute < 60; $minute += 30) {
                                            $time = sprintf('%02d:%02d', $hour, $minute);
                                            echo "<option value=\"$time\">$time</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="party_size" class="form-label">Số lượng khách</label>
                                <select class="form-control" id="party_size" name="party_size" required>
                                    <option value="">Chọn số khách</option>
                                    <?php for($i = 1; $i <= 20; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> người</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="booking_location" class="form-label">Địa chỉ chi nhánh</label>
                                <select class="form-control" id="booking_location" name="booking_location" required>
                                    <option value="">Chọn chi nhánh</option>
                                    <option value="Hà Nội">Hà Nội</option>
                                    <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                    <option value="Đà Nẵng">Đà Nẵng</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="checkAvailability()">Kiểm tra tình trạng bàn</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Kết quả kiểm tra</h5>
                    </div>
                    <div class="card-body">
                        <div id="result" class="alert alert-info" style="display: none;">
                            Chưa có kết quả
                        </div>

                        <div class="mt-3">
                            <h6>Thông tin thời gian:</h6>
                            <p><strong>Thời gian hiện tại:</strong> <span id="currentTime"><?= date('Y-m-d H:i:s') ?></span></p>
                            <p><strong>Thời gian tối thiểu được phép đặt:</strong> <span id="minTime"><?= date('Y-m-d H:i:s', time() + 2*3600) ?></span></p>
                        </div>

                        <div class="mt-3">
                            <h6>Test Cases Nhanh:</h6>
                            <button class="btn btn-sm btn-outline-danger mb-1" onclick="testCase('1_hour')">Test: 1 tiếng sau (Expect: FAIL)</button><br>
                            <button class="btn btn-sm btn-outline-success mb-1" onclick="testCase('3_hours')">Test: 3 tiếng sau (Expect: PASS)</button><br>
                            <button class="btn btn-sm btn-outline-warning mb-1" onclick="testCase('2_hours')">Test: 2 tiếng sau (Expect: PASS)</button><br>
                            <button class="btn btn-sm btn-outline-danger mb-1" onclick="testCase('1_hour_59_min')">Test: 1 tiếng 59 phút (Expect: FAIL)</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCurrentTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleString('sv-SE');

            const minTime = new Date(now.getTime() + 2 * 60 * 60 * 1000);
            document.getElementById('minTime').textContent = minTime.toLocaleString('sv-SE');
        }

        setInterval(updateCurrentTime, 1000);

        function checkAvailability() {
            const date = document.getElementById('booking_date').value;
            const time = document.getElementById('booking_time').value;
            const partySize = document.getElementById('party_size').value;
            const location = document.getElementById('booking_location').value;

            if (!date || !time || !partySize || !location) {
                document.getElementById('result').style.display = 'block';
                document.getElementById('result').className = 'alert alert-warning';
                document.getElementById('result').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Vui lòng chọn đầy đủ thông tin!';
                return;
            }

            document.getElementById('result').style.display = 'block';
            document.getElementById('result').className = 'alert alert-info';
            document.getElementById('result').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra...';

            const formData = new FormData();
            formData.append('booking_date', date);
            formData.append('booking_time', time);
            formData.append('party_size', partySize);
            formData.append('booking_location', location);

            fetch('/buffet_booking_mvc/index.php?page=booking&action=checkAvailability', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('result');
                if (data.available) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                } else {
                    resultDiv.className = 'alert alert-danger';
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('result').className = 'alert alert-danger';
                document.getElementById('result').innerHTML = '<i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra: ' + error.message;
            });
        }

        function testCase(type) {
            const now = new Date();
            let testTime;

            switch(type) {
                case '1_hour':
                    testTime = new Date(now.getTime() + 1 * 60 * 60 * 1000);
                    break;
                case '3_hours':
                    testTime = new Date(now.getTime() + 3 * 60 * 60 * 1000);
                    break;
                case '2_hours':
                    testTime = new Date(now.getTime() + 2 * 60 * 60 * 1000 + 5 * 60 * 1000); // +5 phút để đảm bảo
                    break;
                case '1_hour_59_min':
                    testTime = new Date(now.getTime() + 1 * 60 * 60 * 1000 + 59 * 60 * 1000);
                    break;
            }

            const timeString = testTime.toTimeString().substring(0, 5);

            document.getElementById('booking_date').value = testTime.toISOString().split('T')[0];
            document.getElementById('booking_time').value = timeString;
            document.getElementById('party_size').value = '4';
            document.getElementById('booking_location').value = 'Hà Nội';

            checkAvailability();
        }
    </script>
</body>
</html>
