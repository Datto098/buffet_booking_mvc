<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Seed Booking Data</h2>";

    // Tạo booking cho các ngày khác nhau trong 30 ngày gần nhất
    $testBookings = [
        // Ngày hôm nay
        [
            'customer_name' => 'Nguyễn Văn A',
            'customer_email' => 'nguyenvana@example.com',
            'customer_phone' => '0123456789',
            'booking_date' => date('Y-m-d'),
            'booking_time' => '18:00:00',
            'guest_count' => 4,
            'status' => 'confirmed'
        ],
        [
            'customer_name' => 'Trần Thị B',
            'customer_email' => 'tranthib@example.com',
            'customer_phone' => '0987654321',
            'booking_date' => date('Y-m-d'),
            'booking_time' => '19:30:00',
            'guest_count' => 2,
            'status' => 'pending'
        ],

        // Ngày hôm qua
        [
            'customer_name' => 'Lê Văn C',
            'customer_email' => 'levanc@example.com',
            'customer_phone' => '0111222333',
            'booking_date' => date('Y-m-d', strtotime('-1 day')),
            'booking_time' => '20:00:00',
            'guest_count' => 6,
            'status' => 'confirmed'
        ],
        [
            'customer_name' => 'Phạm Thị D',
            'customer_email' => 'phamthid@example.com',
            'customer_phone' => '0444555666',
            'booking_date' => date('Y-m-d', strtotime('-1 day')),
            'booking_time' => '17:30:00',
            'guest_count' => 3,
            'status' => 'completed'
        ],

        // 2 ngày trước
        [
            'customer_name' => 'Hoàng Văn E',
            'customer_email' => 'hoangvane@example.com',
            'customer_phone' => '0777888999',
            'booking_date' => date('Y-m-d', strtotime('-2 days')),
            'booking_time' => '19:30:00',
            'guest_count' => 5,
            'status' => 'seated'
        ],
        [
            'customer_name' => 'Vũ Thị F',
            'customer_email' => 'vuthif@example.com',
            'customer_phone' => '0555666777',
            'booking_date' => date('Y-m-d', strtotime('-2 days')),
            'booking_time' => '18:30:00',
            'guest_count' => 4,
            'status' => 'confirmed'
        ],

        // 3 ngày trước
        [
            'customer_name' => 'Đặng Văn G',
            'customer_email' => 'dangvang@example.com',
            'customer_phone' => '0333444555',
            'booking_date' => date('Y-m-d', strtotime('-3 days')),
            'booking_time' => '20:30:00',
            'guest_count' => 3,
            'status' => 'cancelled'
        ],
        [
            'customer_name' => 'Bùi Thị H',
            'customer_email' => 'buithih@example.com',
            'customer_phone' => '0666777888',
            'booking_date' => date('Y-m-d', strtotime('-3 days')),
            'booking_time' => '19:00:00',
            'guest_count' => 2,
            'status' => 'confirmed'
        ],

        // 5 ngày trước
        [
            'customer_name' => 'Lý Văn I',
            'customer_email' => 'lyvani@example.com',
            'customer_phone' => '0222333444',
            'booking_date' => date('Y-m-d', strtotime('-5 days')),
            'booking_time' => '18:00:00',
            'guest_count' => 6,
            'status' => 'completed'
        ],
        [
            'customer_name' => 'Hồ Thị K',
            'customer_email' => 'hothik@example.com',
            'customer_phone' => '0888999000',
            'booking_date' => date('Y-m-d', strtotime('-5 days')),
            'booking_time' => '20:00:00',
            'guest_count' => 4,
            'status' => 'confirmed'
        ],

        // 7 ngày trước
        [
            'customer_name' => 'Tô Văn L',
            'customer_email' => 'tovanl@example.com',
            'customer_phone' => '0111000111',
            'booking_date' => date('Y-m-d', strtotime('-7 days')),
            'booking_time' => '19:30:00',
            'guest_count' => 3,
            'status' => 'seated'
        ],
        [
            'customer_name' => 'Dương Thị M',
            'customer_email' => 'duongthim@example.com',
            'customer_phone' => '0999000999',
            'booking_date' => date('Y-m-d', strtotime('-7 days')),
            'booking_time' => '17:30:00',
            'guest_count' => 5,
            'status' => 'confirmed'
        ],

        // 10 ngày trước
        [
            'customer_name' => 'Mai Văn N',
            'customer_email' => 'maivann@example.com',
            'customer_phone' => '0123456780',
            'booking_date' => date('Y-m-d', strtotime('-10 days')),
            'booking_time' => '18:30:00',
            'guest_count' => 2,
            'status' => 'completed'
        ],
        [
            'customer_name' => 'Lâm Thị O',
            'customer_email' => 'lamthio@example.com',
            'customer_phone' => '0987654320',
            'booking_date' => date('Y-m-d', strtotime('-10 days')),
            'booking_time' => '20:30:00',
            'guest_count' => 4,
            'status' => 'confirmed'
        ],

        // 15 ngày trước
        [
            'customer_name' => 'Châu Văn P',
            'customer_email' => 'chauvanp@example.com',
            'customer_phone' => '0111222330',
            'booking_date' => date('Y-m-d', strtotime('-15 days')),
            'booking_time' => '19:00:00',
            'guest_count' => 3,
            'status' => 'cancelled'
        ],
        [
            'customer_name' => 'Thái Thị Q',
            'customer_email' => 'thaithiq@example.com',
            'customer_phone' => '0444555660',
            'booking_date' => date('Y-m-d', strtotime('-15 days')),
            'booking_time' => '18:00:00',
            'guest_count' => 6,
            'status' => 'confirmed'
        ],

        // 20 ngày trước
        [
            'customer_name' => 'Tạ Văn R',
            'customer_email' => 'tavanr@example.com',
            'customer_phone' => '0777888990',
            'booking_date' => date('Y-m-d', strtotime('-20 days')),
            'booking_time' => '20:00:00',
            'guest_count' => 2,
            'status' => 'completed'
        ],
        [
            'customer_name' => 'Hà Thị S',
            'customer_email' => 'hathis@example.com',
            'customer_phone' => '0555666770',
            'booking_date' => date('Y-m-d', strtotime('-20 days')),
            'booking_time' => '19:30:00',
            'guest_count' => 4,
            'status' => 'seated'
        ],

        // 25 ngày trước
        [
            'customer_name' => 'Võ Văn T',
            'customer_email' => 'vovant@example.com',
            'customer_phone' => '0333444550',
            'booking_date' => date('Y-m-d', strtotime('-25 days')),
            'booking_time' => '18:30:00',
            'guest_count' => 3,
            'status' => 'confirmed'
        ],
        [
            'customer_name' => 'Ngô Thị U',
            'customer_email' => 'ngothiu@example.com',
            'customer_phone' => '0666777880',
            'booking_date' => date('Y-m-d', strtotime('-25 days')),
            'booking_time' => '20:30:00',
            'guest_count' => 5,
            'status' => 'pending'
        ]
    ];

    $inserted = 0;
    $errors = [];

    foreach ($testBookings as $booking) {
        try {
            // Tạo booking_reference ngẫu nhiên 10 ký tự
            $booking['booking_reference'] = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));

            $sql = "INSERT INTO bookings (customer_name, customer_email, customer_phone, booking_date, booking_time, guest_count, status, booking_reference)
                    VALUES (:customer_name, :customer_email, :customer_phone, :booking_date, :booking_time, :guest_count, :status, :booking_reference)";

            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($booking)) {
                $inserted++;
                echo "✅ Đã thêm booking: {$booking['customer_name']} - {$booking['booking_date']} - {$booking['status']} - {$booking['booking_reference']}<br>";
            } else {
                $errors[] = "Lỗi khi thêm booking: {$booking['customer_name']}";
            }
        } catch (PDOException $e) {
            $errors[] = "Lỗi database: {$booking['customer_name']} - " . $e->getMessage();
        }
    }

    echo "<br><h3>Kết quả:</h3>";
    echo "<strong>Đã thêm thành công $inserted booking!</strong><br>";

    if (!empty($errors)) {
        echo "<h4>Lỗi:</h4>";
        foreach ($errors as $error) {
            echo "❌ $error<br>";
        }
    }

    echo "<br><h3>Kiểm tra dữ liệu:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE booking_date >= CURDATE() - INTERVAL 30 DAY");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Tổng số booking trong 30 ngày gần nhất: " . $result['total'] . "<br>";

    echo "<br><strong>Bây giờ hãy F5 lại trang dashboard để xem biểu đồ!</strong><br>";

} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
