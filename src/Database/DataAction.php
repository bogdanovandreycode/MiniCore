<?php

namespace MiniCore\Database;

final class DataAction
{
    public function __construct(
        private array $columns,
        private array $properties,
    ) {}

    public function getColumn(string $name): mixed
    {
        return $this->columns[$name] ?? null;
    }

    public function getColumns(): mixed
    {
        return $this->columns;
    }

    public function getKeyColumns(): mixed
    {
        return array_keys($this->columns);
    }

    public function setColumn(string $name, mixed $value): void
    {
        $this->columns[$name] = $value;
    }

    public function setColumns(array $columns): void
    {
        foreach ($columns as $key => $value) {
            $this->columns[$key] = $value;
        }
    }

    public function getPropery(string $name): mixed
    {
        return $this->properties[$name] ?? null;
    }

    public function getProperties(): mixed
    {
        return $this->properties;
    }

    public function setProperty(string $name, mixed $value): void
    {
        $this->properties[$name] = $value;
    }

    public function setProperties(array $properties): void
    {
        foreach ($properties as $key => $value) {
            $this->properties[$key] = $value;
        }
    }
}
