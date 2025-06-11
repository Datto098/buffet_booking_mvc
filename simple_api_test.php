<?php
// Simple API test for getPromotion
echo "<h1>Simple API Test for getPromotion</h1>";

try {
    // Test direct URL access
    $promotionId = 1;
    $testUrl = "http://localhost/buffet_booking_mvc/superadmin/promotions/get/$promotionId";

    echo "<h2>Testing URL: <a href='$testUrl' target='_blank'>$testUrl</a></h2>";

    // Use cURL to test
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $curlError = curl_error($ch);
    curl_close($ch);

    echo "<h3>Results:</h3>";
    echo "<p><strong>HTTP Status:</strong> $httpCode</p>";

    if ($curlError) {
        echo "<p><strong>cURL Error:</strong> $curlError</p>";
    }

    if ($response) {
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        echo "<h4>Response Headers:</h4>";
        echo "<pre>" . htmlspecialchars($headers) . "</pre>";

        echo "<h4>Response Body:</h4>";
        echo "<pre>" . htmlspecialchars($body) . "</pre>";

        // Try to parse as JSON
        if ($httpCode == 200 && !empty($body)) {
            $jsonData = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<h4>✅ Valid JSON Response:</h4>";
                echo "<pre>" . print_r($jsonData, true) . "</pre>";
            } else {
                echo "<h4>❌ JSON Parse Error: " . json_last_error_msg() . "</h4>";
            }
        }
    } else {
        echo "<p>❌ No response received</p>";
    }

    // Test with session cookies
    echo "<hr><h2>Test with Session Cookies</h2>";

    // First, try to login
    $loginUrl = "http://localhost/buffet_booking_mvc/auth/login";
    echo "<p>Attempting login at: $loginUrl</p>";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=admin@admin.com&password=admin123');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'cookies'));
    curl_setopt($ch, CURLOPT_COOKIEFILE, tempnam(sys_get_temp_dir(), 'cookies'));
    curl_setopt($ch, CURLOPT_HEADER, true);

    $loginResponse = curl_exec($ch);
    $loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "<p>Login HTTP Status: $loginCode</p>";

    if ($loginCode == 200 || $loginCode == 302) {
        echo "<p>✅ Login appears successful, testing API with session...</p>";

        // Now test the API with the session cookie
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $testUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, tempnam(sys_get_temp_dir(), 'cookies'));
        curl_setopt($ch, CURLOPT_HEADER, true);

        $apiResponse = curl_exec($ch);
        $apiCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $apiHeaderSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        echo "<p>API HTTP Status with session: $apiCode</p>";

        if ($apiResponse) {
            $apiBody = substr($apiResponse, $apiHeaderSize);
            echo "<h4>API Response with Session:</h4>";
            echo "<pre>" . htmlspecialchars($apiBody) . "</pre>";

            if ($apiCode == 200 && !empty($apiBody)) {
                $jsonData = json_decode($apiBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "<h4>✅ Valid JSON Response with Session:</h4>";
                    echo "<pre>" . print_r($jsonData, true) . "</pre>";
                }
            }
        }
    } else {
        echo "<p>❌ Login failed with status: $loginCode</p>";
    }

} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>Message: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Debug Information</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
hr { margin: 30px 0; }
a { color: #007cba; }
</style>
