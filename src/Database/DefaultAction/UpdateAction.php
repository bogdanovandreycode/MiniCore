<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\ActionInterface;
use MiniCore\Database\DataAction;
use MiniCore\Database\DataBase;

/**
 * Class UpdateAction
 *
 * Handles the update of records in a database table.
 * Dynamically builds and executes an `UPDATE` SQL query with optional conditions and parameters.
 *
 * @example
 * // Example of updating a user's status in the 'users' table:
 * $dataAction = new DataAction();
 * $dataAction->addColumn('status');
 * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
 * $dataAction->addParameters(['set_status' => 'active']);
 *
 * $updateAction = new UpdateAction('users');
 * $updateAction->execute($dataAction);
 */
class UpdateAction implements ActionInterface
{
    /**
     * UpdateAction constructor.
     *
     * @param string $tableName The name of the table to update data in.
     */
    public function __construct(
        public string $tableName
    ) {}

    /**
     * Get the name of the action.
     *
     * @return string The action name ('update').
     */
    public function getName(): string
    {
        return 'update';
    }

    /**
     * Execute the `UPDATE` SQL query.
     *
     * Dynamically builds and executes an `UPDATE` query using provided columns and conditions.
     * Uses prepared statements for security.
     *
     * @param DataAction $data The data containing columns to update, conditions, and parameters.
     * @return mixed The result of the query execution.
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('status');
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
     * $dataAction->addParameters(['set_status' => 'inactive']);
     *
     * $updateAction = new UpdateAction('users');
     * $updateAction->execute($dataAction);
     */
    public function execute(DataAction $data): mixed
    {
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
        return DataBase::execute($sql, $parameters);
    }

    /**
     * Validate the provided data for the update action.
     *
     * Ensures that there are columns specified for the `UPDATE` query.
     *
     * @param DataAction $data The data used for validation.
     * @return bool True if there are columns to update, false otherwise.
     *
     * @example
     * if ($updateAction->validate($dataAction)) {
     *     $updateAction->execute($dataAction);
     * } else {
     *     echo "No columns provided for the update.";
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($data->getColumns());
    }
}
