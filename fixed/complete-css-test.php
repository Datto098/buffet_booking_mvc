<?php
/**
 * Complete System CSS Test
 * Kiểm tra toàn bộ hệ thống CSS và JS
 */

$tests = [];
$errors = [];

// Test 1: Check if luxury-style.css exists and is readable
$cssPath = __DIR__ . '/assets/css/luxury-style.css';
if (file_exists($cssPath) && is_readable($cssPath)) {
    $cssSize = filesize($cssPath);
    $tests['CSS File'] = "✅ OK - Size: " . number_format($cssSize) . " bytes";
} else {
    $tests['CSS File'] = "❌ FAILED - File not found or not readable";
    $errors[] = "CSS file missing or not readable: " . $cssPath;
}

// Test 2: Check if luxury-effects.js exists and is readable
$jsPath = __DIR__ . '/assets/js/luxury-effects.js';
if (file_exists($jsPath) && is_readable($jsPath)) {
    $jsSize = filesize($jsPath);
    $tests['JS File'] = "✅ OK - Size: " . number_format($jsSize) . " bytes";
} else {
    $tests['JS File'] = "❌ FAILED - File not found or not readable";
    $errors[] = "JS file missing or not readable: " . $jsPath;
}

// Test 3: Check if CSS contains key luxury variables
if (file_exists($cssPath)) {
    $cssContent = file_get_contents($cssPath);
    $hasVariables = strpos($cssContent, '--primary-gold') !== false &&
                   strpos($cssContent, '--primary-navy') !== false;

    if ($hasVariables) {
        $tests['CSS Variables'] = "✅ OK - Luxury variables found";
    } else {
        $tests['CSS Variables'] = "❌ FAILED - Luxury variables missing";
        $errors[] = "CSS variables not found in luxury-style.css";
    }
}

// Test 4: Check if header.php includes luxury CSS
$headerPath = __DIR__ . '/views/layouts/header.php';
if (file_exists($headerPath)) {
    $headerContent = file_get_contents($headerPath);
    $hasLuxuryCSS = strpos($headerContent, 'luxury-style.css') !== false;

    if ($hasLuxuryCSS) {
        $tests['Header CSS Link'] = "✅ OK - Luxury CSS linked in header";
    } else {
        $tests['Header CSS Link'] = "❌ FAILED - Luxury CSS not linked";
        $errors[] = "luxury-style.css not linked in header.php";
    }
} else {
    $tests['Header File'] = "❌ FAILED - Header file not found";
    $errors[] = "Header file missing: " . $headerPath;
}

// Test 5: Check configuration
if (defined('SITE_URL')) {
    $tests['Configuration'] = "✅ OK - SITE_URL: " . SITE_URL;
} else {
    $tests['Configuration'] = "❌ FAILED - SITE_URL not defined";
    $errors[] = "SITE_URL constant not defined";
}

// Test 6: Check if assets are accessible via HTTP
$assetsTestUrl = SITE_URL . '/assets/css/luxury-style.css';
$httpTest = @get_headers($assetsTestUrl);
if ($httpTest && strpos($httpTest[0], '200') !== false) {
    $tests['HTTP Access'] = "✅ OK - CSS accessible via HTTP";
} else {
    $tests['HTTP Access'] = "❌ FAILED - CSS not accessible via HTTP";
    $errors[] = "Cannot access CSS via HTTP: " . $assetsTestUrl;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete System CSS Test</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Luxury CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/luxury-style.css" rel="stylesheet">

    <style>
        .test-pass { color: #28a745; }
        .test-fail { color: #dc3545; }
        .test-card {
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }
        .error-card {
            border-left: 4px solid #dc3545;
            background: #fff5f5;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-flask"></i> Complete System CSS Test
                </h1>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Test Time:</strong> <?php echo date('Y-m-d H:i:s'); ?>
                </div>

                <!-- Test Results -->
                <div class="card test-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-check-circle"></i> Test Results</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($tests as $testName => $result): ?>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <strong><?php echo $testName; ?>:</strong>
                                </div>
                                <div class="col-8 <?php echo strpos($result, '✅') !== false ? 'test-pass' : 'test-fail'; ?>">
                                    <?php echo $result; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Errors -->
                <?php if (!empty($errors)): ?>
                <div class="card error-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-exclamation-triangle"></i> Errors Found</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <?php foreach ($errors as $error): ?>
                                <li class="mb-2">
                                    <i class="fas fa-times-circle text-danger"></i>
                                    <?php echo $error; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CSS Test -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-palette"></i> Visual CSS Test</h5>
                    </div>
                    <div class="card-body">
                        <p>Nếu bạn thấy các element dưới đây hiển thị đẹp với màu vàng gold và navy thì CSS đang hoạt động:</p>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card luxury-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-crown luxury-icon mb-3"></i>
                                        <h5 class="luxury-title">Luxury Card</h5>
                                        <p class="text-muted">Testing luxury styling</p>
                                        <button class="btn btn-luxury btn-sm">Test Button</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card luxury-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-star luxury-icon mb-3"></i>
                                        <h5 class="luxury-title">Premium Design</h5>
                                        <p class="text-muted">Testing premium effects</p>
                                        <button class="btn btn-outline-luxury btn-sm">Outline Button</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card luxury-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-gem luxury-icon mb-3"></i>
                                        <h5 class="luxury-title">Elite Experience</h5>
                                        <p class="text-muted">Testing interactive features</p>
                                        <button class="btn btn-luxury btn-sm">Elite Button</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Fixes -->
                <?php if (!empty($errors)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-tools"></i> Suggested Fixes</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Ensure luxury-style.css file exists in /assets/css/ directory</li>
                            <li>Check file permissions for CSS and JS files</li>
                            <li>Verify SITE_URL configuration matches your server setup</li>
                            <li>Clear browser cache and reload</li>
                            <li>Check server logs for 404 errors on asset files</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Luxury Effects JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/luxury-effects.js"></script>
</body>
</html>
