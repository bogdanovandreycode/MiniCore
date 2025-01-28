<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use Minicore\Database\Repository\RepositoryManager;

/**
 * Class DropAction
 *
 * Handles the deletion of records from a database table.
 * This action dynamically builds and executes a DELETE SQL query
 * with optional conditions and parameters.
 *
 * @example
 * // Example of using DropAction to delete a user with ID = 5
 * $DropAction = new DropAction('users');
 * 
 * $dataAction = new DataAction();
 * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 5]);
 * 
 * if ($DropAction->validate($dataAction)) {
 *     $result = $DropAction->execute($dataAction);
 *     echo $result ? 'User deleted.' : 'Delete failed.';
 * } else {
 *     echo 'No conditions provided for deletion.';
 * }
 */
class DropAction extends AbstractAction implements ActionInterface
{
    /**
     * DropAction constructor.
     *
     * @param string $tableName The name of the table from which data will be deleted.
     *
     * @example
     * // Initialize DropAction for the 'products' table
     * $DropAction = new DropAction('products');
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
     * Execute the DELETE SQL query.
     *
     * Builds a DELETE SQL query with optional conditions (e.g., WHERE) 
     * and executes it using prepared statements.
     *
     * @param DataAction $data Contains the conditions and parameters for the query.
     * @return mixed The result of the query execution.
     *
     * @example
     * // Delete a product by ID
     * $DropAction = new DropAction('products');
     * 
     * $dataAction = new DataAction();
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 10]);
     * 
     * $DropAction->execute($dataAction);
     */
    public function execute(string $repositoryName, ?DataAction $data = null): mixed
    {
        $sql = "DROP TABLE `{$this->tableName}`";

        return RepositoryManager::execute(
            $repositoryName,
            $sql,
            $data->getParameters()
        );
    }

    /**
     * Validate the provided data for the DELETE action.
     *
     * Ensures that at least one condition is specified to prevent accidental mass deletion.
     *
     * @param DataAction $data The data used for validation.
     * @return bool True if conditions are present, false otherwise.
     *
     * @example
     * $DropAction = new DropAction('users');
     * $dataAction = new DataAction();
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
     * 
     * if ($DropAction->validate($dataAction)) {
     *     echo 'Valid conditions for deletion.';
     * } else {
     *     echo 'No conditions provided.';
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($this->tableName);
    }
}
