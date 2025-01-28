<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use Minicore\Database\RepositoryManager;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;


/**
 * Class SelectAction
 *
 * Handles the selection of data from a database table.
 * Dynamically builds and executes a `SELECT` SQL query with optional conditions and parameters.
 *
 * @example
 * // Example of selecting user data from the 'users' table:
 * $dataAction = new DataAction();
 * $dataAction->addColumn('id');
 * $dataAction->addColumn('username');
 * $dataAction->addProperty('WHERE', 'status = :status', ['status' => 'active']);
 *
 * $selectAction = new SelectAction('users');
 * $result = $selectAction->execute($dataAction);
 */
class SelectAction extends AbstractAction implements ActionInterface
{
    /**
     * SelectAction constructor.
     *
     * @param string $tableName The name of the table to select data from.
     */
    public function __construct(
        public string $tableName
    ) {
        parent::__construct(
            'select',
            ['mysql', 'postgresql']

        );
    }

    /**
     * Execute the `SELECT` SQL query.
     *
     * Dynamically builds a `SELECT` query using provided columns, conditions, and parameters,
     * and executes it using prepared statements for security.
     *
     * @param DataAction $data The data containing columns, conditions, and parameters for the query.
     * @return mixed The result of the query execution (an array of results or false on failure).
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('username');
     * $dataAction->addProperty('WHERE', 'status = :status', ['status' => 'active']);
     *
     * $selectAction = new SelectAction('users');
     * $result = $selectAction->execute($dataAction);
     *
     * foreach ($result as $user) {
     *     echo $user['username'];
     * }
     */
    public function execute(string $repositoryName, DataAction $data): mixed
    {
        $selectColumns = $data->getColumns();
        $sql = "SELECT " . (!empty($selectColumns) ? implode(', ', $selectColumns) : '*');
        $sql .= " FROM {$this->tableName}";

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
     * Validate the provided data for the select action.
     *
     * Ensures that at least one column is specified for the `SELECT` query.
     *
     * @param DataAction $data The data used for validation.
     * @return bool True if at least one column is provided, false otherwise.
     *
     * @example
     * if ($selectAction->validate($dataAction)) {
     *     $result = $selectAction->execute($dataAction);
     * } else {
     *     echo "No columns specified for selection.";
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($data->getColumns());
    }
}
