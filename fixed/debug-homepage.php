<?php
/**
 * Debug Homepage Routing
 * Kiểm tra tại sao trang chính không load CSS đúng cách
 */

// Include config
require_once 'config/config.php';

echo "<!DOCTYPE html>\n";
echo "<html>\n<head>\n";
echo "<title>Debug Homepage</title>\n";
echo "<style>body { font-family: Arial; padding: 20px; }</style>\n";
echo "</head>\n<body>\n";

echo "<h1>🔍 Debug Homepage Routing</h1>\n";

echo "<h2>📋 Configuration Check</h2>\n";
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>\n";
echo "<strong>SITE_NAME:</strong> " . (defined('SITE_NAME') ? SITE_NAME : 'NOT DEFINED') . "<br>\n";
echo "<strong>SITE_URL:</strong> " . (defined('SITE_URL') ? SITE_URL : 'NOT DEFINED') . "<br>\n";
echo "<strong>ROOT_PATH:</strong> " . (defined('ROOT_PATH') ? ROOT_PATH : 'NOT DEFINED') . "<br>\n";
echo "</div>\n";

echo "<h2>📁 File Check</h2>\n";
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>\n";

$files_to_check = [
    'CSS File' => __DIR__ . '/assets/css/luxury-style.css',
    'JS File' => __DIR__ . '/assets/js/luxury-effects.js',
    'Header File' => __DIR__ . '/views/layouts/header.php',
    'Home View' => __DIR__ . '/views/customer/home.php',
    'Home Controller' => __DIR__ . '/controllers/HomeController.php'
];

foreach ($files_to_check as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    $size = $exists ? filesize($path) : 0;

    echo "<strong>$name:</strong> ";
    if ($exists && $readable) {
        echo "✅ OK (" . number_format($size) . " bytes)";
    } elseif ($exists) {
        echo "⚠️  Exists but not readable";
    } else {
        echo "❌ NOT FOUND - $path";
    }
    echo "<br>\n";
}
echo "</div>\n";

echo "<h2>🌐 HTTP Access Check</h2>\n";
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>\n";

$urls_to_check = [
    'CSS URL' => SITE_URL . '/assets/css/luxury-style.css',
    'JS URL' => SITE_URL . '/assets/js/luxury-effects.js',
    'Homepage' => SITE_URL . '/',
    'Direct Test' => SITE_URL . '/direct-test.php'
];

foreach ($urls_to_check as $name => $url) {
    echo "<strong>$name:</strong> ";
    echo "<a href='$url' target='_blank'>$url</a>";

    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo " ✅ Accessible";
    } else {
        echo " ❌ Not accessible";
    }
    echo "<br>\n";
}
echo "</div>\n";

echo "<h2>🔄 Simulate Homepage Request</h2>\n";
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>\n";

try {
    // Simulate what happens when accessing homepage
    $_SERVER['REQUEST_URI'] = '/';

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/');

    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath !== '/') {
        $uri = substr($uri, strlen($basePath));
    }

    $segments = array_filter(explode('/', $uri));
    $segments = array_values($segments);

    if (empty($segments)) {
        $segments = ['home'];
    }

    echo "<strong>URI Processing:</strong><br>\n";
    echo "- Original URI: " . $_SERVER['REQUEST_URI'] . "<br>\n";
    echo "- Processed URI: $uri<br>\n";
    echo "- Base Path: $basePath<br>\n";
    echo "- Segments: " . implode(', ', $segments) . "<br>\n";

    // Check if HomeController exists and has index method
    if (file_exists(__DIR__ . '/controllers/HomeController.php')) {
        require_once __DIR__ . '/controllers/HomeController.php';

        if (class_exists('HomeController')) {
            $controller = new HomeController();
            if (method_exists($controller, 'index')) {
                echo "<strong>Controller Check:</strong> ✅ HomeController::index() exists<br>\n";
            } else {
                echo "<strong>Controller Check:</strong> ❌ HomeController::index() method not found<br>\n";
            }
        } else {
            echo "<strong>Controller Check:</strong> ❌ HomeController class not found<br>\n";
        }
    } else {
        echo "<strong>Controller Check:</strong> ❌ HomeController.php file not found<br>\n";
    }

} catch (Exception $e) {
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>\n";
}

echo "</div>\n";

echo "<h2>🎨 CSS Content Check</h2>\n";
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>\n";

$cssPath = __DIR__ . '/assets/css/luxury-style.css';
if (file_exists($cssPath)) {
    $cssContent = file_get_contents($cssPath);
    $hasGoldVariable = strpos($cssContent, '--primary-gold') !== false;
    $hasNavyVariable = strpos($cssContent, '--primary-navy') !== false;
    $hasFonts = strpos($cssContent, 'Playfair Display') !== false;

    echo "<strong>CSS Variables:</strong><br>\n";
    echo "- Primary Gold: " . ($hasGoldVariable ? "✅ Found" : "❌ Missing") . "<br>\n";
    echo "- Primary Navy: " . ($hasNavyVariable ? "✅ Found" : "❌ Missing") . "<br>\n";
    echo "- Fonts: " . ($hasFonts ? "✅ Found" : "❌ Missing") . "<br>\n";
    echo "- File Size: " . number_format(strlen($cssContent)) . " bytes<br>\n";
} else {
    echo "<strong>CSS File:</strong> ❌ Not found<br>\n";
}

echo "</div>\n";

echo "<h2>🔧 Quick Fixes</h2>\n";
echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border: 1px solid #ffeaa7;'>\n";
echo "<strong>Try these links to test different approaches:</strong><br>\n";
echo "1. <a href='direct-test.php'>Direct CSS Test</a> (bypass routing)<br>\n";
echo "2. <a href='header-test.php'>Header Only Test</a><br>\n";
echo "3. <a href='complete-css-test.php'>Complete System Test</a><br>\n";
echo "4. <a href='assets/css/luxury-style.css'>Direct CSS File</a><br>\n";
echo "5. <a href='/'>Homepage (through routing)</a><br>\n";
echo "</div>\n";

echo "</body>\n</html>\n";
?>
