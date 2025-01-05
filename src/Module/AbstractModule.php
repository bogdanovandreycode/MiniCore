<?php

namespace MiniCore\Module;

abstract class AbstractModule
{
    private string $modulePath;

    public function __construct(
        protected string $id,
        protected string $name,
        protected string $description,
        protected string $author,
        protected string $version,
        protected string $license,
    ) {
        // Устанавливаем путь к модулю
        $this->modulePath = dirname((new \ReflectionClass($this))->getFileName());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPath(): string
    {
        return $this->modulePath;
    }

    /**
     * Method to be called when the module is booted.
     * Must be implemented in the child class.
     */
    abstract public function boot(): void;
}
