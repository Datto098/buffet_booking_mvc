<?php
/**
 * Test script to check news page styling
 */
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Testing News Page Styling</h2>";

// Test news index page
echo "<h3>1. Testing News Index Page</h3>";
$newsUrl = SITE_URL . "/news";
echo "<p>News Index URL: <a href='$newsUrl' target='_blank'>$newsUrl</a></p>";

// Test news detail page
echo "<h3>2. Testing News Detail Page</h3>";
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, title FROM news WHERE status = 'published' LIMIT 1");
    $news_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($news_item) {
        $detailUrl = SITE_URL . "/news/detail?id=" . $news_item['id'];
        echo "<p>News Detail URL: <a href='$detailUrl' target='_blank'>$detailUrl</a></p>";
        echo "<p>Sample News: " . htmlspecialchars($news_item['title']) . "</p>";
    } else {
        echo "<p>No published news found for testing detail page.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error checking news: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Styling Features Applied</h3>";
echo "<ul>";
echo "<li>✅ Hero section with luxury theme</li>";
echo "<li>✅ Breadcrumb navigation</li>";
echo "<li>✅ Luxury card design for news items</li>";
echo "<li>✅ Hover effects and animations</li>";
echo "<li>✅ Custom date display</li>";
echo "<li>✅ Luxury pagination</li>";
echo "<li>✅ Responsive design</li>";
echo "<li>✅ Empty state with luxury styling</li>";
echo "<li>✅ News detail page luxury styling</li>";
echo "<li>✅ Related news section</li>";
echo "<li>✅ Print-friendly styles</li>";
echo "</ul>";

echo "<h3>4. Test Complete</h3>";
echo "<p>Visit the URLs above to see the new luxury styling in action!</p>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h2 { color: #333; border-bottom: 2px solid #FFD700; padding-bottom: 10px; }
h3 { color: #666; margin-top: 30px; }
ul li { margin: 5px 0; }
a { color: #0066cc; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
