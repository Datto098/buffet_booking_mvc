<?php
/**
 * Setup script to create tables for testing the booking system
 */

// Include necessary configuration
require_once 'config/database.php';
require_once 'config/config.php';

// Create a PDO connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=buffet_booking", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully\n";
      // Check if tables table exists and create it if it doesn't
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `tables` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `table_number` varchar(10) NOT NULL,
            `capacity` int(11) NOT NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        echo "Tables table created or already exists\n";
    } catch (PDOException $e) {
        echo "Error creating tables table: " . $e->getMessage() . "\n";
    }

    // Check if reservations table exists and create it if it doesn't
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `reservations` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) DEFAULT NULL,
            `customer_name` varchar(255) NOT NULL,
            `phone_number` varchar(20) NOT NULL,
            `table_id` int(11) DEFAULT NULL,
            `reservation_time` datetime NOT NULL,
            `number_of_guests` int(11) NOT NULL,
            `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
            `notes` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `table_id` (`table_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        echo "Reservations table created or already exists\n";
    } catch (PDOException $e) {
        echo "Error creating reservations table: " . $e->getMessage() . "\n";
    }

    // Check if there are any tables, and if not, insert some sample tables
    $tableCount = $pdo->query("SELECT COUNT(*) FROM `tables`")->fetchColumn();

    if ($tableCount == 0) {
        // Insert sample tables
        $tables = [
            ['table_number' => 'A1', 'capacity' => 2],
            ['table_number' => 'A2', 'capacity' => 2],
            ['table_number' => 'B1', 'capacity' => 4],
            ['table_number' => 'B2', 'capacity' => 4],
            ['table_number' => 'C1', 'capacity' => 6],
            ['table_number' => 'C2', 'capacity' => 6],
            ['table_number' => 'D1', 'capacity' => 8],
            ['table_number' => 'D2', 'capacity' => 10]
        ];

        $stmt = $pdo->prepare("INSERT INTO `tables` (table_number, capacity) VALUES (:table_number, :capacity)");

        foreach ($tables as $table) {
            $stmt->execute([
                ':table_number' => $table['table_number'],
                ':capacity' => $table['capacity']
            ]);
        }

        echo "Sample tables created successfully\n";
    } else {
        echo "Tables already exist in the database\n";
    }

    echo "Setup completed successfully!\n";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
