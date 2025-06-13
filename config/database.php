<?php
/**
 * Database Configuration
 */

// Prevent multiple includes
if (defined('DB_HOST')) {
    return;
}

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'buffet_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Database connection class
if (!class_exists('Database')) {
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }    // Prevent cloning
    private function __clone() {}    // Prevent unserializing
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton.");
    }
}
} // End class_exists check
?>
