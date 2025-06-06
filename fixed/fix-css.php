<?php
/**
 * CSS Fix Script - Khắc phục các vấn đề CSS
 */

require_once 'config/config.php';

$fixes = [];
$fixed = [];

echo "🔧 CSS Fix Script - Starting repairs...\n\n";

// Fix 1: Ensure CSS file has proper permissions
$cssPath = __DIR__ . '/assets/css/luxury-style.css';
if (file_exists($cssPath)) {
    if (chmod($cssPath, 0644)) {
        $fixes[] = "✅ Fixed CSS file permissions";
    }
} else {
    $fixes[] = "❌ CSS file not found: " . $cssPath;
}

// Fix 2: Ensure JS file has proper permissions
$jsPath = __DIR__ . '/assets/js/luxury-effects.js';
if (file_exists($jsPath)) {
    if (chmod($jsPath, 0644)) {
        $fixes[] = "✅ Fixed JS file permissions";
    }
} else {
    $fixes[] = "❌ JS file not found: " . $jsPath;
}

// Fix 3: Check and fix header.php CSS links
$headerPath = __DIR__ . '/views/layouts/header.php';
if (file_exists($headerPath)) {
    $headerContent = file_get_contents($headerPath);

    // Check if luxury CSS is properly linked
    if (strpos($headerContent, 'luxury-style.css') === false) {
        // Add luxury CSS link after Bootstrap
        $pattern = '/(<link href="https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap@5\.1\.3\/dist\/css\/bootstrap\.min\.css" rel="stylesheet">)/';
        $replacement = '$1' . "\n    <!-- Luxury CSS -->\n    <link href=\"<?php echo SITE_URL; ?>/assets/css/luxury-style.css\" rel=\"stylesheet\">";

        if (preg_match($pattern, $headerContent)) {
            $headerContent = preg_replace($pattern, $replacement, $headerContent);
            file_put_contents($headerPath, $headerContent);
            $fixes[] = "✅ Added luxury CSS link to header.php";
        }
    } else {
        $fixes[] = "✅ Luxury CSS already linked in header.php";
    }
} else {
    $fixes[] = "❌ Header file not found: " . $headerPath;
}

// Fix 4: Create .htaccess for proper asset serving (if needed)
$htaccessPath = __DIR__ . '/.htaccess';
$htaccessContent = "# Enable asset serving
<FilesMatch \"\.(css|js|png|jpg|jpeg|gif|svg|ico)$\">
    Header set Cache-Control \"max-age=31536000\"
    ExpiresActive On
    ExpiresDefault \"access plus 1 year\"
</FilesMatch>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Prevent direct access to PHP files in assets
<FilesMatch \"^(assets|uploads).*\.php$\">
    Order Allow,Deny
    Deny from all
</FilesMatch>
";

if (!file_exists($htaccessPath)) {
    file_put_contents($htaccessPath, $htaccessContent);
    $fixes[] = "✅ Created .htaccess for better asset serving";
} else {
    $fixes[] = "✅ .htaccess already exists";
}

// Fix 5: Verify CSS content integrity
if (file_exists($cssPath)) {
    $cssContent = file_get_contents($cssPath);
    $cssSize = strlen($cssContent);

    if ($cssSize < 1000) {
        $fixes[] = "⚠️  CSS file seems too small (" . $cssSize . " bytes) - may be corrupted";
    } elseif (strpos($cssContent, '--primary-gold') === false) {
        $fixes[] = "⚠️  CSS file missing luxury variables - may be corrupted";
    } else {
        $fixes[] = "✅ CSS file content verified (" . number_format($cssSize) . " bytes)";
    }
}

// Output results
echo "📋 Fix Results:\n";
echo "================\n";
foreach ($fixes as $fix) {
    echo $fix . "\n";
}

echo "\n🔄 Testing CSS loading...\n";

// Test CSS loading
$testUrl = SITE_URL . '/assets/css/luxury-style.css';
$headers = @get_headers($testUrl);

if ($headers && strpos($headers[0], '200') !== false) {
    echo "✅ CSS is accessible via HTTP: " . $testUrl . "\n";
} else {
    echo "❌ CSS not accessible via HTTP: " . $testUrl . "\n";
    echo "Headers: " . print_r($headers, true) . "\n";
}

echo "\n✨ CSS Fix Script Complete!\n";
echo "📋 Next steps:\n";
echo "1. Clear browser cache\n";
echo "2. Test homepage: " . SITE_URL . "\n";
echo "3. Test header: " . SITE_URL . "/header-test.php\n";
echo "4. Run complete test: " . SITE_URL . "/complete-css-test.php\n";

?>
