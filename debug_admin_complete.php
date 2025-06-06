<?php
/**
 * Debug Admin Access - Comprehensive Test
 */

// Start session
session_start();

// Include config
require_once 'config/config.php';
require_once 'models/User.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug Admin Access</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>.debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }</style>";
echo "</head><body>";

echo "<div class='container mt-4'>";
echo "<h1>🔍 Debug Admin Access</h1>";

// Test 1: Check admin user exists
echo "<div class='debug-section'>";
echo "<h3>1. Admin User Check</h3>";
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
        echo "<div class='alert alert-success'>✅ Admin created with ID: $adminId</div>";
        $admin = $userModel->findByEmail('admin@buffet.com');
    }
}

if ($admin) {
    echo "<div class='alert alert-info'>";
    echo "<strong>Admin User Found:</strong><br>";
    echo "ID: {$admin['id']}<br>";
    echo "Email: {$admin['email']}<br>";
    echo "Role: {$admin['role']}<br>";
    echo "Active: " . ($admin['is_active'] ? 'Yes' : 'No');
    echo "</div>";
}
echo "</div>";

// Test 2: Set session and test routing
echo "<div class='debug-section'>";
echo "<h3>2. Session Setup</h3>";

if (isset($_GET['login'])) {
    // Set admin session
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

    echo "<div class='alert alert-success'>✅ Admin session set successfully!</div>";
    echo "<script>window.location.href = '?';</script>";
} else {
    if (isset($_SESSION['user_id'])) {
        echo "<div class='alert alert-success'>";
        echo "<strong>Current Session:</strong><br>";
        echo "User ID: {$_SESSION['user_id']}<br>";
        echo "Role: {$_SESSION['user_role']}<br>";
        echo "Name: {$_SESSION['user_name']}<br>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>No active session</div>";
        echo "<a href='?login=1' class='btn btn-primary'>Set Admin Session</a>";
    }
}
echo "</div>";

// Test 3: Test routing logic
echo "<div class='debug-section'>";
echo "<h3>3. Routing Test</h3>";

if (isset($_SESSION['user_id'])) {
    // Test admin authentication check
    $authCheck = isset($_SESSION['user_id']) && in_array($_SESSION['user_role'], ['manager', 'super_admin']);

    if ($authCheck) {
        echo "<div class='alert alert-success'>✅ Admin authentication would PASS</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Admin authentication would FAIL</div>";
        echo "Current role: " . ($_SESSION['user_role'] ?? 'not set');
    }

    // Test AdminController instantiation
    try {
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        echo "<div class='alert alert-success'>✅ AdminController can be instantiated</div>";

        // Test dashboard method
        if (method_exists($controller, 'dashboard')) {
            echo "<div class='alert alert-success'>✅ Dashboard method exists</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Dashboard method missing</div>";
        }

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ AdminController error: " . $e->getMessage() . "</div>";
    }
}
echo "</div>";

// Test 4: Manual routing simulation
echo "<div class='debug-section'>";
echo "<h3>4. Manual Routing Simulation</h3>";

if (isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-info'>Testing manual admin route...</div>";

    $url = '/admin';
    $path = parse_url($url, PHP_URL_PATH);
    $segments = array_filter(explode('/', trim($path, '/')));

    echo "<p><strong>URL:</strong> $url</p>";
    echo "<p><strong>Path:</strong> $path</p>";
    echo "<p><strong>Segments:</strong> " . implode(', ', $segments) . "</p>";

    if (isset($segments[0]) && $segments[0] === 'admin') {
        echo "<div class='alert alert-success'>✅ Admin route detected</div>";

        // Simulate handleAdminRoute
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['manager', 'super_admin'])) {
            echo "<div class='alert alert-danger'>❌ Would redirect to login</div>";
        } else {
            echo "<div class='alert alert-success'>✅ Would proceed to AdminController</div>";

            if (count($segments) === 1) {
                echo "<div class='alert alert-info'>Would call dashboard() method</div>";
            }
        }
    }
}
echo "</div>";

// Test 5: Test actual URLs
echo "<div class='debug-section'>";
echo "<h3>5. URL Tests</h3>";

if (isset($_SESSION['user_id'])) {
    echo "<div class='row'>";
    echo "<div class='col-md-4'>";
    echo "<h5>Direct Admin Directory:</h5>";
    echo "<a href='/buffet_booking_mvc/admin/' target='_blank' class='btn btn-primary btn-sm'>Test /admin/</a>";
    echo "<p class='small mt-1'>Should go through admin/index.php</p>";
    echo "</div>";

    echo "<div class='col-md-4'>";
    echo "<h5>Admin Route:</h5>";
    echo "<a href='/buffet_booking_mvc/admin' target='_blank' class='btn btn-success btn-sm'>Test /admin</a>";
    echo "<p class='small mt-1'>Should go through main routing</p>";
    echo "</div>";

    echo "<div class='col-md-4'>";
    echo "<h5>Main Index:</h5>";
    echo "<a href='/buffet_booking_mvc/' target='_blank' class='btn btn-info btn-sm'>Test Main Site</a>";
    echo "<p class='small mt-1'>Should load homepage</p>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='alert alert-warning'>Please set admin session first to test URLs</div>";
}
echo "</div>";

// Test 6: File existence check
echo "<div class='debug-section'>";
echo "<h3>6. File Existence Check</h3>";

$files = [
    'admin/index.php' => file_exists('admin/index.php'),
    'controllers/AdminController.php' => file_exists('controllers/AdminController.php'),
    'views/admin/dashboard.php' => file_exists('views/admin/dashboard.php'),
    'config/config.php' => file_exists('config/config.php'),
    '.htaccess' => file_exists('.htaccess')
];

foreach ($files as $file => $exists) {
    if ($exists) {
        echo "<div class='alert alert-success'>✅ $file exists</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ $file missing</div>";
    }
}
echo "</div>";

// Test 7: .htaccess check
echo "<div class='debug-section'>";
echo "<h3>7. URL Rewriting Check</h3>";

if (file_exists('.htaccess')) {
    $htaccess = file_get_contents('.htaccess');
    echo "<div class='alert alert-info'>";
    echo "<strong>.htaccess content:</strong><br>";
    echo "<pre>" . htmlspecialchars($htaccess) . "</pre>";
    echo "</div>";
} else {
    echo "<div class='alert alert-warning'>.htaccess file not found - URL rewriting may not work</div>";
}
echo "</div>";

echo "</div></body></html>";
?>
