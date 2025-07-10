<?php
/**
 * Configuration file - General settings
 */

// Path settings (moved up for error logging)
define('ROOT_PATH', dirname(__DIR__));

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/debug.log');

// Site settings
define('SITE_NAME', 'Buffet Booking');
define('SITE_URL', 'http://localhost/buffet_booking_mvc');
// define('SITE_URL', 'http://localhost:8000');
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

// Path settings (already defined above)
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Include database configuration
require_once __DIR__ . '/../database/install.php';

// Configure session settings for better persistence
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    // Configure session for better persistence
    ini_set('session.save_handler', 'files');
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
    ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

    // Ensure session directory exists and is writable
    $sessionPath = sys_get_temp_dir() . '/php_sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0755, true);
    }
    ini_set('session.save_path', $sessionPath);

    session_start();
}

// Include helper functions
require_once __DIR__ . '/../helpers/functions.php';
?>
