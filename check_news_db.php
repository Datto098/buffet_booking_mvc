<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check news table structure
    $stmt = $pdo->query("DESCRIBE news");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "News table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']}\n";
    }

    echo "\n";

    // Check existing news
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM news');
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total news articles: {$count['count']}\n\n";

    if ($count['count'] > 0) {
        $stmt = $pdo->query('SELECT id, title, LEFT(content, 100) as content_preview FROM news ORDER BY created_at DESC LIMIT 3');
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Recent news articles:\n";
        foreach ($news as $item) {
            echo "ID: {$item['id']}\n";
            echo "Title: {$item['title']}\n";
            echo "Content preview: " . substr(strip_tags($item['content_preview']), 0, 80) . "...\n\n";
        }
    } else {
        echo "No news articles found. You can create one to test the rich text editor.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
