<?php

namespace MiniCore\Module;

/**
 * Class AbstractModule
 *
 * Defines the base structure for all modules in the MiniCore framework.
 * Provides essential metadata about the module and requires a `boot` method for initialization logic.
 *
 * @package MiniCore\Module
 *
 * @example
 * // Example of a custom module extending AbstractModule
 * class UserModule extends AbstractModule
 * {
 *     public function boot(): void
 *     {
 *         // Initialization logic for the module
 *         echo "UserModule has been initialized!";
 *     }
 * }
 *
 * $userModule = new UserModule('user', 'User Module', 'Manages user data', 'John Doe', '1.0.0', 'MIT');
 * echo $userModule->getName(); // Output: User Module
 */
abstract class AbstractModule
{
    /**
     * The filesystem path to the module directory.
     *
     * @var string
     */
    private string $modulePath;

    /**
     * AbstractModule constructor.
     *
     * @param string $id          Unique identifier for the module.
     * @param string $name        Human-readable name of the module.
     * @param string $description Short description of the module.
     * @param string $author      Author of the module.
     * @param string $version     Version of the module.
     * @param string $license     License type of the module.
     *
     * @example
     * $module = new SomeModule('example', 'Example Module', 'A sample module', 'Jane Doe', '1.0.0', 'MIT');
     */
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $description,
        protected string $author,
        protected string $version,
        protected string $license,
    ) {
        // Automatically determine and store the module's filesystem path
        $this->modulePath = dirname((new \ReflectionClass($this))->getFileName());
    }

    /**
     * Get the unique identifier of the module.
     *
     * @return string The module ID.
     *
     * @example
     * echo $module->getId(); // Output: example
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the human-readable name of the module.
     *
     * @return string The module name.
     *
     * @example
     * echo $module->getName(); // Output: Example Module
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the version of the module.
     *
     * @return string The module version.
     *
     * @example
     * echo $module->getVersion(); // Output: 1.0.0
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the description of the module.
     *
     * @return string The module description.
     *
     * @example
     * echo $module->getDescription(); // Output: A sample module
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the filesystem path where the module is located.
     *
     * @return string The module path.
     *
     * @example
     * echo $module->getPath(); // Output: /var/www/html/modules/example
     */
    public function getPath(): string
    {
        return $this->modulePath;
    }

    /**
     * Abstract method to be implemented in child classes for module initialization.
     *
     * This method should contain all startup logic for the module,
     * such as registering routes, hooks, or loading configurations.
     *
     * @return void
     *
     * @example
     * public function boot(): void
     * {
     *     // Custom initialization logic
     *     Router::register('GET', '/user', new UserController());
     * }
     */
    abstract public function boot(): void;
}
