<?php
// Debug the routing system
session_start();

// Set the session to simulate admin login
$_SESSION['user_id'] = 4;
$_SESSION['user_role'] = 'super_admin';
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

echo "<h1>Routing Debug</h1>";

// Simulate the admin/orders request
$uri = '/admin/orders';
echo "<p>Testing URI: $uri</p>";

$uri = rtrim($uri, '/');
$segments = array_filter(explode('/', $uri));
$segments = array_values($segments);

echo "<h2>URI Segments:</h2>";
echo "<pre>";
print_r($segments);
echo "</pre>";

$page = $segments[0] ?? 'home';
echo "<p>Page: $page</p>";

if ($page === 'admin') {
    echo "<p>✓ Admin route detected</p>";

    // Check authentication
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['manager', 'super_admin'])) {
        echo "<p>✗ Authentication failed</p>";
        echo "<p>Session user: " . ($_SESSION['user']['role'] ?? 'not set') . "</p>";
    } else {
        echo "<p>✓ Authentication passed</p>";

        $section = $segments[1] ?? 'dashboard';
        echo "<p>Section: $section</p>";

        if ($section === 'orders') {
            echo "<p>✓ Orders section detected</p>";

            require_once 'controllers/AdminController.php';

            try {
                $controller = new AdminController();
                echo "<p>✓ AdminController instantiated</p>";

                // Try calling the orders method
                echo "<h2>Testing orders() method:</h2>";
                ob_start();
                $controller->orders();
                $output = ob_get_clean();

                if (strlen($output) > 0) {
                    echo "<p>✓ Orders method executed successfully</p>";
                    echo "<p>Output length: " . strlen($output) . " characters</p>";

                    // Show first 500 characters
                    echo "<details><summary>Output Sample</summary>";
                    echo "<pre>" . htmlspecialchars(substr($output, 0, 500)) . "...</pre>";
                    echo "</details>";
                } else {
                    echo "<p>✗ Orders method produced no output</p>";
                }

            } catch (Exception $e) {
                echo "<p>✗ Error instantiating AdminController: " . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        } else {
            echo "<p>✗ Not orders section</p>";
        }
    }
} else {
    echo "<p>✗ Not admin route</p>";
}
?>
