<?php
/**
 * Test Order Management with Session
 */
require_once 'config/config.php';
require_once 'models/User.php';
require_once 'models/Order.php';

// Start session and set up admin login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Order Management Test</h1>";

// Set up admin session
$userModel = new User();
$admins = $userModel->findByCondition(['role' => 'manager']);

if (!empty($admins)) {
    $admin = $admins[0];
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];

    echo "<p style='color: green;'>Admin session established: {$admin['name']} ({$admin['role']})</p>";
} else {
    echo "<p style='color: red;'>No admin users found. Please run test_admin_login.php first.</p>";
    exit;
}

// Test AdminController directly
echo "<h2>Testing AdminController Methods:</h2>";

try {
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    echo "<p style='color: green;'>✓ AdminController loaded successfully</p>";

    // Test 1: Basic orders method
    echo "<h3>1. Testing orders() method:</h3>";
    ob_start();
    try {
        $controller->orders();
        $output = ob_get_contents();
        ob_end_clean();
        echo "<p style='color: green;'>✓ orders() method executed successfully</p>";
        echo "<p>Output length: " . strlen($output) . " characters</p>";

        // Show first 500 characters of output
        if (strlen($output) > 0) {
            echo "<details><summary>View output sample</summary>";
            echo "<pre>" . htmlspecialchars(substr($output, 0, 500)) . "...</pre>";
            echo "</details>";
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo "<p style='color: red;'>✗ orders() method failed: " . $e->getMessage() . "</p>";
    }

    // Test 2: Check if we have orders in database
    echo "<h3>2. Testing order data:</h3>";
    $orderModel = new Order();
    $orders = $orderModel->findAll(5);

    if (empty($orders)) {
        echo "<p style='color: orange;'>No orders found in database. Creating sample order...</p>";

        // Create a sample order
        $sampleOrder = [
            'user_id' => $admin['id'],
            'address_id' => 1, // Assuming address exists
            'total_amount' => 29.99,
            'status' => 'pending',
            'payment_method' => 'card',
            'notes' => 'Test order for admin interface',
            'items' => [
                [
                    'food_item_id' => 1,
                    'quantity' => 2,
                    'price' => 14.99
                ]
            ]
        ];

        try {
            $orderId = $orderModel->createOrder($sampleOrder);
            echo "<p style='color: green;'>✓ Sample order created with ID: $orderId</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Failed to create sample order: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Found " . count($orders) . " orders in database</p>";

        // Test order details for first order
        $firstOrder = $orders[0];
        echo "<h3>3. Testing orderDetails() method with Order ID: {$firstOrder['id']}</h3>";

        ob_start();
        try {
            $controller->orderDetails($firstOrder['id']);
            $output = ob_get_contents();
            ob_end_clean();
            echo "<p style='color: green;'>✓ orderDetails() method executed successfully</p>";
            echo "<p>Output length: " . strlen($output) . " characters</p>";

            if (strlen($output) > 0) {
                echo "<details><summary>View order details output</summary>";
                echo "<div style='border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;'>";
                echo $output;
                echo "</div></details>";
            }
        } catch (Exception $e) {
            ob_end_clean();
            echo "<p style='color: red;'>✗ orderDetails() method failed: " . $e->getMessage() . "</p>";
        }
    }

    // Test 3: CSV Export
    echo "<h3>4. Testing CSV export:</h3>";
    try {
        // Capture CSV output
        ob_start();
        $controller->exportOrdersCSV();
        $csvOutput = ob_get_contents();
        ob_end_clean();

        if (strlen($csvOutput) > 0) {
            echo "<p style='color: green;'>✓ CSV export generated successfully</p>";
            echo "<p>CSV length: " . strlen($csvOutput) . " characters</p>";

            // Show first few lines
            $lines = explode("\n", $csvOutput);
            echo "<details><summary>View CSV sample</summary>";
            echo "<pre>";
            for ($i = 0; $i < min(5, count($lines)); $i++) {
                echo htmlspecialchars($lines[$i]) . "\n";
            }
            echo "</pre></details>";
        } else {
            echo "<p style='color: orange;'>CSV export returned empty output</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ CSV export failed: " . $e->getMessage() . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Failed to load AdminController: " . $e->getMessage() . "</p>";
}

// Test 4: Direct URL access
echo "<h2>5. URL Test Links:</h2>";
echo "<p>Session is set up. Try these links:</p>";
echo "<ul>";
echo "<li><a href='/admin/dashboard' target='_blank'>Admin Dashboard</a></li>";
echo "<li><a href='/admin/orders' target='_blank'>Order Management</a></li>";
echo "<li><a href='/admin/orders/export-csv' target='_blank'>CSV Export</a></li>";
echo "</ul>";

echo "<h2>6. Browser Test:</h2>";
echo "<p>Click the button below to open the admin orders page in a new tab:</p>";
echo "<button onclick=\"window.open('/admin/orders', '_blank')\">Open Order Management</button>";

echo "<script>";
echo "console.log('Session data:', " . json_encode($_SESSION) . ");";
echo "</script>";
?>
