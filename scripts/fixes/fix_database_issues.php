<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Checking database status...\n";

try {
    $dsn = "mysql:host=localhost;dbname=buffet_booking;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "✓ Database connected\n";

    // Check users table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    echo "\nUsers table structure:\n";
    foreach ($columns as $col) {
        echo "  {$col['Field']} - {$col['Type']}\n";
    }

    // Check user count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "\nUsers count: {$result['count']}\n";

    // Check if user_addresses table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'user_addresses'");
    if ($stmt->rowCount() > 0) {
        echo "✓ user_addresses table exists\n";
    } else {
        echo "✗ user_addresses table missing\n";

        // Create user_addresses table
        echo "Creating user_addresses table...\n";
        $sql = "CREATE TABLE user_addresses (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            address_line text NOT NULL,
            is_default tinyint(1) DEFAULT 0,
            created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY fk_user_addresses_user (user_id),
            CONSTRAINT fk_user_addresses_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        )";
        $pdo->exec($sql);
        echo "✓ user_addresses table created\n";
    }

    // If no users, create some sample data
    if ($result['count'] == 0) {
        echo "Creating admin user...\n";
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->execute(['Admin', 'User', 'admin@buffet.com', $hashedPassword, 'manager', 1]);
        echo "✓ Admin user created\n";

        // Create a customer user too
        $stmt->execute(['John', 'Doe', 'john@example.com', password_hash('password', PASSWORD_DEFAULT), 'customer', 1]);
        echo "✓ Customer user created\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
