<?php
/**
 * Test script đơn giản để kiểm tra logic validation 2 tiếng
 */

echo "<h2>Test Logic Validation 2 Tiếng Trước Đặt Bàn</h2>\n";

function testTimeValidation($bookingDate, $bookingTime, $description) {
    echo "<h3>$description</h3>\n";

    $bookingDateTime = $bookingDate . ' ' . $bookingTime;
    $bookingTimestamp = strtotime($bookingDateTime);
    $currentTimestamp = time();
    $minimumAdvanceTime = $currentTimestamp + (2 * 60 * 60); // 2 tiếng = 2 * 60 * 60 giây

    echo "Thời gian hiện tại: " . date('Y-m-d H:i:s', $currentTimestamp) . "\n";
    echo "Thời gian đặt bàn: " . date('Y-m-d H:i:s', $bookingTimestamp) . "\n";
    echo "Thời gian tối thiểu: " . date('Y-m-d H:i:s', $minimumAdvanceTime) . "\n";

    if ($bookingTimestamp < $currentTimestamp) {
        echo "Kết quả: FAIL - Không thể đặt bàn cho thời gian trong quá khứ\n";
        return false;
    } elseif ($bookingTimestamp < $minimumAdvanceTime) {
        $currentTime = date('H:i', $currentTimestamp);
        $requiredTime = date('H:i', $minimumAdvanceTime);
        echo "Kết quả: FAIL - Bạn phải đặt bàn trước ít nhất 2 tiếng. Hiện tại là {$currentTime}, bạn chỉ có thể đặt bàn từ {$requiredTime} trở đi.\n";
        return false;
    } else {
        echo "Kết quả: PASS - Thời gian đặt bàn hợp lệ\n";
        return true;
    }
}

// Test case 1: Đặt bàn cho 1 tiếng sau (should fail)
$result1 = testTimeValidation(
    date('Y-m-d'),
    date('H:i', time() + 3600),
    "Test Case 1: Đặt bàn cho 1 tiếng sau (Expect: FAIL)"
);

echo "\n";

// Test case 2: Đặt bàn cho 3 tiếng sau (should pass)
$result2 = testTimeValidation(
    date('Y-m-d'),
    date('H:i', time() + (3 * 3600)),
    "Test Case 2: Đặt bàn cho 3 tiếng sau (Expect: PASS)"
);

echo "\n";

// Test case 3: Đặt bàn cho quá khứ (should fail)
$result3 = testTimeValidation(
    date('Y-m-d'),
    date('H:i', time() - 3600),
    "Test Case 3: Đặt bàn cho quá khứ (Expect: FAIL)"
);

echo "\n";

// Test case 4: Đặt bàn chính xác 2 tiếng 5 phút sau (boundary test)
$result4 = testTimeValidation(
    date('Y-m-d'),
    date('H:i', time() + (2 * 3600) + (5 * 60)),
    "Test Case 4: Đặt bàn cho 2 tiếng 5 phút sau (Boundary Test - Expect: PASS)"
);

echo "\n";

// Test case 5: Đặt bàn cho 1 tiếng 59 phút sau (should fail)
$result5 = testTimeValidation(
    date('Y-m-d'),
    date('H:i', time() + (1 * 3600) + (59 * 60)),
    "Test Case 5: Đặt bàn cho 1 tiếng 59 phút sau (Expect: FAIL)"
);

echo "\n";

echo "<h3>Tóm tắt Test Results</h3>\n";
echo "- Test case 1 (1 tiếng sau): " . (!$result1 ? 'PASS' : 'FAIL') . "\n";
echo "- Test case 2 (3 tiếng sau): " . ($result2 ? 'PASS' : 'FAIL') . "\n";
echo "- Test case 3 (quá khứ): " . (!$result3 ? 'PASS' : 'FAIL') . "\n";
echo "- Test case 4 (2 tiếng 5 phút sau): " . ($result4 ? 'PASS' : 'FAIL') . "\n";
echo "- Test case 5 (1 tiếng 59 phút sau): " . (!$result5 ? 'PASS' : 'FAIL') . "\n";

$allTestsPassed = !$result1 && $result2 && !$result3 && $result4 && !$result5;
echo "\nTất cả test: " . ($allTestsPassed ? 'PASS' : 'FAIL') . "\n";
?>
