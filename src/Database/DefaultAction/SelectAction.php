<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use Minicore\Database\Repository\RepositoryManager;

/**
 * Class SelectAction
 *
 * Handles the selection of data from a database table.
 * This action dynamically builds and executes a `SELECT` SQL query
 * with optional conditions, filters, and parameters using prepared statements.
 *
 * @package MiniCore\Database\DefaultAction
 *
 * @example
 * // Example of selecting user data from the 'users' table:
 * $dataAction = new DataAction();
 * $dataAction->addColumn('id');
 * $dataAction->addColumn('username');
 * $dataAction->addProperty('WHERE', 'status = :status', ['status' => 'active']);
 *
 * $selectAction = new SelectAction('users');
 * if ($selectAction->validate($dataAction)) {
 *     $result = $selectAction->execute('mysql', $dataAction);
 *     foreach ($result as $user) {
 *         echo $user['username'];
 *     }
 * }
 */
class SelectAction extends AbstractAction implements ActionInterface
{
    /**
     * SelectAction constructor.
     *
     * Initializes a new instance for selecting data from a database table.
     *
     * @param string $tableName The name of the table to select data from.
     *
     * @example
     * // Initialize SelectAction for the 'products' table
     * $selectAction = new SelectAction('products');
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
     * Constructs a `SELECT` SQL statement dynamically based on the provided columns,
     * conditions, and parameters. The query is executed using prepared statements
     * to ensure security.
     *
     * @param string $repositoryName The repository where the select operation should be executed.
     * @param DataAction|null $data The data containing columns, conditions, and parameters for the query.
     * @return array|false The result set as an array of records or `false` on failure.
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('username');
     * $dataAction->addProperty('WHERE', 'status = :status', ['status' => 'active']);
     *
     * $selectAction = new SelectAction('users');
     * $result = $selectAction->execute('mysql', $dataAction);
     *
     * foreach ($result as $user) {
     *     echo $user['username'];
     * }
     */
    public function execute(string $repositoryName, ?DataAction $data): array|false
    {
        if (!$this->validate($data)) {
            throw new \RuntimeException("No columns provided for SELECT operation.");
        }

        $columns = $data->getColumns();
        $columnsList = !empty($columns) ? implode(', ', array_map(fn($col) => "`$col`", $columns)) : '*';
        $sql = "SELECT $columnsList FROM `{$this->tableName}`";

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
     * @param DataAction $data The data containing the query configuration.
     * @return bool True if at least one column is provided, false otherwise.
     *
     * @example
     * if ($selectAction->validate($dataAction)) {
     *     $result = $selectAction->execute('mysql', $dataAction);
     * } else {
     *     echo "No columns specified for selection.";
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($data->getColumns());
    }
}
