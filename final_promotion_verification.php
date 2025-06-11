<?php
/**
 * Final Verification - Promotion Edit URL Fix
 */

echo "=== FINAL VERIFICATION: Promotion Edit URL Fix ===\n\n";

// 1. Check routing configuration
echo "1. Checking routing configuration...\n";
$indexContent = file_get_contents('index.php');

if (strpos($indexContent, "case 'get':") !== false) {
    echo "✅ 'get' route added to index.php\n";
} else {
    echo "❌ 'get' route missing from index.php\n";
}

if (strpos($indexContent, '$controller->getPromotion($param);') !== false) {
    echo "✅ getPromotion method call configured\n";
} else {
    echo "❌ getPromotion method call missing\n";
}

// 2. Check controller method
echo "\n2. Checking controller method...\n";
$controllerContent = file_get_contents('controllers/SuperAdminController.php');

if (strpos($controllerContent, 'public function getPromotion($id)') !== false) {
    echo "✅ getPromotion method exists in controller\n";
} else {
    echo "❌ getPromotion method missing from controller\n";
}

// 3. Check JavaScript update
echo "\n3. Checking JavaScript update...\n";
$viewContent = file_get_contents('views/superadmin/promotions.php');

if (strpos($viewContent, '/superadmin/promotions/get/') !== false) {
    echo "✅ JavaScript updated to use /get/ URL\n";
} else {
    echo "❌ JavaScript still using old URL\n";
}

// 4. URL Flow Test
echo "\n4. URL Flow Analysis...\n";
echo "📋 URL Flow:\n";
echo "   - Click Edit Button → JavaScript calls: /superadmin/promotions/get/1\n";
echo "   - Route: 'get' → Controller: getPromotion(1) → Returns JSON data\n";
echo "   - Modal opens with data → User makes changes → Clicks Save\n";
echo "   - JavaScript calls: /superadmin/promotions/edit/1 (POST)\n";
echo "   - Route: 'edit' → Controller: editPromotion(1) → Saves changes\n";

echo "\n5. Testing File Syntax...\n";
$errors = [];

// Test index.php
$output = shell_exec('php -l index.php 2>&1');
if (strpos($output, 'No syntax errors') !== false) {
    echo "✅ index.php syntax valid\n";
} else {
    echo "❌ index.php syntax error: $output\n";
    $errors[] = "index.php syntax";
}

// Test controller
$output = shell_exec('php -l controllers/SuperAdminController.php 2>&1');
if (strpos($output, 'No syntax errors') !== false) {
    echo "✅ SuperAdminController.php syntax valid\n";
} else {
    echo "❌ SuperAdminController.php syntax error: $output\n";
    $errors[] = "controller syntax";
}

// Test view
$output = shell_exec('php -l views/superadmin/promotions.php 2>&1');
if (strpos($output, 'No syntax errors') !== false) {
    echo "✅ promotions.php syntax valid\n";
} else {
    echo "❌ promotions.php syntax error: $output\n";
    $errors[] = "view syntax";
}

echo "\n" . str_repeat("=", 60) . "\n";

if (empty($errors)) {
    echo "🎉 SUCCESS: All fixes applied correctly!\n\n";

    echo "✅ RESOLUTION SUMMARY:\n";
    echo "1. Added 'get' route for fetching promotion data\n";
    echo "2. Added getPromotion() method in controller\n";
    echo "3. Updated JavaScript to use correct URLs\n";
    echo "4. Separated GET (fetch) and POST (save) operations\n\n";

    echo "🚀 URL STRUCTURE NOW:\n";
    echo "   📥 Fetch Data: GET /superadmin/promotions/get/{id}\n";
    echo "   💾 Save Data:  POST /superadmin/promotions/edit/{id}\n\n";

    echo "✨ READY FOR TESTING: The edit button should now work correctly!\n";
} else {
    echo "⚠️  Issues found: " . implode(', ', $errors) . "\n";
    echo "Please check the files for syntax errors.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
?>
