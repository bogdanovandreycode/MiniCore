<?php

namespace MiniCore\Database;

final class DataAction
{
    private array $columns = [];        // Колонки для SELECT
    private array $properties = [];     // Универсальные свойства (с порядком добавления)
    private array $parameters = [];     // Параметры для prepared statements

    /**
     * Добавить колонку для SELECT.
     */
    public function addColumn(string $name): void
    {
        $this->columns[] = $name;
    }

    /**
     * Получить все колонки для SELECT.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Добавить свойство с учётом порядка добавления.
     */
    public function addProperty(string $type, string $condition, array $parameters = []): void
    {
        $this->properties[] = [
            'type' => strtoupper($type),
            'condition' => $condition,
        ];
        $this->addParameters($parameters);
    }

    /**
     * Получить все свойства.
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name): array
    {
        return $this->properties[$name] ?? [];
    }

    /**
     * Добавить параметры для prepared statements.
     */
    public function addParameters(array $parameters): void
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Получить все параметры.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
