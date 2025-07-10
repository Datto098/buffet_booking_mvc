<?php
/**
 * Helper Functions
 */

// Include configuration
require_once __DIR__ . '/../config/config.php';

/**
 * Sanitize output
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 * @param float $amount
 * @return string
 */
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

/**
 * Format date
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

/**
 * Check if user is authenticated
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'manager', 'super_admin']);
}

/**
 * Redirect to URL
 * @param string $url
 */
function redirect($url) {
    // If the URL doesn't start with http or https, prepend SITE_URL
    if ($url[0] === '/') {
        $url = SITE_URL . $url;
    } elseif (strpos($url, 'http') !== 0) {
        $url = SITE_URL . '/' . $url;
    }

    header("Location: $url");
    exit;
}

/**
 * Get base URL
 * @return string
 */
function baseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'];
}

/**
 * Generate CSRF token
 * @return string
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash message
 * @param string $key
 * @param string $message
 * @param string $type
 */
function flash($key, $message = null, $type = 'info') {
    if ($message === null) {
        $messages = $_SESSION['flash_messages'][$key] ?? null;
        unset($_SESSION['flash_messages'][$key]);
        return $messages;
    }

    $_SESSION['flash_messages'][$key][] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get flash messages
 * @param string $key
 * @return array
 */
function getFlashMessages($key) {
    return flash($key);
}

/**
 * Asset URL
 * @param string $path
 * @return string
 */
function asset($path) {
    return baseUrl() . '/assets/' . ltrim($path, '/');
}

/**
 * URL helper
 * @param string $path
 * @return string
 */
function url($path = '') {
    return baseUrl() . '/' . ltrim($path, '/');
}

/**
 * Old input value
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function old($key, $default = '') {
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Set old input
 * @param array $data
 */
function setOldInput($data) {
    $_SESSION['old_input'] = $data;
}

/**
 * Clear old input
 */
function clearOldInput() {
    unset($_SESSION['old_input']);
}

/**
 * Debug dump
 * @param mixed $data
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Generate random string
 * @param int $length
 * @return string
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Validate email
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Truncate string
 * @param string $string
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate($string, $length = 100, $suffix = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . $suffix;
}

/**
 * Check if request is POST
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Send JSON response
 * @param mixed $data
 * @param int $statusCode
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Check if user is manager
 * @return bool
 */
function isManager() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manager';
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Sanitize input data
 * @param mixed $input Input data to sanitize
 * @return mixed Sanitized input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }

    if (is_string($input)) {
        // Remove whitespace
        $input = trim($input);

        // Remove HTML tags
        $input = strip_tags($input);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }

    // For non-string values, return as is
    return $input;
}

/**
 * Generate CSRF token field for forms
 * @return string HTML input field with CSRF token
 */
function csrf_token_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Generate CSRF token (alias for csrf_token)
 * @return string CSRF token
 */
function generateCSRFToken() {
    return csrf_token();
}
