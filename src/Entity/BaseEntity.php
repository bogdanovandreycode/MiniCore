<?php

namespace MiniCore\Entity;

/**
 * Class BaseEntity
 *
 * An abstract base class for creating data entities. This class provides
 * dynamic property access and common methods for handling entity data.
 *
 * @package MiniCore\Entity
 */
abstract class BaseEntity
{
    /**
     * @var array $data Array to hold the entity's data.
     */
    protected array $data = [];

    /**
     * BaseEntity constructor.
     *
     * Allows optional initialization of the entity with an array of data.
     *
     * @param array $data Initial data for the entity.
     *
     * @example
     * $user = new UserEntity(['name' => 'John', 'email' => 'john@example.com']);
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Magic method to get a property value.
     *
     * @param string $name The name of the property.
     * @return mixed|null The value of the property or null if not set.
     *
     * @example
     * echo $user->name; // Outputs 'John'
     */
    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic method to set a property value.
     *
     * @param string $name The name of the property.
     * @param mixed $value The value to set.
     *
     * @example
     * $user->name = 'Jane';
     */
    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Magic method to check if a property is set.
     *
     * @param string $name The name of the property.
     * @return bool True if set, false otherwise.
     *
     * @example
     * if (isset($user->email)) { ... }
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Magic method to unset a property.
     *
     * @param string $name The name of the property.
     *
     * @example
     * unset($user->email);
     */
    public function __unset(string $name): void
    {
        unset($this->data[$name]);
    }

    /**
     * Convert the entity data to an associative array.
     *
     * @return array The entity's data as an array.
     *
     * @example
     * $array = $user->toArray();
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Populate the entity with an array of data.
     *
     * @param array $data The data to populate the entity with.
     *
     * @example
     * $user->fromArray(['name' => 'Alice', 'email' => 'alice@example.com']);
     */
    public function fromArray(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Get the primary key value (default 'id').
     *
     * @return mixed|null The ID of the entity or null if not set.
     *
     * @example
     * echo $user->getId(); // Outputs the user's ID
     */
    public function getId(): mixed
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set the primary key value (default 'id').
     *
     * @param mixed $id The ID to assign.
     *
     * @example
     * $user->setId(5);
     */
    public function setId(mixed $id): void
    {
        $this->data['id'] = $id;
    }
}
