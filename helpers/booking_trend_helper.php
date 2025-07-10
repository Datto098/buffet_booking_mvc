<?php

/**
 * Booking Trend Helper
 * Tạo dữ liệu booking trend theo ngày tháng năm
 */

class BookingTrendHelper {

            /**
     * Tạo dữ liệu booking trend cho 30 ngày gần nhất
     * @param PDO $pdo Database connection
     * @return array Dữ liệu booking trend
     */
    public static function getBookingTrendData($pdo) {
        $data = [
            'labels' => [],
            'total' => []
        ];

        // Tạo danh sách 30 ngày gần nhất
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            $data['labels'][] = date('M j', strtotime($date));
        }

        // Lấy tổng số booking cho từng ngày (không phân biệt status)
        foreach ($dates as $date) {
            $total = self::getBookingCountByDate($pdo, $date);
            $data['total'][] = $total;
        }

        return $data;
    }

    /**
     * Lấy số lượng booking theo ngày (không phân biệt status)
     * @param PDO $pdo Database connection
     * @param string $date Ngày (Y-m-d)
     * @return int Số lượng booking
     */
    private static function getBookingCountByDate($pdo, $date) {
        try {
            $sql = "SELECT COUNT(*) as count FROM bookings WHERE DATE(booking_date) = :date";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':date' => $date]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            error_log("Error getting booking count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy số lượng booking theo ngày và trạng thái
     * @param PDO $pdo Database connection
     * @param string $date Ngày (Y-m-d)
     * @param string $status Trạng thái booking
     * @return int Số lượng booking
     */
    private static function getBookingCountByDateAndStatus($pdo, $date, $status) {
        try {
            $sql = "SELECT COUNT(*) as count FROM bookings
                    WHERE DATE(booking_date) = :date
                    AND status = :status";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':date' => $date,
                ':status' => $status
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];

        } catch (PDOException $e) {
            error_log("Error getting booking count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tạo dữ liệu booking trend cho 12 tháng gần nhất
     * @param PDO $pdo Database connection
     * @return array Dữ liệu booking trend theo tháng
     */
    public static function getMonthlyBookingTrendData($pdo) {
        $data = [
            'labels' => [],
            'confirmed' => [],
            'pending' => [],
            'cancelled' => [],
            'total' => []
        ];

        // Tạo danh sách 12 tháng gần nhất
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = $month;
            $data['labels'][] = date('M Y', strtotime($month . '-01'));
        }

        // Lấy dữ liệu booking cho từng tháng
        foreach ($months as $month) {
            $confirmed = self::getBookingCountByMonthAndStatus($pdo, $month, 'confirmed');
            $pending = self::getBookingCountByMonthAndStatus($pdo, $month, 'pending');
            $cancelled = self::getBookingCountByMonthAndStatus($pdo, $month, 'cancelled');
            $total = $confirmed + $pending + $cancelled;

            $data['confirmed'][] = $confirmed;
            $data['pending'][] = $pending;
            $data['cancelled'][] = $cancelled;
            $data['total'][] = $total;
        }

        return $data;
    }

    /**
     * Lấy số lượng booking theo tháng và trạng thái
     * @param PDO $pdo Database connection
     * @param string $month Tháng (Y-m)
     * @param string $status Trạng thái booking
     * @return int Số lượng booking
     */
    private static function getBookingCountByMonthAndStatus($pdo, $month, $status) {
        try {
            $sql = "SELECT COUNT(*) as count FROM bookings
                    WHERE DATE_FORMAT(booking_date, '%Y-%m') = :month
                    AND status = :status";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':month' => $month,
                ':status' => $status
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];

        } catch (PDOException $e) {
            error_log("Error getting monthly booking count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tạo dữ liệu booking trend cho 7 ngày gần nhất (tuần)
     * @param PDO $pdo Database connection
     * @return array Dữ liệu booking trend theo tuần
     */
    public static function getWeeklyBookingTrendData($pdo) {
        $data = [
            'labels' => [],
            'confirmed' => [],
            'pending' => [],
            'cancelled' => [],
            'total' => []
        ];

        // Tạo danh sách 7 ngày gần nhất
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            $data['labels'][] = date('D, M j', strtotime($date));
        }

                // Lấy dữ liệu booking cho từng ngày
        foreach ($dates as $date) {
            $confirmed = self::getBookingCountByDateAndStatus($pdo, $date, 'confirmed');
            $pending = self::getBookingCountByDateAndStatus($pdo, $date, 'pending');
            $cancelled = self::getBookingCountByDateAndStatus($pdo, $date, 'cancelled');
            $completed = self::getBookingCountByDateAndStatus($pdo, $date, 'completed');
            $seated = self::getBookingCountByDateAndStatus($pdo, $date, 'seated');
            $no_show = self::getBookingCountByDateAndStatus($pdo, $date, 'no_show');
            $total = $confirmed + $pending + $cancelled + $completed + $seated + $no_show;

            $data['confirmed'][] = $confirmed;
            $data['pending'][] = $pending;
            $data['cancelled'][] = $cancelled;
            $data['total'][] = $total;
        }

        return $data;
    }
}
