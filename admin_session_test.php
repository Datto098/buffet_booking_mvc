<?php
session_start();

// Simple admin check and redirect
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Create a temporary admin session for testing
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_username'] = 'admin';
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    echo "✅ Temporary admin session created for testing<br>";
    echo "🔄 Redirecting to news create page...<br>";
    echo '<script>setTimeout(() => { window.location.href = "/buffet_booking_mvc/admin/news/create"; }, 2000);</script>';
} else {
    echo "✅ Admin already logged in<br>";
    echo "👤 Admin ID: " . $_SESSION['admin_id'] . "<br>";
    echo "👤 Username: " . $_SESSION['admin_username'] . "<br>";
    echo "🔑 CSRF Token: " . substr($_SESSION['csrf_token'], 0, 20) . "...<br>";
    echo '<br><a href="/buffet_booking_mvc/admin/news/create" class="btn btn-primary">Go to Create News</a>';
    echo '<br><a href="/buffet_booking_mvc/admin/news" class="btn btn-secondary">Go to News List</a>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Session Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Admin Session Test</h2>
</body>
</html>
