<?php
/**
 * Test SuperAdmin Controller for debugging
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Promotion.php';

class TestSuperAdminController extends BaseController
{
    private $promotionModel;

    public function __construct()
    {
        // Skip authentication for testing
        $this->promotionModel = new Promotion();
    }

    public function getPromotion($id)
    {
        try {
            // Debug: Log request details
            error_log("TEST getPromotion called with ID: " . $id);

            // GET request - return promotion data for editing
            $promotion = $this->promotionModel->findById($id);
            error_log("TEST Promotion found: " . ($promotion ? 'YES' : 'NO'));

            if ($promotion) {
                // Lấy thêm thông tin food items và categories
                $promotion['food_items'] = $this->promotionModel->getFoodItemIds($id);
                $promotion['categories'] = $this->promotionModel->getCategoryIds($id);

                error_log("TEST Food items count: " . count($promotion['food_items']));
                error_log("TEST Categories count: " . count($promotion['categories']));

                $this->jsonResponse(['success' => true, 'promotion' => $promotion]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Promotion not found.'], 404);
            }
        } catch (Exception $e) {
            error_log("TEST Error in getPromotion: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
}

// Direct test
echo "<h1>Direct Test of getPromotion</h1>";

try {
    $controller = new TestSuperAdminController();
    echo "<p>✅ Controller created successfully</p>";

    $promotionId = 1;
    echo "<p>Testing with promotion ID: $promotionId</p>";

    ob_start();
    $controller->getPromotion($promotionId);
    $output = ob_get_clean();

    echo "<h3>Raw Output:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";

    $jsonData = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h3>✅ Valid JSON Response:</h3>";
        echo "<pre>" . print_r($jsonData, true) . "</pre>";

        if ($jsonData['success'] && isset($jsonData['promotion'])) {
            echo "<h3>✅ Promotion Data Retrieved Successfully</h3>";
            $promo = $jsonData['promotion'];
            echo "<ul>";
            echo "<li>ID: " . $promo['id'] . "</li>";
            echo "<li>Name: " . $promo['name'] . "</li>";
            echo "<li>Code: " . $promo['code'] . "</li>";
            echo "<li>Application Type: " . ($promo['application_type'] ?? 'all') . "</li>";
            echo "<li>Food Items: " . count($promo['food_items']) . "</li>";
            echo "<li>Categories: " . count($promo['categories']) . "</li>";
            echo "</ul>";
        }
    } else {
        echo "<h3>❌ JSON Decode Error: " . json_last_error_msg() . "</h3>";
    }

} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>Message: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
ul { margin: 10px 0; }
</style>
