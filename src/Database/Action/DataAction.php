<?php

namespace MiniCore\Database\Action;

/**
 * Class DataAction
 *
 * A data container for dynamically building SQL queries.
 * This class provides methods for managing columns, conditions, and parameters
 * in SQL statements used by various database actions such as SELECT, INSERT, UPDATE, and DELETE.
 *
 * @package MiniCore\Database\Action
 *
 * @example
 * // Example usage:
 * $dataAction = new DataAction();
 * $dataAction->addColumn('username');
 * $dataAction->addColumn('email');
 * $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
 * $dataAction->addParameters(['status' => 'active']);
 */
final class DataAction
{
    /**
     * @var array List of columns used in the SQL query (e.g., for SELECT, INSERT).
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
     * Add a column for the SQL query.
     *
     * @param string $name The name of the column.
     * @return void
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
     * @return array List of column names.
     *
     * @example
     * $columns = $dataAction->getColumns();
     * print_r($columns); // ['username', 'email']
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
     * @return void
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
     * print_r($properties);
     * // Output:
     * // [['type' => 'WHERE', 'condition' => 'id = :id']]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Get a specific property by type.
     *
     * @param string $name The property type (e.g., 'WHERE').
     * @return array List of conditions for the specified property type.
     *
     * @example
     * $where = $dataAction->getProperty('WHERE');
     * print_r($where);
     */
    public function getProperty(string $name): array
    {
        return $this->properties[$name] ?? [];
    }

    /**
     * Add multiple parameters for the prepared SQL statement.
     *
     * @param array $parameters Associative array of parameters.
     * @return void
     *
     * @example
     * $dataAction->addParameters(['id' => 1, 'status' => 'active']);
     */
    public function addParameters(array $parameters): void
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Add a single parameter for the prepared SQL statement.
     *
     * @param string $key The parameter name.
     * @param mixed $value The value to assign to the parameter.
     * @return void
     *
     * @example
     * $dataAction->addParameter('status', 'active');
     */
    public function addParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Get all parameters for the prepared SQL statement.
     *
     * @return array Associative array of parameters.
     *
     * @example
     * $params = $dataAction->getParameters();
     * print_r($params); // ['id' => 1, 'status' => 'active']
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
