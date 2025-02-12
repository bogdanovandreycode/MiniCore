<?php

namespace MiniCore\Database\DefaultRepository;

use PDO;
use Exception;
use PDOException;
use MiniCore\Database\Table\TableManager;
use Minicore\Database\Repository\RepositoryManager;
use MiniCore\Database\Repository\RepositoryInterface;

/**
 * Class MySqlDatabase
 *
 * Provides an abstraction layer for interacting with a MySQL database using PDO.
 * Supports connection management, query execution, table management, and migrations.
 *
 * @package MiniCore\Database\DefaultRepository
 *
 * @example
 * // Example usage:
 * $config = [
 *     'host' => 'localhost',
 *     'dbname' => 'test_db',
 *     'user' => 'root',
 *     'password' => 'secret'
 * ];
 * 
 * $database = new MySqlDatabase($config);
 * if ($database->isConnected()) {
 *     $users = $database->query("SELECT * FROM users");
 *     print_r($users);
 * }
 */
class MySqlDatabase implements RepositoryInterface
{
    /**
     * @var PDO|null Active PDO database connection.
     */
    private ?PDO $connection = null;

    /**
     * @var TableManager Manager for handling database tables.
     */
    private TableManager $list;

    /**
     * MySqlDatabase constructor.
     *
     * Initializes the database connection and registers the repository.
     *
     * @param array $config Database configuration parameters.
     * @param array $tables List of tables to be managed.
     *
     * @example
     * $database = new MySqlDatabase([
     *     'host' => 'localhost',
     *     'dbname' => 'test_db',
     *     'user' => 'root',
     *     'password' => 'secret'
     * ]);
     */
    public function __construct(
        array $config,
        array $tables = []
    ) {
        $this->list = new TableManager($this, $tables);
        $this->connect($config);
        RepositoryManager::addRepository($this);
    }

    /**
     * Get the table manager instance.
     *
     * @return TableManager The table manager instance.
     */
    public function getList(): TableManager
    {
        return $this->list;
    }

    /**
     * Get the active database connection.
     *
     * @return PDO The current PDO connection.
     *
     * @example
     * $pdo = $database->getConnection();
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Establish a database connection.
     *
     * @param array $config Database configuration parameters.
     * @throws PDOException If the connection fails.
     */
    public function connect(array $config): void
    {
        if ($this->isConnected()) {
            return;
        }

        self::validateConfig($config);
        $config['charset'] = empty($config['charset']) ? 'utf8mb4' : $config['charset'];
        $host = $config['host'];
        $dbname = $config['dbname'];
        $user = $config['user'];
        $password = $config['password'];
        $charset = $config['charset'];
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        try {
            self::$connection = new PDO($dsn, $user, $password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Validate the provided database configuration.
     *
     * @param array $config The database configuration parameters.
     * @throws Exception If any required parameter is missing.
     */
    private function validateConfig(array $config): void
    {
        $errorConditions = [
            [
                'condition' => empty($config['host']),
                'message' => 'Host not set'
            ],
            [
                'condition' => empty($config['dbname']),
                'message' => 'Name database not set'
            ],
            [
                'condition' => empty($config['user']),
                'message' => 'User not set'
            ],
            [
                'condition' => empty($config['password']),
                'message' => 'Password not set'
            ],
        ];

        $errorMessages = [];

        foreach ($errorConditions as $error) {
            if ($error['condition']) {
                $errorMessages[] = $error['message'];
            }
        }

        if (!empty($errorMessages)) {
            throw new Exception(implode("\n", $errorMessages), 1);
        }
    }

    /**
     * Execute a SELECT SQL query and return the results.
     *
     * @param string $sql The SQL query.
     * @param array $params Parameters for the prepared statement.
     * @return array The query result as an associative array.
     *
     * @example
     * $users = $database->query("SELECT * FROM users WHERE role_id = :role_id", ['role_id' => 1]);
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute an INSERT, UPDATE, or DELETE SQL statement.
     *
     * @param string $sql The SQL query.
     * @param array $params Parameters for the prepared statement.
     * @return bool True if execution was successful, false otherwise.
     *
     * @example
     * $database->execute("DELETE FROM users WHERE id = :id", ['id' => 5]);
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Check if the database connection is active.
     *
     * @return bool True if connected, false otherwise.
     */
    public function isConnected(): bool
    {
        if ($this->connection instanceof PDO) {
            try {
                $this->connection->query("SELECT 1");
                return true;
            } catch (PDOException) {
                return false;
            }
        }
        return false;
    }

    /**
     * Close the database connection.
     *
     * @example
     * $database->close();
     */
    public function close(): void
    {
        $this->connection = null;
    }

    /**
     * Get the repository name.
     *
     * @return string The repository name ('mysql').
     *
     * @example
     * echo $database->getNameRepository(); // Output: 'mysql'
     */
    public function getNameRepository(): string
    {
        return 'mysql';
    }
}
