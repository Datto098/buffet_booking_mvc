<?php
/**
 * Login redirect helper
 */

// Include configuration
require_once 'config/config.php';

// Redirect to the correct login URL
redirect(SITE_URL . '/auth/login');
?>
