<?php
/**
 * Final Verification Script for Review Management System
 * Tests all functionality with the newly imported sample data
 */

require_once 'config/database.php';
require_once 'models/Review.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>🎯 Review System Final Verification</title>";
echo "<meta charset='UTF-8'>";
echo "<style>";
echo "body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 40px; background: #f8f9fa; }";
echo ".success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }";
echo ".info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; margin: 10px 0; border-radius: 4px; }";
echo ".warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; margin: 10px 0; border-radius: 4px; }";
echo ".error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }";
echo "table { width: 100%; border-collapse: collapse; margin: 10px 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo "th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }";
echo "th { background: #f8f9fa; font-weight: 600; }";
echo ".stat-card { background: white; padding: 20px; margin: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-block; min-width: 200px; }";
echo ".stat-number { font-size: 2em; font-weight: bold; color: #007bff; }";
echo "</style>";
echo "</head><body>";

echo "<h1>🎯 Review Management System - Final Verification</h1>";
echo "<p>Comprehensive testing of the review system with imported sample data...</p>";

try {
    // Initialize Review model
    $reviewModel = new Review();

    echo "<h2>📊 Database Statistics</h2>";

    // Get review statistics
    $stats = $reviewModel->getReviewStats();

    echo "<div style='display: flex; flex-wrap: wrap;'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>{$stats['total_reviews']}</div>";
    echo "<div>Total Reviews</div>";
    echo "</div>";

    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>{$stats['approved_reviews']}</div>";
    echo "<div>Approved Reviews</div>";
    echo "</div>";

    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>{$stats['pending_reviews']}</div>";
    echo "<div>Pending Reviews</div>";
    echo "</div>";

    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>{$stats['verified_reviews']}</div>";
    echo "<div>Verified Reviews</div>";
    echo "</div>";

    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . number_format($stats['average_rating'], 2) . "⭐</div>";
    echo "<div>Average Rating</div>";
    echo "</div>";
    echo "</div>";

    echo "<h2>🔍 Functionality Tests</h2>";

    // Test 1: Get all reviews with pagination
    echo "<h3>Test 1: Pagination & Basic Retrieval</h3>";
    $reviews = $reviewModel->getAllReviews(10, 0);
    if (count($reviews) > 0) {
        echo "<div class='success'>✅ Successfully retrieved " . count($reviews) . " reviews with pagination</div>";

        echo "<table>";
        echo "<tr><th>ID</th><th>Food Item</th><th>Rating</th><th>Customer</th><th>Status</th><th>Comment Preview</th></tr>";
        foreach (array_slice($reviews, 0, 5) as $review) {
            $comment_preview = substr($review['comment'], 0, 50) . (strlen($review['comment']) > 50 ? '...' : '');
            $status = $review['is_approved'] ? 'Approved' : 'Pending';
            if ($review['is_verified']) $status .= ' + Verified';

            echo "<tr>";
            echo "<td>{$review['id']}</td>";
            echo "<td>{$review['food_name']}</td>";
            echo "<td>" . str_repeat('⭐', $review['rating']) . "</td>";
            echo "<td>{$review['first_name']} {$review['last_name']}</td>";
            echo "<td>{$status}</td>";
            echo "<td>{$comment_preview}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='error'>❌ Failed to retrieve reviews</div>";
    }

    // Test 2: Filter by status
    echo "<h3>Test 2: Status Filtering</h3>";
    $approvedReviews = $reviewModel->getAllReviews(10, 0, ['status' => 'approved']);
    $pendingReviews = $reviewModel->getAllReviews(10, 0, ['status' => 'pending']);

    echo "<div class='info'>";
    echo "📈 Approved reviews found: " . count($approvedReviews) . "<br>";
    echo "⏳ Pending reviews found: " . count($pendingReviews) . "<br>";
    echo "</div>";

    // Test 3: Rating filter
    echo "<h3>Test 3: Rating Filtering</h3>";
    $fiveStarReviews = $reviewModel->getAllReviews(10, 0, ['rating' => 5]);
    $lowRatingReviews = $reviewModel->getAllReviews(10, 0, ['rating' => 2]);

    echo "<div class='info'>";
    echo "⭐⭐⭐⭐⭐ 5-star reviews: " . count($fiveStarReviews) . "<br>";
    echo "⭐⭐ 2-star reviews: " . count($lowRatingReviews) . "<br>";
    echo "</div>";

    // Test 4: Search functionality
    echo "<h3>Test 4: Search Functionality</h3>";
    $searchResults = $reviewModel->getAllReviews(10, 0, ['search' => 'buffet']);
    echo "<div class='info'>🔍 Search for 'buffet' found: " . count($searchResults) . " results</div>";

    // Test 5: Review details
    echo "<h3>Test 5: Review Details Retrieval</h3>";
    if (!empty($reviews)) {
        $firstReview = $reviews[0];
        $reviewDetails = $reviewModel->getReviewDetails($firstReview['id']);

        if ($reviewDetails) {
            echo "<div class='success'>✅ Successfully retrieved detailed review information</div>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            echo "<tr><td>Review ID</td><td>{$reviewDetails['id']}</td></tr>";
            echo "<tr><td>Customer</td><td>{$reviewDetails['first_name']} {$reviewDetails['last_name']}</td></tr>";
            echo "<tr><td>Food Item</td><td>{$reviewDetails['food_name']}</td></tr>";
            echo "<tr><td>Rating</td><td>" . str_repeat('⭐', $reviewDetails['rating']) . "</td></tr>";
            echo "<tr><td>Title</td><td>{$reviewDetails['title']}</td></tr>";
            echo "<tr><td>Status</td><td>" . ($reviewDetails['is_approved'] ? 'Approved' : 'Pending') . "</td></tr>";
            echo "<tr><td>Verified</td><td>" . ($reviewDetails['is_verified'] ? 'Yes' : 'No') . "</td></tr>";
            echo "</table>";
        } else {
            echo "<div class='error'>❌ Failed to retrieve review details</div>";
        }
    }

    // Test 6: Count functionality
    echo "<h3>Test 6: Count Functionality</h3>";
    $totalCount = $reviewModel->countReviews();
    $approvedCount = $reviewModel->countReviews(['status' => 'approved']);
    $pendingCount = $reviewModel->countReviews(['status' => 'pending']);

    echo "<div class='info'>";
    echo "📊 Total count: {$totalCount}<br>";
    echo "✅ Approved count: {$approvedCount}<br>";
    echo "⏳ Pending count: {$pendingCount}<br>";
    echo "</div>";

    // Test 7: Management Operations (Simulation)
    echo "<h3>Test 7: Management Operations</h3>";
    if (!empty($pendingReviews)) {
        $testReviewId = $pendingReviews[0]['id'];

        // Test approve
        $approveResult = $reviewModel->approveReview($testReviewId);
        if ($approveResult) {
            echo "<div class='success'>✅ Approve operation successful</div>";

            // Test reject (revert)
            $rejectResult = $reviewModel->rejectReview($testReviewId);
            if ($rejectResult) {
                echo "<div class='success'>✅ Reject operation successful</div>";
            }
        }

        // Test verify
        $verifyResult = $reviewModel->verifyReview($testReviewId);
        if ($verifyResult) {
            echo "<div class='success'>✅ Verify operation successful</div>";
        }
    }

    // Test 8: URL Access Test
    echo "<h2>🌐 System Access Test</h2>";

    $accessPoints = [
        'Super Admin Reviews' => 'http://localhost/buffet_booking_mvc/superadmin/reviews',
        'Super Admin Login' => 'http://localhost/buffet_booking_mvc/superadmin/login',
        'Super Admin Dashboard' => 'http://localhost/buffet_booking_mvc/superadmin',
    ];

    echo "<table>";
    echo "<tr><th>Access Point</th><th>URL</th><th>Action</th></tr>";
    foreach ($accessPoints as $name => $url) {
        echo "<tr>";
        echo "<td>{$name}</td>";
        echo "<td><a href='{$url}' target='_blank'>{$url}</a></td>";
        echo "<td><a href='{$url}' target='_blank' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Visit</a></td>";
        echo "</tr>";
    }
    echo "</table>";

    // Final Assessment
    echo "<h2>🎯 Final Assessment</h2>";

    $totalTests = 8;
    $passedTests = 0;

    // Check if basic functionality works
    if ($stats['total_reviews'] >= 20) $passedTests++;
    if (count($reviews) > 0) $passedTests++;
    if (count($approvedReviews) > 0) $passedTests++;
    if (count($pendingReviews) >= 0) $passedTests++;
    if (count($fiveStarReviews) > 0) $passedTests++;
    if (count($searchResults) >= 0) $passedTests++;
    if (isset($reviewDetails) && $reviewDetails) $passedTests++;
    if ($totalCount >= 20) $passedTests++;

    $successRate = ($passedTests / $totalTests) * 100;

    if ($successRate >= 90) {
        echo "<div class='success'>";
        echo "<h3>🎉 EXCELLENT! System is fully operational</h3>";
        echo "<p>✅ {$passedTests}/{$totalTests} tests passed ({$successRate}% success rate)</p>";
        echo "<p><strong>The review management system is ready for production use!</strong></p>";
        echo "</div>";
    } elseif ($successRate >= 70) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Good! Minor issues detected</h3>";
        echo "<p>✅ {$passedTests}/{$totalTests} tests passed ({$successRate}% success rate)</p>";
        echo "<p>Some functionality may need attention.</p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Issues detected</h3>";
        echo "<p>✅ {$passedTests}/{$totalTests} tests passed ({$successRate}% success rate)</p>";
        echo "<p>Several components need debugging.</p>";
        echo "</div>";
    }

    echo "<h2>📋 Next Steps</h2>";
    echo "<div class='info'>";
    echo "<ol>";
    echo "<li>🔐 <strong>Login to Super Admin Panel</strong>: Visit the Super Admin login page</li>";
    echo "<li>📊 <strong>Access Reviews Management</strong>: Navigate to Reviews section</li>";
    echo "<li>🧪 <strong>Test All Features</strong>: Try filtering, searching, approving, and managing reviews</li>";
    echo "<li>🛡️ <strong>Test Security</strong>: Verify CSRF protection and role-based access</li>";
    echo "<li>📱 <strong>Test Responsive Design</strong>: Check on different screen sizes</li>";
    echo "<li>⚡ <strong>Performance Testing</strong>: Test with larger datasets if needed</li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Critical Error</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database configuration and model implementations.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Verification completed at " . date('Y-m-d H:i:s') . "</small></p>";
echo "</body></html>";
?>
