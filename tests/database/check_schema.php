<?php
require_once 'config/config.php';

try {
    $db = Database::getInstance()->getConnection();

    // Check users table structure
    $stmt = $db->query('DESCRIBE users');
    $columns = $stmt->fetchAll();
    echo "Users table columns:\n";
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }

    echo "\nChecking for user_addresses table...\n";
    $stmt = $db->query('SHOW TABLES LIKE "user_addresses"');
    if ($stmt->rowCount() > 0) {
        echo "user_addresses table exists\n";
    } else {
        echo "user_addresses table MISSING\n";
    }

    // Check admin users
    echo "\nChecking for admin users...\n";
    $stmt = $db->query('SELECT id, first_name, last_name, email, role FROM users WHERE role IN ("manager", "super_admin") LIMIT 5');
    $admins = $stmt->fetchAll();
    echo "Found " . count($admins) . " admin users\n";

    if (empty($admins)) {
        echo "Creating admin user...\n";
        $stmt = $db->prepare('INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->execute(['Admin', 'User', 'admin@buffet.com', $hashedPassword, 'manager', 1]);
        echo "Admin user created successfully\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
