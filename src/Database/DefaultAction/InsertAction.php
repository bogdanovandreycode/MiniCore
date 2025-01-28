<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use Minicore\Database\RepositoryManager;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;


/**
 * Class InsertAction
 *
 * Handles inserting data into a database table.
 * This action dynamically builds and executes an `INSERT INTO` SQL query
 * using the provided data and parameters.
 *
 * @example
 * // Example of inserting a new user into the 'users' table:
 * $dataAction = new DataAction();
 * $dataAction->addColumn('username');
 * $dataAction->addColumn('email');
 * $dataAction->addParameters([
 *     'username' => 'john_doe',
 *     'email' => 'john@example.com',
 * ]);
 *
 * $insertAction = new InsertAction('users');
 * $insertAction->execute($dataAction);
 */
class InsertAction extends AbstractAction implements ActionInterface
{
    /**
     * InsertAction constructor.
     *
     * @param string $tableName The name of the table where data will be inserted.
     */
    public function __construct(
        public string $tableName
    ) {
        parent::__construct(
            'insert',
            ['mysql', 'postgresql']

        );
    }

    /**
     * Execute the `INSERT INTO` SQL query.
     *
     * Dynamically constructs an `INSERT` SQL query using provided columns and parameters,
     * and executes the query using prepared statements for security.
     *
     * @param DataAction $data The data containing columns and parameters for the insert operation.
     * @return mixed The result of the query execution (usually `true` on success).
     *
     * @throws \RuntimeException If no columns are provided for the insert operation.
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('username');
     * $dataAction->addColumn('email');
     * $dataAction->addParameters([
     *     'username' => 'jane_doe',
     *     'email' => 'jane@example.com',
     * ]);
     *
     * $insertAction = new InsertAction('users');
     * $insertAction->execute($dataAction);
     */
    public function execute(string $repositoryName, DataAction $data): mixed
    {
        $columns = $data->getColumns();

        if (empty($columns)) {
            throw new \RuntimeException("No columns provided for insert operation.");
        }

        $columnsList = implode(', ', $columns);
        $placeholders = implode(', ', array_map(fn($column) => ":$column", $columns));
        $sql = "INSERT INTO {$this->tableName} ($columnsList) VALUES ($placeholders)";

        return RepositoryManager::execute(
            $repositoryName,
            $sql,
            $data->getParameters()
        );
    }

    /**
     * Validate the provided data for the insert action.
     *
     * Ensures that at least one column is specified for insertion.
     *
     * @param DataAction $data The data used for validation.
     * @return bool True if valid data is provided, false otherwise.
     *
     * @example
     * // Validating data before insertion:
     * if ($insertAction->validate($dataAction)) {
     *     $insertAction->execute($dataAction);
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($data->getColumns());
    }
}
