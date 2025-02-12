<?php

namespace Minicore\Database\Repository;

use Exception;
use MiniCore\Database\Repository\RepositoryInterface;

/**
 * Class RepositoryManager
 *
 * Manages a collection of database repositories.
 * Provides methods to register, retrieve, remove, and execute queries on repositories.
 *
 * @package Minicore\Database\Repository
 *
 * @example
 * // Example usage:
 * $mysqlRepository = new MySqlRepository(['host' => 'localhost', 'dbname' => 'test']);
 * RepositoryManager::addRepository($mysqlRepository);
 *
 * // Execute a query on the registered repository
 * $users = RepositoryManager::query('mysql', "SELECT * FROM users WHERE status = :status", ['status' => 'active']);
 * print_r($users);
 *
 * // Remove a repository
 * RepositoryManager::removeRepository('mysql');
 */
class RepositoryManager
{
    /**
     * List of registered repositories.
     *
     * @var RepositoryInterface[]
     */
    private static array $repositories = [];

    /**
     * Register a repository.
     *
     * @param RepositoryInterface $repository The repository to register.
     * @return void
     *
     * @example
     * $repository = new MySqlRepository(['host' => 'localhost', 'dbname' => 'test']);
     * RepositoryManager::addRepository($repository);
     */
    public static function addRepository(RepositoryInterface $repository): void
    {
        self::$repositories[$repository->getNameRepository()] = $repository;
    }

    /**
     * Validate if a repository name exists.
     *
     * @param string $name The repository name to validate.
     * @return void
     * @throws Exception If the repository does not exist.
     *
     * @example
     * RepositoryManager::validateNameRepository('mysql');
     */
    private static function validateNameRepository(string $name): void
    {
        if (empty($name) || !isset(self::$repositories[$name])) {
            throw new Exception("Repository name not found", 1);
        }
    }

    /**
     * Retrieve a repository by name.
     *
     * @param string $name The name of the repository.
     * @return RepositoryInterface The corresponding repository instance.
     * @throws Exception If the repository does not exist.
     *
     * @example
     * $repository = RepositoryManager::getRepository('mysql');
     */
    public static function getRepository(string $name): RepositoryInterface
    {
        self::validateNameRepository($name);
        return self::$repositories[$name];
    }

    /**
     * Remove a repository by name.
     *
     * @param string $name The name of the repository to remove.
     * @return void
     * @throws Exception If the repository does not exist.
     *
     * @example
     * RepositoryManager::removeRepository('mysql');
     */
    public static function removeRepository(string $name): void
    {
        self::validateNameRepository($name);
        unset(self::$repositories[$name]);
    }

    /**
     * Execute a SELECT SQL query on a specific repository.
     *
     * @param string $nameRepository The name of the repository.
     * @param string $sql The SQL query string.
     * @param array $params Optional parameters for prepared statements.
     * @return array The query result as an associative array.
     * @throws Exception If the repository does not exist or if an error occurs during execution.
     *
     * @example
     * $users = RepositoryManager::query('mysql', "SELECT * FROM users WHERE role = :role", ['role' => 'admin']);
     */
    public static function query(string $nameRepository, string $sql, array $params = []): array
    {
        self::validateNameRepository($nameRepository);

        try {
            return self::$repositories[$nameRepository]->query($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Error during query execution: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * Execute an INSERT, UPDATE, or DELETE SQL query on a specific repository.
     *
     * @param string $nameRepository The name of the repository.
     * @param string $sql The SQL query string.
     * @param array $params Optional parameters for prepared statements.
     * @return bool True if the execution was successful, false otherwise.
     * @throws Exception If the repository does not exist or if an error occurs during execution.
     *
     * @example
     * $success = RepositoryManager::execute('mysql', "UPDATE users SET status = :status WHERE id = :id", ['status' => 'active', 'id' => 3]);
     */
    public static function execute(string $nameRepository, string $sql, array $params = []): bool
    {
        self::validateNameRepository($nameRepository);

        try {
            return self::$repositories[$nameRepository]->execute($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Error during query execution: " . $e->getMessage(), 1, $e);
        }
    }
}
