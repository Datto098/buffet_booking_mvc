<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/booking_trend_helper.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Testing Booking Trend Helper</h2>";

    // Test daily trend data
    echo "<h3>Daily Booking Trend (30 days)</h3>";
    $dailyData = BookingTrendHelper::getBookingTrendData($pdo);
    echo "<pre>";
    print_r($dailyData);
    echo "</pre>";

    // Test weekly trend data
    echo "<h3>Weekly Booking Trend (7 days)</h3>";
    $weeklyData = BookingTrendHelper::getWeeklyBookingTrendData($pdo);
    echo "<pre>";
    print_r($weeklyData);
    echo "</pre>";

    // Test monthly trend data
    echo "<h3>Monthly Booking Trend (12 months)</h3>";
    $monthlyData = BookingTrendHelper::getMonthlyBookingTrendData($pdo);
    echo "<pre>";
    print_r($monthlyData);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
