<?php
require_once 'config/config.php';

echo "=== FINAL ROUTING TEST ===\n\n";

// Test URLs directly
$testUrls = [
    'Home' => '/buffet_booking_mvc/',
    'About' => '/buffet_booking_mvc/about',
    'Promotions' => '/buffet_booking_mvc/promotions'
];

foreach ($testUrls as $name => $url) {
    echo "Testing $name page ($url):\n";
    echo "----------------------------------------\n";

    $content = file_get_contents("http://localhost$url");

    if ($content === false) {
        echo "❌ Failed to load page\n\n";
        continue;
    }

    echo "✅ Page loaded successfully\n";
    echo "Content length: " . strlen($content) . " characters\n";

    // Check for specific content based on page
    switch ($name) {
        case 'Home':
            $hasCorrectContent = strpos($content, 'Trang Chủ') !== false;
            echo ($hasCorrectContent ? "✅" : "❌") . " Contains 'Trang Chủ': " . ($hasCorrectContent ? "Yes" : "No") . "\n";
            break;

        case 'About':
            $hasCorrectContent = strpos($content, 'Giới Thiệu') !== false;
            echo ($hasCorrectContent ? "✅" : "❌") . " Contains 'Giới Thiệu': " . ($hasCorrectContent ? "Yes" : "No") . "\n";

            $hasAboutContent = strpos($content, 'Về ' . SITE_NAME) !== false;
            echo ($hasAboutContent ? "✅" : "❌") . " Contains 'Về " . SITE_NAME . "': " . ($hasAboutContent ? "Yes" : "No") . "\n";
            break;

        case 'Promotions':
            $hasCorrectContent = strpos($content, 'Khuyến Mãi') !== false;
            echo ($hasCorrectContent ? "✅" : "❌") . " Contains 'Khuyến Mãi': " . ($hasCorrectContent ? "Yes" : "No") . "\n";

            $hasPromotionContent = strpos($content, 'Deal Hot') !== false;
            echo ($hasPromotionContent ? "✅" : "❌") . " Contains 'Deal Hot': " . ($hasPromotionContent ? "Yes" : "No") . "\n";
            break;
    }

    // Check if it's a proper HTML page
    $hasDoctype = strpos($content, '<!DOCTYPE') !== false;
    echo ($hasDoctype ? "✅" : "❌") . " Has DOCTYPE: " . ($hasDoctype ? "Yes" : "No") . "\n";

    $hasTitle = strpos($content, '<title>') !== false;
    echo ($hasTitle ? "✅" : "❌") . " Has title tag: " . ($hasTitle ? "Yes" : "No") . "\n";

    echo "\n";
}

echo "=== TEST COMPLETED ===\n";
echo "If you see ✅ for all checks, the routing is working correctly!\n";
?>
