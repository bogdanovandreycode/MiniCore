<?php

namespace MiniCore\Tests\Module\Modules\TestModule;

use MiniCore\Module\AbstractModule;

class Module extends AbstractModule
{
    public bool $booted = false;

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

    public function boot(): void
    {
        $this->booted = true;
    }
}
