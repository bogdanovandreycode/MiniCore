<?php

namespace MiniCore\Tests;

use PHPUnit\Framework\TestCase;
use MiniCore\Tests\Module\Modules\TestModule\Module;

class AutoloadTest extends TestCase
{
    public function testAutoload()
    {
        $this->assertTrue(class_exists(Module::class), 'Автозагрузка не работает для Module');
    }
}
