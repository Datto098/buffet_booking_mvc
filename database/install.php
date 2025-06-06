<?php
/**
 * Database Installation Script
 * Run this script to set up the database schema and sample data
 */

require_once dirname(__DIR__) . '/config/config.php';

class DatabaseInstaller {
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;

    public function __construct() {
        $this->host = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
    }

    public function install() {
        try {
            echo "Starting database installation...\n";

            // Connect to MySQL without selecting database
            $this->connection = new PDO(
                "mysql:host={$this->host};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            echo "Connected to MySQL server.\n";

            // Create database if it doesn't exist
            $this->createDatabase();

            // Select the database
            $this->connection->exec("USE {$this->database}");
            echo "Database '{$this->database}' selected.\n";

            // Read and execute schema file
            $this->executeSchemaFile();

            echo "\n✓ Database installation completed successfully!\n";
            echo "✓ Sample data has been inserted.\n";
            echo "\nDefault admin user credentials:\n";
            echo "Email: admin@buffetbooking.com\n";
            echo "Password: password\n";
            echo "\nDefault customer users:\n";
            echo "Email: john@example.com | Password: password\n";
            echo "Email: jane@example.com | Password: password\n";
            echo "Email: mike@example.com | Password: password (Manager)\n";

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return false;
        }

        return true;
    }

    private function createDatabase() {
        $sql = "CREATE DATABASE IF NOT EXISTS `{$this->database}`
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $this->connection->exec($sql);
        echo "Database '{$this->database}' created or already exists.\n";
    }

    private function executeSchemaFile() {
        $schemaFile = __DIR__ . '/schema.sql';

        if (!file_exists($schemaFile)) {
            throw new Exception("Schema file not found: {$schemaFile}");
        }

        $sql = file_get_contents($schemaFile);

        // Split SQL into individual statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^--/', $stmt);
            }
        );

        echo "Executing schema file...\n";

        foreach ($statements as $statement) {
            // Skip comments and empty statements
            if (empty(trim($statement)) || strpos(trim($statement), '--') === 0) {
                continue;
            }

            try {
                $this->connection->exec($statement);
            } catch (PDOException $e) {
                // Some statements might fail if objects already exist, which is OK
                if (strpos($e->getMessage(), 'already exists') === false &&
                    strpos($e->getMessage(), 'Duplicate') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }

        echo "Schema executed successfully.\n";
    }

    public function checkConnection() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            $stmt = $this->connection->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch();

            echo "✓ Database connection successful!\n";
            echo "✓ Found {$result['count']} users in database.\n";

            return true;
        } catch (Exception $e) {
            echo "✗ Database connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function reset() {
        try {
            echo "Resetting database...\n";

            $this->connection = new PDO(
                "mysql:host={$this->host};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            // Drop database if exists
            $this->connection->exec("DROP DATABASE IF EXISTS `{$this->database}`");
            echo "Database '{$this->database}' dropped.\n";

            // Recreate database
            $this->install();

            return true;
        } catch (Exception $e) {
            echo "Error resetting database: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    $installer = new DatabaseInstaller();

    $command = $argv[1] ?? 'install';

    switch ($command) {
        case 'install':
            $installer->install();
            break;

        case 'check':
            $installer->checkConnection();
            break;

        case 'reset':
            $confirmation = readline("Are you sure you want to reset the database? This will delete all data! (yes/no): ");
            if (strtolower($confirmation) === 'yes') {
                $installer->reset();
            } else {
                echo "Reset cancelled.\n";
            }
            break;

        default:
            echo "Usage: php install.php [install|check|reset]\n";
            echo "  install - Install database schema and sample data\n";
            echo "  check   - Check database connection and status\n";
            echo "  reset   - Reset database (WARNING: Deletes all data)\n";
            break;
    }
} else {
    // Web interface
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Installation - Buffet Booking System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">Database Installation</h3>
                        </div>
                        <div class="card-body">
                            <?php
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $installer = new DatabaseInstaller();
                                $action = $_POST['action'] ?? '';

                                echo '<div class="mb-3"><pre>';

                                switch ($action) {
                                    case 'install':
                                        $installer->install();
                                        break;
                                    case 'check':
                                        $installer->checkConnection();
                                        break;
                                    case 'reset':
                                        $installer->reset();
                                        break;
                                }

                                echo '</pre></div>';
                                echo '<a href="?" class="btn btn-primary">Back</a>';
                            } else {
                                ?>
                                <p>Welcome to the Buffet Booking System database installer. Choose an action below:</p>

                                <form method="POST" class="mb-3">
                                    <input type="hidden" name="action" value="install">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-download"></i> Install Database
                                    </button>
                                    <p class="form-text">Install the database schema and sample data.</p>
                                </form>

                                <form method="POST" class="mb-3">
                                    <input type="hidden" name="action" value="check">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-check-circle"></i> Check Connection
                                    </button>
                                    <p class="form-text">Test the database connection and check status.</p>
                                </form>

                                <form method="POST" class="mb-3" onsubmit="return confirm('Are you sure? This will delete all data!')">
                                    <input type="hidden" name="action" value="reset">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-refresh"></i> Reset Database
                                    </button>
                                    <p class="form-text">⚠️ Reset the database (WARNING: Deletes all data).</p>
                                </form>

                                <hr>
                                <h5>Database Configuration</h5>
                                <ul>
                                    <li><strong>Host:</strong> <?= DB_HOST ?></li>
                                    <li><strong>Database:</strong> <?= DB_NAME ?></li>
                                    <li><strong>User:</strong> <?= DB_USER ?></li>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
