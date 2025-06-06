<?php
/**
 * CSS Debug & Fix Script
 * Kiểm tra và sửa tất cả vấn đề CSS
 */

echo "<!DOCTYPE html>\n";
echo "<html lang='vi'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>CSS Debug & Fix - Sửa Lỗi CSS</title>\n";
echo "    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>\n";
echo "    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>\n";
echo "    <style>\n";
echo "        .debug-section { margin: 2rem 0; padding: 1.5rem; border-radius: 10px; }\n";
echo "        .debug-success { background: #d4edda; border: 1px solid #c3e6cb; }\n";
echo "        .debug-error { background: #f8d7da; border: 1px solid #f5c6cb; }\n";
echo "        .debug-warning { background: #fff3cd; border: 1px solid #ffeaa7; }\n";
echo "        .file-check { padding: 0.5rem; margin: 0.25rem 0; border-radius: 5px; }\n";
echo "        .file-exists { background: #d4edda; }\n";
echo "        .file-missing { background: #f8d7da; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";

$base_path = __DIR__;
$server_url = 'http://localhost:8080';

echo "<div class='container mt-4'>\n";
echo "    <h1 class='text-center mb-4'>\n";
echo "        <i class='fas fa-bug me-3'></i>CSS Debug & Fix\n";
echo "    </h1>\n";

// 1. Check config file
echo "    <div class='debug-section debug-success'>\n";
echo "        <h3><i class='fas fa-cog me-2'></i>1. Config Check</h3>\n";

if (file_exists('config/config.php')) {
    include_once 'config/config.php';
    echo "        <div class='file-check file-exists'>\n";
    echo "            <strong>✅ Config file found:</strong><br>\n";
    echo "            SITE_NAME: " . SITE_NAME . "<br>\n";
    echo "            SITE_URL: " . SITE_URL . "<br>\n";
    if (SITE_URL !== $server_url) {
        echo "            <div class='alert alert-warning mt-2'>\n";
        echo "                ⚠️ SITE_URL ({SITE_URL}) khác với server URL ({$server_url})\n";
        echo "            </div>\n";
    }
    echo "        </div>\n";
} else {
    echo "        <div class='file-check file-missing'>\n";
    echo "            ❌ Config file không tìm thấy!\n";
    echo "        </div>\n";
}

echo "    </div>\n";

// 2. Check CSS files
echo "    <div class='debug-section debug-success'>\n";
echo "        <h3><i class='fas fa-file-code me-2'></i>2. CSS Files Check</h3>\n";

$css_files = [
    'assets/css/luxury-style.css' => 'Luxury Style CSS',
    'assets/css/style.css' => 'Main Style CSS',
    'assets/css/admin.css' => 'Admin CSS'
];

foreach ($css_files as $path => $name) {
    $full_path = $base_path . '/' . $path;
    if (file_exists($full_path)) {
        $size = filesize($full_path);
        $readable = is_readable($full_path);
        $status = $readable ? 'file-exists' : 'file-missing';
        $icon = $readable ? '✅' : '❌';

        echo "        <div class='file-check {$status}'>\n";
        echo "            {$icon} <strong>{$name}</strong><br>\n";
        echo "            Path: {$path}<br>\n";
        echo "            Size: " . number_format($size) . " bytes<br>\n";
        echo "            Readable: " . ($readable ? 'Yes' : 'No') . "<br>\n";
        echo "            <a href='{$path}' target='_blank' class='btn btn-sm btn-outline-primary mt-1'>\n";
        echo "                <i class='fas fa-external-link-alt me-1'></i>Test Link\n";
        echo "            </a>\n";
        echo "        </div>\n";
    } else {
        echo "        <div class='file-check file-missing'>\n";
        echo "            ❌ <strong>{$name}</strong> - File không tồn tại!<br>\n";
        echo "            Expected path: {$path}\n";
        echo "        </div>\n";
    }
}

echo "    </div>\n";

// 3. Check JS files
echo "    <div class='debug-section debug-success'>\n";
echo "        <h3><i class='fas fa-code me-2'></i>3. JavaScript Files Check</h3>\n";

$js_files = [
    'assets/js/luxury-effects.js' => 'Luxury Effects JS',
    'assets/js/main.js' => 'Main JS',
    'assets/js/admin.js' => 'Admin JS'
];

foreach ($js_files as $path => $name) {
    $full_path = $base_path . '/' . $path;
    if (file_exists($full_path)) {
        $size = filesize($full_path);
        $readable = is_readable($full_path);
        $status = $readable ? 'file-exists' : 'file-missing';
        $icon = $readable ? '✅' : '❌';

        echo "        <div class='file-check {$status}'>\n";
        echo "            {$icon} <strong>{$name}</strong><br>\n";
        echo "            Path: {$path}<br>\n";
        echo "            Size: " . number_format($size) . " bytes<br>\n";
        echo "            <a href='{$path}' target='_blank' class='btn btn-sm btn-outline-primary mt-1'>\n";
        echo "                <i class='fas fa-external-link-alt me-1'></i>Test Link\n";
        echo "            </a>\n";
        echo "        </div>\n";
    } else {
        echo "        <div class='file-check file-missing'>\n";
        echo "            ❌ <strong>{$name}</strong> - File không tồn tại!\n";
        echo "        </div>\n";
    }
}

echo "    </div>\n";

// 4. Check header.php
echo "    <div class='debug-section debug-success'>\n";
echo "        <h3><i class='fas fa-file-alt me-2'></i>4. Header Template Check</h3>\n";

$header_path = 'views/layouts/header.php';
if (file_exists($header_path)) {
    $header_content = file_get_contents($header_path);
    echo "        <div class='file-check file-exists'>\n";
    echo "            ✅ <strong>Header template found</strong><br>\n";

    // Check for CSS links
    $css_patterns = [
        'luxury-style.css' => preg_match('/luxury-style\.css/', $header_content),
        'style.css' => preg_match('/style\.css/', $header_content),
        'Bootstrap' => preg_match('/bootstrap.*\.css/', $header_content),
        'Font Awesome' => preg_match('/font-awesome|fontawesome/', $header_content),
        'Google Fonts' => preg_match('/fonts\.googleapis\.com/', $header_content)
    ];

    echo "            <strong>CSS Links in header:</strong><br>\n";
    foreach ($css_patterns as $name => $found) {
        $icon = $found ? '✅' : '❌';
        echo "            {$icon} {$name}<br>\n";
    }

    echo "        </div>\n";
} else {
    echo "        <div class='file-check file-missing'>\n";
    echo "            ❌ Header template không tìm thấy!\n";
    echo "        </div>\n";
}

echo "    </div>\n";

// 5. Live URL Tests
echo "    <div class='debug-section debug-warning'>\n";
echo "        <h3><i class='fas fa-globe me-2'></i>5. Live URL Tests</h3>\n";

$test_urls = [
    $server_url => 'Main Site',
    $server_url . '/assets/css/luxury-style.css' => 'Luxury CSS',
    $server_url . '/assets/css/style.css' => 'Main CSS',
    $server_url . '/assets/js/luxury-effects.js' => 'Luxury JS',
    $server_url . '/test-header.html' => 'Header Test Page',
    $server_url . '/css-test.html' => 'CSS Test Page'
];

foreach ($test_urls as $url => $name) {
    echo "        <div class='file-check'>\n";
    echo "            <strong>{$name}:</strong><br>\n";
    echo "            <a href='{$url}' target='_blank' class='btn btn-sm btn-primary'>\n";
    echo "                <i class='fas fa-external-link-alt me-1'></i>{$url}\n";
    echo "            </a>\n";
    echo "        </div>\n";
}

echo "    </div>\n";

// 6. Quick fixes
echo "    <div class='debug-section debug-warning'>\n";
echo "        <h3><i class='fas fa-wrench me-2'></i>6. Quick Fixes</h3>\n";
echo "        <p>Nếu CSS không load, thử các bước sau:</p>\n";
echo "        <ol>\n";
echo "            <li><strong>Hard Refresh:</strong> Ctrl + F5 hoặc Ctrl + Shift + R</li>\n";
echo "            <li><strong>Clear Cache:</strong> Xóa cache browser</li>\n";
echo "            <li><strong>Check Network Tab:</strong> F12 → Network → reload page</li>\n";
echo "            <li><strong>Check SITE_URL:</strong> Đảm bảo SITE_URL đúng với port server</li>\n";
echo "        </ol>\n";

echo "        <div class='mt-3'>\n";
echo "            <button onclick='location.reload(true)' class='btn btn-primary me-2'>\n";
echo "                <i class='fas fa-redo me-1'></i>Hard Refresh\n";
echo "            </button>\n";
echo "            <button onclick='testCSS()' class='btn btn-success me-2'>\n";
echo "                <i class='fas fa-flask me-1'></i>Test CSS Loading\n";
echo "            </button>\n";
echo "        </div>\n";
echo "    </div>\n";

echo "    <div class='debug-section debug-success'>\n";
echo "        <h3><i class='fas fa-info-circle me-2'></i>7. System Info</h3>\n";
echo "        <div class='file-check file-exists'>\n";
echo "            <strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "<br>\n";
echo "            <strong>PHP Version:</strong> " . PHP_VERSION . "<br>\n";
echo "            <strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'PHP Built-in Server') . "<br>\n";
echo "            <strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? __DIR__) . "<br>\n";
echo "            <strong>Script Path:</strong> " . __FILE__ . "<br>\n";
echo "        </div>\n";
echo "    </div>\n";

echo "</div>\n";

echo "<script>\n";
echo "function testCSS() {\n";
echo "    const testDiv = document.createElement('div');\n";
echo "    testDiv.className = 'navbar';\n";
echo "    testDiv.style.position = 'absolute';\n";
echo "    testDiv.style.visibility = 'hidden';\n";
echo "    document.body.appendChild(testDiv);\n";
echo "    \n";
echo "    const styles = window.getComputedStyle(testDiv);\n";
echo "    const hasBackdrop = styles.backdropFilter && styles.backdropFilter.includes('blur');\n";
echo "    \n";
echo "    document.body.removeChild(testDiv);\n";
echo "    \n";
echo "    if (hasBackdrop) {\n";
echo "        alert('✅ Luxury CSS đã load thành công!');\n";
echo "    } else {\n";
echo "        alert('❌ Luxury CSS chưa load hoặc không có backdrop-filter!');\n";
echo "    }\n";
echo "}\n";
echo "\n";
echo "// Auto-check on load\n";
echo "window.addEventListener('load', function() {\n";
echo "    console.log('=== CSS Debug Info ===');\n";
echo "    console.log('Current URL:', window.location.href);\n";
echo "    console.log('Base URL:', window.location.origin);\n";
echo "    \n";
echo "    // Check if stylesheets loaded\n";
echo "    const stylesheets = document.styleSheets;\n";
echo "    console.log('Total stylesheets loaded:', stylesheets.length);\n";
echo "    \n";
echo "    for (let i = 0; i < stylesheets.length; i++) {\n";
echo "        const sheet = stylesheets[i];\n";
echo "        console.log('Stylesheet ' + (i+1) + ':', sheet.href || 'inline');\n";
echo "    }\n";
echo "});\n";
echo "</script>\n";

echo "</body>\n";
echo "</html>\n";
?>
