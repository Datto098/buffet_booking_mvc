<?php
/**
 * Configuration file - General settings
 */

// Site settings
define('SITE_NAME', 'Buffet Booking');
define('SITE_URL', 'http://localhost/buffet_booking_mvc');
define('ADMIN_EMAIL', 'admin@buffetbooking.com');

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_LENGTH', 32);
define('PASSWORD_MIN_LENGTH', 6);

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination settings
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Path settings
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Include database configuration
require_once 'database.php';

// Start session only if not already started and not in CLI mode
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Utility functions
function redirect($url) {
    // If URL already contains http or https, use it as is
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        header("Location: " . $url);
    // If URL starts with /, append to SITE_URL
    } elseif (strpos($url, '/') === 0) {
        header("Location: " . SITE_URL . $url);
    // Otherwise, it's a relative path
    } else {
        header("Location: " . SITE_URL . '/' . $url);
    }
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function isAdmin() {
    return getUserRole() === 'admin';
}

function isManager() {
    return getUserRole() === 'manager';
}

function isCustomer() {
    return getUserRole() === 'customer';
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    return bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
}

function verifyCSRFToken($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Generate an HTML input field with CSRF token
 *
 * @return string HTML input field with CSRF token
 */
function csrf_token_field() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateCSRFToken();
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Generate an HTML input field with CSRF token (alias for csrf_token_field)
 *
 * @return string HTML input field with CSRF token
 */
function csrf_token() {
    return csrf_token_field();
}
?>
