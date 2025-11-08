<?php

namespace App\Core;

use App\Core\Env;
use \PDO;
use \PDOException;

/**
 * Database Connection Manager (Singleton)
 *
 * Handles all database connections using PDO. Implements the Singleton pattern
 * to ensure that only one instance of the database connection is created
 * per request, improving performance and resource management.
 */
class Database
{
    /**
     * @var Database|null The single instance of the Database class.
     */
    private static $instance = null;

    /**
     * @var PDO|null The active PDO connection object.
     */
    private $connection = null;

    /**
     * The private constructor is key to the Singleton pattern.
     * It loads database configuration from the Env class and establishes a PDO connection.
     *
     * @throws \Exception if the database connection fails.
     */
    private function __construct()
    {
        try {
            // Load environment variables if not already loaded
            Env::load();

            // Fetch database configuration from environment variables
            $host = Env::get('DB_HOST', '127.0.0.1');
            $port = Env::get('DB_PORT', '3306');
            $dbName = Env::get('DB_DATABASE', 'student_management_system');
            $user = Env::get('DB_USERNAME', 'root');
            $pass = Env::get('DB_PASSWORD', '');
            $charset = Env::get('DB_CHARSET', 'utf8mb4');

            // Data Source Name (DSN)
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

            // PDO connection options for robustness and security
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,       // Fetch results as objects
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
                PDO::ATTR_PERSISTENT         => false,                  // Do not use persistent connections by default
            ];

            // Establish the PDO connection
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Log the detailed error for the developer
            $this->logError($e);

            // Throw a generic, user-friendly error to halt the application
            // Never expose detailed database errors to the end-user.
            throw new \Exception('Unable to connect to the database. Please check the configuration.');
        }
    }

    /**
     * Get the single instance of the Database class.
     * This is the entry point for the Singleton pattern.
     *
     * @return Database The singleton instance.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the active PDO connection object.
     * Models will call this method to prepare and execute queries.
     *
     * @return PDO The PDO connection object.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Close the database connection.
     * Sets the instance and connection to null.
     */
    public static function disconnect(): void
    {
        if (self::$instance) {
            self::$instance->connection = null;
            self::$instance = null;
        }
    }
    
    /**
     * Logs database connection errors to a file.
     *
     * @param PDOException $exception The exception to log.
     */
    private function logError(PDOException $exception): void
    {
        $logPath = dirname(__DIR__, 2) . '/storage/logs/app.log';
        $logDir = dirname($logPath);

        // Create log directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $errorMessage = sprintf(
            "[%s] Database Connection Error: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        // Append error to the log file
        error_log($errorMessage, 3, $logPath);
    }


    /**
     * Prevent cloning of the instance.
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the instance.
     */
    public function __wakeup() {}
}