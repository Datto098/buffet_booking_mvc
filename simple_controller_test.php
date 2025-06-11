<?php
// Simple controller test
echo "Starting controller test...\n";

try {
    require_once 'config/database.php';
    echo "✅ Database config loaded\n";

    require_once 'models/Promotion.php';
    echo "✅ Promotion model loaded\n";
      require_once 'controllers/SuperAdminController.php';
    echo "✅ SuperAdminController loaded\n";

    $database = Database::getInstance();
    $db = $database->getConnection();
    echo "✅ Database connection created\n";

    $controller = new SuperAdminController($db);
    echo "✅ Controller instantiated\n";

    // Check if methods exist
    echo "Methods check:\n";
    echo "- editPromotion exists: " . (method_exists($controller, 'editPromotion') ? 'YES' : 'NO') . "\n";
    echo "- getPromotion exists: " . (method_exists($controller, 'getPromotion') ? 'YES' : 'NO') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
