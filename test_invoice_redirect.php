<?php
echo "<h2>Testing Invoice View Route Redirect</h2>\n";

$url = "http://localhost/buffet_booking_mvc/admin/invoice/view/1";
echo "<h3>Testing URL: $url</h3>\n";

// Test with cURL - không follow redirect để thấy chính xác redirect đến đâu
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Không follow redirect
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>HTTP Status Code: $httpCode</h3>\n";
echo "<h3>Redirect URL: " . ($redirectUrl ?: 'None') . "</h3>\n";

if ($error) {
    echo "<h3>cURL Error: $error</h3>\n";
}

// Parse headers to find Location header
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "<h3>Response Headers:</h3>\n";
echo "<pre>";
echo htmlspecialchars($headers);
echo "</pre>";

// Find Location header specifically
preg_match('/Location:\s*(.+)/i', $headers, $matches);
if ($matches) {
    echo "<h3>Location Header: " . trim($matches[1]) . "</h3>\n";
}

// Test truy cập trực tiếp vào browser
echo "<h3>Truy cập trực tiếp trong browser:</h3>\n";
echo "<a href='$url' target='_blank'>Click để test trong browser: $url</a><br>";

// Kiểm tra session và authentication state
echo "<h3>Checking Authentication State:</h3>\n";
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
