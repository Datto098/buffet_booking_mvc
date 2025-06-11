<?php
// Test API getPromotion
echo "<h1>Testing getPromotion API</h1>";

$promotionId = 1;
$url = "http://localhost/buffet_booking_mvc/superadmin/promotions/get/$promotionId";

echo "<h2>Testing URL: $url</h2>";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, true);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

// Get headers and body
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "<h3>HTTP Status Code: $httpCode</h3>";
echo "<h3>Response Headers:</h3><pre>" . htmlspecialchars($headers) . "</pre>";
echo "<h3>Response Body:</h3><pre>" . htmlspecialchars($body) . "</pre>";

// Test if it's a JSON response
if ($httpCode == 200) {
    $jsonData = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h3>Parsed JSON:</h3><pre>" . print_r($jsonData, true) . "</pre>";
    } else {
        echo "<h3>Not valid JSON response</h3>";
    }
}

// Test with file_get_contents
echo "<hr><h2>Testing with file_get_contents:</h2>";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Accept: application/json'
        ]
    ]);

    $result = file_get_contents($url, false, $context);
    echo "<h3>Result:</h3><pre>" . htmlspecialchars($result) . "</pre>";

    $jsonData = json_decode($result, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h3>Parsed JSON:</h3><pre>" . print_r($jsonData, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<h3>Error: " . $e->getMessage() . "</h3>";
}

// Test routing - check if the route exists
echo "<hr><h2>Route Testing:</h2>";
require_once 'config/config.php';
require_once 'config/Database.php';

echo "<p>SITE_URL: " . SITE_URL . "</p>";

// Check if promotion exists in database
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM promotions WHERE id = ?");
    $stmt->execute([$promotionId]);
    $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($promotion) {
        echo "<h3>Promotion exists in database:</h3><pre>" . print_r($promotion, true) . "</pre>";
    } else {
        echo "<h3>Promotion ID $promotionId NOT found in database</h3>";

        // List all promotions
        $stmt = $db->query("SELECT id, name, code FROM promotions LIMIT 5");
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h4>Available promotions:</h4><pre>" . print_r($promotions, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<h3>Database Error: " . $e->getMessage() . "</h3>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
hr { margin: 30px 0; }
</style>
