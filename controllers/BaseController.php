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
    }

    protected function loadAdminView($view, $data = []) {
        // Extract variables from data array
        if (!empty($data)) {
            extract($data);
        }

        // Include admin header
        include 'views/layouts/admin_header.php';

        // Include the specific view
        include "views/admin/$view.php";

        // Include admin footer
        include 'views/layouts/admin_footer.php';
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
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url, $statusCode = 302) {
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
    }

    protected function hasRole($roles) {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($_SESSION['user_role'], $roles);
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
    }

    protected function generateCSRF() {
        if (!isset($_SESSION)) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    protected function validateCSRF() {
        if (!isset($_SESSION)) {
            session_start();
        }

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!$token || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
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
