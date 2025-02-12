<?php

namespace MiniCore\Database\Action;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\ActionInterface;

/**
 * Abstract class AbstractAction
 *
 * Serves as a base class for all database actions, providing core functionalities such as name retrieval,
 * available repository checks, and a contract for executing and validating actions.
 *
 * @package MiniCore\Database\Action
 *
 * @example
 * // Example usage:
 * class DeleteAction extends AbstractAction
 * {
 *     public function execute(string $repositoryName, ?DataAction $data): mixed
 *     {
 *         // Execute deletion logic
 *         return true;
 *     }
 * 
 *     public function validate(DataAction $data): bool
 *     {
 *         return !empty($data);
 *     }
 * }
 * 
 * $deleteAction = new DeleteAction('delete', ['users']);
 * echo $deleteAction->getName(); // Output: delete
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * Constructor for AbstractAction
     *
     * @param string $name The name of the action (e.g., 'delete', 'insert').
     * @param array $availableRepositories The list of repositories where this action can be performed.
     */
    public function __construct(
        protected string $name,
        protected array $availableRepositories = [],
    ) {}

    /**
     * Get the name of the action.
     *
     * @return string The action name.
     *
     * @example
     * $deleteAction = new DeleteAction('delete');
     * echo $deleteAction->getName(); // Output: delete
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the list of available repositories for this action.
     *
     * @return array List of repository names where this action can be performed.
     *
     * @example
     * $deleteAction = new DeleteAction('delete', ['users', 'posts']);
     * print_r($deleteAction->getAvailableRepositories()); // Output: ['users', 'posts']
     */
    public function getAvailableRepositories(): array
    {
        return $this->availableRepositories;
    }

    /**
     * Check if the action is available for the given repository.
     *
     * @param string $name The name of the repository to check.
     * @return bool True if the action can be executed in the given repository, false otherwise.
     *
     * @example
     * $deleteAction = new DeleteAction('delete', ['users']);
     * var_dump($deleteAction->checkAvailabilityRepository('users')); // Output: true
     */
    public function checkAvailabilityRepository(string $name): bool
    {
        return in_array($name, $this->availableRepositories);
    }

    /**
     * Execute the action on a specific repository.
     *
     * @param string $repositoryName The name of the repository where the action will be executed.
     * @param DataAction|null $data The data object containing parameters for the action.
     * @return mixed The result of the execution.
     *
     * @example
     * $dataAction = new DataAction();
     * $deleteAction->execute('users', $dataAction);
     */
    abstract public function execute(string $repositoryName, ?DataAction $data): mixed;

    /**
     * Validate the provided data before executing the action.
     *
     * @param DataAction $data The data to be validated.
     * @return bool True if the data is valid, false otherwise.
     *
     * @example
     * $dataAction = new DataAction();
     * $isValid = $deleteAction->validate($dataAction);
     */
    abstract public function validate(DataAction $data): bool;
}
