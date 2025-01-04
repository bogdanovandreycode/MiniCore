<?php

namespace Vendor\Undermarket\Core\Database;

use Vendor\Undermarket\Core\Database\DataBase;
use Vendor\Undermarket\Core\Database\DefaultAction\DeleteAction;
use Vendor\Undermarket\Core\Database\DefaultAction\InsertAction;
use Vendor\Undermarket\Core\Database\DefaultAction\SelectAction;
use Vendor\Undermarket\Core\Database\DefaultAction\UpdateAction;

abstract class Table
{
    protected array $actions = [];

    public function __construct(
        protected string $name,
        protected array $scheme,
    ) {
        $this->actions = [
            new InsertAction($this->name),
            new SelectAction($this->name),
            new UpdateAction($this->name),
            new DeleteAction($this->name),
        ];
    }

    public function create(): void
    {
        $fields = $this->getSchemeToString();

        DataBase::query(
            "CREATE TABLE {$this->name} ({$fields})"
        );
    }

    public function drop(): void
    {
        DataBase::query(
            "DROP TABLE `{$this->name}`"
        );
    }

    public function getSchemeToString(): string
    {
        $fields = '';

        foreach ($this->scheme as $fieldName => $fieldDefinition) {
            $fields .= "$fieldName $fieldDefinition, ";
        }

        return rtrim($fields, ', ');
    }

    public function addAction(ActionInterface $action): void
    {
        $this->actions[] = $action;
    }

    public function removeAction(string $actionName): void
    {
        foreach ($this->actions as $key => $action) {
            if ($action->getName() === $actionName) {
                unset($this->actions[$key]);
                break;
            }
        }
    }

    public function execute(string $actionName, DataAction $data): mixed
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === $actionName) {
                return $action->execute($data);
            }
        }

        return null;
    }
}
