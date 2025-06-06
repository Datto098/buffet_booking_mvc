<?php
/**
 * Direct Admin Access Test
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

echo "<!DOCTYPE html>";
echo "<html><head><title>Direct Admin Test</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body>";

echo "<div class='container mt-4'>";
echo "<h1>🔍 Direct Admin Access Test</h1>";

// Check current URL and request
echo "<div class='alert alert-info'>";
echo "<h4>Request Information:</h4>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "</p>";
echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'not set') . "</p>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "</p>";
echo "</div>";

// Set up admin session for testing
require_once 'config/config.php';
require_once 'models/User.php';

$userModel = new User();
$admin = $userModel->findByEmail('admin@buffet.com');

if (!$admin) {
    echo "<div class='alert alert-warning'>Creating admin user...</div>";
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
        $admin = $userModel->findByEmail('admin@buffet.com');
        echo "<div class='alert alert-success'>Admin user created!</div>";
    }
}

// Set admin session
if ($admin) {
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['is_logged_in'] = true;
    $_SESSION['user'] = [
        'id' => $admin['id'],
        'name' => $admin['name'],
        'email' => $admin['email'],
        'role' => $admin['role']
    ];

    echo "<div class='alert alert-success'>Admin session established!</div>";
}

// Test different admin access methods
echo "<div class='row mt-4'>";

echo "<div class='col-md-6'>";
echo "<div class='card'>";
echo "<div class='card-header'><h4>Method 1: Direct AdminController</h4></div>";
echo "<div class='card-body'>";

try {
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();

    echo "<div class='alert alert-success'>✅ AdminController loaded successfully</div>";

    echo "<div class='mt-3'>";
    echo "<button onclick='testDashboard()' class='btn btn-primary'>Test Dashboard Method</button>";
    echo "<div id='dashboard-result' class='mt-2'></div>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "</div></div></div>";

echo "<div class='col-md-6'>";
echo "<div class='card'>";
echo "<div class='card-header'><h4>Method 2: Routing Simulation</h4></div>";
echo "<div class='card-body'>";

// Simulate the routing logic
$testUrl = '/admin';
$path = parse_url($testUrl, PHP_URL_PATH);
$segments = array_filter(explode('/', trim($path, '/')));

echo "<p><strong>Testing URL:</strong> $testUrl</p>";
echo "<p><strong>Segments:</strong> " . json_encode($segments) . "</p>";

if (isset($segments[0]) && $segments[0] === 'admin') {
    echo "<div class='alert alert-info'>✅ Admin route detected</div>";

    // Check authentication
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['manager', 'super_admin'])) {
        echo "<div class='alert alert-danger'>❌ Authentication failed</div>";
    } else {
        echo "<div class='alert alert-success'>✅ Authentication passed</div>";

        if (count($segments) === 1) {
            echo "<div class='alert alert-info'>Would call dashboard() method</div>";

            echo "<button onclick='testRouting()' class='btn btn-success'>Test Routing</button>";
            echo "<div id='routing-result' class='mt-2'></div>";
        }
    }
}

echo "</div></div></div>";
echo "</div>";

// JavaScript for testing
echo "<script>";
echo "function testDashboard() {";
echo "  document.getElementById('dashboard-result').innerHTML = '<div class=\"alert alert-info\">Testing dashboard...</div>';";
echo "  fetch('?action=test_dashboard')";
echo "    .then(response => response.text())";
echo "    .then(data => {";
echo "      document.getElementById('dashboard-result').innerHTML = '<div class=\"alert alert-success\">Dashboard test completed!</div>';";
echo "    })";
echo "    .catch(error => {";
echo "      document.getElementById('dashboard-result').innerHTML = '<div class=\"alert alert-danger\">Error: ' + error + '</div>';";
echo "    });";
echo "}";

echo "function testRouting() {";
echo "  document.getElementById('routing-result').innerHTML = '<div class=\"alert alert-info\">Testing routing...</div>';";
echo "  window.open('/buffet_booking_mvc/admin', '_blank');";
echo "  document.getElementById('routing-result').innerHTML = '<div class=\"alert alert-success\">Routing test opened in new tab</div>';";
echo "}";
echo "</script>";

// Handle AJAX requests
if (isset($_GET['action']) && $_GET['action'] === 'test_dashboard') {
    if (isset($controller)) {
        ob_start();
        try {
            $controller->dashboard();
            $output = ob_get_clean();
            echo "<div class='alert alert-success'>Dashboard method executed successfully! Output length: " . strlen($output) . " characters</div>";
        } catch (Exception $e) {
            ob_get_clean();
            echo "<div class='alert alert-danger'>Dashboard error: " . $e->getMessage() . "</div>";
        }
    }
    exit;
}

// Add manual test buttons
echo "<div class='card mt-4'>";
echo "<div class='card-header'><h4>Manual URL Tests</h4></div>";
echo "<div class='card-body'>";
echo "<p>Test these URLs manually:</p>";
echo "<div class='d-grid gap-2 d-md-block'>";
echo "<a href='http://localhost/buffet_booking_mvc/admin' target='_blank' class='btn btn-primary'>http://localhost/buffet_booking_mvc/admin</a> ";
echo "<a href='http://localhost/buffet_booking_mvc/admin/' target='_blank' class='btn btn-warning'>http://localhost/buffet_booking_mvc/admin/</a> ";
echo "<a href='http://localhost/buffet_booking_mvc/admin/index.php' target='_blank' class='btn btn-info'>http://localhost/buffet_booking_mvc/admin/index.php</a>";
echo "</div>";
echo "</div></div>";

echo "</div></body></html>";
?>
