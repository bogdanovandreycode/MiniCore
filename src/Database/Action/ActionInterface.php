<?php

namespace MiniCore\Database\Action;

/**
 * Interface ActionInterface
 *
 * Defines a contract for database actions such as SELECT, INSERT, UPDATE, and DELETE.
 * Any class implementing this interface must provide methods for executing actions
 * and validating input data for database operations.
 *
 * This interface ensures consistency and extensibility across different database actions.
 *
 * @package MiniCore\Database
 */
interface ActionInterface
{
    /**
     * Execute the action with the provided data.
     *
     * This method executes the action logic, such as building and running an SQL query.
     * The `$data` parameter contains the columns, conditions, and parameters for the query.
     *
     * @param DataAction $data The data required to build and execute the SQL query.
     * @return mixed The result of the query execution (e.g., query result or execution status).
     *
     * @example
     * $dataAction = new DataAction();
     * $dataAction->addColumn('username');
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
     * $result = $action->execute($dataAction);
     */
    public function execute(string $repositoryName, ?DataAction $data): mixed;

    /**
     * Validate the provided data before executing the action.
     *
     * This method ensures that the data passed to the action is correct and complete.
     * For example, it may check if required columns or conditions are present.
     *
     * @param DataAction $data The data to validate.
     * @return bool True if the data is valid, false otherwise.
     *
     * @example
     * $isValid = $action->validate($dataAction);
     * if ($isValid) {
     *     $action->execute($dataAction);
     * }
     */
    public function validate(DataAction $data): bool;

    public function getName(): string;

    public function getAvailableRepositories(): array;

    public function checkAvailabilityRepository(string $name): bool;
}
