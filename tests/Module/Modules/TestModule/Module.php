<?php

namespace MiniCore\Tests\Module\Modules\TestModule;

use MiniCore\Module\AbstractModule;

/**
 * Class Module
 *
 * This is a test implementation of the AbstractModule class.
 *
 * The Test Module is designed specifically for unit testing the ModuleManager functionality.
 * It provides a minimal structure for verifying module loading, initialization, and bootstrapping.
 *
 * Key Features:
 * - Defines basic module metadata such as ID, name, description, author, version, and license.
 * - Implements the boot method to simulate module initialization.
 * - Contains a public $booted flag to track whether the module has been initialized.
 */
class Module extends AbstractModule
{
    /**
     * Indicates whether the module has been booted.
     *
     * @var bool
     */
    public bool $booted = false;

    /**
     * Module constructor initializes metadata.
     */
    public function __construct()
    {
        parent::__construct(
            'TestModule',
            'Test Module',
            'Модуль для тестирования',
            'Developer',
            '1.0.0',
            'MIT'
        );
    }

    /**
     * Boots the module by setting the booted flag to true.
     */
    public function boot(): void
    {
        $this->booted = true;
    }
}
