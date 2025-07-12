<?php
/**
 * Test script để kiểm tra validation 2 tiếng trước khi đặt bàn
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/BookingController.php';

echo "<h2>Test Validation 2 Tiếng Trước Đặt Bàn</h2>\n";

// Khởi tạo controller
$bookingController = new BookingController();

// Test case 1: Đặt bàn cho 1 tiếng sau (should fail)
echo "<h3>Test Case 1: Đặt bàn cho 1 tiếng sau (Expect: FAIL)</h3>\n";
$_POST = [
    'booking_date' => date('Y-m-d'),
    'booking_time' => date('H:i', time() + 3600), // 1 tiếng sau
    'party_size' => '4',
    'booking_location' => 'Hà Nội'
];

echo "Thời gian hiện tại: " . date('Y-m-d H:i') . "\n";
echo "Thời gian đặt bàn: " . $_POST['booking_date'] . " " . $_POST['booking_time'] . "\n";

ob_start();
$bookingController->checkAvailability();
$output1 = ob_get_clean();
$result1 = json_decode($output1, true);

echo "Kết quả: " . ($result1['available'] ? 'PASS (sai!)' : 'FAIL (đúng!)') . "\n";
echo "Thông báo: " . $result1['message'] . "\n\n";

// Test case 2: Đặt bàn cho 3 tiếng sau (should pass)
echo "<h3>Test Case 2: Đặt bàn cho 3 tiếng sau (Expect: PASS)</h3>\n";
$_POST = [
    'booking_date' => date('Y-m-d'),
    'booking_time' => date('H:i', time() + (3 * 3600)), // 3 tiếng sau
    'party_size' => '4',
    'booking_location' => 'Hà Nội'
];

echo "Thời gian hiện tại: " . date('Y-m-d H:i') . "\n";
echo "Thời gian đặt bàn: " . $_POST['booking_date'] . " " . $_POST['booking_time'] . "\n";

ob_start();
$bookingController->checkAvailability();
$output2 = ob_get_clean();
$result2 = json_decode($output2, true);

echo "Kết quả: " . ($result2['available'] ? 'PASS (có thể đúng)' : 'FAIL (có thể sai nếu không có bàn trống)') . "\n";
echo "Thông báo: " . $result2['message'] . "\n\n";

// Test case 3: Đặt bàn cho quá khứ (should fail)
echo "<h3>Test Case 3: Đặt bàn cho quá khứ (Expect: FAIL)</h3>\n";
$_POST = [
    'booking_date' => date('Y-m-d'),
    'booking_time' => date('H:i', time() - 3600), // 1 tiếng trước
    'party_size' => '4',
    'booking_location' => 'Hà Nội'
];

echo "Thời gian hiện tại: " . date('Y-m-d H:i') . "\n";
echo "Thời gian đặt bàn: " . $_POST['booking_date'] . " " . $_POST['booking_time'] . "\n";

ob_start();
$bookingController->checkAvailability();
$output3 = ob_get_clean();
$result3 = json_decode($output3, true);

echo "Kết quả: " . ($result3['available'] ? 'PASS (sai!)' : 'FAIL (đúng!)') . "\n";
echo "Thông báo: " . $result3['message'] . "\n\n";

// Test case 4: Đặt bàn chính xác 2 tiếng sau (boundary test)
echo "<h3>Test Case 4: Đặt bàn chính xác 2 tiếng sau (Boundary Test)</h3>\n";
$_POST = [
    'booking_date' => date('Y-m-d'),
    'booking_time' => date('H:i', time() + (2 * 3600)), // chính xác 2 tiếng sau
    'party_size' => '4',
    'booking_location' => 'Hà Nội'
];

echo "Thời gian hiện tại: " . date('Y-m-d H:i') . "\n";
echo "Thời gian đặt bàn: " . $_POST['booking_date'] . " " . $_POST['booking_time'] . "\n";

ob_start();
$bookingController->checkAvailability();
$output4 = ob_get_clean();
$result4 = json_decode($output4, true);

echo "Kết quả: " . ($result4['available'] ? 'PASS (có thể đúng)' : 'FAIL (có thể sai nếu không có bàn trống)') . "\n";
echo "Thông báo: " . $result4['message'] . "\n\n";

echo "<h3>Tóm tắt Test</h3>\n";
echo "- Test case 1 (1 tiếng sau): " . (!$result1['available'] ? 'PASS' : 'FAIL') . "\n";
echo "- Test case 2 (3 tiếng sau): Cần kiểm tra có bàn trống không\n";
echo "- Test case 3 (quá khứ): " . (!$result3['available'] ? 'PASS' : 'FAIL') . "\n";
echo "- Test case 4 (2 tiếng sau): Boundary test - Cần kiểm tra có bàn trống không\n";
?>
