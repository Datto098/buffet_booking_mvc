<?php
// Test actual promotion edit API endpoint
echo "<h1>🚀 Testing Actual Promotion Edit API Endpoint</h1>";

// Test data that matches the error payload
$testData = [
    'promotion_id' => '1',
    'csrf_token' => '0b90450b444ece7b2c3fed24f78b58332a9e9c1dfb7dbc1de516f14c969eb39d',
    'name' => 'Welcome Discount',
    'code' => 'WELCOME10',
    'type' => 'percentage',
    'discount_value' => '10.00',
    'application_type' => 'specific_items',
    'food_items' => ['5', '13'],
    'categories' => ['1'],
    'start_date' => '2025-06-10',
    'end_date' => '2025-07-10',
    'usage_limit' => '100',
    'minimum_amount' => '50.00',
    'description' => 'Get 10% off your first order'
];

echo "<h2>Test Data (simulating the exact payload that caused 500 error):</h2>";
echo "<pre>" . print_r($testData, true) . "</pre>";

// Test using cURL
$url = "http://localhost/buffet_booking_mvc/superadmin/promotions/edit/1";

echo "<h2>Testing API Endpoint: $url</h2>";

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Requested-With: XMLHttpRequest',
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $curlError = curl_error($ch);
    curl_close($ch);

    echo "<h3>Results:</h3>";
    echo "<p><strong>HTTP Status:</strong> $httpCode</p>";

    if ($curlError) {
        echo "<p><strong>cURL Error:</strong> $curlError</p>";
    } else {
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        echo "<h4>Response Headers:</h4>";
        echo "<pre>" . htmlspecialchars($headers) . "</pre>";

        echo "<h4>Response Body:</h4>";
        echo "<pre>" . htmlspecialchars($body) . "</pre>";

        // Try to parse as JSON
        $jsonData = json_decode($body, true);
        if ($jsonData) {
            echo "<h4>Parsed JSON Response:</h4>";
            echo "<pre>" . print_r($jsonData, true) . "</pre>";

            if ($httpCode == 200 && isset($jsonData['success']) && $jsonData['success']) {
                echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: Promotion edit API is working!</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>❌ API returned error: " . ($jsonData['message'] ?? 'Unknown error') . "</p>";
            }
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🔗 Additional Test Links</h2>";
echo "<p><a href='http://localhost/buffet_booking_mvc/superadmin/promotions' target='_blank'>📋 Go to Promotions Page</a></p>";
echo "<p><a href='http://localhost/buffet_booking_mvc/superadmin/promotions/get/1' target='_blank'>🔍 Test Get Promotion API</a></p>";
echo "<p><a href='test_promotion_edit_direct.php' target='_blank'>🧪 Test Direct Database Operations</a></p>";

// Also test error log
echo "<h2>📋 Recent Error Log Entries</h2>";
$logFile = 'error_log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -20); // Last 20 lines
    echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    foreach ($recentLines as $line) {
        if (strpos($line, 'promotion') !== false || strpos($line, 'Promotion') !== false) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>No error log file found</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
p { margin: 8px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
hr { margin: 30px 0; }
a { color: #007bff; }
</style>
