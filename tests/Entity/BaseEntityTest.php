<?php

namespace MiniCore\Tests\Entity;

use PHPUnit\Framework\TestCase;
use MiniCore\Entity\BaseEntity;

/**
 * Заглушка для тестирования BaseEntity
 */
class TestEntity extends BaseEntity {}

class BaseEntityTest extends TestCase
{
    private TestEntity $entity;

    protected function setUp(): void
    {
        $this->entity = new TestEntity(['name' => 'John', 'email' => 'john@example.com']);
    }

    /**
     * Тест получения и установки свойства
     */
    public function testGetAndSetProperty()
    {
        $this->assertEquals('John', $this->entity->name);

        $this->entity->name = 'Jane';
        $this->assertEquals('Jane', $this->entity->name);
    }

    /**
     * Тест проверки существования свойства
     */
    public function testIssetProperty()
    {
        $this->assertTrue(isset($this->entity->email));
        $this->assertFalse(isset($this->entity->age));
    }

    /**
     * Тест удаления свойства
     */
    public function testUnsetProperty()
    {
        unset($this->entity->email);
        $this->assertFalse(isset($this->entity->email));
    }

    /**
     * Тест конвертации в массив
     */
    public function testToArray()
    {
        $expected = ['name' => 'John', 'email' => 'john@example.com'];
        $this->assertEquals($expected, $this->entity->toArray());
    }

    /**
     * Тест заполнения из массива
     */
    public function testFromArray()
    {
        $this->entity->fromArray(['name' => 'Alice', 'email' => 'alice@example.com']);
        $this->assertEquals('Alice', $this->entity->name);
    }

    /**
     * Тест работы с ID
     */
    public function testGetAndSetId()
    {
        $this->assertNull($this->entity->getId());

        $this->entity->setId(42);
        $this->assertEquals(42, $this->entity->getId());
    }
}
