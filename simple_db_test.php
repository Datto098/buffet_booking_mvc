<?php
echo "Testing database connection...\n";

try {
    // Direct database connection test
    $pdo = new PDO('mysql:host=localhost;dbname=buffet_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connected successfully!\n";

    // Test news table
    $stmt = $pdo->query("SHOW TABLES LIKE 'news'");
    if ($stmt->rowCount() > 0) {
        echo "✅ News table exists\n";

        $stmt = $pdo->query('SELECT COUNT(*) as count FROM news');
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Total news articles: {$count['count']}\n";

        if ($count['count'] > 0) {
            $stmt = $pdo->query('SELECT id, title FROM news ORDER BY id DESC LIMIT 3');
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "\n📰 Recent articles:\n";
            foreach ($news as $item) {
                echo "- ID: {$item['id']} | {$item['title']}\n";
            }
        }
    } else {
        echo "❌ News table does not exist\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
