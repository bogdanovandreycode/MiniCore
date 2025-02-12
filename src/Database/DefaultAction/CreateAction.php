<?php

namespace MiniCore\Database\DefaultAction;

use Exception;
use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use MiniCore\Database\Repository\RepositoryManager;

/**
 * Class CreateAction
 *
 * Handles the creation of a new database table dynamically.
 * This action builds and executes a CREATE TABLE SQL query using
 * the provided column definitions and constraints.
 *
 * @package MiniCore\Database\DefaultAction
 *
 * @example
 * // Example of using CreateAction to create a "users" table
 * $createAction = new CreateAction('users');
 * 
 * $dataAction = new DataAction();
 * $dataAction->addParameters([
 *     'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
 *     'username' => 'VARCHAR(255) NOT NULL',
 *     'email' => 'VARCHAR(255) UNIQUE NOT NULL'
 * ]);
 * 
 * if ($createAction->validate($dataAction)) {
 *     $result = $createAction->execute('mysql', $dataAction);
 *     echo $result ? 'Table created.' : 'Creation failed.';
 * } else {
 *     echo 'Invalid table structure.';
 * }
 */
class CreateAction extends AbstractAction implements ActionInterface
{
    /**
     * CreateAction constructor.
     *
     * Initializes a new instance for creating a database table.
     *
     * @param string $tableName The name of the table to be created.
     *
     * @example
     * // Initialize CreateAction for the 'products' table
     * $createAction = new CreateAction('products');
     */
    public function __construct(
        public string $tableName
    ) {
        parent::__construct(
            'create',
            ['mysql', 'postgresql']
        );
    }

    /**
     * Execute the CREATE TABLE SQL query.
     *
     * Builds a CREATE TABLE SQL statement using the provided columns and constraints,
     * then executes it using the specified repository.
     *
     * @param string $repositoryName The repository where the table should be created.
     * @param DataAction|null $data Contains column definitions and constraints for the table.
     * @return mixed The result of the query execution.
     * @throws Exception If column definitions are missing.
     *
     * @example
     * // Create a "users" table with columns
     * $createAction = new CreateAction('users');
     * 
     * $dataAction = new DataAction();
     * $dataAction->addParameters([
     *     'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
     *     'username' => 'VARCHAR(255) NOT NULL',
     *     'email' => 'VARCHAR(255) UNIQUE NOT NULL'
     * ]);
     * 
     * $createAction->execute('mysql', $dataAction);
     */
    public function execute(string $repositoryName, ?DataAction $data): mixed
    {
        $parameters = $data->getParameters();

        if (empty($parameters)) {
            throw new Exception("Error creating table {$this->tableName}. Columns not found.", 1);
        }

        $parametersString = $this->getParametersToString($parameters);
        $sql = "CREATE TABLE {$this->tableName} ({$parametersString})";

        return RepositoryManager::execute(
            $repositoryName,
            $sql
        );
    }

    /**
     * Convert column definitions into a formatted SQL string.
     *
     * @param array $parameters Associative array of column definitions.
     * @return string The formatted SQL column definition string.
     *
     * @example
     * $parameters = [
     *     'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
     *     'username' => 'VARCHAR(255) NOT NULL',
     *     'email' => 'VARCHAR(255) UNIQUE NOT NULL'
     * ];
     * echo $this->getParametersToString($parameters);
     * // Output: "id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(255) NOT NULL, email VARCHAR(255) UNIQUE NOT NULL"
     */
    private function getParametersToString(array $parameters): string
    {
        $fields = [];

        foreach ($parameters as $fieldName => $fieldDefinition) {
            $fields[] = "$fieldName $fieldDefinition";
        }

        return implode(', ', $fields);
    }

    /**
     * Validate the provided data for the CREATE action.
     *
     * Ensures that the table name and column definitions are provided.
     *
     * @param DataAction $data The data containing table column definitions.
     * @return bool True if valid, false otherwise.
     *
     * @example
     * $createAction = new CreateAction('users');
     * $dataAction = new DataAction();
     * $dataAction->addParameters([
     *     'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
     *     'username' => 'VARCHAR(255) NOT NULL'
     * ]);
     * 
     * if ($createAction->validate($dataAction)) {
     *     echo 'Valid table structure.';
     * } else {
     *     echo 'Invalid structure.';
     * }
     */
    public function validate(DataAction $data): bool
    {
        return !empty($data->getParameters()) && !empty($this->tableName);
    }
}
