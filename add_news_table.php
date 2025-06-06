<?php
/**
 * Script to add the news table to the database
 * Run this file to create the missing news table
 */

require_once 'config/config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "<h1>Adding News Table to Database</h1>";

    // Check if news table exists
    $stmt = $db->prepare("SHOW TABLES LIKE 'news'");
    $stmt->execute();
    $tableExists = $stmt->fetch();

    if (!$tableExists) {
        echo "<h2>Creating news table...</h2>";

        // Create news table
        $sql = "CREATE TABLE `news` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `content` text NOT NULL,
            `excerpt` text,
            `author_id` int(11) NOT NULL,
            `image_url` varchar(255) DEFAULT NULL,
            `is_published` tinyint(1) DEFAULT 0,
            `is_featured` tinyint(1) DEFAULT 0,
            `meta_title` varchar(255) DEFAULT NULL,
            `meta_description` text DEFAULT NULL,
            `views_count` int(11) DEFAULT 0,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_news_author` (`author_id`),
            KEY `idx_published` (`is_published`),
            KEY `idx_featured` (`is_featured`),
            KEY `idx_created_at` (`created_at`),
            CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->exec($sql);
        echo "<p>✅ News table created successfully</p>";        // Add some sample news articles
        echo "<h3>Adding sample news articles...</h3>";

        // Get admin user ID for author
        $stmt = $db->prepare("SELECT id FROM users WHERE role IN ('manager', 'super_admin') LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch();

        if (!$admin) {
            // If no admin user exists, create one
            echo "<p>No admin user found, creating default admin user...</p>";
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password, role, is_active, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                'Admin',
                'User',
                'admin@buffetbooking.com',
                password_hash('password', PASSWORD_DEFAULT),
                'super_admin',
                1,
                1
            ]);
            $authorId = $db->lastInsertId();
            echo "<p>✅ Default admin user created with ID: {$authorId}</p>";
        } else {
            $authorId = $admin['id'];
            echo "<p>Using existing admin user with ID: {$authorId}</p>";
        }

        $sampleNews = [
            [
                'title' => 'Welcome to Our New Buffet Restaurant!',
                'content' => '<p>We are excited to announce the opening of our brand new buffet restaurant! Experience a wide variety of delicious dishes from around the world, all prepared fresh daily by our talented chefs.</p>

<p>Our buffet features:</p>
<ul>
<li>Fresh seafood selection</li>
<li>International cuisine</li>
<li>Vegetarian and vegan options</li>
<li>Delicious desserts</li>
<li>Live cooking stations</li>
</ul>

<p>Come and join us for an unforgettable dining experience. Book your table today!</p>',
                'excerpt' => 'We are excited to announce the opening of our brand new buffet restaurant! Experience a wide variety of delicious dishes from around the world.',
                'author_id' => $authorId,
                'image_url' => '/assets/images/news/restaurant-opening.jpg',
                'is_published' => 1,
                'is_featured' => 1,
                'meta_title' => 'Welcome to Our New Buffet Restaurant',
                'meta_description' => 'Experience a wide variety of delicious dishes from around the world at our new buffet restaurant. Book your table today!'
            ],
            [
                'title' => 'New Seasonal Menu Available Now',
                'content' => '<p>We are pleased to introduce our new seasonal menu featuring fresh, local ingredients and exciting new flavors!</p>

<p>Highlights of our seasonal menu include:</p>
<ul>
<li>Farm-fresh vegetables and herbs</li>
<li>Seasonal seafood specials</li>
<li>Holiday-themed desserts</li>
<li>Limited-time specialty drinks</li>
</ul>

<p>Our chefs have carefully crafted each dish to bring you the best flavors of the season. Don\'t miss out on these limited-time offerings!</p>',
                'excerpt' => 'We are pleased to introduce our new seasonal menu featuring fresh, local ingredients and exciting new flavors!',
                'author_id' => $authorId,
                'image_url' => '/assets/images/news/seasonal-menu.jpg',
                'is_published' => 1,
                'is_featured' => 0,
                'meta_title' => 'New Seasonal Menu Available Now',
                'meta_description' => 'Discover our new seasonal menu featuring fresh, local ingredients and exciting new flavors. Limited-time offerings available now!'
            ],
            [
                'title' => 'Special Holiday Hours and Events',
                'content' => '<p>Join us for special holiday celebrations with extended hours and exclusive events!</p>

<p>Holiday Schedule:</p>
<ul>
<li>Christmas Eve: Open until 8 PM</li>
<li>Christmas Day: Closed</li>
<li>New Year\'s Eve: Special celebration dinner</li>
<li>New Year\'s Day: Brunch buffet available</li>
</ul>

<p>Make your reservations early as space is limited for our special holiday events. We look forward to celebrating with you!</p>',
                'excerpt' => 'Join us for special holiday celebrations with extended hours and exclusive events!',
                'author_id' => $authorId,
                'image_url' => '/assets/images/news/holiday-events.jpg',
                'is_published' => 1,
                'is_featured' => 0,
                'meta_title' => 'Special Holiday Hours and Events',
                'meta_description' => 'Join us for special holiday celebrations with extended hours and exclusive events. Make your reservations early!'
            ]
        ];

        $stmt = $db->prepare("INSERT INTO news (title, content, excerpt, author_id, image_url, is_published, is_featured, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($sampleNews as $news) {
            $stmt->execute([
                $news['title'],
                $news['content'],
                $news['excerpt'],
                $news['author_id'],
                $news['image_url'],
                $news['is_published'],
                $news['is_featured'],
                $news['meta_title'],
                $news['meta_description']
            ]);
        }

        echo "<p>✅ Sample news articles added successfully</p>";

    } else {
        echo "<p>✅ News table already exists</p>";
    }

    echo "<h2>News table setup completed successfully!</h2>";
    echo "<p>The News model is now ready to use in HomeController.</p>";

} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>
