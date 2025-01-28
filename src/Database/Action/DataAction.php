<?php

namespace MiniCore\Database\Action;

/**
 * Class DataAction
 *
 * A data container for building SQL queries dynamically.
 * This class helps in constructing SQL statements by managing columns, conditions, and parameters.
 * It is used by different database actions like SELECT, INSERT, UPDATE, and DELETE.
 *
 * @package MiniCore\Database
 */
final class DataAction
{
    /**
     * @var array Columns used in SQL queries (e.g., SELECT, INSERT).
     */
    private array $columns = [];

    /**
     * @var array Query conditions and clauses (e.g., WHERE, ORDER BY).
     *            Preserves the order in which properties are added.
     */
    private array $properties = [];

    /**
     * @var array Parameters for prepared SQL statements.
     */
    private array $parameters = [];

    /**
     * Add a column for the SQL query (e.g., SELECT, INSERT).
     *
     * @param string $name The name of the column.
     * 
     * @example
     * $dataAction->addColumn('username');
     * $dataAction->addColumn('email');
     */
    public function addColumn(string $name): void
    {
        $this->columns[] = $name;
    }

    /**
     * Get all columns added for the query.
     *
     * @return array List of columns.
     * 
     * @example
     * $columns = $dataAction->getColumns(); // ['username', 'email']
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Add a query property such as WHERE, ORDER BY, etc.
     *
     * @param string $type The type of the property (e.g., 'WHERE', 'ORDER BY').
     * @param string $condition The condition or clause for the query.
     * @param array $parameters Parameters to bind in the query.
     * 
     * @example
     * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
     * $dataAction->addProperty('ORDER BY', 'created_at DESC');
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
     * Get all properties added to the query.
     *
     * @return array List of properties with their types and conditions.
     * 
     * @example
     * $properties = $dataAction->getProperties();
     * // Output: [['type' => 'WHERE', 'condition' => 'id = :id']]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Get a specific property by type.
     *
     * @param string $name The property type (e.g., 'WHERE').
     * @return array The condition(s) for the specified property type.
     * 
     * @example
     * $where = $dataAction->getProperty('WHERE');
     */
    public function getProperty(string $name): array
    {
        return $this->properties[$name] ?? [];
    }

    /**
     * Add parameters for the prepared SQL statement.
     *
     * @param array $parameters Associative array of parameters.
     * 
     * @example
     * $dataAction->addParameters(['id' => 1, 'status' => 'active']);
     */
    public function addParameters(array $parameters): void
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Get all parameters for the prepared SQL statement.
     *
     * @return array Associative array of parameters.
     * 
     * @example
     * $params = $dataAction->getParameters(); // ['id' => 1, 'status' => 'active']
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
