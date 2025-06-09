<?php
/**
 * Test News Upload Fix
 */

require_once 'config/config.php';
require_once 'controllers/NewsController.php';

echo "<h1>Testing News Upload Fix</h1>";

try {
    // Check if uploads directory exists
    $uploadDir = 'uploads/news_images/';

    if (!file_exists($uploadDir)) {
        echo "<p>Creating uploads directory: {$uploadDir}</p>";
        mkdir($uploadDir, 0777, true);
    }

    if (is_writable($uploadDir)) {
        echo "<p>✅ Upload directory is writable: {$uploadDir}</p>";
    } else {
        echo "<p>❌ Upload directory is not writable: {$uploadDir}</p>";
    }

    // Test upload error handling
    echo "<h2>Testing Upload Error Handling</h2>";

    $newsController = new NewsController();

    // Simulate upload error by creating a fake file array with error
    $fakeFile = [
        'name' => 'test.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '', // Empty tmp_name should trigger error
        'error' => UPLOAD_ERR_INI_SIZE, // File too large error
        'size' => 0
    ];

    // Use reflection to test the private uploadNewsImage method
    $reflection = new ReflectionClass($newsController);
    $uploadMethod = $reflection->getMethod('uploadNewsImage');
    $uploadMethod->setAccessible(true);

    $result = $uploadMethod->invoke($newsController, $fakeFile);

    if (!$result['success']) {
        echo "<p>✅ Upload error handling works: {$result['message']}</p>";
    } else {
        echo "<p>❌ Upload error handling failed - should have returned error</p>";
    }

    // Test with no file error but empty tmp_name
    $fakeFile2 = [
        'name' => 'test.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '',
        'error' => UPLOAD_ERR_OK,
        'size' => 1000
    ];

    $result2 = $uploadMethod->invoke($newsController, $fakeFile2);

    if (!$result2['success']) {
        echo "<p>✅ Empty tmp_name handling works: {$result2['message']}</p>";
    } else {
        echo "<p>❌ Empty tmp_name handling failed - should have returned error</p>";
    }

    echo "<h2>Upload Fix Test Completed</h2>";
    echo "<p>The news upload function should now properly handle upload errors without causing fatal errors.</p>";

} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
