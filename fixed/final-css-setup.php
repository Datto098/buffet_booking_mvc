<?php
/**
 * Final CSS Setup & Verification Script
 * Reinitialize CSS system và đảm bảo mọi thứ hoạt động perfect
 */

require_once 'config/config.php';

$results = [];
$fixes_applied = [];

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>Final CSS Setup</title>\n";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>\n";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>\n";
echo "<link href='" . SITE_URL . "/assets/css/luxury-style.css' rel='stylesheet'>\n";
echo "</head>\n<body class='p-4'>\n";

echo "<h1><i class='fas fa-magic'></i> Final CSS Setup & Verification</h1>\n";

// Step 1: Verify all files exist
echo "<div class='card mb-4'>\n<div class='card-header'><h3>📁 File Verification</h3></div>\n<div class='card-body'>\n";

$critical_files = [
    'Luxury CSS' => __DIR__ . '/assets/css/luxury-style.css',
    'Luxury JS' => __DIR__ . '/assets/js/luxury-effects.js',
    'Header Layout' => __DIR__ . '/views/layouts/header.php',
    'Footer Layout' => __DIR__ . '/views/layouts/footer.php',
    'Home View' => __DIR__ . '/views/customer/home.php',
    'Home Controller' => __DIR__ . '/controllers/HomeController.php'
];

foreach ($critical_files as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    $size = $exists ? filesize($path) : 0;

    echo "<div class='row mb-2'>\n";
    echo "<div class='col-3'><strong>$name:</strong></div>\n";
    echo "<div class='col-9'>";

    if ($exists && $readable && $size > 100) {
        echo "<span class='badge bg-success'>✅ OK</span> " . number_format($size) . " bytes";
        $results[$name] = 'OK';
    } else if ($exists && $size <= 100) {
        echo "<span class='badge bg-warning'>⚠️ TOO SMALL</span> " . $size . " bytes";
        $results[$name] = 'WARNING';
    } else if ($exists) {
        echo "<span class='badge bg-danger'>❌ NOT READABLE</span>";
        $results[$name] = 'ERROR';
    } else {
        echo "<span class='badge bg-danger'>❌ NOT FOUND</span>";
        $results[$name] = 'ERROR';
    }

    echo "</div></div>\n";
}

echo "</div></div>\n";

// Step 2: Verify CSS content
echo "<div class='card mb-4'>\n<div class='card-header'><h3>🎨 CSS Content Verification</h3></div>\n<div class='card-body'>\n";

$cssPath = __DIR__ . '/assets/css/luxury-style.css';
if (file_exists($cssPath)) {
    $cssContent = file_get_contents($cssPath);

    $css_checks = [
        'CSS Variables' => strpos($cssContent, ':root {') !== false,
        'Primary Gold' => strpos($cssContent, '--primary-gold') !== false,
        'Primary Navy' => strpos($cssContent, '--primary-navy') !== false,
        'Font Variables' => strpos($cssContent, 'Playfair Display') !== false,
        'Luxury Classes' => strpos($cssContent, '.luxury-card') !== false,
        'Button Styles' => strpos($cssContent, '.btn-luxury') !== false,
        'Navbar Styles' => strpos($cssContent, '.navbar') !== false
    ];

    foreach ($css_checks as $check => $passed) {
        echo "<div class='row mb-1'>\n";
        echo "<div class='col-4'>$check:</div>\n";
        echo "<div class='col-8'>";
        echo $passed ? "<span class='badge bg-success'>✅ Found</span>" : "<span class='badge bg-danger'>❌ Missing</span>";
        echo "</div></div>\n";
    }

    echo "<div class='mt-3'><strong>Total CSS Size:</strong> " . number_format(strlen($cssContent)) . " bytes</div>\n";
} else {
    echo "<div class='alert alert-danger'>CSS file not found!</div>\n";
}

echo "</div></div>\n";

// Step 3: HTTP Access Test
echo "<div class='card mb-4'>\n<div class='card-header'><h3>🌐 HTTP Access Test</h3></div>\n<div class='card-body'>\n";

$http_tests = [
    'Homepage' => SITE_URL . '/',
    'CSS File' => SITE_URL . '/assets/css/luxury-style.css',
    'JS File' => SITE_URL . '/assets/js/luxury-effects.js',
    'Direct Test' => SITE_URL . '/direct-test.php'
];

foreach ($http_tests as $name => $url) {
    echo "<div class='row mb-2'>\n";
    echo "<div class='col-3'><strong>$name:</strong></div>\n";
    echo "<div class='col-9'>";

    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "<span class='badge bg-success'>✅ 200 OK</span> ";
        echo "<a href='$url' target='_blank'>$url</a>";
    } else {
        echo "<span class='badge bg-danger'>❌ ERROR</span> ";
        echo "<code>$url</code>";
    }

    echo "</div></div>\n";
}

echo "</div></div>\n";

// Step 4: Live CSS Test
echo "<div class='card mb-4'>\n<div class='card-header'><h3>🎯 Live CSS Test</h3></div>\n<div class='card-body'>\n";

echo "<p>If CSS is working properly, you should see luxury styling below:</p>\n";

echo "<div class='row'>\n";
echo "<div class='col-md-4 mb-3'>\n";
echo "<div class='card luxury-card'>\n";
echo "<div class='card-body text-center'>\n";
echo "<i class='fas fa-crown luxury-icon mb-3'></i>\n";
echo "<h5 class='luxury-title'>Luxury Test</h5>\n";
echo "<p class='text-muted'>This should look premium</p>\n";
echo "<button class='btn btn-luxury btn-sm'>Test Button</button>\n";
echo "</div></div></div>\n";

echo "<div class='col-md-4 mb-3'>\n";
echo "<div class='card luxury-card'>\n";
echo "<div class='card-body text-center'>\n";
echo "<i class='fas fa-star luxury-icon mb-3'></i>\n";
echo "<h5 class='luxury-title'>Premium Design</h5>\n";
echo "<p class='text-muted'>Gold and navy colors</p>\n";
echo "<button class='btn btn-outline-luxury btn-sm'>Outline Test</button>\n";
echo "</div></div></div>\n";

echo "<div class='col-md-4 mb-3'>\n";
echo "<div class='card luxury-card'>\n";
echo "<div class='card-body text-center'>\n";
echo "<i class='fas fa-gem luxury-icon mb-3'></i>\n";
echo "<h5 class='luxury-title'>Elite Experience</h5>\n";
echo "<p class='text-muted'>Elegant typography</p>\n";
echo "<button class='btn btn-booking btn-sm'>Booking Test</button>\n";
echo "</div></div></div>\n";
echo "</div>\n";

echo "</div></div>\n";

// Step 5: Configuration Check
echo "<div class='card mb-4'>\n<div class='card-header'><h3>⚙️ Configuration Status</h3></div>\n<div class='card-body'>\n";

$config_items = [
    'SITE_NAME' => defined('SITE_NAME') ? SITE_NAME : 'NOT DEFINED',
    'SITE_URL' => defined('SITE_URL') ? SITE_URL : 'NOT DEFINED',
    'ROOT_PATH' => defined('ROOT_PATH') ? ROOT_PATH : 'NOT DEFINED',
    'PHP Version' => PHP_VERSION,
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
];

foreach ($config_items as $key => $value) {
    echo "<div class='row mb-1'>\n";
    echo "<div class='col-4'><strong>$key:</strong></div>\n";
    echo "<div class='col-8'><code>$value</code></div>\n";
    echo "</div>\n";
}

echo "</div></div>\n";

// Step 6: Quick Action Buttons
echo "<div class='card'>\n<div class='card-header'><h3>🚀 Quick Actions</h3></div>\n<div class='card-body'>\n";

echo "<div class='row'>\n";
echo "<div class='col-md-3 mb-2'>\n";
echo "<a href='" . SITE_URL . "/' class='btn btn-primary w-100' target='_blank'>\n";
echo "<i class='fas fa-home'></i> Test Homepage\n";
echo "</a></div>\n";

echo "<div class='col-md-3 mb-2'>\n";
echo "<a href='" . SITE_URL . "/direct-test.php' class='btn btn-success w-100' target='_blank'>\n";
echo "<i class='fas fa-flask'></i> Direct CSS Test\n";
echo "</a></div>\n";

echo "<div class='col-md-3 mb-2'>\n";
echo "<a href='" . SITE_URL . "/css-loading-test.html' class='btn btn-info w-100' target='_blank'>\n";
echo "<i class='fas fa-vial'></i> CSS Loading Test\n";
echo "</a></div>\n";

echo "<div class='col-md-3 mb-2'>\n";
echo "<a href='" . SITE_URL . "/assets/css/luxury-style.css' class='btn btn-secondary w-100' target='_blank'>\n";
echo "<i class='fas fa-file-code'></i> View CSS File\n";
echo "</a></div>\n";
echo "</div>\n";

echo "</div></div>\n";

echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>\n";
echo "<script src='" . SITE_URL . "/assets/js/luxury-effects.js'></script>\n";
echo "</body>\n</html>\n";
?>
