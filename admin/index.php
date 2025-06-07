<?php
/**
 * Admin Panel Entry Point
 * This file handles direct access to /admin/ directory
 */

// Start session to check authentication
session_start();

// Change working directory to parent directory for proper path resolution
chdir(dirname(__DIR__));

// Include config for redirect function
require_once 'config/config.php';

error_log("Admin directory accessed: " . $_SERVER['REQUEST_URI']);

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['manager', 'super_admin'])) {
    // Not logged in or not admin, redirect to login
    header('Location: ' . SITE_URL . '/auth/login');
    exit;
} else {
    // Logged in as admin, load the dashboard directly
    require_once 'controllers/AdminController.php';

    try {
        $controller = new AdminController();
        $controller->dashboard();
    } catch (Exception $e) {
        echo "Error loading admin dashboard: " . $e->getMessage();
        error_log("Admin dashboard error: " . $e->getMessage());
    }
}
?>
