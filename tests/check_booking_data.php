<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Kiểm tra dữ liệu Booking</h2>";

    // Kiểm tra cấu trúc bảng
    echo "<h3>1. Cấu trúc bảng bookings:</h3>";
    $stmt = $pdo->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";

    // Kiểm tra tổng số booking
    echo "<h3>2. Tổng số booking:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Tổng số booking: " . $total['total'] . "<br><br>";

    // Kiểm tra các giá trị status
    echo "<h3>3. Các giá trị status:</h3>";
    $stmt = $pdo->query("SELECT DISTINCT status FROM bookings");
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($statuses);
    echo "</pre>";

    // Kiểm tra 10 booking gần nhất
    echo "<h3>4. 10 booking gần nhất:</h3>";
    $stmt = $pdo->query("SELECT id, booking_date, status FROM bookings ORDER BY booking_date DESC LIMIT 10");
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($recent);
    echo "</pre>";

    // Kiểm tra booking trong 30 ngày gần nhất
    echo "<h3>5. Booking trong 30 ngày gần nhất:</h3>";
    $stmt = $pdo->query("SELECT id, booking_date, status FROM bookings WHERE booking_date >= CURDATE() - INTERVAL 30 DAY");
    $recent30 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Số booking trong 30 ngày: " . count($recent30) . "<br>";
    echo "<pre>";
    print_r($recent30);
    echo "</pre>";

    // Kiểm tra booking theo status
    echo "<h3>6. Số lượng booking theo status:</h3>";
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    $statusCount = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($statusCount);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
