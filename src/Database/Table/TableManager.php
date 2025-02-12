<?php

namespace MiniCore\Database\Table;

use MiniCore\Database\Table\AbstractTable;
use MiniCore\Database\Repository\RepositoryInterface;
use MiniCore\Database\DefaultAction\ShowTablesAction;

/**
 * Class TableManager
 *
 * Manages database tables within a repository, providing methods to register,
 * create, drop, and retrieve tables dynamically.
 *
 * @package MiniCore\Database\Table
 *
 * @example
 * // Example usage:
 * $repository = new MySqlDatabase(['host' => 'localhost', 'dbname' => 'test']);
 * $tableManager = new TableManager($repository, [new UsersTable()]);
 *
 * // Create tables if they don't exist
 * $tableManager->createTables();
 *
 * // Get all managed tables
 * $tables = $tableManager->getTables();
 * print_r($tables);
 *
 * // Drop tables
 * $tableManager->dropTables();
 */
class TableManager
{
    /**
     * @var array List of existing tables in the database.
     */
    private array $existingTables = [];

    /**
     * TableManager constructor.
     *
     * Initializes the table manager with a repository and a list of tables.
     * Automatically checks for existing tables and creates them if necessary.
     *
     * @param RepositoryInterface $repository The database repository instance.
     * @param array $tables List of tables to manage.
     *
     * @example
     * $repository = new MySqlDatabase(['host' => 'localhost', 'dbname' => 'test']);
     * $tableManager = new TableManager($repository, [new UsersTable()]);
     */
    public function __construct(
        private RepositoryInterface $repository,
        private array $tables = []
    ) {
        $this->updateExistingTables();
        $this->createTables();
    }

    /**
     * Register a new table for management.
     *
     * @param AbstractTable $table The table instance to register.
     * @return void
     *
     * @example
     * $tableManager->addTable(new UsersTable());
     */
    public function addTable(AbstractTable $table): void
    {
        $this->tables[] = $table;
    }

    /**
     * Unregister a table from management.
     *
     * @param AbstractTable $table The table instance to remove.
     * @return void
     *
     * @example
     * $tableManager->removeTable(new UsersTable());
     */
    public function removeTable(AbstractTable $table): void
    {
        $index = array_search($table, $this->tables, true);

        if ($index !== false) {
            unset($this->tables[$index]);
            $this->tables = array_values($this->tables); // Reindex the array
        }
    }

    /**
     * Create all registered tables if they do not already exist.
     *
     * @return void
     *
     * @example
     * $tableManager->createTables();
     */
    public function createTables(): void
    {
        foreach ($this->tables as $table) {
            if (!in_array($table->getName(), $this->existingTables)) {
                $table->create();
            }
        }

        $this->updateExistingTables();
    }

    /**
     * Drop all registered tables if they exist.
     *
     * @return void
     *
     * @example
     * $tableManager->dropTables();
     */
    public function dropTables(): void
    {
        foreach ($this->tables as $table) {
            if (in_array($table->getName(), $this->existingTables)) {
                $table->drop();
            }
        }

        $this->updateExistingTables();
    }

    /**
     * Get all registered tables.
     *
     * @return array List of registered table instances.
     *
     * @example
     * $tables = $tableManager->getTables();
     * print_r($tables);
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * Update the list of existing tables in the database.
     *
     * @return void
     */
    private function updateExistingTables(): void
    {
        $showTablesAction = new ShowTablesAction();
        $this->existingTables = $showTablesAction->execute($this->repository->getNameRepository());
    }
}
