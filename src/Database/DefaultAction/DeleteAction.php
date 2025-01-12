<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\ActionInterface;
use MiniCore\Database\DataAction;
use MiniCore\Database\DataBase;

/**
 * Class DeleteAction
 *
 * Handles the deletion of records from a database table.
 * This action dynamically builds and executes a DELETE SQL query
 * with optional conditions and parameters.
 *
 * @example
 * // Example of using DeleteAction to delete a user with ID = 5
 * $deleteAction = new DeleteAction('users');
 * 
 * $dataAction = new DataAction();
 * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 5]);
 * 
 * if ($deleteAction->validate($dataAction)) {
 *     $result = $deleteAction->execute($dataAction);
 *     echo $result ? 'User deleted.' : 'Delete failed.';
 * } else {
 *     echo 'No conditions provided for deletion.';
 * }
 */
class DeleteAction implements ActionInterface
{
    /**
     * DeleteAction constructor.
     *
     * @param string $tableName The name of the table from which data will be deleted.
     *
     * @example
     * // Initialize DeleteAction for the 'products' table
     * $deleteAction = new DeleteAction('products');
     */
    public function __construct(
        public string $tableName,
    ) {}

    /**
     * Get the name of the action.
     *
     * @return string The action name ('delete').
     *
     * @example
     * $deleteAction = new DeleteAction('users');
     * echo $deleteAction->getName(); // Output: delete
     */
    public function getName(): string
    {
        return 'delete';
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
     * $deleteAction = new DeleteAction('products');
     * 
     * $dataAction = new DataAction();
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 10]);
     * 
     * $deleteAction->execute($dataAction);
     */
    public function execute(DataAction $data): mixed
    {
        $sql = "DELETE FROM {$this->tableName}";

        foreach ($data->getProperties() as $property) {
            $sql .= " {$property['type']} {$property['condition']}";
        }

        $parameters = $data->getParameters();
        return DataBase::execute($sql, $parameters);
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
        $hasConditions = !empty($data->getProperties());
        return $hasConditions;
    }
}
