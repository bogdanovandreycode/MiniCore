<?php

namespace MiniCore\Database\Table;

use Exception;
use MiniCore\Database\Table\AbstractTable;
use MiniCore\Database\Repository\MySqlDatabase;
use MiniCore\Database\Repository\RepositoryInterface;

/**
 * Class TableManager
 *
 * Provides an abstraction layer for interacting with the database using PDO.
 * Supports connection management, query execution, table management, and migrations.
 *
 * @package MiniCore\Database
 */
class TableManager
{
    /**
     * @var array List of registered tables for management.
     */
    public function __construct(
        private RepositoryInterface $repository,
        private array $tables = [],
    ) {}

    /**
     * Register a new table for database management.
     *
     * @param AbstractTable $table The table instance to register.
     * 
     * @example
     * DataBase::addTable(new UsersTable());
     */
    public function addTable(AbstractTable $table): void
    {
        self::$tables[] = $table;
    }

    /**
     * Unregister a table from the database manager.
     *
     * @param AbstractTable $table The table instance to remove.
     * 
     * @example
     * DataBase::removeTable(new UsersTable());
     */
    public function removeTable(AbstractTable $table): void
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
    public function createTables(): void
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
    public function dropTables(): void
    {
        foreach (self::$tables as $table) {
            if ($table->exist()) {
                $table->drop();
            }
        }
    }

    public function getTables(): array
    {
        //надо переписать и сделать чтобы подгружалось 1 раз
        if (!isset(self::$connection)) {
            throw new Exception('No database connection established.');
        }

        $sql = "SHOW TABLES";
        $result = $this->repository->query($sql);
        return array_map('current', $result);
    }
}
