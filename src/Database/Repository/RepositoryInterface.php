<?php

namespace MiniCore\Database\Repository;

use MiniCore\Database\Table\TableManager;

/**
 * Interface RepositoryInterface
 *
 * Defines the contract for database repository implementations.
 * A repository provides an abstraction layer for database interactions, 
 * including connecting to a database, executing queries, and managing tables.
 *
 * @package MiniCore\Database\Repository
 *
 * @example
 * // Example implementation:
 * class MySqlRepository implements RepositoryInterface
 * {
 *     public function __construct(array $config, array $tables = [])
 *     {
 *         // Initialize repository
 *         $this->connect($config);
 *     }
 *
 *     public function connect(array $config): void
 *     {
 *         // Establish a database connection
 *     }
 *
 *     public function isConnected(): bool
 *     {
 *         // Check if connection is active
 *     }
 *
 *     public function query(string $sql, array $params = []): array
 *     {
 *         // Execute a SELECT query
 *     }
 *
 *     public function execute(string $sql, array $params = []): bool
 *     {
 *         // Execute an INSERT, UPDATE, or DELETE query
 *     }
 *
 *     public function close(): void
 *     {
 *         // Close the database connection
 *     }
 *
 *     public function getNameRepository(): string
 *     {
 *         return 'mysql';
 *     }
 *
 *     public function getList(): TableManager
 *     {
 *         return new TableManager();
 *     }
 * }
 */
interface RepositoryInterface
{
    /**
     * RepositoryInterface constructor.
     *
     * @param array $config Database connection configuration.
     * @param array $tables List of tables to manage.
     *
     * @example
     * $repository = new MySqlRepository(['host' => 'localhost', 'dbname' => 'test']);
     */
    public function __construct(array $config, array $tables = []);

    /**
     * Establish a connection to the database.
     *
     * @param array $config Connection configuration parameters.
     * @return void
     *
     * @example
     * $repository->connect(['host' => 'localhost', 'dbname' => 'test']);
     */
    public function connect(array $config): void;

    /**
     * Check if the database connection is active.
     *
     * @return bool True if the connection is active, false otherwise.
     *
     * @example
     * if ($repository->isConnected()) {
     *     echo "Connected to the database.";
     * }
     */
    public function isConnected(): bool;

    /**
     * Get the table manager associated with the repository.
     *
     * @return TableManager The table manager instance.
     *
     * @example
     * $tableManager = $repository->getList();
     */
    public function getList(): TableManager;

    /**
     * Execute a SELECT SQL query and return the results.
     *
     * @param string $sql The SQL query string.
     * @param array $params Optional parameters for prepared statements.
     * @return array The query result as an associative array.
     *
     * @example
     * $users = $repository->query("SELECT * FROM users WHERE role_id = :role_id", ['role_id' => 1]);
     */
    public function query(string $sql, array $params = []): array;

    /**
     * Execute an INSERT, UPDATE, or DELETE SQL statement.
     *
     * @param string $sql The SQL query string.
     * @param array $params Optional parameters for prepared statements.
     * @return bool True if the execution was successful, false otherwise.
     *
     * @example
     * $success = $repository->execute("UPDATE users SET status = :status WHERE id = :id", ['status' => 'active', 'id' => 3]);
     */
    public function execute(string $sql, array $params = []): bool;

    /**
     * Close the database connection.
     *
     * @return void
     *
     * @example
     * $repository->close();
     */
    public function close(): void;

    /**
     * Get the name of the repository type (e.g., MySQL, PostgreSQL).
     *
     * @return string The repository name.
     *
     * @example
     * echo $repository->getNameRepository(); // Output: 'mysql'
     */
    public function getNameRepository(): string;
}
