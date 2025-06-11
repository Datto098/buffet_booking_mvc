<?php
// Create admin user for testing
require_once 'config/config.php';
require_once 'config/Database.php';

echo "<h1>Create Admin User for Testing</h1>";

try {
    $db = Database::getInstance()->getConnection();

    // Check if admin user exists
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'super_admin'");
    $stmt->execute(['admin@admin.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        echo "<h2>✅ Admin user already exists</h2>";
        echo "<p>Email: " . $admin['email'] . "</p>";
        echo "<p>Role: " . $admin['role'] . "</p>";
        echo "<p>Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "</p>";

        // Update password to known value
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $admin['id']]);
        echo "<p>✅ Password updated to 'admin123'</p>";

    } else {
        echo "<h2>Creating new admin user...</h2>";

        $stmt = $db->prepare("
            INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);

        $result = $stmt->execute([
            'Super',
            'Admin',
            'admin@admin.com',
            $hashedPassword,
            'super_admin',
            1
        ]);

        if ($result) {
            $adminId = $db->lastInsertId();
            echo "<p>✅ Admin user created successfully with ID: $adminId</p>";
        } else {
            echo "<p>❌ Failed to create admin user</p>";
        }
    }

    echo "<hr>";
    echo "<h2>Test Login Credentials</h2>";
    echo "<p><strong>Email:</strong> admin@admin.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='test_promotion_edit_auth.html' target='_blank'>🔗 Test Authentication</a></p>";
    echo "<p><a href='superadmin/promotions' target='_blank'>🔗 Go to Promotions Management</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
a { color: #007cba; }
hr { margin: 30px 0; }
</style>
