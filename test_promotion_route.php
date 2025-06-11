<?php
// Test actual routing for promotions/get
echo "<h1>Testing Promotion Get Route</h1>";

// Mock session for authentication
session_start();
$_SESSION['user'] = [
    'id' => 1,
    'role' => 'super_admin',
    'email' => 'admin@test.com'
];

// Set up environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/buffet_booking_mvc/superadmin/promotions/get/1';

require_once 'config/config.php';

echo "<h2>Config loaded successfully</h2>";
echo "<p>SITE_URL: " . (defined('SITE_URL') ? SITE_URL : 'NOT DEFINED') . "</p>";

try {
    require_once 'config/Database.php';
    echo "<h3>✅ Database config loaded</h3>";

    require_once 'controllers/SuperAdminController.php';
    echo "<h3>✅ SuperAdminController loaded</h3>";

    // Create controller instance
    $controller = new SuperAdminController();
    echo "<h3>✅ Controller instantiated</h3>";

    // Test the getPromotion method directly
    echo "<h3>Testing getPromotion(1) method:</h3>";

    // Capture output
    ob_start();
    $controller->getPromotion(1);
    $output = ob_get_clean();

    echo "<h4>Method Output:</h4>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";

    // Try to decode as JSON
    $jsonData = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h4>Decoded JSON:</h4>";
        echo "<pre>" . print_r($jsonData, true) . "</pre>";
    } else {
        echo "<h4>JSON Decode Error: " . json_last_error_msg() . "</h4>";
    }

} catch (Exception $e) {
    echo "<h3>❌ Error occurred:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre><strong>Stack Trace:</strong>\n" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>Alternative: Test via cURL</h2>";

// Test with cURL to the actual endpoint
$url = "http://localhost/buffet_booking_mvc/superadmin/promotions/get/1";
echo "<p>Testing URL: <a href='$url' target='_blank'>$url</a></p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'cookies'));
curl_setopt($ch, CURLOPT_COOKIEFILE, tempnam(sys_get_temp_dir(), 'cookies'));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "<h3>cURL Results:</h3>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
echo "<p><strong>Headers:</strong></p><pre>" . htmlspecialchars($headers) . "</pre>";
echo "<p><strong>Body:</strong></p><pre>" . htmlspecialchars($body) . "</pre>";

if ($httpCode == 200 && !empty($body)) {
    $jsonData = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h4>✅ Valid JSON Response:</h4>";
        echo "<pre>" . print_r($jsonData, true) . "</pre>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
hr { margin: 30px 0; }
a { color: #007cba; }
</style>
