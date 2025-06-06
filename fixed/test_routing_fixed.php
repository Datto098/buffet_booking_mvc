<?php
// Test routing fix
require_once 'config/config.php';

echo "<h1>Testing Fixed Routing</h1>";

// Test about page
echo "<h2>Testing About Page</h2>";
echo "<a href='/buffet_booking_mvc/about' target='_blank'>Click to test About page</a>";
echo "<br><br>";

// Test promotions page
echo "<h2>Testing Promotions Page</h2>";
echo "<a href='/buffet_booking_mvc/promotions' target='_blank'>Click to test Promotions page</a>";
echo "<br><br>";

// Test home page for comparison
echo "<h2>Testing Home Page (for comparison)</h2>";
echo "<a href='/buffet_booking_mvc/' target='_blank'>Click to test Home page</a>";
echo "<br><br>";

// Direct controller test
echo "<h2>Direct Controller Test</h2>";
try {
    require_once 'controllers/HomeController.php';
    $controller = new HomeController();

    echo "<h3>Testing about() method directly:</h3>";
    ob_start();
    $controller->about();
    $aboutOutput = ob_get_clean();

    echo "<p>About method output length: " . strlen($aboutOutput) . " characters</p>";
    if (strpos($aboutOutput, 'Giới Thiệu') !== false) {
        echo "<p style='color:green'>✓ About page contains 'Giới Thiệu'</p>";
    } else {
        echo "<p style='color:red'>✗ About page does not contain 'Giới Thiệu'</p>";
    }

    echo "<h3>Testing promotions() method directly:</h3>";
    ob_start();
    $controller->promotions();
    $promotionsOutput = ob_get_clean();

    echo "<p>Promotions method output length: " . strlen($promotionsOutput) . " characters</p>";
    if (strpos($promotionsOutput, 'Khuyến Mãi') !== false) {
        echo "<p style='color:green'>✓ Promotions page contains 'Khuyến Mãi'</p>";
    } else {
        echo "<p style='color:red'>✗ Promotions page does not contain 'Khuyến Mãi'</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
