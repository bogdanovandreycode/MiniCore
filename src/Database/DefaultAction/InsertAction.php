<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use Minicore\Database\Repository\RepositoryManager;

/**
 * Class InsertAction
 *
 * Handles inserting data into a database table.
 * This action dynamically builds and executes an `INSERT INTO` SQL query
 * using the provided data and parameters.
 *
 * @package MiniCore\Database\DefaultAction
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
 * if ($insertAction->validate($dataAction)) {
 *     $insertAction->execute('mysql', $dataAction);
 * }
 */
class InsertAction extends AbstractAction implements ActionInterface
{
    /**
     * InsertAction constructor.
     *
     * Initializes a new instance for inserting data into a database table.
     *
     * @param string $tableName The name of the table where data will be inserted.
     *
     * @example
     * // Initialize InsertAction for the 'products' table
     * $insertAction = new InsertAction('products');
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
     * Dynamically constructs an `INSERT INTO` SQL query using provided columns and parameters,
     * then executes the query using prepared statements to prevent SQL injection.
     *
     * @param string $repositoryName The repository where the insert operation should be executed.
     * @param DataAction|null $data The data containing columns and parameters for the insert operation.
     * @return mixed The result of the query execution (usually `true` on success).
     *
     * @throws \RuntimeException If no columns are provided for the insert operation.
     *
     * @example
     * // Insert a new product into the 'products' table
     * $dataAction = new DataAction();
     * $dataAction->addColumn('name');
     * $dataAction->addColumn('price');
     * $dataAction->addParameters([
     *     'name' => 'Laptop',
     *     'price' => 1500,
     * ]);
     *
     * $insertAction = new InsertAction('products');
     * $insertAction->execute('mysql', $dataAction);
     */
    public function execute(string $repositoryName, ?DataAction $data): mixed
    {
        if (!$this->validate($data)) {
            throw new \RuntimeException("No columns provided for insert operation.");
        }

        $columns = $data->getColumns();
        $columnsList = implode(', ', array_map(fn($col) => "`$col`", $columns)); // Escaping column names
        $placeholders = implode(', ', array_map(fn($col) => ":$col", $columns));

        $sql = "INSERT INTO `{$this->tableName}` ($columnsList) VALUES ($placeholders)";

        return RepositoryManager::execute(
            $repositoryName,
            $sql,
            $data->getParameters()
        );
    }

    /**
     * Validate the provided data for the insert action.
     *
     * Ensures that at least one column is specified for insertion and
     * that the number of columns matches the number of parameters.
     *
     * @param DataAction $data The data used for validation.
     * @return bool True if valid data is provided, false otherwise.
     *
     * @example
     * // Validating data before insertion:
     * if ($insertAction->validate($dataAction)) {
     *     $insertAction->execute('mysql', $dataAction);
     * }
     */
    public function validate(DataAction $data): bool
    {
        $columns = $data->getColumns();
        $parameters = $data->getParameters();
        return !empty($columns) && count($parameters) >= count($columns);
    }
}
