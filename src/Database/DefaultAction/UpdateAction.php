<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use MiniCore\Database\Repository\RepositoryManager;

/**
 * Class UpdateAction
 *
 * Handles updating records in a database table.
 * This action dynamically builds and executes an `UPDATE` SQL query
 * using provided column values, conditions, and parameters.
 *
 * @package MiniCore\Database\DefaultAction
 *
 * @example
 * // Example of updating a user's status in the 'users' table:
 * $dataAction = new DataAction();
 * $dataAction->addColumn('status');
 * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
 * $dataAction->addParameters(['status' => 'active']);
 *
 * $updateAction = new UpdateAction('users');
 * if ($updateAction->validate($dataAction)) {
 *     $updateAction->execute('mysql', $dataAction);
 * }
 */
class UpdateAction extends AbstractAction implements ActionInterface
{
    /**
     * UpdateAction constructor.
     *
     * Initializes a new instance for updating records in a database table.
     *
     * @param string $tableName The name of the table to update data in.
     *
     * @example
     * // Initialize UpdateAction for the 'products' table
     * $updateAction = new UpdateAction('products');
     */
    public function __construct(
        public string $tableName
    ) {
        parent::__construct(
            'update',
            ['mysql', 'postgresql']
        );
    }

    /**
     * Execute the `UPDATE` SQL query.
     *
     * Dynamically builds an `UPDATE` query using provided columns, conditions, and parameters.
     * Uses prepared statements to prevent SQL injection.
     *
     * @param string $repositoryName The repository (database type) where the update should be executed.
     * @param DataAction|null $data The data containing columns to update, conditions, and parameters.
     * @return mixed The result of the query execution (usually `true` on success).
     *
     * @throws \RuntimeException If no columns are provided for the update operation.
     *
     * @example
     * // Update a user's status in the 'users' table
     * $dataAction = new DataAction();
     * $dataAction->addColumn('status');
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
     * $dataAction->addParameters(['status' => 'inactive']);
     *
     * $updateAction = new UpdateAction('users');
     * $updateAction->execute('mysql', $dataAction);
     */
    public function execute(string $repositoryName, ?DataAction $data): mixed
    {
        if (!$this->validate($data)) {
            throw new \RuntimeException("No columns provided for update operation.");
        }

        $updateColumns = $data->getColumns();
        $setClauses = [];

        foreach ($updateColumns as $column) {
            $setClauses[] = "$column = :set_$column";
        }

        $setClause = implode(', ', $setClauses);
        $whereConditions = implode(' ', $data->getProperty('WHERE'));
        $sql = "UPDATE {$this->tableName} SET $setClause";

        if (!empty($whereConditions)) {
            $sql .= " WHERE $whereConditions";
        }

        $parameters = [];

        foreach ($updateColumns as $column) {
            $parameters["set_$column"] = $data->getParameters()["set_$column"] ?? null;
        }

        $parameters = array_merge($parameters, $data->getParameters());

        return RepositoryManager::execute(
            $repositoryName,
            $sql,
            $parameters
        );
    }

    /**
     * Validate the provided data for the update action.
     *
     * Ensures that at least one column is specified for the `UPDATE` query.
     *
     * @param DataAction $data The data used for validation.
     * @return bool True if at least one column is provided, false otherwise.
     *
     * @example
     * if ($updateAction->validate($dataAction)) {
     *     $updateAction->execute('mysql', $dataAction);
     * } else {
     *     echo "No columns provided for the update.";
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($data->getColumns());
    }
}
