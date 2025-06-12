<?php
/**
 * Super Admin Panel Entry Point
 * This file handles direct access to /superadmin/ directory
 */

// Start session to check authentication
session_start();

// Change working directory to parent directory for proper path resolution
chdir(dirname(__DIR__));

// Include necessary files
require_once 'config/config.php';
require_once 'config/database.php';

error_log("SuperAdmin directory accessed: " . $_SERVER['REQUEST_URI']);

// Check if user is logged in and has super admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    // Not logged in or not super admin, redirect to login
    header('Location: ' . SITE_URL . '/auth/login');
    exit;
} else {
    // Logged in as super admin, load the dashboard directly
    require_once 'controllers/SuperAdminController.php';

    try {
        $controller = new SuperAdminController();
        $controller->dashboard();
    } catch (Exception $e) {
        echo "Error loading super admin dashboard: " . $e->getMessage();
        error_log("SuperAdmin dashboard error: " . $e->getMessage());
    }
}
?>
