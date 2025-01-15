<?php

namespace MiniCore\Database;

use PDO;
use PDOException;

/**
 * Class DataBase
 *
 * Provides an abstraction layer for interacting with the database using PDO.
 * Supports connection management, query execution, table management, and migrations.
 *
 * @package MiniCore\Database
 */
class DataBase
{
    /**
     * @var PDO Active PDO database connection.
     */
    private static PDO $connection;

    /**
     * @var array List of registered tables for management.
     */
    private static array $tables = [];

    /**
     * Get the active database connection.
     *
     * @return PDO The current PDO connection.
     * 
     * @example
     * $pdo = DataBase::getConnection();
     */
    public static function getConnection(): PDO
    {
        return self::$connection;
    }

    /**
     * Establish a new database connection.
     *
     * @param string $host Database host (e.g., 'localhost').
     * @param string $dbname Database name.
     * @param string $user Database username.
     * @param string $password Database password.
     * @param string $charset Character set for the connection (default: utf8mb4).
     * @throws PDOException If the connection fails.
     * 
     * @example
     * DataBase::setConnection('localhost', 'my_database', 'root', 'password');
     */
    public static function setConnection(string $host, string $dbname, string $user, string $password, string $charset = 'utf8mb4')
    {
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
     * Execute a SELECT SQL query and return the results.
     *
     * @param string $sql The SQL query.
     * @param array $params Parameters for the prepared statement.
     * @return array The query result as an associative array.
     * 
     * @example
     * $users = DataBase::query("SELECT * FROM users WHERE role_id = :role_id", ['role_id' => 1]);
     */
    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::$connection->prepare($sql);
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
     * DataBase::execute("DELETE FROM users WHERE id = :id", ['id' => 5]);
     */
    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::$connection->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Register a new table for database management.
     *
     * @param Table $table The table instance to register.
     * 
     * @example
     * DataBase::addTable(new UsersTable());
     */
    public static function addTable(Table $table): void
    {
        self::$tables[] = $table;
    }

    /**
     * Unregister a table from the database manager.
     *
     * @param Table $table The table instance to remove.
     * 
     * @example
     * DataBase::removeTable(new UsersTable());
     */
    public static function removeTable(Table $table): void
    {
        $index = array_search($table, self::$tables, true);

        if ($index !== false) {
            unset(self::$tables[$index]);
            self::$tables = array_values(self::$tables); // Reindex the array
        }
    }

    /**
     * Create all registered tables if they do not exist.
     * 
     * @example
     * DataBase::createTables();
     */
    public static function createTables(): void
    {
        foreach (self::$tables as $table) {
            if (!$table->exist()) {
                $table->create();
            }
        }
    }

    /**
     * Drop all registered tables if they exist.
     * 
     * @example
     * DataBase::dropTables();
     */
    public static function dropTables(): void
    {
        foreach (self::$tables as $table) {
            if ($table->exist()) {
                $table->drop();
            }
        }
    }

    /**
     * Apply a database migration.
     *
     * @param MigrationInterface $migration The migration instance.
     * 
     * @example
     * DataBase::migrate(new CreateUsersTable());
     */
    public static function migrate(MigrationInterface $migration): void
    {
        if ($migration->up()) {
            echo "Migration applied successfully.\n";
        } else {
            echo "Migration failed.\n";
        }
    }

    /**
     * Roll back a database migration.
     *
     * @param MigrationInterface $migration The migration instance.
     * 
     * @example
     * DataBase::rollback(new CreateUsersTable());
     */
    public static function rollback(MigrationInterface $migration): void
    {
        if ($migration->down()) {
            echo "Rollback migration applied successfully.\n";
        } else {
            echo "Rollback migration failed.\n";
        }
    }
}
