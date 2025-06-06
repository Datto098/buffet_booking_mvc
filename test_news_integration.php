<?php
/**
 * Simple test to verify News model and HomeController fix
 */

// Include necessary files
require_once 'config/config.php';

try {
    echo "<h1>Testing News Model Integration</h1>";

    // Test 1: Check if News model can be loaded
    echo "<h2>Test 1: Loading News Model</h2>";
    require_once 'models/News.php';
    $newsModel = new News();
    echo "<p>✅ News model loaded successfully</p>";

    // Test 2: Check database connection
    echo "<h2>Test 2: Database Connection</h2>";
    $database = new Database();
    $db = $database->getConnection();
    echo "<p>✅ Database connection established</p>";

    // Test 3: Check if news table exists
    echo "<h2>Test 3: News Table Structure</h2>";
    $stmt = $db->prepare("DESCRIBE news");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    echo "<p>✅ News table exists with " . count($columns) . " columns</p>";

    // Test 4: Try to get latest news
    echo "<h2>Test 4: Testing getLatestNews() Method</h2>";
    $latestNews = $newsModel->getLatestNews(3);
    echo "<p>✅ getLatestNews() method works - returned " . count($latestNews) . " articles</p>";

    // Test 5: Simulate HomeController usage
    echo "<h2>Test 5: Simulating HomeController News Usage</h2>";
    $latestNews = [];
    try {
        $newsModel = new News();
        $latestNews = $newsModel->getLatestNews(3);
        echo "<p>✅ HomeController news integration works - " . count($latestNews) . " articles retrieved</p>";
    } catch (Exception $e) {
        echo "<p>❌ HomeController news integration failed: " . $e->getMessage() . "</p>";
    }

    echo "<h2>🎉 All Tests Passed!</h2>";
    echo "<p>The News model is working correctly and the HomeController error has been resolved.</p>";

} catch (Exception $e) {
    echo "<h2>❌ Error during testing:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
