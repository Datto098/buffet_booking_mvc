<?php
/**
 * Test News Model functionality
 */

require_once 'config/config.php';
require_once 'models/News.php';

try {
    echo "<h1>Testing News Model</h1>";

    $newsModel = new News();

    // Test getting latest news
    echo "<h2>Testing getLatestNews()...</h2>";
    $latestNews = $newsModel->getLatestNews(3);

    if (empty($latestNews)) {
        echo "<p>No news articles found. Creating a test article...</p>";

        // Get or create an admin user
        $database = new Database();
        $db = $database->getConnection();

        $stmt = $db->prepare("SELECT id FROM users LIMIT 1");
        $stmt->execute();
        $user = $stmt->fetch();

        if (!$user) {
            echo "<p>No users found. Creating a test user...</p>";
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['Test', 'User', 'test@example.com', password_hash('password', PASSWORD_DEFAULT), 'customer']);
            $userId = $db->lastInsertId();
        } else {
            $userId = $user['id'];
        }

        // Create a test news article
        $testNews = [
            'title' => 'Test News Article',
            'content' => 'This is a test news article to verify the News model is working properly.',
            'excerpt' => 'Test article for News model verification',
            'author_id' => $userId,
            'is_published' => 1
        ];

        if ($newsModel->createNews($testNews)) {
            echo "<p>✅ Test news article created successfully</p>";

            // Try to get latest news again
            $latestNews = $newsModel->getLatestNews(3);
            if (!empty($latestNews)) {
                echo "<p>✅ Successfully retrieved latest news</p>";
                foreach ($latestNews as $news) {
                    echo "<p>- {$news['title']}</p>";
                }
            } else {
                echo "<p>❌ Failed to retrieve news after creation</p>";
            }
        } else {
            echo "<p>❌ Failed to create test news article</p>";
        }
    } else {
        echo "<p>✅ Found " . count($latestNews) . " news articles</p>";
        foreach ($latestNews as $news) {
            echo "<p>- {$news['title']}</p>";
        }
    }

    echo "<h2>News Model Test Completed</h2>";

} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
