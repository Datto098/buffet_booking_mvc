<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Thêm booking test</h2>";

    // Tạo booking cho các ngày gần đây
    $testBookings = [
        [
            'customer_name' => 'Nguyễn Văn A',
            'customer_email' => 'test1@example.com',
            'customer_phone' => '0123456789',
            'booking_date' => date('Y-m-d', strtotime('-2 days')),
            'booking_time' => '18:00:00',
            'guest_count' => 4,
            'status' => 'confirmed'
        ],
        [
            'customer_name' => 'Trần Thị B',
            'customer_email' => 'test2@example.com',
            'customer_phone' => '0987654321',
            'booking_date' => date('Y-m-d', strtotime('-1 day')),
            'booking_time' => '19:00:00',
            'guest_count' => 2,
            'status' => 'pending'
        ],
        [
            'customer_name' => 'Lê Văn C',
            'customer_email' => 'test3@example.com',
            'customer_phone' => '0111222333',
            'booking_date' => date('Y-m-d'), // Hôm nay
            'booking_time' => '20:00:00',
            'guest_count' => 6,
            'status' => 'confirmed'
        ],
        [
            'customer_name' => 'Phạm Thị D',
            'customer_email' => 'test4@example.com',
            'customer_phone' => '0444555666',
            'booking_date' => date('Y-m-d', strtotime('-5 days')),
            'booking_time' => '17:30:00',
            'guest_count' => 3,
            'status' => 'completed'
        ],
        [
            'customer_name' => 'Hoàng Văn E',
            'customer_email' => 'test5@example.com',
            'customer_phone' => '0777888999',
            'booking_date' => date('Y-m-d', strtotime('-3 days')),
            'booking_time' => '19:30:00',
            'guest_count' => 5,
            'status' => 'seated'
        ]
    ];

    $inserted = 0;
    foreach ($testBookings as $booking) {
        $sql = "INSERT INTO bookings (customer_name, customer_email, customer_phone, booking_date, booking_time, guest_count, status)
                VALUES (:customer_name, :customer_email, :customer_phone, :booking_date, :booking_time, :guest_count, :status)";

        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($booking)) {
            $inserted++;
            echo "✅ Đã thêm booking: {$booking['customer_name']} - {$booking['booking_date']} - {$booking['status']}<br>";
        }
    }

    echo "<br><strong>Đã thêm thành công $inserted booking test!</strong><br>";
    echo "Bây giờ hãy F5 lại trang dashboard để xem biểu đồ.<br>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
