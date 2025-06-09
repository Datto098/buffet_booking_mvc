<?php
require_once 'config/database.php';
require_once 'models/News.php';

echo "=== Testing News Model Methods ===\n";

try {
    $newsModel = new News();

    // Test if new methods exist
    echo "Testing updateNewsStatus method: ";
    if (method_exists($newsModel, 'updateNewsStatus')) {
        echo "EXISTS\n";
    } else {
        echo "MISSING\n";
    }

    echo "Testing bulkUpdateStatus method: ";
    if (method_exists($newsModel, 'bulkUpdateStatus')) {
        echo "EXISTS\n";
    } else {
        echo "MISSING\n";
    }

    echo "Testing bulkDelete method: ";
    if (method_exists($newsModel, 'bulkDelete')) {
        echo "EXISTS\n";
    } else {
        echo "MISSING\n";
    }

    // Test getting all news for admin
    echo "\n=== Testing getAllForAdmin ===\n";
    $news = $newsModel->getAllForAdmin(5);
    if ($news) {
        echo "Found " . count($news) . " news articles\n";
        foreach ($news as $article) {
            echo "- ID: {$article['id']}, Title: " . substr($article['title'], 0, 50) . "..., Status: {$article['status']}\n";
        }
    } else {
        echo "No news articles found\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
