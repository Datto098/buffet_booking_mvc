<?php
/**
 * Test SITE_URL Fix for Promotions JavaScript
 */

echo "=== TESTING SITE_URL FIX FOR PROMOTIONS ===\n\n";

// Load config to get SITE_URL
require_once 'config/config.php';

echo "SITE_URL: " . SITE_URL . "\n\n";

// Read the promotions.php file
$promotionsContent = file_get_contents('views/superadmin/promotions.php');

// Test URLs that should be generated
$testUrls = [
    'Get Promotion' => SITE_URL . '/superadmin/promotions/get/1',
    'Edit Promotion' => SITE_URL . '/superadmin/promotions/edit/1',
    'Create Promotion' => SITE_URL . '/superadmin/promotions/create',
    'Toggle Promotion' => SITE_URL . '/superadmin/promotions/toggle/1',
    'Delete Promotion' => SITE_URL . '/superadmin/promotions/delete/1'
];

echo "Expected Generated URLs:\n";
foreach ($testUrls as $action => $url) {
    echo "- $action: $url\n";
}

echo "\n=== VERIFYING FETCH CALLS ===\n";

// Check for SITE_URL usage in fetch calls
$fetchCallsFound = [];
preg_match_all('/fetch\(`\<\?= SITE_URL \?\>\/superadmin\/promotions\/([^`]+)`/', $promotionsContent, $matches);

if (!empty($matches[1])) {
    echo "✅ Found " . count($matches[1]) . " fetch calls with SITE_URL:\n";
    foreach ($matches[1] as $endpoint) {
        echo "  - /superadmin/promotions/$endpoint\n";
        $fetchCallsFound[] = $endpoint;
    }
} else {
    echo "❌ No fetch calls with SITE_URL found\n";
}

// Check for any remaining fetch calls without SITE_URL
preg_match_all('/fetch\(`\/superadmin\/promotions\/([^`]+)`/', $promotionsContent, $badMatches);

if (!empty($badMatches[1])) {
    echo "\n⚠️  Found " . count($badMatches[1]) . " fetch calls WITHOUT SITE_URL:\n";
    foreach ($badMatches[1] as $endpoint) {
        echo "  - /superadmin/promotions/$endpoint (NEEDS FIXING)\n";
    }
} else {
    echo "\n✅ No fetch calls without SITE_URL found\n";
}

echo "\n=== SUMMARY ===\n";
if (empty($badMatches[1]) && !empty($fetchCallsFound)) {
    echo "✅ All fetch calls properly use SITE_URL\n";
    echo "✅ JavaScript URLs will work correctly\n";
    echo "✅ Promotions page should now function properly\n";
} else {
    echo "❌ Some issues found that need fixing\n";
}

echo "\n=== TESTING RECOMMENDATIONS ===\n";
echo "1. Test in browser: " . SITE_URL . "/superadmin/promotions\n";
echo "2. Try editing a promotion to test AJAX calls\n";
echo "3. Test toggle and delete functionality\n";
echo "4. Check browser console for any 404 errors\n";
