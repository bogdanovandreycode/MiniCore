<?php

namespace MiniCore\Database\Action;

/**
 * Interface ActionInterface
 *
 * Defines a contract for database actions such as SELECT, INSERT, UPDATE, and DELETE.
 * Any class implementing this interface must provide methods for executing actions,
 * validating input data, and checking availability across different repositories.
 *
 * This interface ensures consistency and extensibility across various database actions.
 *
 * @package MiniCore\Database\Action
 *
 * @example
 * class InsertAction implements ActionInterface
 * {
 *     public function execute(string $repositoryName, ?DataAction $data): mixed
 *     {
 *         // Perform insertion logic
 *         return true;
 *     }
 * 
 *     public function validate(DataAction $data): bool
 *     {
 *         return !empty($data->getColumns());
 *     }
 * 
 *     public function getName(): string
 *     {
 *         return 'insert';
 *     }
 * 
 *     public function getAvailableRepositories(): array
 *     {
 *         return ['mysql', 'postgresql'];
 *     }
 * 
 *     public function checkAvailabilityRepository(string $name): bool
 *     {
 *         return in_array($name, $this->getAvailableRepositories());
 *     }
 * }
 */
interface ActionInterface
{
    /**
     * Execute the action with the provided data.
     *
     * This method executes the action logic, such as constructing and executing an SQL query.
     * The `$data` parameter contains columns, conditions, and parameters for the query.
     *
     * @param string $repositoryName The name of the repository where the action should be executed.
     * @param DataAction|null $data The data required to build and execute the SQL query.
     * @return mixed The result of the query execution (e.g., query result, boolean status, or an error).
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('username');
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
     * $result = $action->execute('users', $dataAction);
     */
    public function execute(string $repositoryName, ?DataAction $data): mixed;

    /**
     * Validate the provided data before executing the action.
     *
     * This method ensures that the data passed to the action is correct and complete.
     * It may check if required columns or conditions are provided.
     *
     * @param DataAction $data The data to validate.
     * @return bool True if the data is valid, false otherwise.
     *
     * @example
     * if ($action->validate($dataAction)) {
     *     $action->execute('users', $dataAction);
     * }
     */
    public function validate(DataAction $data): bool;

    /**
     * Get the name of the action.
     *
     * @return string The action name (e.g., 'insert', 'update', 'delete').
     *
     * @example
     * echo $action->getName(); // Output: 'insert'
     */
    public function getName(): string;

    /**
     * Get the list of available repositories where this action can be performed.
     *
     * @return array List of repository names (e.g., ['mysql', 'postgresql']).
     *
     * @example
     * print_r($action->getAvailableRepositories()); // Output: ['mysql', 'postgresql']
     */
    public function getAvailableRepositories(): array;

    /**
     * Check if the action is available for a given repository.
     *
     * @param string $name The repository name to check.
     * @return bool True if the action can be executed in the specified repository, false otherwise.
     *
     * @example
     * var_dump($action->checkAvailabilityRepository('mysql')); // Output: true
     */
    public function checkAvailabilityRepository(string $name): bool;
}
