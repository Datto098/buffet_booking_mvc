<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/booking_trend_helper.php';

// Bật error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Admin Dashboard Data Flow</h2>";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>1. Kiểm tra AdminController dashboardStats:</h3>";

    // Giả lập dữ liệu như AdminController
    $stats = [
        'total_orders' => 0,
        'monthly_revenue' => 0,
        'active_bookings' => 0,
        'total_users' => 0,
        'confirmed_bookings' => 0,
        'pending_bookings' => 0,
        'cancelled_bookings' => 0,
        'recent_orders' => [],
        'recent_bookings' => [],
        'booking_trend_data' => BookingTrendHelper::getBookingTrendData($pdo)
    ];

    echo "<h4>Dữ liệu stats được tạo:</h4>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";

    echo "<h3>2. Kiểm tra booking_trend_data:</h3>";
    echo "<pre>";
    print_r($stats['booking_trend_data']);
    echo "</pre>";

    echo "<h3>3. Kiểm tra JSON encode:</h3>";
    $jsonData = json_encode($stats['booking_trend_data']);
    echo "JSON data: " . $jsonData . "<br>";

    echo "<h3>4. Kiểm tra JavaScript data:</h3>";
    echo "Dữ liệu sẽ được truyền vào JavaScript:<br>";
    echo "booking_trend_data: " . json_encode($stats['booking_trend_data'] ?? [
        'labels' => [],
        'total' => []
    ]) . "<br>";

    echo "<h3>5. Kiểm tra có dữ liệu không:</h3>";
    $hasData = false;
    if (isset($stats['booking_trend_data']['total']) && is_array($stats['booking_trend_data']['total'])) {
        foreach ($stats['booking_trend_data']['total'] as $count) {
            if ($count > 0) {
                $hasData = true;
                break;
            }
        }
    }

    if ($hasData) {
        echo "✅ Có dữ liệu booking trend<br>";
    } else {
        echo "❌ Không có dữ liệu booking trend<br>";
    }

    echo "<h3>6. Log file test:</h3>";
    $logMessage = "Dashboard Debug - " . date('Y-m-d H:i:s') . " - Has data: " . ($hasData ? 'YES' : 'NO') . "\n";
    file_put_contents('logs/dashboard_debug.log', $logMessage, FILE_APPEND);
    echo "Đã ghi log vào logs/dashboard_debug.log<br>";

    echo "<h3>7. Kiểm tra AdminController có được load không:</h3>";
    if (file_exists('controllers/AdminController.php')) {
        echo "✅ AdminController.php tồn tại<br>";

        // Kiểm tra xem có require helper không
        $controllerContent = file_get_contents('controllers/AdminController.php');
        if (strpos($controllerContent, 'BookingTrendHelper') !== false) {
            echo "✅ AdminController có sử dụng BookingTrendHelper<br>";
        } else {
            echo "❌ AdminController KHÔNG sử dụng BookingTrendHelper<br>";
        }

        if (strpos($controllerContent, 'booking_trend_data') !== false) {
            echo "✅ AdminController có booking_trend_data<br>";
        } else {
            echo "❌ AdminController KHÔNG có booking_trend_data<br>";
        }
    } else {
        echo "❌ AdminController.php không tồn tại<br>";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
