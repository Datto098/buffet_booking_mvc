<?php
/**
 * Import sample review data for testing the review management system
 * Run this script to add realistic review data to your database
 */

// Include database configuration
require_once __DIR__ . '/../../config/database.php';

try {
    // Create database connection
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>🍽️ Importing Sample Review Data</h1>";
    echo "<p>Adding realistic Vietnamese review data to test the review management system...</p>";

    // Sample review data
    $reviews = [
        [5, null, 1, 5, 'Buffet tuyệt vời!', 'Deluxe Buffet thực sự xứng đáng với giá tiền. Đồ ăn đa dạng, tươi ngon, nhân viên phục vụ chu đáo. Nhất định sẽ quay lại!', 1, 1, 15, '2025-06-08 10:30:00'],
        [1, null, 5, 4, 'Thịt bò Mỹ ngon', 'Ba chỉ bò Mỹ nướng vừa phải, thịt mềm và thấm gia vị. Tuy nhiên hơi mặn một chút theo ý kiến cá nhân.', 1, 1, 8, '2025-06-09 14:20:00'],
        [7, null, 7, 5, 'Sushi tươi ngon', 'Sushi cá hồi tại đây rất tươi, cơm vừa phải, cá hồi béo ngậy. Giá cả hợp lý so với chất lượng.', 1, 1, 12, '2025-06-09 16:45:00'],
        [5, null, 13, 5, 'Bánh flan hoàn hảo', 'Bánh flan mềm mượt, vị caramel đậm đà nhưng không quá ngọt. Món tráng miệng lý tưởng!', 0, 1, 6, '2025-06-09 19:15:00'],
        [1, null, 2, 3, 'Buffet bình thường', 'Standard Buffet có đủ món nhưng không có gì đặc biệt. Phù hợp với ngân sách nhưng không có điểm nhấn.', 0, 1, 3, '2025-06-10 12:00:00'],
        [7, null, 8, 4, 'Gimbap Hàn Quốc authentic', 'Gimbap làm khá giống với bên Hàn Quốc, nhân đầy đủ và cơm nêm vừa miệng. Chỉ tiếc là hơi nhỏ.', 1, 1, 9, '2025-06-10 13:30:00'],
        [5, null, 14, 5, 'Trà đào cam sả tuyệt vời', 'Nước uống rất thơm và mát, vị chua ngọt hài hòa. Rất phù hợp với thời tiết nóng bức ở Sài Gòn.', 1, 1, 11, '2025-06-10 15:45:00'],
        [1, null, 6, 4, 'Sườn bò non tuyệt', 'Sườn non nướng vừa tái vừa chín, ướp gia vị đậm đà. Thịt mềm và rất thơm, giá hơi cao nhưng xứng đáng.', 0, 1, 7, '2025-06-10 18:20:00'],
        [7, null, 9, 3, 'Tempura tôm cần cải thiện', 'Vỏ bột hơi dày, tôm tươi nhưng chiên hơi kỹ. Cần cải thiện kỹ thuật chiên để giữ độ giòn.', 1, 0, 2, '2025-06-10 20:00:00'],
        [5, null, 11, 4, 'Cơm chiên Nhật ngon', 'Cơm chiên không bị khô, trứng và rau củ tươi ngon. Phần ăn vừa phải, phù hợp cho bữa trưa nhẹ.', 1, 1, 5, '2025-06-11 11:30:00'],
        [1, null, 12, 5, 'Mì udon tuyệt hảo', 'Mì dai ngon, thịt bò mềm và ngọt. Nước dùng đậm đà, một trong những món ngon nhất tại đây!', 1, 1, 13, '2025-06-11 12:45:00'],
        [7, null, 3, 4, 'Buffet chay đa dạng', 'Vegetarian Special có nhiều lựa chọn cho người ăn chay. Món ăn tươi ngon, giá cả hợp lý.', 0, 1, 4, '2025-06-11 14:00:00'],
        [5, null, 10, 3, 'Há cảo bình thường', 'Há cảo hấp ổn nhưng không có gì đặc biệt. Nhân tôm tươi nhưng vỏ hơi dày, cần cải thiện.', 1, 0, 1, '2025-06-11 16:30:00'],
        [1, null, 15, 4, 'Trà tắc thanh mát', 'Trà tắc có vị chua nhẹ và thơm mùi trà. Thức uống phù hợp để kết thúc bữa ăn buffet.', 0, 1, 6, '2025-06-11 17:15:00'],
        [7, null, 16, 2, 'Bia không đặc biệt', 'Bia lon thông thường, không có gì đặc sắc. Giá hơi cao so với chất lượng, nên cân nhắc.', 1, 1, 0, '2025-06-11 19:00:00'],
        [5, null, 1, 5, 'Lần thứ hai vẫn tuyệt!', 'Quay lại lần thứ hai vẫn rất hài lòng với Deluxe Buffet. Chất lượng đồ ăn ổn định, dịch vụ tốt.', 1, 1, 10, '2025-06-11 20:30:00'],
        [1, null, 7, 5, 'Sushi chuẩn Nhật', 'Sushi cá hồi ở đây làm rất chuẩn, cơm nêm vừa phải, cá tươi ngon. Một trong những món phải thử!', 1, 1, 14, '2025-06-11 21:00:00'],
        [7, null, 5, 5, 'Ba chỉ bò xuất sắc', 'Thịt bò Mỹ nướng hoàn hảo, ướp gia vị đậm đà, mềm và juicy. Đây là lý do tôi quay lại nhà hàng!', 1, 1, 16, '2025-06-11 21:45:00'],
        [5, null, 14, 4, 'Thức uống tốt', 'Trà đào cam sả rất ngon, hương vị tự nhiên không bị ngọt gắt. Rất thích hợp cho mùa hè.', 0, 0, 3, '2025-06-11 22:15:00'],
        [1, null, 13, 5, 'Tráng miệng hoàn hảo', 'Bánh flan ở đây ngon nhất từ trước đến nay. Mềm mượt, ngọt vừa phải, caramel thơm lừng.', 1, 1, 9, '2025-06-11 22:30:00'],
    ];

    // Check if reviews table has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reviews");
    $currentCount = $stmt->fetchColumn();

    echo "<p><strong>Current reviews in database:</strong> {$currentCount}</p>";

    // Prepare INSERT statement
    $sql = "INSERT INTO reviews (user_id, order_id, food_item_id, rating, title, comment, is_verified, is_approved, helpful_count, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                rating = VALUES(rating),
                title = VALUES(title),
                comment = VALUES(comment),
                is_verified = VALUES(is_verified),
                is_approved = VALUES(is_approved),
                helpful_count = VALUES(helpful_count),
                updated_at = NOW()";

    $stmt = $pdo->prepare($sql);

    $successCount = 0;
    $errorCount = 0;

    echo "<h3>📝 Importing Reviews...</h3>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 10px; margin: 10px 0;'>";

    foreach ($reviews as $index => $review) {
        try {
            $result = $stmt->execute($review);
            if ($result) {
                $successCount++;
                echo "✅ Review " . ($index + 1) . ": '{$review[4]}' - {$review[3]} stars<br>";
            }
        } catch (PDOException $e) {
            $errorCount++;
            echo "❌ Error importing review " . ($index + 1) . ": " . $e->getMessage() . "<br>";
        }
    }

    echo "</div>";

    // Get final count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reviews");
    $finalCount = $stmt->fetchColumn();

    // Get some statistics
    $stats = $pdo->query("
        SELECT
            COUNT(*) as total,
            AVG(rating) as avg_rating,
            COUNT(CASE WHEN is_approved = 1 THEN 1 END) as approved,
            COUNT(CASE WHEN is_verified = 1 THEN 1 END) as verified
        FROM reviews
    ")->fetch(PDO::FETCH_ASSOC);

    echo "<h3>📊 Import Results</h3>";
    echo "<table style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Metric</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Value</th>";
    echo "</tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Successfully imported</td><td style='border: 1px solid #ddd; padding: 8px; color: green;'>{$successCount}</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Errors</td><td style='border: 1px solid #ddd; padding: 8px; color: red;'>{$errorCount}</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Total reviews in database</td><td style='border: 1px solid #ddd; padding: 8px;'>{$finalCount}</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Average rating</td><td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($stats['avg_rating'], 2) . " ⭐</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Approved reviews</td><td style='border: 1px solid #ddd; padding: 8px;'>{$stats['approved']}</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Verified reviews</td><td style='border: 1px solid #ddd; padding: 8px;'>{$stats['verified']}</td></tr>";
    echo "</table>";

    if ($successCount > 0) {
        echo "<h3>🎉 Success!</h3>";
        echo "<p>Sample review data has been successfully imported. You can now:</p>";
        echo "<ul>";
        echo "<li>📱 Visit the <strong>Super Admin Panel</strong> → <strong>Reviews Management</strong></li>";
        echo "<li>🔍 Test filtering by status, rating, and search functionality</li>";
        echo "<li>✅ Practice approving/rejecting reviews</li>";
        echo "<li>🛡️ Test the verification system</li>";
        echo "<li>📊 View review statistics and analytics</li>";
        echo "</ul>";

        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 15px 0; border-radius: 4px;'>";
        echo "<strong>🔗 Quick Links:</strong><br>";
        echo "• Super Admin Reviews: <a href='http://localhost/buffet_booking_mvc/superadmin/reviews' target='_blank'>http://localhost/buffet_booking_mvc/superadmin/reviews</a><br>";
        echo "• Super Admin Login: <a href='http://localhost/buffet_booking_mvc/superadmin/login' target='_blank'>http://localhost/buffet_booking_mvc/superadmin/login</a>";
        echo "</div>";
    }

} catch (PDOException $e) {
    echo "<h3>❌ Database Connection Error</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration and ensure the buffet_booking database exists.</p>";
} catch (Exception $e) {
    echo "<h3>❌ General Error</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><small>Script completed at " . date('Y-m-d H:i:s') . "</small></p>";
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 40px;
    background: #f8f9fa;
}
h1, h3 {
    color: #333;
}
table {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
