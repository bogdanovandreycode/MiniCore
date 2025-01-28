<?php

namespace MiniCore\Database\Action;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\ActionInterface;

abstract class AbstractAction implements ActionInterface
{
    public function __construct(
        protected string $name,
        protected array $availableRepositories = [],
    ) {}

    /**
     * Get the name of the action.
     *
     * @return string The action name ('delete').
     *
     * @example
     * $deleteAction = new DeleteAction('users');
     * echo $deleteAction->getName(); // Output: delete
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getAvailableRepositories(): array
    {
        return $this->availableRepositories;
    }

    public function checkAvailabilityRepository(string $name): bool
    {
        return in_array($name, $this->availableRepositories);
    }

    abstract public function execute(string $repositoryName, DataAction $data): mixed;

    abstract public function validate(DataAction $data): bool;
}
