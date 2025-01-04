<?php

namespace MiniCore\Entity;

abstract class BaseEntity
{
    /**
     * Array to hold the data of the entity.
     */
    protected array $data = [];

    /**
     * BaseEntity constructor.
     * Optionally, pass initial data to populate the entity.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get a property value.
     */
    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Set a property value.
     */
    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if a property is set.
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Unset a property.
     */
    public function __unset(string $name): void
    {
        unset($this->data[$name]);
    }

    /**
     * Get all data as an array.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Populate the entity with an array of data.
     */
    public function fromArray(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Get the primary key value (assuming 'id' is the primary key by default).
     */
    public function getId(): mixed
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set the primary key value (assuming 'id' is the primary key by default).
     */
    public function setId(mixed $id): void
    {
        $this->data['id'] = $id;
    }
}
