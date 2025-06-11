<?php
/**
 * Test Promotion Edit API with proper authentication
 */

// Start session
session_start();

// Include necessary files
require_once 'config/config.php';
require_once 'database/install.php';

echo "<h1>🔐 Testing Promotion Edit API with Authentication</h1>";

try {
    // First, let's simulate a proper login session
    echo "<h2>Step 1: Setting up authentication</h2>";

    // Get database connection
    $db = Database::getInstance()->getConnection();

    // FIRST: Fix the database structure by adding missing application_type column
    echo "<h2>Step 0: Fixing database structure</h2>";

    // Check if application_type column exists
    $stmt = $db->query("SHOW COLUMNS FROM promotions LIKE 'application_type'");
    $columnExists = $stmt->rowCount() > 0;

    if (!$columnExists) {
        echo "<p style='color: orange;'>⚠️ application_type column missing. Adding it now...</p>";

        try {
            // Add the missing column
            $sql = "ALTER TABLE promotions ADD COLUMN application_type ENUM('all', 'specific_items', 'categories') DEFAULT 'all' AFTER type";
            $db->exec($sql);
            echo "<p style='color: green;'>✅ Successfully added application_type column</p>";

            // Update existing promotions
            $updateSql = "UPDATE promotions SET application_type = 'all' WHERE application_type IS NULL";
            $affected = $db->exec($updateSql);
            echo "<p style='color: green;'>✅ Updated $affected existing promotions with default application_type</p>";

        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error adding column: " . $e->getMessage() . "</p>";
            throw $e;
        }
    } else {
        echo "<p style='color: green;'>✅ application_type column already exists</p>";
    }

    // Find or create a super admin user
    $stmt = $db->prepare("SELECT * FROM users WHERE role = 'super_admin' LIMIT 1");
    $stmt->execute();
    $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$superAdmin) {
        echo "<p style='color: orange;'>⚠️ No super admin user found. Creating one...</p>";

        // Create a super admin user
        $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute([
            'Super',
            'Admin',
            'superadmin@buffetbooking.com',
            password_hash('admin123', PASSWORD_DEFAULT),
            'super_admin',
            1
        ]);

        if ($result) {
            $superAdminId = $db->lastInsertId();
            $superAdmin = [
                'id' => $superAdminId,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@buffetbooking.com',
                'role' => 'super_admin',
                'is_active' => 1
            ];
            echo "<p style='color: green;'>✅ Super admin user created successfully!</p>";
        } else {
            throw new Exception("Failed to create super admin user");
        }
    } else {
        echo "<p style='color: green;'>✅ Super admin user found: " . $superAdmin['email'] . "</p>";
    }

    // Set up the session to simulate a logged-in super admin
    $_SESSION['user'] = $superAdmin;
    $_SESSION['user_id'] = $superAdmin['id'];
    $_SESSION['role'] = $superAdmin['role'];
    $_SESSION['is_logged_in'] = true;

    echo "<p style='color: green;'>✅ Session authenticated as super admin</p>";

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    echo "<h2>Step 2: Preparing test data</h2>";

    // Check if we have a test promotion
    $stmt = $db->prepare("SELECT * FROM promotions WHERE id = 1");
    $stmt->execute();
    $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$promotion) {
        echo "<p style='color: orange;'>⚠️ No promotion with ID 1 found. Creating test promotion...</p>";

        // Create a test promotion
        $stmt = $db->prepare("INSERT INTO promotions (name, code, description, type, application_type, discount_value, start_date, end_date, usage_limit, minimum_amount, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute([
            'Test Promotion',
            'TEST10',
            'Test promotion for API testing',
            'percentage',
            'all',
            10.00,
            '2025-06-10',
            '2025-07-10',
            100,
            50.00,
            1
        ]);

        if ($result) {
            echo "<p style='color: green;'>✅ Test promotion created successfully!</p>";
        } else {
            throw new Exception("Failed to create test promotion");
        }
    } else {
        echo "<p style='color: green;'>✅ Test promotion found: " . $promotion['name'] . "</p>";
    }

    echo "<h2>Step 3: Testing promotion edit API</h2>";

    // Prepare test data
    $testData = [
        'promotion_id' => '1',
        'csrf_token' => $_SESSION['csrf_token'],
        'name' => 'Updated Welcome Discount',
        'code' => 'WELCOME15',
        'type' => 'percentage',
        'discount_value' => '15.00',
        'application_type' => 'all',
        'start_date' => '2025-06-10',
        'end_date' => '2025-07-10',
        'usage_limit' => '150',
        'minimum_amount' => '75.00',
        'description' => 'Updated test promotion description'
    ];

    echo "<h3>Test Data:</h3>";
    echo "<pre>" . print_r($testData, true) . "</pre>";

    // Now include the controller and test the method directly
    require_once 'controllers/SuperAdminController.php';

    // Simulate POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = $testData;

    echo "<h3>Testing editPromotion method directly:</h3>";

    // Create controller instance
    $controller = new SuperAdminController();

    // Capture output
    ob_start();

    try {
        $controller->editPromotion(1);
        $output = ob_get_clean();

        echo "<h4>Controller Response:</h4>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";

        // Try to decode as JSON
        $jsonResponse = json_decode($output, true);
        if ($jsonResponse) {
            echo "<h4>Parsed JSON Response:</h4>";
            echo "<pre>" . print_r($jsonResponse, true) . "</pre>";

            if (isset($jsonResponse['success']) && $jsonResponse['success']) {
                echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: Promotion edit API is working!</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>❌ API returned error: " . ($jsonResponse['message'] ?? 'Unknown error') . "</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Response is not valid JSON or is empty</p>";
        }

    } catch (Exception $e) {
        ob_end_clean();
        echo "<p style='color: red;'>❌ Exception occurred: " . $e->getMessage() . "</p>";
        echo "<p>Stack trace:</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

    echo "<h2>Step 4: Checking database after test</h2>";

    // Check if the promotion was updated
    $stmt = $db->prepare("SELECT * FROM promotions WHERE id = 1");
    $stmt->execute();
    $updatedPromotion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($updatedPromotion) {
        echo "<h3>Updated Promotion Data:</h3>";
        echo "<pre>" . print_r($updatedPromotion, true) . "</pre>";

        if ($updatedPromotion['name'] === 'Updated Welcome Discount') {
            echo "<p style='color: green; font-weight: bold;'>✅ Promotion was successfully updated in database!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Promotion data may not have been updated correctly</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>🔗 Test Links</h2>";
echo "<p><a href='http://localhost/buffet_booking_mvc/superadmin/promotions' target='_blank'>📋 Go to Promotions Page</a></p>";
echo "<p><a href='test_promotion_edit_direct.php' target='_blank'>🧪 Test Direct Database Operations</a></p>";

// Check recent error logs
echo "<h2>📋 Recent Error Log Entries</h2>";
$errorLogPath = 'logs/error.log';
if (file_exists($errorLogPath)) {
    $logLines = file($errorLogPath);
    $recentLines = array_slice($logLines, -10); // Last 10 lines
    echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
} else {
    echo "<p>No error log file found at: $errorLogPath</p>";

    // Check PHP error log
    $phpErrorLog = ini_get('error_log');
    if ($phpErrorLog && file_exists($phpErrorLog)) {
        echo "<p>Checking PHP error log: $phpErrorLog</p>";
        $logLines = file($phpErrorLog);
        $recentLines = array_slice($logLines, -10);
        echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
p { margin: 8px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
hr { margin: 30px 0; }
a { color: #007bff; }
</style>
