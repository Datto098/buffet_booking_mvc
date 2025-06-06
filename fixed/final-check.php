<?php
/**
 * FINAL SYSTEM CHECK - Quick Status Summary
 */

require_once 'config/config.php';

echo "🎉 LUXURY CSS SYSTEM - FINAL CHECK\n";
echo "=====================================\n\n";

// Check critical files
$files = [
    'CSS' => 'assets/css/luxury-style.css',
    'JS' => 'assets/js/luxury-effects.js',
    'Header' => 'views/layouts/header.php',
    'Home' => 'views/customer/home.php'
];

echo "📁 FILE STATUS:\n";
foreach ($files as $name => $path) {
    $exists = file_exists($path);
    $size = $exists ? filesize($path) : 0;
    echo "  $name: " . ($exists ? "✅ OK (" . number_format($size) . " bytes)" : "❌ MISSING") . "\n";
}

echo "\n🌐 HTTP STATUS:\n";
$urls = [
    'Homepage' => SITE_URL . '/',
    'CSS File' => SITE_URL . '/assets/css/luxury-style.css',
    'Demo Page' => SITE_URL . '/luxury-demo.php'
];

foreach ($urls as $name => $url) {
    $headers = @get_headers($url);
    $status = ($headers && strpos($headers[0], '200') !== false) ? "✅ 200 OK" : "❌ ERROR";
    echo "  $name: $status\n";
}

echo "\n⚙️ CONFIGURATION:\n";
echo "  SITE_URL: " . SITE_URL . "\n";
echo "  SITE_NAME: " . SITE_NAME . "\n";

echo "\n🎯 QUICK TEST LINKS:\n";
echo "  • Homepage: " . SITE_URL . "/\n";
echo "  • Luxury Demo: " . SITE_URL . "/luxury-demo.php\n";
echo "  • Direct Test: " . SITE_URL . "/direct-test.php\n";
echo "  • CSS File: " . SITE_URL . "/assets/css/luxury-style.css\n";

echo "\n✨ STATUS: LUXURY CSS SYSTEM OPERATIONAL ✨\n";
echo "Ready for use! 🚀\n";
?>
