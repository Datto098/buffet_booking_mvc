<?php
/**
 * Test Admin Login and Order Management
 */
require_once 'config/config.php';
require_once 'models/User.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Testing Admin Login and Order Management</h1>";

// Test 1: Check if we have admin users
$userModel = new User();
$admins = $userModel->findByCondition(['role' => 'manager']);

echo "<h2>1. Available Admin Users:</h2>";
if (empty($admins)) {
    echo "<p style='color: red;'>No admin users found. Creating one...</p>";

    // Create a test admin user
    $adminData = [
        'name' => 'Test Admin',
        'email' => 'admin@buffet.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'manager',
        'phone_number' => '1234567890',
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $adminId = $userModel->create($adminData);
    if ($adminId) {
        echo "<p style='color: green;'>Admin user created with ID: $adminId</p>";
        echo "<p><strong>Login:</strong> admin@buffet.com / admin123</p>";
    } else {
        echo "<p style='color: red;'>Failed to create admin user</p>";
    }
} else {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    foreach ($admins as $admin) {
        $status = $admin['is_active'] ? 'Active' : 'Inactive';
        echo "<tr>";
        echo "<td>{$admin['id']}</td>";
        echo "<td>{$admin['name']}</td>";
        echo "<td>{$admin['email']}</td>";
        echo "<td>{$admin['role']}</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 2: Auto-login as admin
if (!empty($admins)) {
    $admin = $admins[0];

    echo "<h2>2. Auto-Login Test:</h2>";
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['is_logged_in'] = true;

    echo "<p style='color: green;'>Logged in as: {$admin['name']} ({$admin['role']})</p>";
    echo "<p>Session data set:</p>";
    echo "<ul>";
    echo "<li>user_id: {$_SESSION['user_id']}</li>";
    echo "<li>user_role: {$_SESSION['user_role']}</li>";
    echo "<li>user_name: {$_SESSION['user_name']}</li>";
    echo "</ul>";

    echo "<h2>3. Test Links:</h2>";
    echo "<ul>";
    echo "<li><a href='/admin/dashboard' target='_blank'>Admin Dashboard</a></li>";
    echo "<li><a href='/admin/orders' target='_blank'>Order Management</a></li>";
    echo "<li><a href='http://localhost:8000/admin/orders' target='_blank'>Order Management (Full URL)</a></li>";
    echo "</ul>";
}

// Test 3: Check database connection and tables
echo "<h2>4. Database Status:</h2>";
try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>Database connection: Success</p>";

    // Check if orders table exists and has data
    $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
    $result = $stmt->fetch();
    echo "<p>Orders in database: {$result['count']}</p>";

    // Check if order_items table exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM order_items");
    $result = $stmt->fetch();
    echo "<p>Order items in database: {$result['count']}</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Test 4: Test direct controller access
echo "<h2>5. Test Direct Controller Access:</h2>";
try {
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    echo "<p style='color: green;'>AdminController instantiated successfully</p>";

    // Test method existence
    $methods = ['orders', 'orderDetails', 'printOrder', 'exportOrdersCSV'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "<p style='color: green;'>✓ Method $method exists</p>";
        } else {
            echo "<p style='color: red;'>✗ Method $method missing</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Controller error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Click on the 'Order Management' link above</li>";
echo "<li>Test the order details modal</li>";
echo "<li>Test the print functionality</li>";
echo "<li>Test the CSV export</li>";
echo "</ol>";
?>
