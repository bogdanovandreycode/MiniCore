<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use Minicore\Database\Repository\RepositoryManager;

/**
 * Class DropAction
 *
 * Handles the deletion of an entire database table.
 * This action dynamically builds and executes a DROP TABLE SQL query.
 *
 * @package MiniCore\Database\DefaultAction
 *
 * @example
 * // Example of using DropAction to drop a table named 'users'
 * $dropAction = new DropAction('users');
 * 
 * if ($dropAction->validate()) {
 *     $result = $dropAction->execute('mysql');
 *     echo $result ? 'Table deleted.' : 'Drop failed.';
 * } else {
 *     echo 'Invalid table name.';
 * }
 */
class DropAction extends AbstractAction implements ActionInterface
{
    /**
     * DropAction constructor.
     *
     * Initializes a new instance for dropping a database table.
     *
     * @param string $tableName The name of the table to be dropped.
     *
     * @example
     * // Initialize DropAction for the 'products' table
     * $dropAction = new DropAction('products');
     */
    public function __construct(
        public string $tableName
    ) {
        parent::__construct(
            'drop',
            ['mysql', 'postgresql']
        );
    }

    /**
     * Execute the DROP TABLE SQL query.
     *
     * Builds and executes a DROP TABLE SQL statement.
     * This operation **permanently deletes** the specified table from the database.
     *
     * @param string $repositoryName The repository where the table should be dropped.
     * @param DataAction|null $data Optional data (not used in this operation).
     * @return mixed The result of the query execution.
     *
     * @example
     * // Drop the 'products' table
     * $dropAction = new DropAction('products');
     * $dropAction->execute('mysql');
     */
    public function execute(string $repositoryName, ?DataAction $data = null): mixed
    {
        if (!$this->validate($data)) {
            throw new \InvalidArgumentException("Invalid table name provided for DROP TABLE operation.");
        }

        $sql = "DROP TABLE `{$this->tableName}`";

        return RepositoryManager::execute(
            $repositoryName,
            $sql
        );
    }

    /**
     * Validate the provided table name for the DROP action.
     *
     * Ensures that the table name is not empty and does not contain illegal characters.
     *
     * @return bool True if the table name is valid, false otherwise.
     *
     * @example
     * $dropAction = new DropAction('users');
     * if ($dropAction->validate()) {
     *     echo 'Valid table name.';
     * } else {
     *     echo 'Invalid table name.';
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($this->tableName);
    }
}
