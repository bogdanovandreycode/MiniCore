<?php

namespace MiniCore\Database\Table;

use Exception;
use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\ActionInterface;
use MiniCore\Database\DefaultAction\CreateAction;
use MiniCore\Database\DefaultAction\DeleteAction;
use MiniCore\Database\DefaultAction\DropAction;
use MiniCore\Database\DefaultAction\InsertAction;
use MiniCore\Database\DefaultAction\SelectAction;
use MiniCore\Database\DefaultAction\UpdateAction;
use MiniCore\Database\Repository\RepositoryManager;

/**
 * Class AbstractTable
 *
 * Provides an abstraction for database tables, allowing CRUD operations,
 * table creation, deletion, and structure management.
 *
 * **Core functionalities:**
 * - Defines table schema
 * - Supports table creation and deletion
 * - Executes CRUD operations using actions
 * - Checks if a table exists in the database
 *
 * @package MiniCore\Database\Table
 *
 * @example
 * // Example usage:
 * class UsersTable extends AbstractTable {
 *     public function __construct() {
 *         parent::__construct(
 *             'users',
 *             'mysql',
 *             [
 *                 'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
 *                 'name' => 'VARCHAR(255) NOT NULL',
 *                 'email' => 'VARCHAR(255) UNIQUE NOT NULL',
 *                 'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
 *             ]
 *         );
 *     }
 * }
 * 
 * $usersTable = new UsersTable();
 * $usersTable->create(); // Create table in the database
 */
abstract class AbstractTable
{
    /**
     * @var ActionInterface[] List of available actions for the table.
     */
    protected array $actions = [];

    /**
     * AbstractTable constructor.
     *
     * @param string $name The table name.
     * @param string $repositoryName The database repository name.
     * @param array $scheme The schema definition of the table as an associative array.
     *
     * @example
     * $table = new UsersTable();
     */
    public function __construct(
        protected string $name,
        protected string $repositoryName,
        protected array $scheme
    ) {
        // Register default CRUD actions
        $this->actions = [
            'insert' => new InsertAction($this->name),
            'select' => new SelectAction($this->name),
            'update' => new UpdateAction($this->name),
            'delete' => new DeleteAction($this->name),
            'create' => new CreateAction($this->name),
            'drop' => new DropAction($this->name),
        ];
    }

    /**
     * Create the table in the database.
     *
     * @return void
     * @throws Exception If the schema is incorrect or the action is unavailable.
     *
     * @example
     * $table->create();
     */
    public function create(): void
    {
        if (!$this->existAvailableAction('create')) {
            throw new Exception("Error: Action 'create' not found", 1);
        }

        if (empty($this->scheme)) {
            throw new Exception("Error: Incorrect schema", 1);
        }

        $data = new DataAction();
        $data->addParameters($this->scheme);
        $this->actions['create']->execute($this->repositoryName, $data);
    }

    /**
     * Check if a specific action is available for the table.
     *
     * @param string $actionName The name of the action to check.
     * @return bool True if the action is available, false otherwise.
     *
     * @example
     * if ($table->existAvailableAction('drop')) {
     *     echo "Drop action is available.";
     * }
     */
    public function existAvailableAction(string $actionName): bool
    {
        return isset($this->actions[$actionName]) &&
            $this->actions[$actionName]->checkAvailabilityRepository($this->repositoryName);
    }

    /**
     * Drop the table from the database.
     *
     * @return void
     * @throws Exception If the schema is incorrect or the action is unavailable.
     *
     * @example
     * $table->drop();
     */
    public function drop(): void
    {
        if (!$this->existAvailableAction('drop')) {
            throw new Exception("Error: Action 'drop' not found", 1);
        }

        if (empty($this->scheme)) {
            throw new Exception("Error: Incorrect scheme", 1);
        }

        $data = new DataAction();
        $data->addParameters($this->scheme);
        $this->actions['drop']->execute($this->repositoryName, $data);
    }

    /**
     * Check if the table exists in the database.
     *
     * @return bool True if the table exists, false otherwise.
     *
     * @example
     * if ($table->exist()) {
     *     echo "Table exists.";
     * }
     */
    public function exist(): bool
    {
        $query = "SHOW TABLES LIKE :table_name";

        $result = RepositoryManager::query(
            $this->repositoryName,
            $query,
            ['table_name' => $this->name]
        );

        return !empty($result);
    }

    /**
     * Convert the table schema into a formatted SQL string.
     *
     * @return string The formatted schema string.
     *
     * @example
     * echo $table->getSchemeToString();
     * // Output: "id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL"
     */
    public function getSchemeToString(): string
    {
        return implode(', ', array_map(
            fn($field, $definition) => "$field $definition",
            array_keys($this->scheme),
            $this->scheme
        ));
    }

    /**
     * Add a custom action to the table.
     *
     * @param ActionInterface $action The action to be added.
     *
     * @example
     * $table->addAction(new CustomAction($tableName));
     */
    public function addAction(ActionInterface $action): void
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * Remove an action by its name.
     *
     * @param string $actionName The name of the action to remove.
     *
     * @example
     * $table->removeAction('update');
     */
    public function removeAction(string $actionName): void
    {
        unset($this->actions[$actionName]);
    }

    /**
     * Execute an action by its name with the provided data.
     *
     * @param string $repositoryName The name of the repository.
     * @param string $actionName The name of the action to execute.
     * @param DataAction $data The data for the action.
     * @return mixed The result of the executed action or null if the action is unavailable.
     * @throws Exception If the repository name is not found.
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('name');
     * $dataAction->addParameters(['name' => 'John']);
     * $table->execute('mysql', 'insert', $dataAction);
     */
    public function execute(string $repositoryName, string $actionName, DataAction $data): mixed
    {
        if (empty($repositoryName)) {
            throw new Exception("Error: Repository name not found", 1);
        }

        if ($this->existAvailableAction($actionName)) {
            return $this->actions[$actionName]->execute($repositoryName, $data);
        }

        return null;
    }

    /**
     * Get the name of the table.
     *
     * @return string The name of the table.
     *
     * @example
     * echo $table->getName(); // Output: "users"
     */
    public function getName(): string
    {
        return $this->name;
    }
}
