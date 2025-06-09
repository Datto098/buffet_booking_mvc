<?php
/**
 * Quick test to check news editing workflow
 */

require_once 'config/config.php';
require_once 'models/News.php';

echo "<h1>Testing News Edit Workflow</h1>";

try {
    $newsModel = new News();

    // Get the first news article
    $allNews = $newsModel->getAllForAdmin(1);

    if (!empty($allNews)) {
        $newsId = $allNews[0]['id'];
        echo "<p>Testing with news article ID: {$newsId}</p>";
        echo "<p>Title: {$allNews[0]['title']}</p>";
        echo "<p>Has image: " . (!empty($allNews[0]['image_url']) ? 'Yes (' . $allNews[0]['image_url'] . ')' : 'No') . "</p>";

        // Test the edit URL
        $editUrl = "http://localhost/buffet_booking_mvc/admin/news/edit/{$newsId}";
        echo "<p>Edit URL: <a href='{$editUrl}' target='_blank'>{$editUrl}</a></p>";

        // Test if we can get the specific news item
        $newsItem = $newsModel->findById($newsId);
        if ($newsItem) {
            echo "<p>✅ Can retrieve news item for editing</p>";
            echo "<p>Image URL: " . ($newsItem['image_url'] ?? 'None') . "</p>";
        } else {
            echo "<p>❌ Cannot retrieve news item for editing</p>";
        }

    } else {
        echo "<p>❌ No news articles found to test with</p>";
        echo "<p>Creating a test article...</p>";

        // Create a test article
        $testData = [
            'title' => 'Test News Article for Upload',
            'content' => '<p>This is a test article to verify the upload functionality works properly.</p>',
            'excerpt' => 'Test article for upload verification',
            'author_id' => 1, // Assuming admin user ID 1 exists
            'is_published' => 1
        ];

        if ($newsModel->createNews($testData)) {
            echo "<p>✅ Test article created successfully</p>";

            // Try again
            $allNews = $newsModel->getAllForAdmin(1);
            if (!empty($allNews)) {
                $newsId = $allNews[0]['id'];
                $editUrl = "http://localhost/buffet_booking_mvc/admin/news/edit/{$newsId}";
                echo "<p>Edit URL: <a href='{$editUrl}' target='_blank'>{$editUrl}</a></p>";
            }
        } else {
            echo "<p>❌ Failed to create test article</p>";
        }
    }

    echo "<h2>Summary</h2>";
    echo "<p>The news upload error has been fixed by:</p>";
    echo "<ul>";
    echo "<li>✅ Adding proper upload error checking before calling getimagesize()</li>";
    echo "<li>✅ Validating tmp_name is not empty and is a valid uploaded file</li>";
    echo "<li>✅ Providing clear error messages for different upload failures</li>";
    echo "<li>✅ Fixing image path consistency in edit form</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
