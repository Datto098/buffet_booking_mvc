<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/booking_trend_helper.php';

// Bật error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Dashboard View Data</h2>";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

    echo "<h3>1. Dữ liệu stats:</h3>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";

    echo "<h3>2. Kiểm tra booking_trend_data trong stats:</h3>";
    if (isset($stats['booking_trend_data'])) {
        echo "✅ booking_trend_data có tồn tại<br>";
        echo "Loại dữ liệu: " . gettype($stats['booking_trend_data']) . "<br>";
        echo "Có phải array: " . (is_array($stats['booking_trend_data']) ? 'YES' : 'NO') . "<br>";

        if (is_array($stats['booking_trend_data'])) {
            echo "Keys: " . implode(', ', array_keys($stats['booking_trend_data'])) . "<br>";

            if (isset($stats['booking_trend_data']['total'])) {
                echo "Total array length: " . count($stats['booking_trend_data']['total']) . "<br>";
                echo "Có dữ liệu > 0: ";
                $hasData = false;
                foreach ($stats['booking_trend_data']['total'] as $count) {
                    if ($count > 0) {
                        $hasData = true;
                        break;
                    }
                }
                echo ($hasData ? 'YES' : 'NO') . "<br>";
            }
        }
    } else {
        echo "❌ booking_trend_data KHÔNG tồn tại<br>";
    }

    echo "<h3>3. Giả lập JavaScript data:</h3>";
    $jsData = [
        'booking_trend_data' => $stats['booking_trend_data'] ?? [
            'labels' => [],
            'total' => []
        ],
        'booking_stats' => [
            'confirmed' => $stats['confirmed_bookings'] ?? 0,
            'pending' => $stats['pending_bookings'] ?? 0,
            'cancelled' => $stats['cancelled_bookings'] ?? 0
        ]
    ];

    echo "Dữ liệu sẽ được truyền vào JavaScript:<br>";
    echo "<pre>";
    print_r($jsData);
    echo "</pre>";

    echo "<h3>4. JSON encode test:</h3>";
    $jsonData = json_encode($jsData['booking_trend_data']);
    echo "JSON: " . $jsonData . "<br>";

    echo "<h3>5. Kiểm tra dashboard.php có nhận được dữ liệu không:</h3>";
    if (file_exists('views/admin/dashboard.php')) {
        echo "✅ dashboard.php tồn tại<br>";

        $dashboardContent = file_get_contents('views/admin/dashboard.php');
        if (strpos($dashboardContent, 'booking_trend_data') !== false) {
            echo "✅ dashboard.php có sử dụng booking_trend_data<br>";
        } else {
            echo "❌ dashboard.php KHÔNG sử dụng booking_trend_data<br>";
        }

        if (strpos($dashboardContent, 'initializeCharts') !== false) {
            echo "✅ dashboard.php có gọi initializeCharts<br>";
        } else {
            echo "❌ dashboard.php KHÔNG gọi initializeCharts<br>";
        }
    } else {
        echo "❌ dashboard.php không tồn tại<br>";
    }

    echo "<h3>6. Log kết quả:</h3>";
    $logMessage = "Dashboard View Debug - " . date('Y-m-d H:i:s') . " - Data exists: " . (isset($stats['booking_trend_data']) ? 'YES' : 'NO') . "\n";
    file_put_contents('logs/dashboard_debug.log', $logMessage, FILE_APPEND);
    echo "Đã ghi log vào logs/dashboard_debug.log<br>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
