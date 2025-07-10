<?php
/**
 * Script để cập nhật AdminController với booking trend data
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/booking_trend_helper.php';

// Đọc file AdminController hiện tại
$adminControllerPath = 'controllers/AdminController.php';
$content = file_get_contents($adminControllerPath);

// Tìm và thay thế phần dashboardStats
$dashboardStatsPattern = '/public function dashboardStats\(\)\s*\{[\s\S]*?\}/';
$newDashboardStats = 'public function dashboardStats() {
        try {
            $pdo = $this->getDatabase();

            // Lấy thống kê cơ bản
            $stats = [
                \'total_orders\' => $this->getTotalOrders($pdo),
                \'monthly_revenue\' => $this->getMonthlyRevenue($pdo),
                \'active_bookings\' => $this->getActiveBookings($pdo),
                \'total_users\' => $this->getTotalUsers($pdo),
                \'confirmed_bookings\' => $this->getBookingCountByStatus($pdo, \'confirmed\'),
                \'pending_bookings\' => $this->getBookingCountByStatus($pdo, \'pending\'),
                \'cancelled_bookings\' => $this->getBookingCountByStatus($pdo, \'cancelled\'),
                \'recent_orders\' => $this->getRecentOrders($pdo),
                \'recent_bookings\' => $this->getRecentBookings($pdo),
                \'booking_trend_data\' => BookingTrendHelper::getBookingTrendData($pdo)
            ];

            header(\'Content-Type: application/json\');
            echo json_encode($stats);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([\'error\' => $e->getMessage()]);
        }
    }';

// Thay thế hàm dashboardStats
$updatedContent = preg_replace($dashboardStatsPattern, $newDashboardStats, $content);

// Thêm require_once cho BookingTrendHelper nếu chưa có
if (strpos($updatedContent, 'require_once \'../helpers/booking_trend_helper.php\';') === false) {
    $updatedContent = str_replace(
        '<?php',
        '<?php' . PHP_EOL . 'require_once \'../helpers/booking_trend_helper.php\';' . PHP_EOL,
        $updatedContent
    );
}

// Ghi lại file
if (file_put_contents($adminControllerPath, $updatedContent)) {
    echo "AdminController đã được cập nhật thành công!\n";
    echo "Đã thêm booking trend data vào dashboard.\n";
} else {
    echo "Lỗi khi cập nhật AdminController!\n";
}

// Tạo backup
$backupPath = 'controllers/AdminController_backup_' . date('Y-m-d_H-i-s') . '.php';
if (file_put_contents($backupPath, $content)) {
    echo "Backup đã được tạo tại: $backupPath\n";
}

echo "Hoàn thành!\n";
?>
