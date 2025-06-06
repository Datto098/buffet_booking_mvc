<?php
// Simple test without xdebug interference
require_once 'config/config.php';
require_once 'controllers/HomeController.php';

// Turn off output buffering to avoid conflicts
while (ob_get_level()) {
    ob_end_clean();
}

echo "=== ROUTING FIX TEST ===\n\n";

// Test HomeController methods directly
$controller = new HomeController();

echo "1. Testing about() method:\n";
echo "-------------------------------\n";
ob_start();
$controller->about();
$aboutContent = ob_get_clean();

if (strpos($aboutContent, 'Giới Thiệu') !== false) {
    echo "✓ About page contains 'Giới Thiệu'\n";
} else {
    echo "✗ About page does NOT contain 'Giới Thiệu'\n";
}

if (strpos($aboutContent, 'Về ' . SITE_NAME) !== false) {
    echo "✓ About page contains site name\n";
} else {
    echo "✗ About page does NOT contain site name\n";
}

echo "About content length: " . strlen($aboutContent) . " characters\n\n";

echo "2. Testing promotions() method:\n";
echo "-------------------------------\n";
ob_start();
$controller->promotions();
$promotionsContent = ob_get_clean();

if (strpos($promotionsContent, 'Khuyến Mãi') !== false) {
    echo "✓ Promotions page contains 'Khuyến Mãi'\n";
} else {
    echo "✗ Promotions page does NOT contain 'Khuyến Mãi'\n";
}

if (strpos($promotionsContent, 'Deal Hot') !== false) {
    echo "✓ Promotions page contains 'Deal Hot'\n";
} else {
    echo "✗ Promotions page does NOT contain 'Deal Hot'\n";
}

echo "Promotions content length: " . strlen($promotionsContent) . " characters\n\n";

echo "3. Testing index() method (for comparison):\n";
echo "-------------------------------\n";
ob_start();
$controller->index();
$homeContent = ob_get_clean();

if (strpos($homeContent, 'Trang Chủ') !== false) {
    echo "✓ Home page contains 'Trang Chủ'\n";
} else {
    echo "✗ Home page does NOT contain 'Trang Chủ'\n";
}

echo "Home content length: " . strlen($homeContent) . " characters\n\n";

// Test if they are different
if ($aboutContent !== $homeContent) {
    echo "✓ About page is DIFFERENT from home page\n";
} else {
    echo "✗ About page is SAME as home page\n";
}

if ($promotionsContent !== $homeContent) {
    echo "✓ Promotions page is DIFFERENT from home page\n";
} else {
    echo "✗ Promotions page is SAME as home page\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
