<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use MiniCore\Database\Repository\RepositoryManager;

/**
 * Class DeleteAction
 *
 * Handles the deletion of records from a database table.
 * This action dynamically builds and executes a DELETE SQL query
 * with optional conditions and parameters to ensure data integrity.
 *
 * @package MiniCore\Database\DefaultAction
 *
 * @example
 * // Example of using DeleteAction to delete a user with ID = 5
 * $deleteAction = new DeleteAction('users');
 * 
 * $dataAction = new DataAction();
 * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 5]);
 * 
 * if ($deleteAction->validate($dataAction)) {
 *     $result = $deleteAction->execute('mysql', $dataAction);
 *     echo $result ? 'User deleted.' : 'Delete failed.';
 * } else {
 *     echo 'No conditions provided for deletion.';
 * }
 */
class DeleteAction extends AbstractAction implements ActionInterface
{
    /**
     * DeleteAction constructor.
     *
     * Initializes a new instance for deleting records from a specified table.
     *
     * @param string $tableName The name of the table from which data will be deleted.
     *
     * @example
     * // Initialize DeleteAction for the 'products' table
     * $deleteAction = new DeleteAction('products');
     */
    public function __construct(
        public string $tableName,
    ) {
        parent::__construct(
            'delete',
            ['mysql', 'postgresql']
        );
    }

    /**
     * Execute the DELETE SQL query.
     *
     * Constructs a DELETE SQL query with the specified conditions (e.g., WHERE)
     * and executes it using a prepared statement.
     *
     * @param string $repositoryName The repository where the deletion should occur.
     * @param DataAction|null $data Contains the conditions and parameters for the query.
     * @return mixed The result of the query execution.
     *
     * @example
     * // Delete a product by ID
     * $deleteAction = new DeleteAction('products');
     * 
     * $dataAction = new DataAction();
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 10]);
     * 
     * $deleteAction->execute('mysql', $dataAction);
     */
    public function execute(string $repositoryName, ?DataAction $data): mixed
    {
        if (empty($data) || empty($data->getProperties())) {
            throw new \InvalidArgumentException("DELETE operation requires at least one condition to prevent mass deletion.");
        }

        $sql = "DELETE FROM {$this->tableName}";

        foreach ($data->getProperties() as $property) {
            $sql .= " {$property['type']} {$property['condition']}";
        }

        return RepositoryManager::execute(
            $repositoryName,
            $sql,
            $data->getParameters()
        );
    }

    /**
     * Validate the provided data for the DELETE action.
     *
     * Ensures that at least one condition (e.g., WHERE) is specified
     * to prevent accidental mass deletion.
     *
     * @param DataAction $data The data containing conditions for validation.
     * @return bool True if at least one condition is present, false otherwise.
     *
     * @example
     * $deleteAction = new DeleteAction('users');
     * $dataAction = new DataAction();
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
     * 
     * if ($deleteAction->validate($dataAction)) {
     *     echo 'Valid conditions for deletion.';
     * } else {
     *     echo 'No conditions provided.';
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($data->getProperties());
    }
}
