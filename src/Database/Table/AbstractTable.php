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
use Minicore\Database\Repository\RepositoryManager;

/**
 * Class Table
 *
 * Abstract class that serves as a base for database table operations.
 * Provides core functionalities for managing tables, such as creation, deletion,
 * existence checks, and executing CRUD operations through actions.
 *
 * @package MiniCore\Database
 */
abstract class AbstractTable
{
    /**
     * @var ActionInterface[] List of actions (Insert, Select, Update, Delete) associated with the table.
     */
    protected array $actions = [];

    /**
     * Table constructor.
     *
     * @param string $name The name of the database table.
     * @param array $scheme The schema definition of the table as an associative array.
     *
     * @example
     * [
     *     'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
     *     'name' => 'VARCHAR(255) NOT NULL',
     *     'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
     * ]
     */
    public function __construct(
        protected string $name,
        protected string $repositoryName,
        protected array $scheme,
    ) {
        // Register default CRUD actions for the table.
        $this->actions = [
            new InsertAction($this->name),
            new SelectAction($this->name),
            new UpdateAction($this->name),
            new DeleteAction($this->name),
            new CreateAction($this->name),
            new DropAction($this->name),
        ];
    }

    /**
     * Create the table in the database based on the defined schema.
     *
     * @return void
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
            throw new Exception("Error: Incorrect scheme", 1);
        }

        $data = new DataAction();
        $data->addParameters($this->scheme);
        $this->actions['create']->execute($this->repositoryName, $data);
    }

    public function existAvailableAction(string $actionName)
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === $actionName && $action->checkAvailabilityRepository($this->repositoryName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Drop the table from the database.
     *
     * @return void
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
            'mysql',
            $query,
            ['table_name' => $this->name]
        );

        return !empty($result);
    }

    /**
     * Convert the schema array into a string for SQL execution.
     *
     * @return string The formatted schema string for SQL.
     *
     * @example
     * id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL
     */
    public function getSchemeToString(): string
    {
        $fields = '';

        foreach ($this->scheme as $fieldName => $fieldDefinition) {
            $fields .= "$fieldName $fieldDefinition, ";
        }

        return rtrim($fields, ', ');
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
        $this->actions[] = $action;
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
        foreach ($this->actions as $key => $action) {
            if ($action->getName() === $actionName) {
                unset($this->actions[$key]);
                break;
            }
        }
    }

    /**
     * Execute an action by its name with the provided data.
     *
     * @param string $actionName The name of the action to execute.
     * @param DataAction $data The data for the action.
     * @return mixed The result of the executed action or null if not found.
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('name');
     * $dataAction->addParameters(['name' => 'John']);
     * $table->execute('insert', $dataAction);
     */
    public function execute(string $repositoryName, string $actionName, DataAction $data): mixed
    {
        if (empty($repositoryName)) {
            throw new Exception("Error: Repository name not found", 1);
        }

        foreach ($this->actions as $action) {
            if ($action->getName() === $actionName && $action->checkAvailabilityRepository($repositoryName)) {
                return $action->execute($repositoryName, $data);
            }
        }

        return null;
    }
}
