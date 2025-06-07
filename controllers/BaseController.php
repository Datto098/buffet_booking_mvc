<?php
/**
 * Base Controller Class
 */

class BaseController {
    protected function loadView($view, $data = []) {
        // Extract variables from data array
        if (!empty($data)) {
            extract($data);
        }

        // Include header
        include 'views/layouts/header.php';

        // Include the specific view
        include "views/$view.php";

        // Include footer
        include 'views/layouts/footer.php';
    }    protected function loadAdminView($view, $data = []) {
        // Extract variables from data array
        if (!empty($data)) {
            extract($data);
        }

        // Include admin header
        include 'views/admin/layouts/header.php';

        // Include admin sidebar
        include 'views/admin/layouts/sidebar.php';

        // Start main content area
        echo '<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">';

        // Include the specific view
        include "views/admin/$view.php";

        // End main content area
        echo '</main>';

        // Include admin footer
        include 'views/admin/layouts/footer.php';
    }

    protected function loadSuperAdminView($view, $data = []) {
        // Extract variables from data array
        if (!empty($data)) {
            extract($data);
        }

        // Include superadmin header
        include 'views/layouts/superadmin_header.php';

        // Include the specific view
        include "views/superadmin/$view.php";

        // Include superadmin footer
        include 'views/layouts/superadmin_footer.php';
    }    protected function requireLogin() {
        if (!isLoggedIn()) {
            redirect('/auth/login');
        }
    }

    protected function requireAdmin() {
        $this->requireLogin();
        if (!isAdmin() && !isManager()) {
            redirect('/index.php');
        }
    }

    protected function requireSuperAdmin() {
        $this->requireLogin();
        if (!isAdmin()) {
            redirect('/index.php');
        }
    }

    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Load a view file with data
     */
    protected function view($viewPath, $data = [], $includeLayout = true) {
        // Extract data array into variables
        if (!empty($data)) {
            extract($data);
        }

        // Determine the full view file path
        $viewFile = "views/{$viewPath}.php";

        // Check if view file exists
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: {$viewFile}");
        }

        if ($includeLayout) {
            // Include the view with layout
            include $viewFile;
        } else {
            // Include view without layout (for AJAX/modal content)
            include $viewFile;
        }
    }    /**
     * Redirect to a URL
     */
    protected function redirect($url, $statusCode = 302) {
        // If the URL doesn't start with http or https, prepend SITE_URL
        if ($url[0] === '/') {
            $url = SITE_URL . $url;
        } elseif (strpos($url, 'http') !== 0) {
            $url = SITE_URL . '/' . $url;
        }

        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Get flash message
     */
    protected function getFlash($type = null) {
        if (!isset($_SESSION)) {
            session_start();
        }

        if ($type) {
            $message = $_SESSION['flash'][$type] ?? null;
            unset($_SESSION['flash'][$type]);
            return $message;
        }

        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }

    /**
     * Sanitize input data
     */
    protected function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }    protected function hasRole($roles) {
        // Check for standard session structure
        $userRole = $_SESSION['user_role'] ?? null;

        // Also check user array structure (fallback)
        if (!$userRole && isset($_SESSION['user']['role'])) {
            $userRole = $_SESSION['user']['role'];
        }

        if (!$userRole) {
            return false;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($userRole, $roles);
    }

    protected function requireRole($roles) {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }

        if (!$this->hasRole($roles)) {
            $this->redirect('/');
        }
    }    protected function generateCSRF() {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Only generate a new token if one doesn't exist
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $token;
        }

        return $_SESSION['csrf_token'];
    }protected function validateCSRF() {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Check multiple possible sources for CSRF token
        // X-CSRF-Token header becomes HTTP_X_CSRF_TOKEN in $_SERVER
        $token = $_POST['csrf_token'] ??
                 $_SERVER['HTTP_X_CSRF_TOKEN'] ??
                 null;

        // Alternative way to get headers
        if (!$token && function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-Token'] ?? $headers['x-csrf-token'] ?? null;
        }        if (!$token || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            error_log("CSRF validation failed. Token: " . ($token ?? 'null') .
                     ", Session token: " . ($_SESSION['csrf_token'] ?? 'null') .
                     ", Request method: " . ($_SERVER['REQUEST_METHOD'] ?? 'unknown') .
                     ", Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown'));
            return false;
        }

        return true;
    }

    protected function csrfToken() {
        $token = $this->generateCSRF();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Get URL segments from REQUEST_URI for clean URL routing
     *
     * @return array Array of URL segments
     */    protected function getUrlSegments() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        // Remove query string if present
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Get the path relative to the installation directory
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'] ?? '');
        $uri = str_replace($basePath, '', $uri);

        // Remove leading and trailing slashes
        $uri = trim($uri, '/');

        // Split into segments
        $segments = explode('/', $uri);

        return $segments;
    }

    /**
     * Get current URL segment at specified position
     *
     * @param int $position Position of the segment (1-based)
     * @return string|null Segment value or null if not exists
     */
    protected function getSegment($position) {
        $segments = $this->getUrlSegments();
        return isset($segments[$position - 1]) ? $segments[$position - 1] : null;
    }
}
?>
