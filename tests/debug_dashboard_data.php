<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/booking_trend_helper.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Debug Dashboard Data</h2>";

    // Test trực tiếp helper
    echo "<h3>1. Dữ liệu từ BookingTrendHelper:</h3>";
    $trendData = BookingTrendHelper::getBookingTrendData($pdo);
    echo "<pre>";
    print_r($trendData);
    echo "</pre>";

    // Kiểm tra xem có dữ liệu không
    echo "<h3>2. Kiểm tra dữ liệu:</h3>";
    $hasData = false;
    foreach ($trendData['total'] as $count) {
        if ($count > 0) {
            $hasData = true;
            break;
        }
    }

    if ($hasData) {
        echo "✅ Có dữ liệu booking trong 30 ngày gần nhất<br>";
    } else {
        echo "❌ Không có dữ liệu booking trong 30 ngày gần nhất<br>";
    }

    // Kiểm tra booking gần nhất
    echo "<h3>3. 10 booking gần nhất:</h3>";
    $stmt = $pdo->query("SELECT id, booking_date, status FROM bookings ORDER BY booking_date DESC LIMIT 10");
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($recent);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
