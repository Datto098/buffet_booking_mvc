<?php
/**
 * Database Structure Check
 */
require_once 'config/config.php';

echo "<h1>Database Structure Check</h1>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";

    // Check tables
    $tables = ['users', 'orders', 'order_items', 'food_items', 'categories', 'user_addresses'];

    echo "<h2>Table Status:</h2>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Table</th><th>Exists</th><th>Record Count</th><th>Sample Data</th></tr>";

    foreach ($tables as $table) {
        echo "<tr>";
        echo "<td>$table</td>";

        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            $count = $result['count'];

            echo "<td style='color: green;'>✓</td>";
            echo "<td>$count</td>";

            // Get sample data
            if ($count > 0) {
                $stmt = $db->query("SELECT * FROM $table LIMIT 1");
                $sample = $stmt->fetch();
                $sampleKeys = array_keys($sample);
                echo "<td>" . implode(', ', array_slice($sampleKeys, 0, 3)) . "...</td>";
            } else {
                echo "<td>No data</td>";
            }

        } catch (Exception $e) {
            echo "<td style='color: red;'>✗</td>";
            echo "<td>-</td>";
            echo "<td>" . $e->getMessage() . "</td>";
        }

        echo "</tr>";
    }
    echo "</table>";

    // Check for admin users
    echo "<h2>Admin Users:</h2>";
    try {
        $stmt = $db->query("SELECT id, name, email, role FROM users WHERE role IN ('manager', 'super_admin')");
        $admins = $stmt->fetchAll();

        if (empty($admins)) {
            echo "<p style='color: red;'>No admin users found!</p>";
            echo "<p>Creating default admin user...</p>";

            $stmt = $db->prepare("INSERT INTO users (name, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $success = $stmt->execute([
                'Admin User',
                'admin@buffet.com',
                password_hash('admin123', PASSWORD_DEFAULT),
                'manager',
                1
            ]);

            if ($success) {
                echo "<p style='color: green;'>✓ Admin user created: admin@buffet.com / admin123</p>";
            }
        } else {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
            foreach ($admins as $admin) {
                echo "<tr>";
                echo "<td>{$admin['id']}</td>";
                echo "<td>{$admin['name']}</td>";
                echo "<td>{$admin['email']}</td>";
                echo "<td>{$admin['role']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error checking admin users: " . $e->getMessage() . "</p>";
    }

    // Check for sample orders
    echo "<h2>Sample Orders:</h2>";
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
        $result = $stmt->fetch();
        $orderCount = $result['count'];

        if ($orderCount == 0) {
            echo "<p style='color: orange;'>No orders found. Creating sample orders...</p>";

            // Check if we have user addresses first
            $stmt = $db->query("SELECT COUNT(*) as count FROM user_addresses");
            $addressResult = $stmt->fetch();

            if ($addressResult['count'] == 0) {
                echo "<p>Creating sample address...</p>";
                $stmt = $db->prepare("INSERT INTO user_addresses (user_id, address_line, city, state, postal_code, is_default) VALUES (1, '123 Main St', 'Sample City', 'ST', '12345', 1)");
                $stmt->execute();
            }

            // Create sample orders
            for ($i = 1; $i <= 5; $i++) {
                $stmt = $db->prepare("INSERT INTO orders (user_id, address_id, total_amount, status, payment_method, notes, created_at) VALUES (1, 1, ?, ?, 'card', 'Sample order $i', NOW())");
                $amount = 20 + ($i * 5);
                $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered'];
                $status = $statuses[($i - 1) % count($statuses)];
                $stmt->execute([$amount, $status]);

                $orderId = $db->lastInsertId();

                // Add order items
                $stmt = $db->prepare("INSERT INTO order_items (order_id, food_item_id, quantity, price) VALUES (?, 1, ?, ?)");
                $stmt->execute([$orderId, $i, 10 + $i]);
            }

            echo "<p style='color: green;'>✓ Created 5 sample orders</p>";
        } else {
            echo "<p style='color: green;'>✓ Found $orderCount orders in database</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error with sample orders: " . $e->getMessage() . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='test_order_management.php'>← Back to Order Management Test</a></p>";
?>
