<?php
/**
 * Script to add a single sample news article for testing
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/News.php';

try {
    echo "<h1>Creating Sample News Article</h1>";

    // Get admin user ID
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id FROM users WHERE role IN ('manager', 'super_admin') LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();

    if (!$admin) {
        echo "<p>No admin user found. Please create an admin user first.</p>";
        exit;
    }

    $authorId = $admin['id'];

    // Create news article
    $newsModel = new News();

    $newsData = [
        'title' => 'Grand Opening of Our Buffet Restaurant',
        'content' => '<p>We are excited to announce the grand opening of our brand new buffet restaurant!</p>
                     <p>Join us for a culinary journey featuring a wide variety of dishes from around the world.
                     Our experienced chefs have crafted a menu that will satisfy even the most discerning palates.</p>
                     <h3>Special Opening Offers</h3>
                     <ul>
                        <li>20% discount for all bookings made before June 15th</li>
                        <li>Free dessert for parties of 4 or more</li>
                        <li>Complimentary welcome drink for all guests</li>
                     </ul>
                     <p>We look forward to serving you and providing an unforgettable dining experience!</p>',
        'excerpt' => 'Join us for the grand opening of our new buffet restaurant with special offers and discounts!',
        'author_id' => $authorId,
        'is_published' => 1,
        'meta_title' => 'Grand Opening - Buffet Restaurant',
        'meta_description' => 'Join us for the grand opening of our new buffet restaurant with special offers and discounts!'
    ];

    if ($newsModel->createNews($newsData)) {
        echo "<p>✅ Sample news article created successfully!</p>";
        echo "<p>You can now view the news page at: <a href='" . SITE_URL . "/news'>News Page</a></p>";
    } else {
        echo "<p>❌ Failed to create sample news article.</p>";
    }

} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
